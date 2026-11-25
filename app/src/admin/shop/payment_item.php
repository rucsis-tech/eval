<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('PaymentItem');

    $Item = null;

    $api_type = $request['api_type'] ?? '';

    if ($api_type == 'ADD') {
        // 追加登録
        $API->auth('PaymentItemAdd', $request);
        unset($request['id']);
        $id = PaymentItem::insert([
            'type' => $request['data']['type'],
            'amount' => $request['data']['amount'],
            'quantity' => $request['data']['quantity'],
            'sales_begin_at' => $request['data']['sales_begin_at'],
            'sales_close_at' => $request['data']['sales_close_at'],
        ]);
        $Item = PaymentItem::first($id);
        $result = $Item->store($request['data']);

    } else if ($api_type == 'MOD') {
        $API->auth('PaymentItemMod', $request);

        $Item = PaymentItem::first($request['data']['id']);
        $result = $Item->store($request['data']);

    } else {
        // リスト抽出
        $API->auth('PaymentItem', $request);
        if (!empty($request['type'])) {
            $list = PaymentItem::where('type', $request['type'])
                ->all();

        } else {
            $list = PaymentItem::where('type', 'IN', [PaymentItem::TYPE_SUBSC, PaymentItem::TYPE_POINT])
                ->all();
        }


        foreach ($list as $item) {
            $item = PaymentItem::cast($item);
            $item->datalize();
        }
    }


    return [
        'itemList' => $list ?? [],
        'item' => $Item,
        'request' => $request,
        'result' => $result ?? null,
        'METHODS' => PaymentHistory::METHODS,
        'PLANS' => PaymentItem::PLANS,
        'KINDS' => SubscriptionEx::KINDS,
    ];
});