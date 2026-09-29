<?php

use classes\AWSClass;
use classes\CryptClass;
use classes\LogClass;
use classes\MailClass;

class App {
    //--------------------------------------------
    // 月イチイベント

    const EVENT_NAME = '2026年8月イベント'; // ※識別用に使うので設定必須
    const EVENT_DEV_AT = '2026-08-24 00:00:00'; // local/develop で適用する開始日時
    const EVENT_START_AT = '2026-08-28 15:00:00';
    const EVENT_END_AT = '2026-08-30 23:59:59';
    const EVENT_POINT = 500;

    //--------------------------------------------
    // プライバシーポリシー改定日時
    const PRIVACY_POLICY_UPDATE = '2026-05-19 12:00:00';

    //--------------------------------------------
    // コンテンツ種別と対象

    const TARGET_CHAT = 'chat';                     //チャット
    const TARGET_CHAT_EX = 'chat_ex';               //有料チャット
    const TARGET_AUTHOR = 'author';                 // 著者
    const TARGET_EXPART = 'expert';                 // エキスパート
    const TARGET_AUTHOR_TITLE = 'author_title';     // 著者一覧
    const TARGET_YOSO_CONTENT = 'yoso_content';     // 予想コラム（記事ページ）
    const TARGET_YOSO_TITLE = 'yoso_title';         // 予想コラム（一覧ページ）
    const TARGET_COLUMN_CONTENT = 'column_content'; // ニュース／コラム（記事ページ）
    const TARGET_COLUMN_TITLE = 'column_title';     // ニュース／コラム（一覧ページ）
    const TARGET_COMMENT = 'comment';               // 会員コメント
    const TARGET_VOTES = 'votes';                  // みんなの投票
    const TARGET_RACE = 'race_info';                // レース
    const TARGET_RACE_UMA = 'race_uma';              // 特定レースの馬
    const TARGET_ENQUETE = 'enquete';                  // みんなの投票

    // 主にスコア専用
    const TARGET_USER_CREATE = 'user_create';        // 新規登録
    const TARGET_PROFILE = 'profile';                 // Myルームで追加アンケを全項目入力する
    const TARGET_GOOD = 'good_log';                   // いいね
    const TARGET_SUBSCRIPTION_FIRST = 'subsc_first';   // 初サブスク
    const TARGET_SUBSCRIPTION_CONTINUE = 'subsc_continue';// 継続サブスク
    const TARGET_MY_AUTHOR = 'my_author';              // My著者に登録する
    const TARGET_MY_UMA = 'my_uma';                    // 馬を「My馬」に登録する 終了
    const TARGET_MY_UMA_FIRST = 'my_uma_first';        // 初めて「My馬」に登録する
    const TARGET_COMPENSATION = 'compensation';        // 仕様変更による補填付与
    const TARGET_CAMPAIGN = 'campaign';                 // キャンペーンで付与

    const SCORE_TARGETS = [
        self::TARGET_USER_CREATE => '新規登録',
        self::TARGET_VOTES => 'みんなの投票',
        self::TARGET_CHAT => 'チャット',
        self::TARGET_PROFILE => '追加アンケを全項目入力',
        self::TARGET_GOOD => 'いいね',
        self::TARGET_SUBSCRIPTION_FIRST => '初サブスク',
        self::TARGET_SUBSCRIPTION_CONTINUE => '継続サブスク',
        self::TARGET_MY_AUTHOR => 'My著者に登録',
        self::TARGET_MY_UMA => 'My馬に登録 終了',
        self::TARGET_MY_UMA_FIRST => '初めてMy馬に登録',
        self::TARGET_COMPENSATION => '仕様変更による補填付与',
        self::TARGET_CAMPAIGN => 'キャンペーンで付与',
        self::TARGET_ENQUETE => 'アンケート回答',
    ];

    //--------------------------------------------
    // 環境定数
    const ENV_RELEASE = 'RELEASE';
    const ENV_API = 'API';
    const ENV_STAGING = 'STAGING';
    const ENV_DEVELOP = 'DEVELOP';
    const ENV_LOCAL = 'LOCAL';

    const DB_PRODUCTION = 'PRD';
    const DB_DEV = 'DEV';
    const DB_LOCAL = 'LCL';

    const SECURITY_IP_TEXT = 'app/data/sh/security_ip.txt';
    const WORK_IP_TEXT = 'app/data/sh/work_ip.txt';

