<?php

class Point extends Model {
    protected static string $tableName = 'point_purchased';

    // -------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------
    const TYPE_PREMIUM = 0;
    const TYPE_GIFT = 1;

    const PREMIUM_EXPIRE_DAYS = 180;

    // -------------------------------------------------------------------------------------
    // ORIGIN用定数
    // -------------------------------------------------------------------------------------
    //　用途
    // -------------付与
    const KIND_PURCHASED = 10;  // 購入
    const KIND_GIVE_POINTS = 20;  // 付与
    const KIND_CANCEL = 25;     // 取消
    // -------------消費
    const KIND_INVALIDATION = 30; // 失効
    const KIND_SPENDING = 50;   // 消費
    // 内容詳細


    // -------------------------------------------------------------------------------------
    // 購入
    // -------------------------------------------------------------------------------------
    const CATEGORY_PAYMENT = 'payment_history'; // 購入
    // -----------------
    // rid = payment_history.id
    // 識別子 identifier TYPE_PREMIUM / TYPE_GIFT


    // -------------------------------------------------------------------------------------
    // 付与
    // -------------------------------------------------------------------------------------
    const CATEGORY_GIVE = 'give';   // 付与
    const CATEGORY_CONTENT_CANCEL = 'content_cancel'; // 記事ライセンス等・ポイント消費分返還　特別付与


    // -------------------------------------------------------------------------------------
    // 失効
    // -------------------------------------------------------------------------------------
    const CATEGORY_INVALIDATION = 'invalidation';// 失効


    // -------------------------------------------------------------------------------------
    // 取り消し
    // -------------------------------------------------------------------------------------
    const CATEGORY_CANCEL = 'cancel';           // 取消


    // -------------------------------------------------------------------------------------
    // 消費
    // -------------------------------------------------------------------------------------
    const CATEGORY_CONTENT = 'content';         // 記事ライセンス消費
    // -----------------
    // rid = content.id
    // -----------------
    // 識別子 identifier
    // page_all   記事一括
    // kenkai     見解
    // mark_all   印全て
    // mark_1 -> mark_9 各印
    // kaime      買目

    const CATEGORY_PACK = 'point_pack';               // パック商品消費
    // -----------------
    // rid = point_pack.id
    // -----------------
    // 識別子 identifier
    // pack_start   早割
    // pack_publish 通常

    const CATEGORY_UMA_COMMENT = 'uma_comment';     // コメント消費
    // -----------------
    // rid = uma_comment.id
    // -----------------
    // 識別子 identifier
    // -----------------


    const CATEGORY_WINDEX_RACE = 'windex_race';     // レース指数消費
    // -----------------
    // rid = windex_race.id
    // identifier = [race_code]

    // -------------------------------------------------------------------------------------

    // 管理表示の補助用文字列
    const LICENSE_IDENTIFIERS = [
        ContentCondition::LICENSE_PAGE_ALL => '記事まとめ',
        ContentCondition::LICENSE_KENKAI => '見解単品',
        ContentCondition::LICENSE_KAIME => '買い目単品',
        ContentCondition::LICENSE_MARK_ALL => '印まとめ',
        ContentCondition::LICENSE_MARK_N_ => '印単品',
        UmaComment::CATEGORY_SPOILER => 'スポイラー単品',
        UmaComment::CATEGORY_CHAT_COMMENT => 'チャットコメント単品',
        PointPack::LICENSE_START => '早割',
        PointPack::LICENSE_PUBLISH => '通常',
    ];



    // -------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------

    static
    public function logQuery() {
        $logDB = App::logDB();
        return self::from("{$logDB}.point_log")
            ->select(['point_log.*', 'point_origin.category', 'point_origin.rid', 'point_origin.identifier',])
            ->leftJoin("point_origin", "point_origin.id = point_log.origin_id");
    }

