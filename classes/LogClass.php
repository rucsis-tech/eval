<?php

namespace classes;

use Exception;

class LogClass {

    static private float $startTime = 0;
    static public array $workTimes = [];
    static public string $UNIQ = '';

    static string $ERROR_LOGIN_MSG = 'Login Failure';
    static string $ERROR_LOGIN = 'LOGIN';
    static string $ERROR_SQL = 'SQL';
    static string $ERROR_POST = 'POST';

    public function __construct() {
        if (self::$startTime == 0) {

        }
    }
    // -------------------------------------------------------------------------------
    // 時間計測ログ用

    public static function start() {
        self::$startTime = microtime(true);
        self::$workTimes = [];
    }

    /**
     * 経過時間
     * @return float
     */
    public static function getPassTime(): float {
        return microtime(true) - self::$startTime;
    }

    public static function watch($name = ''): void {
        $sec = self::getPassTime(true);
        $n = count(self::$workTimes) - 1;
        if ($n >= 0) {
            self::$workTimes[$n]['work'] = floatval(sprintf("%f8", $sec - self::$workTimes[$n]['sec']));
        }
        self::$workTimes[] = [
            'name' => $name,
            'sec' => $sec,
            'work' => 0,
        ];
    }

    // -------------------------------------------------------------------------------

    // 呼び出し実行ファイル名取得
    private static function getName($kind = null) {
        if (empty($kind)) {
            if (DBClass::isCli()) {
                $kind = basename($_SERVER['argv'][0]);
            } else {
                $kind = $_SERVER['REQUEST_URI'];
            }
        }
        return $kind;
    }

    private static function logDB($table) {
        $db = config('DB_LOG_NAME');
        if (empty($table)) return $db;
        return "{$db}.{$table}";
    }

    // -------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------

    // 通常ユーザーログ
    public static function log($message, $kind = ''): void {
        $kind = self::getName($kind);
        self::insertLog('log', $kind, $message);
    }

    // バッチファイルログ
    public static function bat($message, $kind = ''): void {
        $sec = time() - self::$startTime;
        self::$startTime = time();
        if (is_string($message)) {
            $message = str_replace('(sec)', "({$sec}sec)", $message);
        }

        $kind = self::getName($kind);
        self::insertLog('bat', $kind, $message);
    }

    // 管理ログ
    public static function dev($message, $kind = ''): void {
        $kind = self::getName($kind);
        self::insertLog('dev', $kind, $message);
    }

    // APIログ
    public static function api($message, $kind = '', $id = null): void {
        $kind = $_SERVER['REQUEST_METHOD'] . ' ' . self::getName($kind);
        if (empty($id)) $id = self::getUIP();
        self::$UNIQ = $id;
        self::insertLog('api', $kind, $message);
    }


    // 任意ログ
    public static function write($mode, $kind, $message): void {
        self::insertLog($mode, $kind, $message);
    }

    // データ上の注意喚起
    public static function notice($message, $kind = ''): void {
        $kind = self::getName($kind);
        self::insertLog('notice', $kind, $message);
    }

    // ユーザーアクションログ
    public static function action($message, $kind = ''): void {
        $kind = self::getName($kind);
        self::insertLog('user', $kind, $message);
    }


    public static function info($message, $kind = '') {
        $kind = self::getName($kind);
        $trace = debug_backtrace();
        self::insertLog('info', $kind, [$message, $trace]);
    }

    public static function warning($message, $kind = '') {
        $kind = self::getName($kind);
        $trace = debug_backtrace();
        self::insertLog('warning', $kind, [$message, $trace]);
    }

    public static function debug($message, $kind = '') {
        $kind = self::getName($kind);
        $trace = debug_backtrace();
        self::insertLog('debug', $kind, [$message, $trace]);
    }

    /**
     * セッションログインログ
     * @param $provider
     * @throws Exception
     */
    public static function login($provider) {

        $ua_id = self::getUAID();
        $ip = self::getUIP();
        $message = ['provider' => $provider, 'ip' => $ip];

        self::insertLog('login', $ua_id, $message);
    }

    // -------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------

    /**
     * ユーザーエージエントIDを取得
     * @return string
     * @throws Exception
     */
    public static function getUAID(): int {
        $table = self::logDB('access_agent');
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
        DBClass::sql("INSERT INTO $table (`user_agent`,`count`) VALUES (?,1) ON DUPLICATE KEY UPDATE `count`=`count`+1 ", [$ua]);
        $aa = DBClass::sql("SELECT id FROM $table WHERE user_agent= ?", [$ua]);
        return intval($aa[0]['id'] ?? 0);
    }

    public static function getUA(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? '-';
    }

    /**
     * ユーザーのIPアドレス取得
     * @return string
     */

