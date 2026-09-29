<?php
namespace classes\auth;
use Exception;

class AuthGoogleClass extends AuthClass {
    public static string $provider = 'GOOGLE';

    /**
     * @param $redirect_url
     * @param $state
     * @return string
     * @throws \Exception
     */
    static
    public function getLoginURL($redirect_url, $state): string {
        $query = array(
            'client_id' => config('GOOGLE_CLIENT_ID'),
            'redirect_uri' => $redirect_url,
            'state' => $state,
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'response_type' => 'code',
            'access_type' => 'offline',
        );

        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($query);
    }


    /* エラー時
    error_description：エラーメッセージ 例：The resource owner denied the request.
    state:リクエスト時のstateの値
    error：エラー内容 例：access_denied

    成功時（初回）
    ["code"]=> string(20) "YKOWZlDTpz8iEAPaePEk"	// アクセストークン取得コード
    ["state"]=> string(6) "123456"
    */

    public function token($code): array {
        $postData = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => BASE_URL,
            'client_id' => config('GOOGLE_CLIENT_ID'),
            'client_secret' => config('GOOGLE_CLIENT_SECRET')
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        try {
            $json = json_decode($response, true);
        } catch (Exception $e) {
            $json = ['error' => $e->getMessage()];
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

            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);
            if (isset($response['id'])) {
                $response['guid'] = self::makeGUID($response['id']);
                $response['displayName'] = $response['name'];
                $response['pictureUrl'] = $response['picture'];
                //$json['mail'] = $json['email'];
            }

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
            'client_id' => config('GOOGLE_CLIENT_ID'),
            'client_secret' => config('GOOGLE_CLIENT_SECRET')
        );
        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');

        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v4/token');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        try {
            $json = json_decode($response, true);
        } catch (Exception $e) {
            $json = ['error' => $e->getMessage()];
        }

        return $json;
    }


}