<?php

namespace classes\auth;

use classes\DBClass;
use Exception;

class AuthCodeClass extends AuthClass {

    public static string $provider = 'CODE';

    /**
     * @param $redirect_url_url
     * @param $uid
     * @param $seed
     * @return string
     * @throws Exception
     */
    static
    public function getLoginURL($redirect_url_url, $uid, $seed = null): string {

        if (empty($redirect_url_url)) $redirect_url_url = BASE_URL;

        $UserAuth = DBClass::table('user_auth')
            ->where('uid', $uid)
            ->where('provider', self::$provider)
            ->first();
        if (empty($UserAuth)) {
            $code = 'CODE-' . DBClass::makeHash(20);
            DBClass::insert(['uid' => $uid, 'guid' => $code, 'provider' => self::$provider], 'user_auth');
        } else {
            $code = $UserAuth->guid;
        }

        $query = array(
            'state' => self::getLoginState($uid, $seed),
            'code' => $code,
        );
        return $redirect_url_url . '?' . http_build_query($query);
    }


    /**
     * @param $code
     * @return array
     * @throws Exception
     */
    public function token($code): array {
        $UserAuth = DBClass::table('user_auth')
            ->where('guid', $code)
            ->first();
        if (empty($UserAuth)) {
            return ['error' => 'no user'];
        }
        return [
            'access_token' => $code,
            'token_type' => '',
            'refresh_token' => $code,
            'expires_in' => 2592000,
            'scope' => '',
        ];
    }

    /**
     * @param $accessToken
     * @return array|mixed
     * @throws Exception
     */

    public function profile($accessToken) {
        $UserAuth = DBClass::table('user_auth')
            ->where('guid', $accessToken)
            ->first();
        if (empty($UserAuth)) {
            return ['error' => 'no user'];
        }
        return [
            'guid' => $accessToken,
            'displayName' => '',
            'pictureUrl' => '',
        ];
    }

    /**
     * @param $refresh_token
     * @return array
     * @throws Exception
     */
    public function refresh($refresh_token) {
        $UserAuth = DBClass::table('user_auth')
            ->where('guid', $refresh_token)
            ->first();
        if (empty($UserAuth)) {
            return ['error' => 'no user'];
        }
        return [
            'access_token' => $refresh_token,
            'expires_in' => 2592000,
        ];

    }


}