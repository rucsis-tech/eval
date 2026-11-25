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
    $type = $request['type'];
    $staff = (($request['staff'] ?? '') == 'true');
    $uid = $request['uid'] ?? '';
    $method = $request['method'] ?? '';

    $query = PaymentHistory::select([
        'payment_history.*',
        'payment_item.name', 'payment_item.type',
        'MEMBER.nickname', 'MEMBER.ad_code'])
        ->where('purchased_at IS NOT NULL')
        ->where('payment_history.purchased_at', '>=', $since)
        ->where('payment_history.purchased_at', '<', $until)
        ->leftJoin('member_condition AS CND', 'CND.uid=payment_history.uid')
        ->leftJoin('member_master AS MEMBER', 'MEMBER.uid=payment_history.uid')
        ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id');

    // UID
    if ($uid) $query->where('payment_history.uid', $uid);
    // 決済種別
    if ($method && $method == PaymentHistory::METHOD_CREDIT) $query->where('payment_history.method', 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT]);
    if ($method && $method == PaymentHistory::METHOD_AOZORA) $query->where('payment_history.method', $method);
    // 商品タイプ
    if ($type == PaymentItem::TYPE_SUBSC) $query->where('payment_item.type', PaymentItem::TYPE_SUBSC);
    if ($type == PaymentItem::TYPE_POINT) $query->where('payment_item.type', PaymentItem::TYPE_POINT);


    if (!$staff) {
        // 一般会員売上のみに制限する
        $query->where('(CND.staff_flag=0)');
    }
    $List = $query->all();

    // ------------------------------------------------------------------------------------------------------
    // グラフ用データ
    $bars = [];
    for ($tm = strtotime($since); $tm <= strtotime($until); $tm += 86400) {
        $bars[date('Y-m-d', $tm)] = 0;
    }

    foreach ($List as $Item) {
        $day = substr($Item->purchased_at, 0, 10);
        $bars[$day] += $Item->amount;
        $Item->gift_point = $Item->work('gift_point', [])['quantity'] ?? 0;
        $Item->premium_point = $Item->work('premium_point', [])['quantity'] ?? 0;
    }

    // ------------------------------------------------------------------------------------------------------
    // 全期間売上
    $sumQuery = PaymentHistory::select('SUM(amount) AS SUM')
        ->where('purchased_at IS NOT NULL')
        ->leftJoin('member_condition AS CND', 'CND.uid=payment_history.uid')
        ->leftJoin('member_master AS MEMBER', 'MEMBER.uid=payment_history.uid');

    if (!$staff) {
        // 一般会員売上のみに制限する　
        $sumQuery->where('(CND.staff_flag=0) ');
    }
    $sum = $sumQuery->row();


    return [
        'request' => $request,
        'since' => $since,
        'until' => $until,
        'list' => $List,
        'bars' => $bars ?? [],
        'total' => $sum['SUM'] ?? 0,
    ];

});