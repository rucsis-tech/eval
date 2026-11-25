<?php

class PaymentHistory extends Model {

    protected static string $tableName = 'payment_history';

    // -----------------------------------------------------------------------------------------------------------------------------------
    //　---------　APP独自設定値
    const RESULT_NONE = ''; // 初期化
    const RESULT_CHARGING = 'CHARGING'; // 購読チャージ試行中
    const RESULT_FAILED = 'FAILED'; // 失敗
    // ---------- GMO定義の戻り値
    const RESULT_CREATE = 'CREATE';//　URL発行済み	CREATE	決済URL取得APIを実行して、URLを発行した状態
    const RESULT_SEND = 'SEND';//　メール送信済み	SEND	お客様に決済URL案内メールを送信した状態
    const RESULT_PAYSTART = 'PAYSTART';//　決済開始	   PAYSTART	リンクタイプ Plus決済URLにお客様がアクセスし決済入力画面を表示した状態
    const RESULT_CONFIRM = 'CONFIRM';//　確認中	        CONFIRM	お客様が決済選択/入力画面上で確定ボタンを押下し、決済確認画面を表示した状態
    const RESULT_REQPROCESS = 'REQPROCESS';//　決済手続中	   REQPROCESS	お客様が決済確認画面上で確定ボタンを押下し、決済処理が開始された状態
    const RESULT_REQSUCCESS = 'REQSUCCESS';//　要求成功	   REQSUCCESS	・お客様が決済手続きを完了し、お客様の入金処理が必要な状態

    //本人認証サービスを利用し、お客様が認証中の状態
    const RESULT_PAYSUCCESS = 'PAYSUCCESS';//　決済完了	   PAYSUCCESS	決済手続きが完了した状態
    const RESULT_ERROR = 'ERROR';//　決済失敗	   ERROR	決済手続きが失敗した状態（お客様の操作による決済手続きの中止を含みます）
    const RESULT_EXPIRED = 'EXPIRED';//　期限切れ	   EXPIRED	加盟店様が指定した取引有効日数内に決済手続きが完了しなかった状態
    const RESULT_INVALID = 'INVALID';//　無効	       INVALID	加盟店様の操作によりリンクタイプ Plus取引が無効化された状態（加盟店様による取引中止）

    // ---------------------------------------
    // 個別キャンセル措置
    const RESULT_REVOCATION = 'REVOCATION'; // 個別キャンセル措置 後追いでの決済撤回

    // -----------------------------------------------------------------------------------------------------------------------------------

    // -------------------------クレカ
    const STATUS_AUTH = 'AUTH'; //:仮売上
    const STATUS_CAPTURE = 'CAPTURE'; //:即時売上
    const STATUS_PAYSUCCESS = 'PAYSUCCESS';//　決済完了	   PAYSUCCESS	決済手続きが完了した状態
    const STATUS_UNPROSESSED = 'UNPROSESSED'; //:未決済（オーソリエラー等で決済が完了していない状態）
    const STATUS_AUTHENTICATED = 'AUTHENTICATED'; //:未決済（3DS登録済）
    // ------------------------- 銀行振込
    const STATUS_TRADING = 'TRADING'; //:取引中（お客様に振込依頼後、未入金状態）
    const STATUS_TRANSFERRED = 'TRANSFERRED'; //:入金済み


    // -----------------------------------------------------------------------------------------------------------------------------------
    // 決済手段

    const METHOD_CREDIT = 'credit'; // クレジットカード
    const METHOD_ONFILE_CREDIT = 'credit,onfile'; // 自動決済・クレジットカード
    const METHOD_VIRTUAL = 'virtualaccount'; // 銀行振込（バーチャル口座）
    const METHOD_AOZORA = 'ganb'; // 銀行振込（バーチャル口座 あおぞら）
    const METHOD_ADJUST = 'adjust'; // 過剰金調整等　調整決済

