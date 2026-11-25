<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $API->auth('NarrowExec');


    $since = $request['since'];
    $until = $request['until'];

    $query = Member::select([
        'member_condition.uid',
        'nickname',
        'member_master.ad_code',
        'member_master.created_at',
        //'last_approved_at',

        'IFNULL(VIP_T.value,0) AS vip_count',
        'IFNULL(VIP_E.value,0) AS vip_empty',
        'IFNULL(LITE_T.value,0) AS lite_count',
        'IFNULL(LITE_E.value,0) AS lite_empty',

        'QUBES.registered',

        'member_condition.acquired_score',
        'AQ.acquired',

        'IFNULL(all_quantity,0) AS apply_quantity',
        'IFNULL(all_payment,0) AS apply_payment',
        'IFNULL(approved_quantity,0) AS approved_quantity',
        'IFNULL(approved_payment,0) AS approved_payment',

        'IFNULL(lite_all_quantity,0) AS lite_apply_quantity',
        'IFNULL(lite_all_payment,0) AS lite_apply_payment',
        'IFNULL(lite_approved_quantity,0) AS lite_approved_quantity',
        'IFNULL(lite_approved_payment,0) AS lite_approved_payment',


        'member_condition.staff_flag',
    ])
        ->orderBy('member_condition.uid')
        ->join('member_condition', 'member_condition.uid=member_master.uid')
        // --------------------------------------------------------------------------------------
        // LQ数
        ->leftJoin('member_count AS VIP_T', "VIP_T.uid=member_master.uid AND VIP_T.type='" . MemberCount::TYPE_QUBE_NORMAL_TOTAL . "'")
        ->leftJoin('member_count AS VIP_E', "VIP_E.uid=member_master.uid AND VIP_E.type='" . MemberCount::TYPE_QUBE_NORMAL_EMPTY . "'")
        ->leftJoin('member_count AS LITE_T', "LITE_T.uid=member_master.uid AND LITE_T.type='" . MemberCount::TYPE_QUBE_LITE_TOTAL . "'")
        ->leftJoin('member_count AS LITE_E', "LITE_E.uid=member_master.uid AND LITE_E.type='" . MemberCount::TYPE_QUBE_LITE_EMPTY . "'")

        //->leftJoin('(select uid,MAX(approved_at) AS last_approved_at '
        //    . " FROM payment_history "
        //    . " WHERE (approve=1) AND (payment>0) "
        //    . " GROUP BY uid) AS LASTS ",
        //    'LASTS.uid=member_master.uid')
        // --------------------------------------------------------------------------------------
        // 期間指名数
        ->leftJoin("(SELECT uid,count(generation) as registered  "
            . " FROM member_qube "
            . " WHERE (generation=0) AND (nominated_at BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY uid) AS QUBES ",
            'QUBES.uid=member_master.uid')
        // --------------------------------------------------------------------------------------
        // 期間獲得スコア
        ->leftJoin("(SELECT uid,SUM(acquired) as acquired  "
            . " FROM member_acquired "
            . " WHERE (acquired_day BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY uid) AS AQ ",
            'AQ.uid=member_master.uid')
        // --------------------------------------------------------------------------------------
        // 期間購入
        ->leftJoin("(SELECT P.uid,SUM(P.quantity) as all_quantity , SUM(P.payment) as all_payment "
            . " FROM payment_history AS P "
            . " LEFT JOIN item_master ON item_master.id=P.item_id "
            . " WHERE (qube_type=0) AND (P.status=0) AND (P.payment>0) AND (P.created_at BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY P.uid) AS VIP_A ",
            'VIP_A.uid=member_master.uid')
        ->leftJoin("(SELECT P.uid,SUM(P.quantity) as approved_quantity , SUM(P.payment) as approved_payment "
            . " FROM payment_history AS P "
            . " LEFT JOIN item_master ON item_master.id=P.item_id "
            . " WHERE (qube_type=0) AND (P.status=0) AND (P.approve=1) AND (P.payment>0) AND (P.created_at BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY P.uid) AS VIP_P ",
            'VIP_P.uid=member_master.uid')
        ->leftJoin("(SELECT P.uid,SUM(P.quantity) as lite_all_quantity , SUM(P.payment) as lite_all_payment "
            . " FROM payment_history AS P "
            . " LEFT JOIN item_master ON item_master.id=P.item_id "
            . " WHERE (qube_type=1) AND (P.status=0) AND (P.payment>0) AND (P.created_at BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY P.uid) AS LITE_A ",
            'LITE_A.uid=member_master.uid')
        ->leftJoin("(SELECT P.uid,SUM(P.quantity) as lite_approved_quantity , SUM(P.payment) as lite_approved_payment "
            . " FROM payment_history AS P "
            . " LEFT JOIN item_master ON item_master.id=P.item_id "
            . " WHERE (qube_type=1) AND (P.status=0) AND (P.approve=1) AND (P.payment>0) AND (P.created_at BETWEEN '$since 00:00' AND '$until 23:59:59') "
            . " GROUP BY P.uid) AS LITE_P ",
            'LITE_P.uid=member_master.uid');

    $list = $query->rows();

    return
        [
            'list' => $list ?? [],
            'request' => $request,
            'query' => isset($query) ? $query->getRequestQuery() : '',
        ];

});
