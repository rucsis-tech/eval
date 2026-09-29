<?php

// 集計用
class KPIActivities {

    /**
     * 会員情報の集計
     * @return array
     */
    static public function members() {

        $members = [];
        foreach (Member::RANKS as $rank => $item) {
            $rankers = Member::sql("SELECT count(*) AS CNT , SUM(premium_point) AS PREMIUM , SUM(gift_point) AS GIFT FROM member_state WHERE rank=? ", [$rank])[0];
            $rankers['name'] = $item['rank_name'];

            $sub_none = Member::sql("SELECT count(*) AS CNT FROM member_state LEFT JOIN subscription ON subscription.id=member_state.subscription_id WHERE rank=? AND member_state.subscription_id>0 AND mode<>? ", [$rank, SubscriptionEx::MODE_CANCEL])[0];
            $sub_cancel = Member::sql("SELECT count(*) AS CNT FROM member_state LEFT JOIN subscription ON subscription.id=member_state.subscription_id WHERE rank=? AND member_state.subscription_id>0 AND mode=? ", [$rank, SubscriptionEx::MODE_CANCEL])[0];
//var_dump($sub_none);exit;
            $rankers['sub_none'] = $sub_none['CNT'];
            $rankers['sub_cancel'] = $sub_cancel['CNT'];

            $members[$rank] = $rankers;
        }


        return $members;
    }

}