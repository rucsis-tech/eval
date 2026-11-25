<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth(Admin::MemberList);

    $uid = $request['uid'];

    $Member = Member::getDetail($uid);
    $Member->payments = PaymentHistory::where('uid', $uid)
        ->select(["payment_history.*", "payment_item.name"])
        ->innerJoin("payment_item", "payment_item.id", "=", "payment_history.item_id")
        ->all();
    $Member->point_purchased = Point::purchasedQuery()->where('point_purchased.uid', $uid)->orderBy('id', 'ASC')->all();
    $Member->points = Point::logQuery()->where('uid', $uid)
        ->where([['kind', '=', Point::KIND_SPENDING], 'OR', ['origin_id', '=', '0']])
        ->orderBy('id', 'ASC')->all();
    $Member->scores = Score::logQuery()->where('uid', $uid)->orderBy('id', 'ASC')->all();
    $Member->flags = MemberFlag::getFlags($uid);

    // WinSalon（月額予想）サブスク
    $Member->Subscription = SubscriptionEx::select(['subscription.*', 'payment_history.purchased_at', 'payment_item.name', 'payment_item.id AS item_id'])
        ->where('subscription.uid', $uid)
        ->where('subscription.kind', SubscriptionEx::KIND_CENTER)
        ->where('subscription.expire_at', '>', Model::date())
        ->leftJoin('payment_history', 'payment_history.id=payment_id')
        ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id')
        ->first();

    // WINDEX サブスク（プレミアム／ベーシック）
    $Member->WindexSubscription = SubscriptionEx::select(['subscription.*', 'payment_history.purchased_at', 'payment_item.name', 'payment_item.id AS item_id'])
        ->where('subscription.uid', $uid)
        ->where('subscription.kind', 'IN', [SubscriptionEx::KIND_LOCAL, SubscriptionEx::KIND_ALL])
        ->where('subscription.expire_at', '>', Model::date())
        ->leftJoin('payment_history', 'payment_history.id=payment_id')
        ->leftJoin('payment_item', 'payment_item.id=payment_history.item_id')
        ->orderBy('subscription.id', 'DESC')
        ->first();

    if ($Member->subscription_id == -1) {
        $Member->debug_subscription = true;
    } else {
        $Member->debug_subscription = false;
    }
    $Member->setRankName();

    // 使用回数
    $PurchasedCount = MemberProfile::first(['uid' => $uid, 'category' => MemberProfile::CATEGORY_PURCHASED_COUNT]);
    $Member->purchased_count = $PurchasedCount->value ?? 0;
    $PointUseCount = MemberProfile::first(['uid' => $uid, 'category' => MemberProfile::CATEGORY_POINT_USED_COUNT]);
    $Member->point_used_count = $PointUseCount->value ?? 0;
    $PaymentSum = MemberProfile::first(['uid' => $uid, 'category' => MemberProfile::CATEGORY_PAYMENT_SUM]);
    $Member->payment_sum = $PaymentSum->value ?? 0;


    return [
        'member' => $Member,
        'request' => $request,
        'query' => isset($query) ? $query->getRequestQuery() : '',
    ];


});