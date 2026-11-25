<?php

class PaymentItem extends Model {

    protected static string $tableName = 'payment_item';

    const TYPE_SUBSC = 'subsc';
    const TYPE_POINT = 'point';

    // 状態オプション指定 todo redirect時個別対応

    const PLAN_NONE = 'none';
    const PLAN_FIRST_ONLY = 'not_experienced';
    const PLAN_COMEBACK = 'comeback';
    const PLAN_COUPON = 'need_promo_code';
    const PLAN_ONETIME = 'onetime';
    const PLAN_FLAGS = 'flags';
    const PLAN_ADJUST_POINT = 'adjust_point';
    const PLAN_WINDEX_UPGRADE = 'windex_upgrade';

    const PLANS = [
        self::PLAN_NONE => ['name' => 'なし', 'value' => self::PLAN_NONE,],
        self::PLAN_FIRST_ONLY => ['name' => '初回限定', 'value' => self::PLAN_FIRST_ONLY,],
        self::PLAN_COMEBACK => ['name' => '復帰限定', 'value' => self::PLAN_COMEBACK,],
        self::PLAN_COUPON => ['name' => 'クーポンコード', 'value' => self::PLAN_COUPON,],
        //self::PLAN_ONETIME => ['name' => '見放題プラン', 'value' => self::PLAN_ONETIME,],
        self::PLAN_FLAGS => ['name' => '会員フラグ限定', 'value' => self::PLAN_FLAGS,],
        //self::PLAN_WINDEX_UPGRADE => ['name' => 'WINDEXプレミアム乗換え', 'value' => self::PLAN_WINDEX_UPGRADE,],
        //self::PLAN_ADJUST_POINT => ['name' => 'ポイント調整商品', 'value' => self::PLAN_ADJUST_POINT,],　// 用途未定
    ];

    // 確認用データ保存配列
    private static array $CHECKS = [];

    /**
     * 有効販売アイテムの基本クエリ
     * @return \classes\DBQueryClass
     * @throws Exception
     */
    static
    public function getCurrentQuery() {
        $now = self::date();
        return self::where('sales_begin_at', '<=', $now)
            ->where('sales_close_at', '>=', $now)
            ->where('closed', 0)
            ->orderBy('priority', 'ASC');
    }

    /**
     * 有効販売アイテム一覧
     * @param $type
     * @return array|bool
     * @throws Exception
     */

    static
    public function getCurrentList($type) {
        $now = self::date();

        return self::getCurrentQuery()
            ->where('type', $type)
            ->all();

    }


    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    public function datalize(): self {
        $this->gift_point = $this->work('gift_point', 0);
        $this->gift_expire = $this->work('gift_expire', 0);
        $this->PayMethods = $this->work('PayMethods', []);
        $this->plan = '';
        if ($this->type === self::TYPE_SUBSC) {
            $this->initial_amount = $this->work('initial_amount', 0);
            $this->initial_cycle = $this->work('initial_cycle', 0);
        }

        $this->plans = [];
        foreach ($this->work('plans', []) as $item) {
            $key = $item['plan'];
            if ($key) $this->plans[$key] = $item;
        }
        if (count($this->plans) > 0) {
            $plan = $this->plans[array_key_first($this->plans)];
            $this->plan = $plan['plan'];
            $this->value = $plan['value'];
        }
        if (empty($this->plans)) $this->plans = null;


        // 銀行振込のみフラグ
        $this->bank_only = false;
        if (count($this->PayMethods) === 1) {
            if ($this->PayMethods[0] === PaymentHistory::METHOD_AOZORA) {
                $this->bank_only = true;
            }
        }

        // 種類
        $this->kind = $this->work('kind', SubscriptionEx::KIND_CENTER);

        return $this;
    }

    public function store($post) {

        $this->work(['gift_point' => $post['gift_point']]);
        $this->work(['gift_expire' => $post['gift_expire']]);
        $this->work(['PayMethods' => $post['PayMethods']]);
        unset($post['gift_point']);
        unset($post['gift_expire']);
        unset($post['PayMethods']);
        if ($this->type === self::TYPE_SUBSC) {
            // サブスク情報
            $this->work(['initial_amount' => $post['initial_amount']]);
            $this->work(['initial_cycle' => $post['initial_cycle']]);

            unset($post['initial_amount']);
            unset($post['initial_cycle']);
        }
        $plans = [];
        foreach ($post['plans'] ?? [] as $item) {
            $plans[$item['plan']] = $item;
        }
        $this->work(['plans' => $plans]);
        unset($post['plans']);
        unset($post['value']);

        // 種類
        $this->work(['kind' => ($post['kind'] ?? '')]);
        unset($post['kind']);


        $post['work'] = $this->work;

        // -----------------------------------------------

        return $this->save($post)->getRequestQuery();
    }

