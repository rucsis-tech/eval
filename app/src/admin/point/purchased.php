<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('Sales');

    // ------------------------------------------------------------------------------------------------------
    // 期間
    $term = $request['term'];

    $last_month = date('Y-m-01 00:00:00', strtotime('last month'));
    $this_month = date('Y-m-01 00:00:00', strtotime('this month'));
    $next_month = date('Y-m-01 00:00:00', strtotime('next month'));
    if ($term == 'THIS_MONTH') {

        $since = $this_month;
        $until = $next_month;

    } else if ($term == 'LAST_MONTH') {
        $since = $last_month;
        $until = $this_month;

    } else {
        $since = $request['since'];
        $until = Model::date(strtotime($request['until']) + 86400);
    }

    // ------------------------------------------------------------------------------------------------------
    // リスト抽出
    $kind = $request['kind'];
    $staff = (($request['staff'] ?? '') == 'true');
    $uid = $request['uid'] ?? '';
    $method = $request['method'] ?? '';

    $query = Point::fromLog("point_log")
        ->select(["point_log.*",
            "payment_item.name AS item_name", "payment_item.id AS item_id",
            "payment_history.amount", 'payment_history.method',
            "point_origin.id AS oid", "point_origin.kind", "point_origin.category", "point_origin.identifier", "point_origin.work AS reason",
            "member_master.id AS mid",
        ])
        ->where("point_origin.id > 0")
        ->where("point_origin.kind", 'IN', [Point::KIND_PURCHASED, Point::KIND_GIVE_POINTS, Point::KIND_CANCEL])
        ->leftJoin("point_origin", "point_origin.id = point_log.origin_id")
        ->leftJoin("payment_history", "payment_history.id = point_origin.rid AND payment_history.uid=point_log.uid")
        ->leftJoin("payment_item", "payment_item.id = payment_history.item_id")
        ->leftJoin('member_master', 'member_master.uid=point_log.uid')
        ->leftJoin('member_condition AS CND', 'CND.uid=point_log.uid')
        ->orderBy('point_log.created_at', 'ASC')
        ->where('point_log.created_at', '>=', $since)
        ->where('point_log.created_at', '<', $until);

    // UID
    if ($uid) $query->where('point_log.uid', $uid);
    // 決済種別
    if ($method && $method == PaymentHistory::METHOD_CREDIT) $query->where('payment_history.method', 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT]);
    if ($method && $method == PaymentHistory::METHOD_AOZORA) $query->where('payment_history.method', $method);
    // 処理
    if ($kind !== 'ALL') {
        $query->where('point_origin.kind', $kind);
    }

    if (!$staff) {
        // 一般会員売上のみに制限する
        $query->where('(CND.staff_flag=0)');
    }
    $List = $query->all();

    // ------------------------------------------------------------------------------------------------------
    // 全期間


    return [
        'request' => $request,
        'since' => $since,
        'until' => $until,
        'list' => $List,
        'bars' => $bars ?? [],
        'total' => $sum['SUM'] ?? 0,
    ];

});