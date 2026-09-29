<?php

namespace classes\auth;
class AuthYahooClass {

    public static $provider = 'YAHOO';

    /**********************************************************************
     * ヤフーログイン
     **********************************************************************/
    /*
        // ヤフー機能確認
        $res = file_get_contents( 'https://auth.login.yahoo.co.jp/yconnect/v2/.well-known/openid-configuration' );
        $json = json_decode($res);
        var_dump($json);
        exit();
    */
    /*
        Yahoo連携 ログイン
        引数：なし
        ret：null エラー
             成功：トークン
    */
    public function token($code) {
        $public_keys = json_decode(file_get_contents('https://auth.login.yahoo.co.jp/yconnect/v2/public-keys'));

        // アクセストークン取得
        $postData = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => OPENID_REDIRECT_URI,
            'client_id' => CLIENT_ID_YAHOO,
            'client_secret' => CHANNEL_SECRET_YAHOO
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://auth.login.yahoo.co.jp/yconnect/v2/token');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        try {
            $json = json_decode($response, true);
        } catch (Exception $e) {
            $json = [];
        }
        return $json;
    }

    public function profile($accessToken) {
        // プロフィール取得
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
            curl_setopt($ch, CURLOPT_URL, 'https://userinfo.yahooapis.jp/yconnect/v2/attribute');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($result, true);

        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $response;
    }

    public function refresh($refresh_token, $access_token) {

        // アクセストークン取得
        $postData = array(
            'grant_type' => 'refresh_token',
            'client_id' => CLIENT_ID_YAHOO,
            'client_secret' => CHANNEL_SECRET_YAHOO,
            'refresh_token' => $refresh_token
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://auth.login.yahoo.co.jp/yconnect/v2/token');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        try {
            $json = json_decode($response, true);
        } catch (Exception $e) {
            $json = [];
        }
        return $json;
    }

}

/*
// ヤフーシグネチャ照合
    function verifySignature($parts, $payload, $signature, $publicKey) {
        $data = $parts . '.' . $payload;
        $decodedSignature = base64UrlDecode($signature);
        $publicKeyId = openssl_pkey_get_public($publicKey);
        if (!$publicKeyId) {
        // failed to get public key resource
            return false;
        }
        $result = openssl_verify($data, $decodedSignature, $publicKeyId, 'RSA-SHA256');
        openssl_free_key($publicKeyId);
        if ($result !== 1) {
            // invalid signature
            return false;
        }
        return true;
    }

// ヤフーbase64でコード
    function base64UrlDecode($data) {
        $replaced = str_replace(array('-', '_'), array('+', '/'), $data);
        $lack = strlen($replaced) % 4;
        if ($lack > 0) {
            $replaced .= str_repeat("=", 4 - $lack);
        }
        return base64_decode($replaced);
    }
*/
