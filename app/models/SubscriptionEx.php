<?php

/**
 * サブスク関連
 * Windex追加バージョン
 */

class SubscriptionEx extends Model {

    protected static string $tableName = 'subscription';

    const TYPE_ONETIME = 'ONETIME'; //買い切り
    const TYPE_TRIAL = 'TRIAL';     // 仮(お試し無料)期間
    const TYPE_FIRST = 'FIRST';     // 初回課金
    const TYPE_EXTEND = 'EXTEND';   // 延長購読
    const TYPE_DEBUG = 'DEBUG';   // デバッグ用

    const KIND_CENTER = 'center_ltd';       // 通常の予想サブスク
    const KIND_LOCAL = 'local_ltd';   // WindexサブスクPremium
    const KIND_ALL = 'Unlimited';   // WindexサブスクStandard

    const KINDS = [
        self::KIND_CENTER => '中央競馬限定',
        self::KIND_LOCAL => '地方競馬限定',
        self::KIND_ALL => '全機能使い放題',
    ];

    const MODE_NONE = ''; // 特になし・通常継続
    const MODE_CANCEL = 'CANCEL'; // 継続キャンセル指定
    const MODE_FAILED = 'FAILED'; // 継続失敗
    const MODE_CONTINUE = 'CONTINUE'; // 継続処理済

    /**
     * 新規作成
     * @param $uid
     * @param $kind
     * @param $type
     * @param $number
     * @param $payment_id
     * @param $start_at
     * @param $expire_at
     * @param $work
     * @return SubscriptionEx
     * @throws Exception
     */
    static
    public function create($uid, $kind, $type, $number, $payment_id, $start_at, $expire_at, $work): SubscriptionEx {
        $data = [
            'uid' => $uid,
            'kind' => $kind,
            'type' => $type,
            'mode' => '',
            'number' => $number,
            'payment_id' => $payment_id,
            'start_at' => $start_at,
            'expire_at' => $expire_at,
            'work' => json_encode($work),
        ];

        $id = self::insert($data);

        return self::first($id);
    }

    /**
     * 現在有効なサブスクを返す
     * @param $uid
     * @param $kind
     * @return self|null
     * @throws Exception
     */
    static public function currentQuery($uid, $kind = null): \classes\DBQueryClass {

        $Query = self::select(['subscription.*', 'payment_item.name'])
            ->where('subscription.uid', $uid)
            ->where('subscription.expire_at', '>=', self::date())
            ->where('subscription.start_at', '<=', self::date())
            ->leftJoin('payment_history', 'payment_history.id=payment_id')
            ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id')
            ->orderBy('subscription.id', 'DESC');

        if (is_string($kind)) $Query->where('subscription.kind', $kind);
        if (is_array($kind)) $Query->where('subscription.kind', 'IN', $kind);

        return $Query;

    }

    /**
     * 継続ON/OFF設定
     * @param $uid
     * @param string $kind
     * @param null $on_off
     * @return SubscriptionEx|null
     * @throws Exception
     */
    static
    public function setExtend($uid, $on_off = null, string $kind = self::KIND_CENTER): SubscriptionEx|null {

        $Subscription = self::select(['subscription.*', 'payment_item.name'])
            ->where('subscription.uid', $uid)
            ->where('subscription.kind', $kind)
            ->where('subscription.expire_at', '>', self::date())
            ->where('subscription.type', 'IN', [SubscriptionEx::TYPE_TRIAL, SubscriptionEx::TYPE_FIRST, SubscriptionEx::TYPE_EXTEND])
            ->where('subscription.mode', 'IN', [SubscriptionEx::MODE_CANCEL, SubscriptionEx::MODE_NONE])
            ->leftJoin('payment_history', 'payment_history.id=payment_id')
            ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id')
            ->orderBy('id', 'DESC')
            ->first();

        if ($Subscription) {
            // ON指定
            if ($on_off == 'on' && $Subscription->mode == self::MODE_CANCEL) {
                $Subscription->save(['mode' => self::MODE_NONE]);
                AppLog::log(['mode' => 'on', 'expire_at' => $Subscription->expire_at, 'kind' => $kind, 'id' => $Subscription->id], 'subscription');
            }
            // OFF指定
            if ($on_off == 'off' && $Subscription->mode == self::MODE_NONE) {
                $Subscription->save(['mode' => self::MODE_CANCEL]);
                AppLog::log(['mode' => 'off', 'expire_at' => $Subscription->expire_at, 'kind' => $kind, 'id' => $Subscription->id], 'subscription');
            }
        }
        return $Subscription;

    }


    /**
     * @param $uid
     * @return void
     * @throws Exception
     */
    static public function cancelAll($uid) {
        // 利用中のサブスクがあれば全て延長をキャンセルする
        self::where('uid', $uid)
            ->where('type', '<>', self::TYPE_ONETIME)
            ->where('mode', self::MODE_NONE)
            ->update(['mode' => self::MODE_CANCEL]);
    }

    /**
     * @return void
     * @throws Exception
     */

    public function checkNext() {

        // 継続処理済みか？
        if ($this->mode == self::MODE_CONTINUE) return;

        $uid = $this->uid;
        // アイテム特定
        $Payment = PaymentHistory::where('id', $this->payment_id)->first();
        $Item = PaymentItem::where('id', $Payment->item_id)->first();

        // 継続回数
        $next_number = $this->number + 1;
        // 価格
        $amount = $Item->amount;
        $initial_amount = $Item->work("initial_amount", 0);
        $initial_cycle = $Item->work("initial_cycle", 0);
        if ($next_number < $initial_cycle) {
            // 割引価格期間内なのでそちらで購読
            $amount = $initial_amount;
        }

        //Payment 既に作成しているか確認
        $NextPayment = PaymentHistory::where('uid', $uid)
            ->where('item_id', $Item->id)
            ->where('amount', $amount)
            ->where('method', PaymentHistory::METHOD_ONFILE_CREDIT)
            ->where('created_at', '>', PaymentHistory::date(-86400 * 2))
            ->first();
        if ($NextPayment) {
            $subscription_id = $NextPayment->work('pre_subscription_id', 0);
            if ($subscription_id == $this->id) {
                // 作成済みなので新規Paymentを作らない
                return;
            }
        }

        //Payment 新規作成
        $OrderID = PaymentHistory::createOrderId($this->uid);
        $work = ['pre_subscription_id' => $this->id];
        PaymentHistory::insert([
            'order_id' => $OrderID,
            'uid' => $uid,
            'result' => PaymentHistory::RESULT_CHARGING,
            'item_id' => $Item->id,
            'amount' => $amount,
            'expire_at' => PaymentHistory::date(86400 * 7),
            'work' => json_encode($work, true),
            'method' => PaymentHistory::METHOD_ONFILE_CREDIT,
        ]);

        // NEXT処理済み
        $this->save(["mode" => self::MODE_CONTINUE]);
    }
}