    const METHODS = [
        self::METHOD_CREDIT => 'クレカ',
        self::METHOD_AOZORA => '銀行振込',
        self::METHOD_ADJUST => '調整決済',
    ];

    /**
     * オーダーID指定生成
     * @param $uid
     * @return string
     * @throws Exception
     */

    static
    public function createOrderId($uid) {
        $time = time();
        while (true) {
            $ymdHis = date("ymdHis", $time);
            $order_id = $uid . '-' . $ymdHis;
            if (empty(self::first('order_id', $order_id))) break;
            $time++;
        }
        return $order_id;

    }

    static
    public function tranDate($tranDate) {
        return substr($tranDate, 0, 4)
            . '-' . substr($tranDate, 4, 2)
            . '-' . substr($tranDate, 6, 2)
            . ' ' . substr($tranDate, 8, 2)
            . ':' . substr($tranDate, 10, 2)
            . ':' . substr($tranDate, 12, 2);
    }

    /**
     * 決済遷移後レスポンス処理
     * @param $response
     * @return bool
     * @throws Exception
     */
    public function setResponse($response): bool {
        $result = false;
        $Result = $response['transactionresult']['Result'];
        $Status = $response['ganb']['Status'] ?? ($response['credit']['Status'] ?? '');
        if ($this->result != self::RESULT_PAYSUCCESS) {
            if ($Result == self::RESULT_PAYSUCCESS) {
                // 決済成功になっていない場合のみ付与処理実行
                $this->result = self::RESULT_PAYSUCCESS;
                // 売上たった
                $result = true;
                $Item = PaymentItem::where('id', $this->item_id)->first();
                $this->purchased_at = isset($response['credit']['TranDate']) ? self::tranDate($response['credit']['TranDate']) : self::date();
                $this->addition($Item);
            }
        }
        $this->work(['response' => $response]);
        $this->save();

        return $result;
    }

    /**
     * Webhook対応
     * @param $webhook
     * @param $order
     * @return bool
     * @throws Exception
     */
    public function setOrder($order, $webhook = []): bool {
        $purchased = false;
        $Status = $order['orderReference']['status'] ?? '';
        $Item = PaymentItem::where('id', $this->item_id)->first();
        if ($this->result != self::RESULT_PAYSUCCESS) {
            // まだ決済成功になっていない場合のみ実行
            $this->result = $Status;
            if (in_array($Status, [self::STATUS_CAPTURE, self::STATUS_PAYSUCCESS, self::STATUS_AUTH])) {
                // 今売上たった
                $this->result = self::RESULT_PAYSUCCESS;
                if (isset($webhook['TranDate'])) $this->purchased_at = self::tranDate($webhook['TranDate']);
                else $this->purchased_at = self::date(strtotime($order['orderReference']['updated'] ?? ''));
                $this->addition($Item);
                $purchased = true;
            }
        }
        if (isset($order['cashResult']['paymentExpiryDateTime'])) {
            $this->expire_at = date("Y-m-d H:i:s", strtotime($order['cashResult']['paymentExpiryDateTime']));
        }
        if ($webhook) {
            // webhook記録
            $webhooks = $this->work('webhooks', []);
            $last_webhook = $webhooks ? $webhooks[count($webhooks) - 1] : [];
            // 更新確認
            $last_tran_date = $last_webhook['TranDate'] ?? '-';
            if ($webhook['TranDate'] != $last_tran_date) {
                $webhooks[] = MulPay::trimWeb($webhooks, $webhook);
                $this->work(['webhooks' => $webhooks]);
            }
        }
        if ($order) {
            // 最新order 取得
            $orders = $this->work('orders', []);
            // order 更新確認
            $last_order = $orders ? $orders[count($orders) - 1] : [];
            $last_updated = $last_order['orderReference']['updated'] ?? '-';
            if ($order['orderReference']['updated'] != $last_updated) {
                $orders[] = MulPay::trimOrder($order);
                $this->work(['orders' => $orders]);
            }
            // 銀行振り込みの預かり総額
            if (isset($order['cashResult']['bankTransferPaymentInformation']['depositAmount']))
                $this->work(['depositAmount' => $order['cashResult']['bankTransferPaymentInformation']['depositAmount']]);
        }

        $this->save();
        return $purchased;
    }