    const  WEEKS = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];
    //--------------------------------------------

    const CONF_COMMUNITY_TOP = 'community_top';

    //--------------------------------------------

    static public $UID = null;

    static private $RESTRICT_CODE_NAME = 'restrict_code';
    static private $CRYPT_KEY = 'neouma';


    /**
     * NGワードチェック
     * @throws Exception
     */
    public static function checkNGWord($nick): bool {
        $list = Model::from('app_ng_word')
            ->where('enable', 1)
            ->rows();
        foreach ($list as $item) {
            if ($item && mb_strpos($nick, $item['word']) !== false) {
                return false;
            }
        }
        return true;
    }

    // 環境判定
    static public function isLocal(): bool {
        return (config('APP_ENV') == self::ENV_LOCAL);
    }

    static public function isDevelop(): bool {
        return self::isLocal() || (config('APP_ENV') == self::ENV_DEVELOP);
    }

    static public function isStaging(): bool {
        return (config('APP_ENV') == self::ENV_STAGING);
    }

    static public function isAPI(): bool {
        return (config('APP_ENV') == self::ENV_API);
    }

    /**
     * @return bool
     * @throws Exception
     */
    static public function isRelease(): bool {
        return (config('APP_ENV') == self::ENV_RELEASE || self::isAPI());
    }

    /**
     * 連続アクセス制限
     */
    static
    public function block() {
        $ip = LogClass::getUIP();
        $dir = TMP_DIR . 'ip_block';
        if (!file_exists($dir)) mkdir($dir, 0777, true);
        $filename = "{$dir}/{$ip}";
        $time = time();
        // 存在しないので作成してとりあえずヨシ
        if (!file_exists($filename)) return touch($filename, $time);

        $last = filemtime($filename);
        // 30秒以上前なら削除してヨシ
        if ($last < $time - 30) return unlink($filename);

        // size = 連続アクセス数
        $size = filesize($filename);
        if ($size > 5) {
            // リクエスト過多
            header('HTTP/1.1 429 Too Many Requests');
            echo "429 Too Many Requests";
            exit;
        } else if ($last + 2 > $time) {
            // 短時間連続アクセスされているとりあえずアクセス数として追記する
            $fp = fopen($filename, 'a');
            fwrite($fp, "1");
            fclose($fp);
        }
    }

    static private function sweepBlockFile() {
        $dir = TMP_DIR . 'ip_block';
        foreach (glob($dir) as $path) {
            unlink($path);
        }
    }

    /**
     * 本番以外でのアクセス制限
     * @return void
     * @throws Exception
     */

    static public function restrictServerAccess(): void {
        // 本番以外とAPP_MAINTENANCE=TRUEの場合チェックする
        if (!self::isRelease() || self::isMaintenance()) {

            // ローカルではチェックしない
            if (self::isLocal()) return;
            // アクセスコードある？
            if (self::checkAccessCodeTime()) return;

            $text = '';
            // 生成許可リスト取り込み
            $ip_text_file = SYS_ROOT . App::SECURITY_IP_TEXT;
            if (file_exists($ip_text_file)) $text = file_get_contents($ip_text_file);
            // 手動許可リスト取り込み
            $work_text_file = SYS_ROOT . App::WORK_IP_TEXT;
            if (file_exists($work_text_file)) $text .= file_get_contents($work_text_file);

            $ip = LogClass::getUIP(); // チェック用にアクセスしてきたIP取得
            if (!str_contains($text, "$ip/32")) {
                // 未許可ユーザーのようだ
                if (config('AWS_KEY')) { // AWS の場合 念の為セキュリティグループから自動抽出してリストに更新かける
                    $list = AWSClass::getSecurityIpRanges('develop');
                    file_put_contents($ip_text_file, print_r($list, true));
                }

                if (self::isMaintenance()) {
                    // 本番ならメンテナンス画面
                    self::maintenance();
                } else if (config('GUEST_ACCESS_CHECK', false)) {

                    header("Location: /guest.php");
                    exit();
                } else {
                    // アクセス禁止
                    http_response_code(403);
                    echo "Access denied.";
                    exit;
                }
            }
        }
    }

    static
    public function setAccessCode(): void {
        // 3日ほど有効
        $code = CryptClass::encrypt(time() + 86400 * 3, self::$CRYPT_KEY);
        setcookie(self::$RESTRICT_CODE_NAME, $code, time() + 86400 * 3, '/');
    }

    static
    private function checkAccessCodeTime(): bool {
        $code = $_COOKIE[self::$RESTRICT_CODE_NAME] ?? null;
        if (empty($code)) return false;
        $time = CryptClass::decrypt($code, self::$CRYPT_KEY);
        if ($time < time()) return false;

        return true;
    }


    static public function isDebug() {
        return (config('APP_DEBUG'));
    }

    static public function isMaintenance() {
        try {
            return (config('APP_MAINTENANCE'));
        } catch (Exception $e) {
            return false;
        }
    }

    static public function maintenance() {
        $html = file_get_contents(APP_ROOT . 'htdocs/maintenance.html');
        echo $html;
        exit;
    }

    /**
     * 内部アクセス制限
     * @return void
     * @throws Exception
     */
    static public function checkInternal() {
        if (self::isStaging() || self::isRelease()) {
            $ip = LogClass::getUIP();
            if ($ip != '127.0.0.1') {
                echo "OK";
                exit;
            }
        }
    }

    /**
     * マイグレーションチェック
     * @return void
     * @throws Exception
     */

    static public function checkMigration(): void {
        if (!self::isRelease()) {
            // キャッシュディレクトリ
            if (!file_exists(TMP_DIR)) mkdir(TMP_DIR, 0777, true);

            // マイグレーションチェック
            $Migrate = new Migrate();
            $migrations = $Migrate->checkMigration();
            if (!$migrations) {
                $Migrate->migrate(true);
            }


        }
    }


    /**
     * メール送信クラスを返す
     * @return MailClass|MailRucsisClass
     */
    static public function mail() {
        if (self::isLocal()) {
            return new MailRucsisClass();
            //return new MailClass();
        } else {
            return new MailRucsisClass();
        }

    }


    static public function setCookieUID($uid): void {
        if (empty($uid)) {
            $uid = $_COOKIE['uid'] ?? '';
        }
        if (empty($uid)) {
            $uid = $_COOKIE['trq2'] ?? '';
        }
        if (empty($uid)) return;

        setcookie('uid', $uid, time() + 86400 * 30, '/');
    }

    /**
     * ログ記録用DB名
     * @param null $table
     * @return string
     * @throws Exception
     */
    static public function logDB($table = null): string {
        $db = config('DB_LOG_NAME');
        if (empty($table)) return $db;
        return "{$db}.{$table}";
    }

    /**
     * ファイルキャッシュシステム
     * @param $cache_name
     * @param $life_sec
     * @param $callback
     * @return array
     */
    static public function cache($cache_name, $life_sec, $callback): array {
        // 保存先のディレクトリ
        $cache_dir = SYS_ROOT . 'cache/files';
        if (!file_exists($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        $cache_file = "{$cache_dir}/{$cache_name}";
        if (file_exists($cache_file)) {
            if (filemtime($cache_file) + $life_sec > time()) {
                $cache = file_get_contents($cache_file);
                return json_decode($cache, true);
            }
        }


        $cache = $callback();
        file_put_contents($cache_file, json_encode($cache));
        return $cache;
    }

    static public function date($format, $time): string {
        if (str_contains($format, 'ww')) {
            $w = date('w', $time);
            $format = str_replace('ww', self::WEEKS[$w], $format);
        }

        return date($format, $time);


    }


    // ---------------------------------------------------------
    // ログインサービスチェック
    /**
     * @param $Member
     * @return array 適用サービス
     * @throws Exception
     */
    static public function checkLoginService($Member): array {
        $result = [];

        if (empty($Member->uid)) return $result;

        // ・ログインボーナスイベント
        // 対象者：会員全員

        $now_at = date('Y-m-d H:i:s');
        $start_at = self::EVENT_START_AT;
        $end_at = self::EVENT_END_AT;
        // 開発時適用
        if (App::isDevelop()) $start_at = self::EVENT_DEV_AT;

        if ($start_at <= $now_at && $now_at <= $end_at) {
            $event_name = self::EVENT_NAME;
            $event_point = self::EVENT_POINT;
            $origin_id = Point::giftOrigin(0, "{$event_point}-{$event_name}", $event_name);
            $Exists = Point::getLog($Member->uid, $origin_id, '2026-01-01');
            if (empty($Exists)) {
                $result['name'] = $event_name;
                $result['point'] = $event_point;
                Point::begin();
                Point::give($Member->uid, $origin_id, $event_point, 30);
                Point::commit();

                $Member->gift_point += $event_point; // 表示用に加算しておく。DB上の実体は加算済み
            }

        }

        return $result;
    }


}


