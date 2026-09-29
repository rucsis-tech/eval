<?php
namespace classes\auth;
use Exception;

class AuthTwitterClass {

    public static $provider = 'TWITTER';


    public function token($code) {

        $oauth_token = empty($_REQUEST['oauth_token']) ? '' : $_REQUEST['oauth_token'];
        $oauth_verifier = empty($_REQUEST['oauth_verifier']) ? '' : $_REQUEST['oauth_verifier'];

        $postData = array(
            'oauth_token' => $oauth_token,
            'oauth_verifier' => $oauth_verifier,
        );
        $request_url = 'https://api.twitter.com/oauth/access_token';
        $authorizations = self::_getAuthorization('POST', $request_url);
        $authorizationHeader = 'Authorization: OAuth ' . http_build_query($authorizations, '', ',');


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorizationHeader]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        // HTTPヘッダ

        $response = curl_exec($ch);
        curl_close($ch);

        try {
            // oauth_token=252872999-RPkGazW2QCPDanRodnNyI4CkD38Di6cLo7062fkC
            //　&oauth_token_secret=3BykUNXap2tt3PtbQdWAoIcikvOw43YE8L8BJvmU9q5XU
            //　&user_id=252872999
            //　&screen_name=flathomehs

            parse_str($response, $array);
            $array['access_token'] = $array['oauth_token'] . '&' . $array['oauth_token_secret'];
            $array['refresh_token'] = 'none';
            $array['expires_in'] = 86400 * 365;

        } catch (Exception $e) {
            $array = [];
        }
        return $array;
    }

    public function profile($accessToken) {

        try {
            $ch = curl_init();

            $request_url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
            $authorizations = self::_getAuthorization('GET', $request_url, $accessToken);
            $authorizationHeader = 'Authorization: OAuth ' . http_build_query($authorizations, '', ',');

            // LogClass ::log($authorizationHeader, '$authorizationHeader');
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [$authorizationHeader]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($result, true);
            if (isset($response['id'])) {
                $response['userId'] = self::$provider . '-' . $response['id'];
                $response['displayName'] = $response['name'];
                //$json['mail'] = $json['email'];
            }
        } catch (Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        return $response;
    }

    /**
     * そもそも$refrsh_tokenは存在しないようだ
     * @param $refrsh_token
     * @param $access_token
     * @return array|mixed
     */

    public function refresh($refrsh_token, $access_token) {
        return [];
    }

    /**
     * 認証用のAPP内リダイレクトURLを返す
     * @return string
     */

    static public function getLoginURL() {

        return TWITTER_AUTH_REDIRECT;

    }

    /**
     * OAuth1.0認証準備をしてTwitterへリダイレクト
     * @return string
     */

    static public function getLocation() {

        $request_url = 'https://api.twitter.com/oauth/request_token';
        $authorizations = self::_getAuthorization('POST', $request_url);
        $authorizationHeader = 'Authorization: OAuth ' . http_build_query($authorizations, '', ',');

        /*
         * cURL設定
         */
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // SSL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        // レスポンスヘッダの出力
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        // Locationヘッダを追跡
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        // HTTPヘッダ
        curl_setopt($curl, CURLOPT_HTTPHEADER, [$authorizationHeader]);


        // 結果達
        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);

        // 終了
        curl_close($curl);

        /*
         * cURLの結果

        $result = [
            'response' => $response,
            // cURLでリクエストした結果達
            'code'     => $code,
            'info'     => $info,
            'error'    => $error,
            'errno'    => $errno,
        ];

        // {"response":"oauth_token=D-QPxgAAAAABMK1DAAABd0SU8cs&oauth_token_secret=VThUDyVmRohs0Q2EZzfaMa5r1Ll3XAEV&oauth_callback_confirmed=true","code":200,"info":{"url":"https:\/\/api.twitter.com\/oauth\/request_token","content_type":"text\/html;charset=utf-8","http_code":200,"header_size":1504,"request_size":361,"filetime":-1,"ssl_verify_result":0,"redirect_count":0,"total_time":0.206084,"namelookup_time":0.000871,"connect_time":0.004794,"pretransfer_time":0.027364,"size_upload":0,"size_download":121,"speed_download":587,"speed_upload":0,"download_content_length":121,"upload_content_length":-1,"starttransfer_time":0.20604,"redirect_time":0,"redirect_url":"","primary_ip":"104.244.42.66","certinfo":[],"primary_port":443,"local_ip":"192.168.1.13","local_port":52556,"http_version":2,"protocol":2,"ssl_verifyresult":0,"scheme":"HTTPS","appconnect_time_us":27232,"connect_time_us":4794,"namelookup_time_us":871,"pretransfer_time_us":27364,"redirect_time_us":0,"starttransfer_time_us":206040,"total_time_us":206084,"request_header":"POST \/oauth\/request_token HTTP\/1.1\r\nHost: api.twitter.com\r\nAccept: *\/*\r\nAuthorization: OAuth oauth_callback=http%3A%2F%2Flocalhost%3A8000%2F,oauth_consumer_key=rr2zFbQOl40yxMNPnWMFxGj4m,oauth_nonce=dbb61e30addb23e8ce836ef09cd04bc8,oauth_signature_method=HMAC-SHA1,oauth_timestamp=1611763346,oauth_version=1.0,oauth_signature=IJcHb8SXLIS7NmCaoY%2BXVqUm40I%3D\r\n\r\n"},"error":"","errno":0}
         */

        return 'https://api.twitter.com/oauth/authorize?' . $response;


    }


    /**
     * 認証ヘッダの作成
     * @param $method GET/POST
     * @param $request_url
     * @param null $accessToken token と secret を&で繋いだもの
     * @return array
     */

    static public function _getAuthorization($method, $request_url, $accessToken = null) {

        $consumer_key = TWITTER_CONSUMER_KEY; // Twitter Developer登録でアプリ登録した時の値
        $consumer_secret = TWITTER_CONSUMER_SECRET;    // Twitter Developer登録でアプリ登録した時の値
        $state = 'T' . md5(microtime());
        $authorizations = [
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => md5(uniqid(rand(), true)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_callback' => BASE_URL . "?state=$state",
        ];
        /*
        * Signature_keyの作成
        * oauth_signatureを作る時のキー部分
        * oauth_access_secretがあれば"&"の後ろにつける
        */
        $signing_key = self::_rawurlencode($consumer_secret) . '&';

        if ($accessToken) {
            list($oauth_token, $oauth_token_secret) = explode('&', $accessToken);

            $authorizations['oauth_token'] = $oauth_token;
            unset($authorizations['oauth_callback']);

            $signing_key .= self::_rawurlencode($oauth_token_secret);

        }

        // 署名(signature)作成では、アルファベット順にソートする決まり
        ksort($authorizations);

        /*
         * Signature_base_stringを作成
         * ・oauth_signatureを作る時のメッセージ部分
         */
        $signature_base_string = http_build_query($authorizations, '', '&');
        // HTTPメソッドとリクエストトークンURLをURLエンコードして＆で繋ぐ
        $signature_base_string = self::_rawurlencode($method)
            . '&'
            . self::_rawurlencode($request_url)
            . '&'
            . self::_rawurlencode($signature_base_string);


        /*
         * oauth_signatureの作成
         */
        $oauth_signature = base64_encode(
            hash_hmac('sha1', $signature_base_string, $signing_key, true)
        );
        $authorizations['oauth_signature'] = $oauth_signature;
        return $authorizations;

    }

    /**
     * URLエンコード RFC2986版
     */

    static public function _rawurlencode($str = '') {
        if (!$str) {
            return $str;
        }
        $result = str_replace(['+', '%7E'], ['%20', '"'], $str);
        $result = rawurlencode($result);
        return $result;
    }

}