    /**
     * 自動継続
     */

    public function setOnFile($response) {
        $Item = PaymentItem::where('id', $this->item_id)->first();
        if ($this->result != self::RESULT_PAYSUCCESS) {
            // まだ決済成功になっていない場合のみ実行
            // 売上たった
            $this->result = self::RESULT_PAYSUCCESS;
            $this->purchased_at = self::date();
            $this->addition($Item);
        }
        $this->work(['response' => $response]);
        $this->save();
    }

    /**
     * 決済成功アイテム付与
     * @param PaymentItem $Item
     * @param $Status
     * @return void
     * @throws Exception
     */
    private function addition(PaymentItem $Item): void {
        $Item->datalize();

        // 決済成功
        if ($Item->type == PaymentItem::TYPE_SUBSC) {
            $this->addSubscription($Item);
        }
        if ($Item->type == PaymentItem::TYPE_POINT) {
            $this->addPoint($Item);
        }
        // 最終購入日付
        Member::conditionAt($this->uid, Member::CONDITION_PAYMENTED_AT);

    }

    /**
     * ポイント購入付与
     * @param PaymentItem $Item
     * @return void
     * @throws Exception
     */
    private function addPoint(PaymentItem $Item): void {
        $time = strtotime($this->purchased_at);

        // 有償ポイント
        $premium_point = $Item->quantity * $this->quantity;
        $premium_at = Point::getExpireAt($time, 180);
        // 無償ポイント
        $gift_point = $Item->work('gift_point', 0) * $this->quantity;
        $gift_at = Point::getExpireAt($time, $Item->work('gift_expire', 30));

        // 特別プラン措置
        $plans = $this->work('plans', []);
        if (isset($plans[PaymentItem::PLAN_ADJUST_POINT])) {
            // ポイント調整商品

        }

        // ポイント付与実施
        Point::purchased($this->uid, $this->id, $premium_point, $gift_point, $premium_at, $gift_at);

        if ($premium_point) {
            $this->work(['premium_point' => [
                'quantity' => $premium_point,
                'expire_at' => $premium_at,
            ]]);
        }

        if ($gift_point) {
            $this->work(['gift_point' => [
                'quantity' => $gift_point,
                'expire_at' => $gift_at,
            ]]);
        }

        Point::totalize($this->uid);

    }