    public function getPlan($plan) {
        $plans = $this->work('plans', []);
        foreach ($plans as $item) {
            if ($item['plan'] === $plan) {
                return $item['value'];
            }
        }

        return null;
    }


    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------

    /**
     * 販売条件確認
     * @param $uid
     * @param $condition
     * @return boolean
     * @throws Exception
     */

    public function checkSalesCondition($uid, $condition): bool {

        // ----------------------------------
        // クーポンコード
        if (isset($this->plans[PaymentItem::PLAN_COUPON])) {
            $promo_code = $this->plans[PaymentItem::PLAN_COUPON]['value'];
            if ($promo_code !== $condition['promo_code']) return false;
        }

        // ----------------------------------
        // MemberFlag確認
        if (isset($this->plans[PaymentItem::PLAN_FLAGS])) {
            $flags = $this->plans[PaymentItem::PLAN_FLAGS]['value'];
            $flags = explode(',', $flags);
            $flag_ok = false;
            foreach ($flags as $number) {
                if (MemberFlag::checkFlag($condition['uid'], $number)) {
                    $flag_ok = true;
                }
            }
            if (!$flag_ok) return false;
        }


        // ----------------------------------
        // WINDEX basic -> premium アップグレード

        if (isset($this->plans[PaymentItem::PLAN_WINDEX_UPGRADE])) {

            // 現在WINDEX BASIC加入中か？
            $CURRENT_WINDEX_BASIC = "CURRENT_WINDEX_BASIC_{$uid}";
            if (!isset(self::$CHECKS[$CURRENT_WINDEX_BASIC])) self::$CHECKS[$CURRENT_WINDEX_BASIC] = SubscriptionEx::currentQuery($uid, SubscriptionEx::KIND_ALL)->first();

            // 非加入なので駄目
            if (empty(self::$CHECKS[$CURRENT_WINDEX_BASIC])) return false;
        }


        // ----------------------------------
        // サブスク初回限定商品

        $kind = $this->kind;

        if (isset($this->plans[PaymentItem::PLAN_FIRST_ONLY])) {

            // 購入履歴取得
            $Subscriptions = self::getSubscriptionPayments($uid, $kind);
            //if ($kind === SubscriptionEx::KIND_WINDEX_PRM) prex($Subscriptions);
            if (isset($this->plans[PaymentItem::PLAN_WINDEX_UPGRADE])) {

                // WINDEX UPGRADE の場合、PaymentHistory に UPGRADE があるかチェックする必要がある
                $upgraded = false;
                foreach ($Subscriptions as $Subscription) {
                    $item = $Subscription->json('item');
                    if (isset($item['plans'][PaymentItem::PLAN_WINDEX_UPGRADE])) $upgraded = true;
                }
                // UPGRADEしたことがある
                if ($upgraded) return false;


            } else {
                // WinSalon  WINDEX BASIC  WINDEX PREMIUM
                // 初回かどうかを調べる
                // 履歴があるので不可
                if (!empty($Subscriptions)) return false;
            }
        }

        // ----------------------------------
        // サブスクカムバック商品

        if (isset($this->plans[PaymentItem::PLAN_COMEBACK])) {
            // 加入歴を調べる
            $Subscriptions = self::getSubscriptionPayments($uid, $kind);

            // 購入履歴がなければ不可
            if (empty($Subscriptions)) return false;
        }


        // ----------------------------------
        // チェックOK
        return true;
    }

    /**
     * サブスク購入履歴の有無確認
     * @param $uid
     * @param $kind
     * @return array
     * @throws Exception
     */

    private static function getSubscriptionPayments($uid, $kind) {
        if (isset(self::$CHECKS[$uid][$kind]['Subscription'])) $SubscriptionPayments = self::$CHECKS[$uid][$kind]['Subscription'];
        else $SubscriptionPayments = self::table('payment_history')
            ->select(['payment_history.*', 'kind', 'payment_item.work AS item'])
            ->where('payment_history.uid', $uid)
            ->join('subscription', 'subscription.payment_id = payment_history.id')
            ->join('payment_item', 'payment_item.id = payment_history.item_id')
            ->where('subscription.kind', $kind)
            ->all();

        self::$CHECKS[$uid][$kind]['Subscription'] = $SubscriptionPayments;

        return $SubscriptionPayments;
    }


}