<?php
namespace classes\auth;

class AuthFacebookClass {

    public static $provider = 'FACEBOOK';


    /* エラー時
error_description：エラーメッセージ 例：The resource owner denied the request.
state:リクエスト時のstateの値
error：エラー内容 例：access_denied

成功時（初回）
["code"]=> string(20) "YKOWZlDTpz8iEAPaePEk"	// アクセストークン取得コード
["state"]=> string(6) "123456"
*/

    public function token($code) {
        $postData = array(
            'grant_type' => 'authorization_code',
            'client_id' => CLIENT_ID_FACEBOOK,
            'redirect_uri' => OPENID_REDIRECT_URI,
            'client_secret' => CHANNEL_SECRET_FACEBOOK,
            'code' => $code
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v3.3/oauth/access_token');
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

        $postData = array(
            'access_token' => $accessToken,
            'fields' => 'id'
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/me');
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

    public function refresh($refresh_token, $access_token) {
        $arr = array(
            'access_token' => $refresh_token,
            'expires' => 3600
        );

        return $arr;
    }

}