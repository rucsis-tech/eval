<?php

namespace classes\auth;

use Exception;

class AuthMailClass extends AuthClass {

    public static string $USER_AGENT_HEADER = 'User-Agent: Local API/3.0 (PHP)';
    public static string $provider = 'MAIL';


    /**
     * @param $redirect_url
     * @param $id
     * @param $seed
     * @return string
     * @throws Exception
     */
    static
    public function getLoginURL($redirect_url, $state): string {
        $query = array(
            'client_id' => config('APP_NAME'),
            'redirect_uri' => $redirect_url,
            'state' => $state,
            'scope' => 'profile',
        );

        return BASE_URL . 'auth/login?' . http_build_query($query);
    }


    /**
     * @param $code
     * @return array
     * @throws Exception
     */
    public function token($code): array {
        // アクセストークン取得
        $postData = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('CLIENT_ID_AUTH', 'APP'),
            'client_secret' => config('CHANNEL_SECRET_AUTH', 'SECRET')
        );

        try {
            $ch = curl_init();
            $api_url = AUTH_API_URL . "v3/token.php";

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', self::$USER_AGENT_HEADER));
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty(config('API_BASIC_USERPWD'))) {
                curl_setopt($ch, CURLOPT_USERPWD, config('API_BASIC_USERPWD'));
            }

            $response = curl_exec($ch);

            curl_close($ch);

            if (empty($response)) {
                $json = ['error' => "NULL response API($api_url)"];
            } else {
                $json = json_decode($response, true);
            }

        } catch (Exception $e) {

            $json = ['error' => $e->getMessage()];
        }
        return $json;
    }

    /**
     * @param $accessToken
     * @return array
     */

    public function profile($accessToken): array {
        // アプリアクセストークン
        if (str_contains($accessToken, '@')) {
            $postData = ['mail' => $accessToken];
        } else {
            $postData = ['access_token' => $accessToken];
        }

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', self::$USER_AGENT_HEADER));
            curl_setopt($ch, CURLOPT_URL, AUTH_API_URL . 'v3/profile.php');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty(config('API_BASIC_USERPWD'))) {
                curl_setopt($ch, CURLOPT_USERPWD, config('API_BASIC_USERPWD'));
            }

            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);

            if (isset($response['error'])) throw new Exception($response['error'] . ': ' . $response['message']);

            $response['guid'] = self::makeGUID($response['userId']);

        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }


        return $response;
    }

    /**
     * @param $refresh_token
     * @return array
     * @throws Exception
     */
    public function refresh($refresh_token): array {

        $postData = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
            'client_id' => config('CLIENT_ID_LINE'),
            'client_secret' => config('CHANNEL_SECRET_LINE')
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', self::$USER_AGENT_HEADER));
        curl_setopt($ch, CURLOPT_URL, AUTH_API_URL . 'v3/token.php');
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