    /**
     * サブスク付与
     * @param PaymentItem $Item
     * @return void
     * @throws Exception
     */
    private function addSubscription(PaymentItem $Item): void {

        // 継続確認
        $number = 0;
        $work = [];
        $pre_subscription_id = $this->work('pre_subscription_id', 0);

        // 種類
        $work['kind'] = $Item->kind;

        // 期限設定
        $start_at = $this->purchased_at;
        // 初回か買い切り設定　明日から
        $today_time = strtotime(substr($start_at, 0, 10));
        $expire_at = date("Y-m-d 23:59:59", $today_time + (86400 * $Item->quantity));

        // タイプ
        if ($Item->getPlan(PaymentItem::PLAN_ONETIME) !== null) {
            // 買い切り
            $type = SubscriptionEx::TYPE_ONETIME;
        } else if ($this->amount === 0) {
            // お試し無料
            $type = SubscriptionEx::TYPE_TRIAL;
        } else if ($pre_subscription_id) {
            // 継続
            $type = SubscriptionEx::TYPE_EXTEND;
            $PreSubscription = SubscriptionEx::first($pre_subscription_id);
            $number = $PreSubscription->number + 1;
            $work['pre_subscription_id'] = $pre_subscription_id;
            // 延長期限
            $pre_expire = strtotime($PreSubscription->expire_at);
            $expire_at = date("Y-m-d 23:59:59", $pre_expire + 86400 * $Item->quantity);
            // 延長開始日時
            $start_at = date("Y-m-d 00:00:00", $pre_expire + 1);

        } else {
            // 初回
            $type = SubscriptionEx::TYPE_FIRST;
            // アップグレード判定 WindexSTD -> WindexPRM
            if (isset($Item->plans[PaymentItem::PLAN_WINDEX_UPGRADE])) {
                // 前STDの自動継続OFF
                $Subscription = SubscriptionEx::setExtend($this->uid, 'off', SubscriptionEx::KIND_ALL);
                if ($Subscription) {
                    $Subscription->save(['expire_at' => self::date(strtotime($start_at) - 1)]);
                }
            }

        }


        // サブスクレコード作成
        $Exists = SubscriptionEx::where('uid', $this->uid)->first(); // 既存レコード有りか確認
        $Created = SubscriptionEx::create($this->uid, $Item->kind, $type, $number, $this->id, $start_at, $expire_at, $work);
        $this->work(['subscription' => ['expire_at' => $expire_at]]);

        // スコア 有料会員登録（サブスク）を完了する
        if (empty($Exists)) Score::add($this->uid, App::TARGET_SUBSCRIPTION_FIRST, $Created->id, Score::SUBSCRIPTION_FIRST);

        // 継続の場合の処置
        if ($number > 0) {
            // 継続ギフトポイント
            $gift_point = $Item->work('gift_point');
            if ($gift_point) {
                $expire_at = Point::getExpireAt(strtotime($start_at), $Item->work('gift_expire', 30));
                Point::purchased($this->uid, $this->id, 0, $gift_point, $expire_at, $expire_at);
                $this->work(['gift_point' => [
                    'quantity' => $gift_point,
                    'expire_at' => $expire_at,
                ]]);
            }
            Point::totalize($this->uid);
            // スコア サブスクを継続更新する
            Score::add($this->uid, App::TARGET_SUBSCRIPTION_CONTINUE, $Created->id, Score::SUBSCRIPTION_EXT);
        }
    }

    /**
     * 継続課金パラメータの作成
     * @return array
     * @throws Exception
     */
    public function getOnfileParams() {
        $uid = $this->uid;

        $User = User::where('uid', $uid)->first();
        $Member = Member::getMember($uid);
        $payer_name = $Member->nickname;
        $email = $Member->email;

        return [
            "merchant" => [
                "name" => "Winsight",
                "nameKana" => "ウィンサイト",
                "nameAlphabet" => "Winsight",
                "nameShort" => "Winsight",
                "contactName" => "株式会社Neo Sports Winsight サポート窓口",
                "contactEmail" => "support@example.com",
                "contactUrl" => BASE_URL . "inquiry/form",
                "contactPhone" => "03-6400-3116",
                "contactOpeningHours" => "10:00-18:00",
                //"callbackUrl" => "https://example.com/callback",
                "webhookUrl" => (App::isDevelop() ? "https://dev.winsight.jp/" : "https://winsight.jp/") . "api/payment/webhook",
                "csrfToken" => md5($this->id . $this->order_id . $this->created_at),
            ],
            "order" => [
                "orderId" => $this->order_id,
                "amount" => $this->amount,
                "currency" => "JPY",
                "clientFields" => [
                    "clientField1" => ""
                ],
                //"items" => [], // 商品情報の一覧

                // transactionType required
                // この取引がお客様操作によって開始されたか、自動チャージのように加盟店様操作で行われたかを示します。現時点では設定する値によって当サービス内の処理は変わりません。
                // CIT: Customer Initiated Transaction(お客様操作起点) MIT: Merchant Initiated Transaction(加盟店様操作起点) OTHER: 上記以外
                "transactionType" => "MIT",

                //"shippingAddress" => [],
                //"addressMatch" => false,
                //"billingAddress" => []
            ],
            "payer" => [
                "name" => $payer_name,//"見本 太郎",
                //"nameKana" => "ミホン　タロウ",
                //"nameAlphabet" => "Taro Mihon",
                //"gender" => "MALE",
                //"dateOfBirth" => "19950308",
                "email" => $email,//"taro.mihon@example.com",
                "deliveryEmail" => $email,//"taro.mihon.delivery@example.com",
                //"phones" => [],
                "accountId" => $uid,
                //"ip" => "172.16.0.1",
                //"deviceType" => "PC_WEB",
                //"osType" => "IOS",
                //"httpUserAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36"
            ],
            "creditOnfileInformation" => [
                "onfileCard" => [
                    "memberId" => $uid,
                    "type" => "CREDIT_CARD",
                ],
                "creditChargeOptions" => [
                    "authorizationMode" => "CAPTURE",
                    "useTds2" => false,
                    "useFraudDetection" => true, //Default: false 不正検知の利用有無 Apple Payは利用できません。
                ],
            ],

            //"tds2Information" => [ //3Dセキュアオプション情報
            //    "tds2Options" => [],
            //    "tds2Data" => []
            //],

            //"fraudDetectionInformation" => [ // 不正検知情報
            //    "fraudDetectionOptions" => [],
            //    "fraudDetectionData" => [],
            //    "fraudDetectionCustomData" => [],
            //],

            //"additionalOptions" => [], //追加情報  //予備項目であり、通常は使用しないでください。
        ];

    }

