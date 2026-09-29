<?php

namespace classes;

use classes\auth\AuthClass;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class SessionClass extends DBClass {

    static bool $SESSION_CREATED = false;
    static string $TOKEN = '';
    static string $GUID = '';
    static string $PROVIDER = '';
    static string $STATE = '';
    static string $WORK = '';


    static string $EMAIL = '';
    static string $ACCESS_TOKEN = '';
    static string $REFRESH_TOKEN = '';
    static string $EXPIRE_AT;

    static string $MSG = '';
    static string $REDIRECT_URL = '';

    const DEL_EXPIRES_TIME = 86400 * 30; // 30 Day
    const COOKIE_EXPIRES = 86400 * 30;

    static string $TRQ2 = '';

    #[NoReturn] static
    public function location($url): void {
        header("Location: " . $url);
        exit();
    }

    // -----------------------------------
    // ログイン処理

    /**
     * Webアクセスの認証遷移・セッション継続
     * @param array $option
     * @return string
     * @throws Exception
     */
    static
    public function checkWebSession($option = []): string {

        self::$TRQ2 = self::getTRQ2();

        $state = $_GET['state'] ?? '';  // クロスサイトリクエストフォージェリ (opens new window)防止用の固有な英数字の文字列
        $code = $_GET['code'] ?? '';    // アクセストークンの取得に使用される認可コード

        try {
            if ($state) {
                // 新規認証遷移による認証
                $session = self::auth($state, $code);
            }

            $token = $_COOKIE['token'] ?? null;
            if (empty($session) && $token) {
                // セッショントークンによる継続
                $session = self::checkSessionToken($token);
            }

        } catch (Exception $e) {

            if (config('APP_DEBUG', false)) throw $e;

            $session = null;
        }


        if (empty($session)) {
            if (empty($option['guest_enable'])) {
                // ゲストセッション無効 なにもしない
                return '';
            } else {
                // 新規ゲストセッション
                self::create();
            }
        }

        // 遷移認証の場合、code/state等引数外して再読み込み
        if ($code && $state) {
            $paths = parse_url($_SERVER['REQUEST_URI']);
            $query = $paths['query'];
            if ($query) {
                parse_str($paths['query'], $queries);
                unset($queries['code']);
                unset($queries['state']);
                $query = !empty($queries) ? ('?' . http_build_query($queries)) : '';
            }
            self::$REDIRECT_URL = BASE_URL . substr($paths['path'], 1) . $query;
        }
        // リダイレクト指定があれば遷移
        $redirect = $_REQUEST['redirect'] ?? '';
        if ($redirect) {
            self::$REDIRECT_URL = $redirect;
        }

        return self::$TOKEN;
    }

    /**
     * 現行セッションの終了・破棄
     * @param $token
     * @return bool
     * @throws Exception
     */
    // セッション削除

    static
    public function destroy($token = null): bool {

        if (empty($token)) $token = SessionClass::$TOKEN ?? '';

        $sql = "UPDATE `sessions` SET `created_at` = '2000-01-01' , `guid` = '' WHERE `token` = ?";
        $res = self::sql($sql, [$token]);
        setcookie('token', "", time() - 86400, '/');
        setcookie('stamp', "", time() - 86400, '/');
        if ($res) {
            return true;
        }
        return false;
    }

    /**
     * 指定 guid のセッション削除
     * @param $guid
     * @return bool
     * @throws Exception
     */
    static
    public function deleteSession($guid): bool {
        $sql = "UPDATE `sessions` SET `created_at` = '2000-01-01' , `guid` = '' WHERE `guid` = ?";
        $res = self::sql($sql, [$guid]);
        if ($res) {
            return true;
        }
        return false;
    }


    /**z
     * セッションをトークンで確認
     * @param string $token
     * @return array
     * @throws Exception
     */

    static
    public function checkSessionToken(string $token): array {

        $res = self::sql("SELECT * FROM `sessions` WHERE `token` = ? AND created_at > ? ", [$token, self::date(-86400 * 30)]);
        if (count($res) == 0) {
            return [];   // セッション情報なし
        }

        $session = $res[0];
        if (strtotime($session['created_at']) < time() - 86400 * 10) {
            // 限界日時更新のため created_at を書き換え
            self::sql("UPDATE `sessions` SET created_at = ? WHERE `token` = ? ", [self::date(), $token]);
        }
        self::setSessionData($session);

        return $session;
    }

    /**
     * 認証API連携確認
     * @param $state string // クロスサイトリクエストフォージェリ (opens new window)防止用の固有な英数字の文字列。この値が認可URLに付与したstateパラメータの値と一致することを検証してください。
     * @param $code string // アクセストークンの取得に使用される認可コード
     * @return array
     * @throws Exception
     */

    static
    public function auth(string $state, string $code): array {

        $states = AuthClass::explodeLoginState($state);
        if (empty($states['time'])) throw new Exception('Auth State Error:' . json_encode($states));

        $state_time = $states['time'];
        if ($state_time < time() - 60 * 60) throw new Exception('State Time Error:' . json_encode($states));

        // $code から アクセストークン取得
        $API = AuthClass::getAuthAPI($states['provider']);
        $tokenData = $API->token($code);
        if (!empty($tokenData['error'])) throw new Exception('State Token Error:' . json_encode($tokenData));

        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'];
        $expire_at = self::date(time() + $tokenData['expires_in']);

        // アクセストークンからユーザー情報取得
        $profile = $API->profile($accessToken);
        if (!empty($profile['error'])) {
            LogClass::notice($tokenData);
            throw new Exception($profile['error']);
        }

        $session = array(
            "guid" => $profile['guid'],
            "provider" => $API::$provider,
        );

        self::$EMAIL = $profile['email'] ?? '';
        self::$EXPIRE_AT = $expire_at;
        self::$ACCESS_TOKEN = $accessToken;
        self::$REFRESH_TOKEN = $refreshToken;

        return self::create($session);
    }

    private
    static function getTRQ2(): string {
        if (!empty($_GET['trq2'])) {
            // GET付与コード
            $trq2 = $_GET['trq2'];

        } else if (!empty($_COOKIE['trq2'])) {
            // Cookie付与コード
            $trq2 = $_COOKIE['trq2'];

        } else {
            // 新規作成 7桁
            $yn = date('yn');
            $trq2 = substr($yn, 1, 1) . dechex(substr($yn, 2)) . substr(hash('sha256', random_bytes(10)), 0, 5);
            header("x-trq2: {$trq2}");
        }
        // apache_note('trq2', $trq2);
        if (empty($_COOKIE['trq2'])) setcookie('trq2', $trq2, time() + 86400 * 30, '/');
        
        return $trq2;
    }

    /**
     * 新規セッション作成
     * @param array $session
     * @return array
     * @throws Exception
     */

    private
    static function create(array $session = []): array {

        if (empty($session['guid'])) {
            // guid ゲスト発行
            $session['guid'] = 'GUEST-' . self::$TRQ2;
        }

        $session['token'] = hash('sha256', $session['guid'] . microtime());
        $session['provider'] = $session['provider'] ?? '';
        $work = null;
        if (self::$ACCESS_TOKEN) {
            $work = json_encode(['access_token' => self::$ACCESS_TOKEN, 'refresh_token' => self::$REFRESH_TOKEN]);
        }

        $values = [
            $session['token'],
            $session['guid'],
            $session['provider'],
            $work,
        ];

        self::begin();
        $disposal = self::sql("SELECT id FROM `sessions` WHERE `guid` = ? OR `created_at` < ? LIMIT 0,1 FOR UPDATE ", [$session['guid'], self::date(-86400 * 35)]);
        if ($disposal) {
            // 再利用
            $id = $disposal[0]['id'];
            self::sql("UPDATE `sessions` SET `token`=?, `guid`=?, `provider`=?, `work`=?, `created_at`=now() WHERE `id`=$id", $values);
        } else {
            // 新規
            self::sql("INSERT INTO `sessions` ( `token`, `guid`, `provider`, `work`, `created_at` )  VALUES ( ?, ?, ?, ?, now() ) ", $values);
        }
        self::commit();

        setcookie('token', $session['token'], time() + self::COOKIE_EXPIRES, '/');
        self::setSessionData($session);
        self::$SESSION_CREATED = true;

        // 履歴
        LogClass::login($session['provider']);

        return $session;
    }

    static
    private function setSessionData(array $session): void {
        self::$TOKEN = $session['token'] ?? '';
        self::$GUID = $session['guid'] ?? '';
        self::$STATE = $session['state'] ?? '';
        self::$PROVIDER = $session['provider'] ?? '';
        self::$WORK = $session['work'] ?? '';
    }


    static public function getToken() {
        return self::$TOKEN;
    }

    static public function getProvider() {
        return self::$PROVIDER;
    }

    static public function getWorks() {
        if (empty(self::$WORK)) return [];
        try {
            $work = json_decode(self::$WORK, true);
        } catch (Exception $e) {
            $work = [];
        }
        return $work;
    }

    static public function setWorks($array) {
        if ($array) {
            $work = json_encode($array);
        } else {
            $work = NULL;
        }
        self::sql("UPDATE `sessions` SET `work`=? WHERE `token`=?", [$work, self::$TOKEN]);
    }


}