<?php

class MulPay {

    /* エラー時レスポンス
    type	エラーの説明ページURL 該当のエラーに説明ページがない場合は何も返りません	https://mp-faq.gmo-pg.com/s/article/D00923
    title	エラーの内容サマリー エラーコードとして取り扱えます 一覧は以下の表を参照ください	invalid_parameter
    detail	エラーの詳細説明 エラーメッセージとして取り扱えます 全て英文です。	The value provided for parameter is invalid.(orderid)
    instance	エラーが発生したエンドポイント	/credit/charge
    */

    public static $ERROR_TITLE = [
        'invalid_contract' => '対象ショップの契約状態が無効であるか、ショップの設定が不足しています。	400',
        'invalid_header' => 'リクエストヘッダーの値が不正です。	400',
        'invalid_parameter' => 'リクエストパラメーターの書式または桁数が不正です。	400',
        'invalid_request' => '対象の取引に対して不正なリクエストです。	400',
        'invalid_status' => '対象の取引状態に対して処理できないリクエストです。	400',
        'missing_parameter' => '必須パラメーターが設定されていません。	400',
        'transaction_count_exceeded' => '同一取引に対する処理上限を超過しています。所定の返金回数を超過した場合などに発生します。	400',
        'transaction_expired' => '処理期限を超過しています。オーソリの有効期限超過後に確定を行った場合などに発生します。	400',
        'unauthorized_request' => '認証情報が正しくないか、許可されないIPアドレスのため、リクエストは受け付けられません。	401',
        'operation_aborted_by_user' => 'お客様の操作により決済手続きが中止されました。	402',
        'card_declined' => 'カード起因のエラーにより、リクエストが拒否されました。	402',
        'insufficient_balance' => '残高が不足しているため、リクエストが拒否されました。	402',
        'amount_limit_exceeded' => '上限金額を超過しているため、リクエストが拒否されました。	402',
        'processing_failure' => '決済事業者からエラーが返りました。詳細はdetailを参照ください。	402',
        'resource_not_found' => 'APIエンドポイントが無効であるため、リクエストは受け付けられません。	404',
        'method_not_allowed' => 'HTTPメソッドが正しくないため、リクエストは受け付けられません。リダイレクト時はGET、それ以外はPOSTです。	405',
        'conflict' => '二重リクエストのため、リクエストは受け付けられません。	409',
        'unsupported_media_type' => 'Content-Typeヘッダーが正しくないため、リクエストは受け付けられません。application/jsonのみに対応しています。	415',
        'too_many_requests' => '同時接続数の上限を超過しています。	429',
        'internal_server_error' => '当サービスのサーバーで問題が発生したため、リクエストを処理できませんでした。	500',
        'bad_gateway' => '外部事業者やネットワークで問題が発生したため、リクエストを処理できませんでした。	502',
        'maintenance' => '外部事業者によるメンテナンスのため、リクエストは受け付けられません。	502',
        'service_unavailable' => '当サービスがメンテナンス中のため、リクエストは受け付けられません。	503',
    ];

    /**
     * @throws Exception
     */
    static
    public function request($path, $postData, $IdempotencyKey = null) {

        $GMO_SHOP_ID = config('GMO_SHOP_ID');
        $GMO_SHOP_PASSWORD = config('GMO_SHOP_PASSWORD');
        $AUTH_CODE = base64_encode($GMO_SHOP_ID . ":" . $GMO_SHOP_PASSWORD);
        $headers = [
            "Content-Type: application/json;charset=UTF-8",
            "Authorization: Basic $AUTH_CODE",
        ];
        if (!empty($IdempotencyKey)) {
            $headers[] = "Idempotency-Key: $IdempotencyKey";
        }
        //pre($headers);
        if (str_starts_with($path, 'http')) {
            $url = $path;
        } else {
            $url = config('GMO_PAYMENT_URL') . $path;
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (App::isLocal()) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            $http_code = $info['http_code'];
            $content_type = $info['content_type'];

            $json_decoded = json_decode($response, true);
            if ($http_code < 200 || 299 < $http_code) {
                $json_decoded['error'] = $http_code;
            }

        } catch (Exception $e) {
            $json_decoded = [
                'error' => 1,
                'message' => $e->getMessage(),
            ];
        }

        return $json_decoded;

    }

    /**
     * $webhook の保存に不要な情報を削除
     * @param $webhook
     * @return mixed
     */

    static public function trimWeb($webhooks, $webhook) {
        unset($webhook['ShopID']);
        unset($webhook['ShopPass']);
        unset($webhook['AccessID']);
        unset($webhook['AccessPass']);
        unset($webhook['OrderID']);

        $recent = [];
        foreach ($webhooks as $current) {
            $recent = array_merge($recent, $current);
        }

        foreach ($recent as $key => $value) {
            $post = $webhook[$key] ?? '-';
            if ($post === $value) {
                unset($webhook[$key]);
            }
        }
        return $webhook;
    }

    /**
     * $order の保存に不要な情報を削除
     * @param $order
     * @return mixed
     */
    static public function trimOrder($order) {
        unset($order['orderReference']['accessId']);
        unset($order['orderReference']['accessPass']);
        unset($order['orderReference']['orderId']);
        return $order;
    }

}