    /**
     * 継続決済リクエスト
     * @return bool
     * @throws Exception
     */
    public function requestOnFile(): bool {
        $tried = $this->work('tried', 0);
        if ($tried >= 8) {
            // 失敗状態
            $this->save(['result' => self::RESULT_FAILED]);
            // サブスクを継続失敗に
            $pre_subscription_id = $this->work('pre_subscription_id', 0);
            $PreSubscription = SubscriptionEx::first($pre_subscription_id);
            $PreSubscription->save(['mode' => SubscriptionEx::MODE_FAILED]);
            return false;
        }

        // リクエストパラメータ取得
        $params = $this->getOnfileParams();
        // OpenAPI通信
        $response = MulPay::request('credit/on-file/charge', $params, $this->order_id);

        if (isset($response['orderReference']['status']) && $response['orderReference']['status'] == PaymentHistory::STATUS_CAPTURE) {
            // 成功
            $this->setOnFile($response);
            return true;
        } else {
            // 失敗
            $tried = $this->work('tried', 0);
            $this->work(['failed' => $response, 'tried' => $tried + 1]);
            $this->save();
            AppLog::error($response, ['error_mode' => 'charge', 'requestOnFile payment' => $this->id], 86400);
            return false;
        }
    }

    /**
     * 支払い完了メール送信
     * @return void
     * @throws Exception
     */