    static
    public function purchasedQuery() {
        return self::from("point_purchased")
            ->select(["point_purchased.*",
                "payment_item.name AS item_name", "payment_item.id AS item_id",
                "payment_history.amount",
                "point_origin.id AS ORIGIN", "point_origin.category", "point_origin.work AS reason
                "])
            ->leftJoin("point_origin", "point_origin.id = point_purchased.origin_id")
            ->leftJoin("payment_history", "payment_history.id = point_origin.rid")
            ->leftJoin("payment_item", "payment_item.id = payment_history.item_id");

    }

    /**
     * ポイント購入での入手
     * @param $uid
     * @param $payment_id
     * @param int $premium
     * @param int $gift
     * @param string $premium_at
     * @param string $gift_at
     * @return void
     * @throws Exception
     */

    static
    public function purchased($uid, $payment_id, int $premium, int $gift, string $premium_at, string $gift_at) {
        self::invalid($uid);

        $origin_id = self::origin(self::KIND_PURCHASED, self::CATEGORY_PAYMENT, $payment_id, '');

        // 特商法の表記では、ポイント発行日から180日 ※ギフトポイントの有効期限は別途定めるところによります。
        if ($premium) {
            self::insert(['uid' => $uid, 'type' => self::TYPE_PREMIUM, 'acquire' => $premium, 'current' => $premium, 'origin_id' => $origin_id, 'expire_at' => $premium_at]);
        }
        if ($gift) {
            self::insert(['uid' => $uid, 'type' => self::TYPE_GIFT, 'acquire' => $gift, 'current' => $gift, 'origin_id' => $origin_id, 'expire_at' => $gift_at]);
        }

        // 入手ログ
        $log_id = self::log($uid, $origin_id, $premium, $gift);

        // 入手ポイント累計
        self::sql("UPDATE member_state SET acquire_point = acquire_point + ? WHERE uid=?", [$premium + $gift, $uid]);

        Member::conditionAt($uid, Member::CONDITION_POINTED_AT);
    }

    /**
     * ギフトポイント付与
     * @param $uid
     * @param $origin_id
     * @param int $gift_point
     * @param int $expire_days
     * @return void
     * @throws Exception
     */
    static
    public function give($uid, $origin_id, int $gift_point, int $expire_days = 30) {
        self::invalid($uid);

        $expire_at = self::getExpireAt(time(), $expire_days);
        self::insert(['uid' => $uid, 'type' => self::TYPE_GIFT, 'acquire' => $gift_point, 'current' => $gift_point, 'origin_id' => $origin_id, 'expire_at' => $expire_at]);

        // 入手ログ
        $log_id = self::log($uid, $origin_id, 0, $gift_point);

        // 入手ポイント累計
        self::sql("UPDATE member_state SET acquire_point = acquire_point + ? WHERE uid=?", [$gift_point, $uid]);

        Member::conditionAt($uid, Member::CONDITION_POINTED_AT);
    }

    static function getExpireAt($now, $days) {
        $at = self::date($now + 86400 * $days);
        $at = substr($at, 0, 10) . " 23:59:59";
        return $at;
    }

    /**
     * 消費 同じものにポイント支払いは発生しない
     * @param $uid
     * @param $amount
     * @param $category
     * @param $id
     * @param $identifier
     * @param $back_at
     * @return string // POINT使用ID
     * @throws Exception
     */
    static
    public function spend($uid, $amount, $category, $id, $identifier, $back_at): string {
        self::invalid($uid);

        $target = $amount;
        $premium = 0;
        $gift = 0;

        $origin_id = self::origin(self::KIND_SPENDING, $category, $id, $identifier);
        $Exists = self::getLog($uid, $origin_id, $back_at);
        if ($Exists) {
            throw new Exception('既にポイントで購入済みです');
        }
        // ポイント消費
        while ($target > 0) {
            $Points = self::where('uid', $uid)
                ->where('current', '>', 0)
                ->where('expire_at', '>', self::date())
                ->orderBy('expire_at', 'ASC')
                ->limit(5)
                ->all();

            if (empty($Points)) throw new Exception('ポイントが足りません');

            foreach ($Points as $Point) {
                if ($target <= $Point->current) {
                    // 全部払える
                    $spend = $target;
                    $Point->save(['current' => $Point->current - $target]);
                    $target = 0;
                } else {
                    // 部分充当
                    $spend = $Point->current;
                    $target -= $Point->current;
                    $Point->save(['current' => 0]);
                }

                if ($Point->type == self::TYPE_PREMIUM) {
                    $premium += $spend;
                } else {
                    $gift += $spend;
                }
            }
        }
        // ポイント使用ログ
        $log_id = self::log($uid, $origin_id, -$premium, -$gift);

        // 最終ポイント使用日時
        Member::conditionAt($uid, Member::CONDITION_POINT_USED_AT);
        MemberProfile::addPointUsedCount($uid);

        return "{$origin_id}-{$log_id}";
    }

    /** 失効処理
     * @param $uid
     * @return void
     * @throws Exception
     */

    static
    public function invalid($uid): void {
        $Invalids = self::where('uid', $uid)
            ->where('current', '>', 0)
            ->where('expire_at', '<', self::date())
            ->orderBy('expire_at', 'ASC')
            ->all();

        foreach ($Invalids as $Invalid) {
            $current = $Invalid->current;
            $type = $Invalid->type;
            $Invalid->save(['current' => 0, 'invalid' => $current]);
            $origin_id = self::origin(self::KIND_INVALIDATION, self::CATEGORY_INVALIDATION, strtotime($Invalid->expire_at), $type ? 'gift' : 'premium', null);
            $works = ['ppid' => $Invalid->id];
            self::log($uid, $origin_id,
                $type == self::TYPE_PREMIUM ? -$current : 0,
                $type == self::TYPE_GIFT ? -$current : 0,
                $Invalid->expire_at,
                $works,
            );
        }

    }

    /**
     * 総額計算
     * @param $uid
     * @return array
     * @throws Exception
     */
    static
    public function totalize($uid): array {
        // 合計集計
        $now = self::date();
        $premium = self::sql("SELECT SUM(current) AS point FROM point_purchased WHERE uid=? AND type=? AND current > 0 AND expire_at > ? ", [$uid, self::TYPE_PREMIUM, $now]);
        $gift = self::sql("SELECT SUM(current) AS point FROM point_purchased WHERE uid=? AND type=? AND current > 0 AND expire_at > ? ", [$uid, self::TYPE_GIFT, $now]);

        // 最短期限
        $point_expire_at = null;
        $Recent = self::where('uid', $uid)
            ->where('current', '>', 0)
            ->where('expire_at', '>', self::date())
            ->orderBy('expire_at', 'ASC')
            ->first();
        if ($Recent) $point_expire_at = $Recent->expire_at;

        $premium_point = $premium[0]['point'] ?? 0;
        $gift_point = $gift[0]['point'] ?? 0;

        self::sql("UPDATE member_state SET premium_point=?,gift_point=?,point_expire_at=? WHERE uid=?", [$premium_point, $gift_point, $point_expire_at, $uid]);
        return ['premium' => $premium_point, 'gift' => $gift_point];
    }

    /**
     * @param $kind
     * @param $category
     * @param $rid
     * @param $identifier
     * @param null $work
     * @return int
     * @throws Exception
     */
    static function origin($kind, $category, $rid, $identifier, $work = null): int {
        for ($i = 0; $i < 10; $i++) {
            // 事由を確認
            $Origin = self::sql("SELECT id FROM point_origin WHERE category=? AND rid=? AND identifier=?", [$category, $rid, $identifier]);
            if ($Origin) {
                $origin_id = $Origin[0]['id'];
            } else {
                if ($work !== null) $work = json_encode($work, JSON_UNESCAPED_UNICODE);
                try {
                    $origin_id = self::insert(['category' => $category, 'rid' => $rid, 'identifier' => $identifier, 'kind' => $kind, 'work' => $work], 'point_origin');
                } catch (Exception $e) {
                    // 同時挿入競合エラーの場合何度かトライしてみる
                    sleep(1);
                    continue;
                }
            }
            break;
        }
        if (empty($origin_id)) throw new Exception("origin insert error");

        return $origin_id;

    }

    /**
     * 付与用のOrigin取得
     * @param $rid
     * @param $identifier
     * @param $reason
     * @return int
     * @throws Exception
     */
    static function giftOrigin($rid, $identifier, $reason, $text = ''): int {
        $work = ['reason' => $reason, 'text' => $text];
        return self::origin(self::KIND_GIVE_POINTS, self::CATEGORY_GIVE, $rid, $identifier, $work);
    }


    /**
     * @param $uid
     * @param $kind
     * @param $category
     * @param $rid
     * @param $identifier
     * @param $premium
     * @param $gift
     * @param $origin_id
     * @return int
     * @throws Exception
     */
    // ログ記録
    static
    public function log($uid, $origin_id, $premium, $gift, $created_at = null, $works = null): int {

        // 残高計算・ログ
        $balance = Point::totalize($uid);

        // ログ記入
        $table = App::logDB('point_log');
        $data = [
            'uid' => $uid,
            'origin_id' => $origin_id,
            'premium_point' => $premium,
            'gift_point' => $gift,
            'ppb' => $balance['premium'],
            'gpb' => $balance['gift'],
        ];
        if ($created_at) $data['created_at'] = $created_at;
        if ($works) $data['work'] = json_encode($works, JSON_UNESCAPED_UNICODE);

        $log_id = self::insert($data, $table);

        return $log_id;
    }

    /**
     * @param $uid
     * @param $origin_id
     * @param $back_at
     * @return Point|null
     * @throws Exception
     */
    static
    public function getLog($uid, $origin_id, $back_at): static|null {
        $logDB = App::logDB();
        return self::from("{$logDB}.point_log")
            ->where('uid', $uid)
            ->where('origin_id', $origin_id)
            ->where('created_at', '>', $back_at)
            ->first();
    }


    /**
     * ポイント使用メール送信
     * @param $uid
     * @param $license
     * @param $log_id
     * @param $point
     * @return void
     * @throws Exception
     */

    static
    public function sendUsePointMail($uid, $license, $log_id, $point) {
        $Member = Member::getMember($uid);
        $data = [
            'email' => $Member->email,
            'name' => ContentCondition::LICENSE_NAMES[$license],
            'id' => $log_id,
            'point' => number_format($point),
            'created_at' => self::date(),
        ];
        $Mail = new MailRucsisClass();
        $Mail->sendMail('shop/Content', $data);
    }


}