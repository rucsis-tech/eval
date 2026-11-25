<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('Subscription');

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
    $purchased = $request['purchased'] ?? false;
    $free = $request['free'] ?? false;

    $query = SubscriptionEx::select([
        'SUBSC.*',
        'payment_history.amount', 'payment_history.method',
        'payment_item.id AS item_id', 'payment_item.name AS item_name',
        'MEMBER.nickname', 'MEMBER.ad_code', 'MEMBER.id AS mid',
    ])
        ->from('subscription AS SUBSC')
        ->where('SUBSC.start_at', '>=', $since)
        ->where('SUBSC.start_at', '<', $until)
        ->leftJoin('member_condition AS CND', 'CND.uid=SUBSC.uid')
        ->leftJoin('member_master AS MEMBER', 'MEMBER.uid=SUBSC.uid')
        ->leftJoin('payment_history', 'payment_history.id=SUBSC.payment_id')
        ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id');

    // UID
    if ($uid) $query->where('SUBSC.uid', $uid);
    // 決済種別
    if ($method && $method == PaymentHistory::METHOD_CREDIT) $query->where('payment_history.method', 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT]);
    if ($method && $method == PaymentHistory::METHOD_AOZORA) $query->where('payment_history.method', $method);

    // 新規/継続
    if ($type == SubscriptionEx::TYPE_FIRST) $query->where('SUBSC.type', SubscriptionEx::TYPE_FIRST);
    if ($type == SubscriptionEx::TYPE_EXTEND) $query->where('SUBSC.type', SubscriptionEx::TYPE_EXTEND);

    // 有償購入・無料

    if (!$staff) {
        // 一般会員売上のみに制限する
        $query->where('(CND.staff_flag=0)');
    }
    $List = $query->all();


    return [
        'request' => $request,
        'since' => $since,
        'until' => $until,
        'list' => $List,
        'bars' => $bars ?? [],
    ];

});