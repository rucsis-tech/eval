<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('Sales');
    $TYPE_ALL = 'ALL';
    $TYPE_INVALID = 'INVALID';

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
    $type = $request['type'];
    $staff = (($request['staff'] ?? '') == 'true');
    $uid = $request['uid'] ?? '';
    $method = $request['method'] ?? '';

    $query = Point::fromLog("point_log")
        ->select([
            "point_log.uid", "-premium_point AS premium_point", "-gift_point AS gift_point", "-premium_point-gift_point AS point", "point_log.ppb", "point_log.gpb", "point_log.created_at",
            "point_origin.id AS oid", "point_origin.kind", "point_origin.category", "point_origin.rid", "point_origin.identifier", "point_origin.work AS reason",
            "member_master.id AS mid",
            "UC.race_code", "UC.registration",
        ])
        ->where("point_origin.id > 0")
        ->where("point_origin.kind", "IN", [Point::KIND_INVALIDATION, Point::KIND_SPENDING])
        ->leftJoin("point_origin", "point_origin.id = point_log.origin_id")
        ->leftJoin('member_master', 'member_master.uid=point_log.uid')
        ->leftJoin('member_condition AS CND', 'CND.uid=point_log.uid')
        ->leftJoin('uma_comment AS UC', "UC.id=point_origin.rid AND UC.category in ('chat_comment','spoiler')")
        ->orderBy('point_log.created_at', 'ASC')
        ->where('point_log.created_at', '>=', $since)
        ->where('point_log.created_at', '<', $until);

    // UID
    if ($uid) $query->where('point_log.uid', $uid);
    // 決済種別
    if ($method && $method == PaymentHistory::METHOD_CREDIT) $query->where('payment_history.method', 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT]);
    if ($method && $method == PaymentHistory::METHOD_AOZORA) $query->where('payment_history.method', $method);
    // 処理
    if ($type === $TYPE_INVALID) {
        $query->where('point_origin.kind', Point::KIND_INVALIDATION);
    } else if ($type !== $TYPE_ALL) {
        $query->where('point_origin.identifier', 'LIKE', "{$type}%");
    }

    if (!$staff) {
        // 一般会員売上のみに制限する
        $query->where('(CND.staff_flag=0)');
    }
    $List = $query->all();

    // ------------------------------------------------------------------------------------------------------
    // 全期間


    $LICENSE_IDENTIFIERS = array_merge([$TYPE_ALL => '全て', $TYPE_INVALID => '失効'], Point::LICENSE_IDENTIFIERS);

    return [
        'request' => $request,
        'since' => $since,
        'until' => $until,
        'list' => $List,
        'LICENSE_IDENTIFIERS' => $LICENSE_IDENTIFIERS,
        'bars' => $bars ?? [],
        'total' => $sum['SUM'] ?? 0,
    ];

});