    public static function getUIP(): string {
        return $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    private static function getUniq(): string {
        $uniq = self::$UNIQ ?: (empty(SessionClass::$GUID) ? self::getUIP() : SessionClass::$GUID);
        if (DBClass::isCli()) {
            // CLI実行の場合。環境名とIPアドレス
            $uniq = config('APP_ENV', "*");
            try {
                $uniq .= ' ' . file_get_contents(config('INTERNAL_URL', BASE_URL) . 'checkip.php');
            } catch (Exception $e) {
            }
        }
        return $uniq;
    }



    // -------------------------------------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------------------

    protected static function insertLog($mode, $kind, $message): bool|string {

        $uniq = self::getUniq();
        if (is_array($message)) $message = json_encode($message, JSON_UNESCAPED_UNICODE);

        // CLIでは echo する
        if (DBClass::isCli()) echo "\n$uniq $mode $kind\n$message\n";

        try {
            return DBClass::insert([
                'uniq' => $uniq,
                'mode' => $mode,
                'kind' => $kind,
                'message' => $message,
            ], self::logDB('access_log'));
        } catch (Exception $e) {

            return false;
        }
    }

    // -------------------------------------------------------------------------------------------------------------
    // エラーログ
    // -------------------------------------------------------------------------------------------------------------

    /**
     * @param $msg
     * @param $kind
     * @param $message
     * @return string
     */

    public static function getErrorMode($msg, $kind, $message = ''): string {
        if (str_contains($msg, 'SQLSTATE')) return self::$ERROR_SQL;
        if (str_contains($msg, 'POST Content-Length')) return self::$ERROR_POST;
        if (str_contains($msg, self::$ERROR_LOGIN_MSG)) return self::$ERROR_LOGIN;
        return 'error';
    }


    /**
     * 例外発生時エラー
     * @param \Throwable $throwable
     * @param array $values
     * @return bool|string
     */
    public
    static function except(\Throwable $throwable, array $values = []): bool|string {
        $msg = $throwable->getMessage() . ' in ' . $throwable->getFile() . ' :line(' . $throwable->getLine() . ')';
        if (!is_array($values)) $values = [$values];
        $values['trace'] = $throwable->getTraceAsString();
        //prex($values['trace']);
        $values['trace'] = str_replace("\n#", " \n___", $values['trace']);
        $values['trace'] = str_replace(' ' . realpath(SYS_ROOT), '___', $values['trace']);

        $error_code = $throwable->getCode();
        if ($error_code === 401) $values['error_mode'] = '401';

        /* 詳細なトレース情報
        $Traces = $throwable->getTrace();
        foreach ($Traces as $item) {
            $args = array_to_string($item['args']);
            if (strlen($args) > 1000) {
                // だが引数情報が長過ぎる場合
                $replace = [];
                foreach ($item['args'] as $arg) {
                    if (is_object($arg)) {
                        // オブジェクトの引数はクラス名のみに改変
                        $replace[] = '[' . get_class($arg) . ']';
                    } else {
                        $replace[] = $arg;
                    }
                }
                $item['args'] = $replace;
            }
            $values[] = array_to_string($item);
        }
        */

        return self::error($msg, $values);
    }

    /**
     *
     * 任意エラー出力
     * @param $message
     * @param $values
     * @param $kind
     * @return bool|string
     */
    public static function error($message, $values = [], $exclusion_sec = 0): bool|string {

        $kind = self::getName(null);
        $values = self::makeErrorValues($message, $values);

        return self::insertError($kind, $values, $exclusion_sec);
    }

    private static function makeErrorValues($message, $values): array {
        if (is_string($values)) {
            $values = ['values' => $values];
        } else if (!is_array($values)) {
            $values = ['values' => json_encode($values)];
        }
        $ip = self::getUIP();
        if ($ip) $values['ip'] = $ip;
        $referer = ($_SERVER['HTTP_REFERER'] ?? '');
        if ($referer) $values['referer'] = str_replace(BASE_URL, '', $referer);

        return array_merge(['msg' => $message], $values);
    }

    /**
     * DB挿入
     * @param $kind
     * @param $message
     * @param $check_sec
     * @return bool|string
     */
    protected static function insertError($kind, $message, $exclusion_sec = 0): bool|string {

        $uniq = self::getUniq();
        $error_mode = $message['error_mode'] ?? null;
        if (is_array($message)) {
            $message['SERVER_ADDR'] = $_SERVER['SERVER_ADDR'] ?? '';
            $message['ENV'] = config('APP_ENV', '');
            unset($message['error_mode']);
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }
        $message = str_replace('\\\\\\\\', '\\\\', $message);
        // CLIでは echo する
        if (DBClass::isCli()) echo "\n$uniq $kind\n$message\n";


        $table = self::logDB('error_log');
        $param = [
            'uniq' => $uniq,
            'mode' => $error_mode ?? static::getErrorMode($message, $kind, $message),
            'kind' => $kind,
            'message' => $message,
        ];

        if ($exclusion_sec) {
            // 同一ログの排除
            $Exists = DBClass::from($table)->where('created_at', '>', date('Y-m-d H:i:s', time() - $exclusion_sec))
                ->where($param)->first();
            if ($Exists) return false;
        }


        try {
            return DBClass::insert($param, $table);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

    }

}