<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('Sales');

    $order_id = $request['order_id'];
    $Payment = PaymentHistory::where('order_id', $order_id)->first();
    if (empty($Payment)) throw new Exception("No Payment [{$order_id}]");

    $response = $Payment->work('response',[]);
    $webhooks = $Payment->work('$webhooks',[]);

    //$order = MulPay::request('order/inquiry', ['accessId' => $AccessID]);
    $order = MulPay::request('order/inquiry', ['orderId' => $order_id]);

    if (!isset($order['orderReference'])) throw new Exception("No Order [{$order_id}]");

    try {
        $Payment = PaymentHistory::begin('order_id', $order_id);
        if ($Payment) {
            $Payment->setOrder($order);
        }
        PaymentHistory::commit();

    } catch (Exception $e) {
        PaymentHistory::rollback();
        throw new Exception($e->getMessage());
    }

    return [
        'request' => $request,
        'order_id' => $order_id,
        'response' => $response,
        'webhooks' => $webhooks,
        'order' => $order,
    ];

});