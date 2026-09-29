<?php

namespace classes\auth;

use classes\CryptClass;

class AuthClass {

    public static string $provider = '';
    const CRYPT_KEY = 'auth-crypt-13224';

    static
    public function getAuthAPI($provider): AuthClass {

        switch ($provider) {
            case 'CODE':
                $API = new AuthCodeClass();
                break;
            case 'FACEBOOK':
                $API = new AuthFacebookClass();
                break;
            case 'LINE':
                $API = new AuthLineClass();
                break;
            case 'GOOGLE':
                $API = new AuthGoogleClass();
                break;
            case 'TWITTER':
                $API = new AuthTwitterClass();
                break;
            case 'YAHOO':
                $API = new AuthYahooClass();
                break;
            case 'MAIL':
            default:
                $API = new AuthMailClass();
                break;
        }

        return $API;
    }

    /**
     * @param $redirect_url
     * @param $state
     * @return string
     */

    static
    public function getLoginURL($redirect_url, $state): string {
        return "";
    }


    static
    public function getLoginState($id = ''): string {
        $provider = static::$provider;
        $time = time();
        return CryptClass::encrypt("$provider,$id,$time", self::CRYPT_KEY);
    }

    static public function explodeLoginState($state): array {
        $states = CryptClass::decrypt($state, self::CRYPT_KEY);
        $array = explode(',', $states);
        return [
            'provider' => $array[0] ?? '',
            'id' => $array[1] ?? '',
            'time' => $array[2] ?? '',
        ];
    }

    static public function makeGUID($id): string {
        return static::$provider . '-' . $id;
    }

    public function token(string $code): array {
        return [];
    }

    public function profile($accessToken): array {
        return [];
    }

    public function refresh($refresh_token): array {
        return [];
    }


}