    public function sendMail() {
        $Item = PaymentItem::first($this->item_id);
        $data = [
            'name' => $Item->name,
            'amount' => number_format($this->amount) . '円',
            'id' => $this->order_id,
            'created_at' => str_replace('-', '/', $this->purchased_at ?? $this->created_at),
        ];

        if ($this->purchased_at) {
            // ---------------------------------------------------------------------------------
            // 決済完了している
            $mailed_success = $this->work('mailed_success', 0);
            if (empty($mailed_success)) {
                $Content = ($Item->type == PaymentItem::TYPE_SUBSC) ? 'Subscription' : 'Point';
                $this->work(['mailed_success' => 1]);
                // 各種期限設定
                $subscription = $this->work('subscription', null);
                if ($subscription) {
                    $data['subscription_expire_at'] = $subscription['expire_at'] ?? '-';
                }

                $point_expire = '';
                $premium_point = $this->work('premium_point', null);
                if ($premium_point) {
                    $point_expire .= "有償（{$premium_point['expire_at']}）\n";
                }
                $gift_point = $this->work('gift_point', null);
                if ($gift_point) {
                    $point_expire .= "ギフト（{$gift_point['expire_at']}）\n";
                }
                if ($point_expire) {
                    $data['point_expire'] = "ポイント有効期限\n" . $point_expire;
                } else {
                    $data['point_expire'] = '';
                }
            }
            // ---------------------------------------------------------------------------------

        } else if ($this->result === self::STATUS_TRADING) {
            // ---------------------------------------------------------------------------------
            // 口座情報送信
            $mailed_trading = $this->work('mailed_trading', 0);
            if (empty($mailed_trading)) {
                // 入金待ち
                $Content = ($Item->type == PaymentItem::TYPE_SUBSC) ? 'SubscriptionBank' : 'PointBank';
                $this->work(['mailed_trading' => 1]);
                // 口座情報
                $orders = $this->work('orders', []);
                $order = array_pop($orders);
                $paymentExpiryDateTime = strtotime($order['cashResult']['paymentExpiryDateTime']);
                $bankTransferPaymentInformation = $order['cashResult']['bankTransferPaymentInformation'];

                $data['銀行名'] = $bankTransferPaymentInformation['bankName'];
                $data['支店名'] = $bankTransferPaymentInformation['branchName'];
                $data['口座種別'] = $bankTransferPaymentInformation['accountType'] == 1 ? '普通預金' : '';
                $data['口座番号'] = $bankTransferPaymentInformation['accountNumber'];

                $data['expire_at'] = date("Y/m/d", $paymentExpiryDateTime);
            }
            // ---------------------------------------------------------------------------------
        }


        if (!empty($Content)) {
            $Member = Member::getMember($this->uid);
            $data['email'] = $Member->email;
            $Mail = new MailRucsisClass();
            $Mail->sendMail("shop/{$Content}", $data);
            $this->save();
        }


    }

    // -----------------------------------------------------------------------------------------------------------------------------------
    //　過剰金調整用
    // -----------------------------------------------------------------------------------------------------------------------------------
    /**
     * 過剰金の補填レコードの作成
     * @param $item_id
     * @param $amount // 金額
     * @param $memo
     * @param $new_memo
     * @return int
     * @throws Exception
     */

    public function split($ADJUST_POINT_ITEM_ID, $amount, $memo, $new_memo): int {

        // 調整用商品
        $Item = PaymentItem::where('id', $ADJUST_POINT_ITEM_ID)->first();
        $ADJUST_POINT_ITEM_PRICE = $Item->amount;

        // メモ保存
        $this->work(['memo' => $memo]);
        $this->save();

        // 枝 order_id 作成
        $order_id = $this->order_id;
        $count = self::where('order_id', 'LIKE', "{$order_id}%")->count();
        $order_id = "{$order_id}-{$count}";


        // --------------------------------------------------
        //  取引レコード生成
        $id = PaymentHistory::insert([
            'order_id' => $order_id,
            'uid' => $this->uid,
            'result' => PaymentHistory::RESULT_NONE,
            'item_id' => $ADJUST_POINT_ITEM_ID,
            'amount' => $amount,
            'quantity' => intval($amount / $ADJUST_POINT_ITEM_PRICE),
            'expire_at' => PaymentHistory::date(86400 * 7),
            'work' => json_encode(['memo' => $new_memo], JSON_UNESCAPED_UNICODE),
            'method' => PaymentHistory::METHOD_ADJUST,
        ]);

        return $id;
    }

    /**
     * 補填調整決済実施
     * @param null $purchased_at
     * @return void
     * @throws Exception
     */
    public function setAdjust($purchased_at = null): void {
        $Item = PaymentItem::where('id', $this->item_id)->first();
        if ($this->result != self::RESULT_PAYSUCCESS) {
            // まだ決済成功になっていない場合のみ実行
            $this->result = self::RESULT_PAYSUCCESS;
            $this->purchased_at = $purchased_at ?: self::date();
            $this->addition($Item);
            $this->save();
        }
    }


}