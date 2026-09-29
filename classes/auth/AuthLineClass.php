<?php

namespace classes\auth;

use Exception;

class AuthLineClass extends AuthClass {

    public static string $provider = 'LINE';


    /**
     * 連携ログイン用URLの取得
     * @param $redirect_url
     * @return string
     * @throws Exception
     */

    static
    public function getLoginURL($redirect_url, $state): string {

        $url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code"
            . "&client_id=" . config('CLIENT_ID_LINE')
            . "&redirect_uri=" . urlencode($redirect_url)
            . "&state=" . $state
            . "&bot_prompt=normal"
            . "&scope=profile%20openid"
            . "&nonce=" . md5(microtime());
        return $url;
    }



    /* エラー時
    error_description：エラーメッセージ 例：The resource owner denied the request.
    state:リクエスト時のstateの値
    error：エラー内容 例：access_denied

    成功時（初回）
    ["code"]=> string(20) "YKOWZlDTpz8iEAPaePEk"	// アクセストークン取得コード
    ["state"]=> string(6) "123456"
    */

    /**
     * @param $code string // アクセストークンの取得に使用される認可コード。有効期間は10分です。また、認可コードは1回のみ利用可能です。
     * @return array
     * @throws \Exception
     */

    public function token(string $code): array {
        $postData = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => BASE_URL,
            'client_id' => config('CLIENT_ID_LINE'),
            'client_secret' => config('CHANNEL_SECRET_LINE')
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/oauth2/v2.1/token');
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

    /*成功時
    ["access_token"]=> string(236) "eyJhbGciOiJIUzI1NiJ9.2zEKv7VemzkH_VdT2HXwhKfjMdpv9J8b2ytNSKzhBmKSUXUpf0bHdV_d4H0SYshZ49e2NQ2vKJ7y7NqZ59yMKnJ9zIa_BxPGFy1xFYfKJVlI85eXgIgqhC8DPUNDQrsMDYHgY3DQx1LJDqYSifn-YMXjG67S4AFIbEKrM5iaaNM.a3qIey_bGdknsvcrL0I2WfxW3WgyNaivUELgCpuAmuQ"
    ["token_type"]=> string(6) "Bearer"
    ["refresh_token"]=> string(20) "taAoAdTmul8rlRHmLgWD"
    ["expires_in"]=> int(2592000)
    ["scope"]=> string(7) "profile"
    */

    public function profile($accessToken): array {

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken));
            curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/profile');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);
            $response['guid'] = self::makeGUID($response['userId']);

        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $response;
    }

    public function refresh($refresh_token): array {
        // アクセストークン取得
        $postData = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => config('CLIENT_ID_LINE'),
            'client_secret' => config('CHANNEL_SECRET_LINE')
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/oauth2/v2.1/token');
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