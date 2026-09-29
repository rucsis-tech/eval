<?php

use JetBrains\PhpStorm\NoReturn;

class Limiter {

    private static $RELEASE_CACHE_DIR = '/opt/neouma/cache';
    private static $TMP_BLOCK_DIR = '/opt/neouma/cache/block';
    private static $TMP_MARK_DIR = '/opt/neouma/cache/mark';
    public static $WEB_CPU_USAGE = 0;
    public static $API_CPU_USAGE = 0;
    private static $API_USAGE_FILE = '/tmp/api.cpu';
    private static $WEB_USAGE_FILE = '/tmp/ec2.cpu';

    /**
     * テンポラリなどの初期化
     * @return void
     */

    static public function init(): void {
        // テンポラリディレクトリ確認
        if (!file_exists(self::$TMP_BLOCK_DIR)) mkdir(self::$TMP_BLOCK_DIR, 0777);
        if (!file_exists(self::$TMP_MARK_DIR)) mkdir(self::$TMP_MARK_DIR, 0777);

        // ブロックファイル除去

        // GoogleIPリスト取得・作成
        $gip24 = self::getGoogleIP24();
        file_put_contents(self::$TMP_BLOCK_DIR . '/google_ip24.txt', $gip24);
    }

    /**
     * 過剰アクセスに対して負荷制限措置を実施する
     * @return bool
     */

    static public function loadStartBlock(): bool {

        // IPアドレス
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        if ($ip == '127.0.0.1' || $ip == '::1') return true; // ローカル内アクセスなので可

        // URL精査
        $REQUEST_URI = $_SERVER['REQUEST_URI'] ?? '';

        // meta-web indexer のアクセスがあまりに多すぎるので trq2は not found とする trq2失敗の一時処置
        if (str_contains($REQUEST_URI, '?trq2=') && empty($_COOKIE['uid'])) self::notFound();

        $QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '';
        if ($QUERY_STRING) $REQUEST_URI = substr($REQUEST_URI, 0, strlen($REQUEST_URI) - strlen($QUERY_STRING) - 1);
        if (empty($REQUEST_URI) || $REQUEST_URI == '/') return true; // ROOT
        if (str_contains($REQUEST_URI, '/auth/v3')) return true;     // auth
        if (str_contains($REQUEST_URI, '/api/')) return true;        // api


        // CPU使用率の確認
        if (file_exists(self::$API_USAGE_FILE)) self::$API_CPU_USAGE = intval(trim(file_get_contents(self::$API_USAGE_FILE)));
        if (file_exists(self::$WEB_USAGE_FILE)) self::$WEB_USAGE_FILE = intval(trim(file_get_contents(self::$WEB_USAGE_FILE)));
        if (self::$API_CPU_USAGE < 50 && self::$WEB_CPU_USAGE < 50) return true;

        if (empty($_COOKIE['uid']) && empty($_SERVER['HTTP_REFERER'])) {
            // ------------------------------------------------------------
            // クローラーやボットでは？

            // googleアクセスは許可
            if (self::isGoogleIP($ip)) return true;

            // IP/24ブロックでブロック判定実施
            $ip24 = substr($ip, 0, strrpos($ip, '.') + 1) . 'X';
            self::checkBlock("x_block.{$ip24}");

        } else {
            // ------------------------------------------------------------
            // UIDやリファラがあるブラウザと思われるアクセスの場合

            // IP判定で必要ならブロック実施
            self::checkBlock("block.{$ip}");

        }

        return true;

    }

    /**
     * リクエスト過多で終了
     * @return void
     */
    #[NoReturn] static public function tooManyRequests(): void {
        header('HTTP/1.1 429 Too Many Requests');
        echo "429 Too Many Requests";
        exit;
    }

    #[NoReturn] static public function notFound(): void {
        header('HTTP/1.1 404 Not Found');
        echo "404 Not Found";
        exit;
    }

    /**
     * ブロック用ファイルにてブロック判例
     * @param $block_file
     * @param int $limit_count
     * @return void
     */
    static private function checkBlock($block_file, int $limit_count = 1): void {

        // テンポラリディレクトリ確認
        if (!file_exists(self::$TMP_BLOCK_DIR)) mkdir(self::$TMP_BLOCK_DIR, 0777);

        $block_file = self::$TMP_BLOCK_DIR . "/{$block_file}";

        try {
            // ブロック用ファイルが存在しないので作成してとりあえずヨシ　(1回目)
            if (!file_exists($block_file)) {
                touch($block_file);
                return;
            }

            // 前回が30秒以上前ならブロック用ファイルを削除してヨシ(2回目)
            $time = time();
            $last = filemtime($block_file);
            if ($last < $time - 30) {
                try {
                    unlink($block_file);
                    return;
                } catch (Throwable $exception) {
                    return;
                }
            }

            // size = 連続アクセス数
            $size = filesize($block_file);
            if ($size >= $limit_count) {
                // 短時間リクエスト過多 終了
                self::tooManyRequests();

            } else if ($last + 5 > $time) {
                // 5sec以内短時間連続アクセスされている -> とりあえずアクセス数を１文字として追記する (2回目)1コ (3回目)2コ　となる
                $fp = fopen($block_file, 'a');
                fwrite($fp, "1");
                fclose($fp);
            }

        } catch (Throwable $e) {
            // タイミングによっては unlink できないことがある

        }

    }

    /**
     * GoogleのボットアクセスIPか確認
     * @param $ip
     * @return bool
     */

    static function isGoogleIP($ip): bool {
        $google_ip24 = self::$TMP_BLOCK_DIR . '/google_ip24.txt';
        if (!file_exists($google_ip24)) self::init();

        $gip24 = file_get_contents($google_ip24);
        $ipv4 = explode('.', $ip);
        $ip24 = $ipv4[0] . '.' . $ipv4[1] . '.' . $ipv4[2];
        if (str_contains($gip24, $ip24)) return true;
        return false;
    }

    /**
     * GoogleクローラーのIPアドレスを取得・キャッシュ作成
     * @return string
     */
    static function getGoogleIP24(): string {
        $result = [];
        // https://developers.google.com/static/crawling/ipranges/common-crawlers.json
        $json = file_get_contents('https://developers.google.com/static/crawling/ipranges/common-crawlers.json');
        $ips = json_decode($json, true);
        $prefix_list = $ips['prefixes'] ?? [];
        foreach ($prefix_list as $prefix) {
            if (isset($prefix['ipv4Prefix'])) {
                $ipv4Prefix = $prefix['ipv4Prefix'];
                $ipv4 = explode('.', $ipv4Prefix);
                $ip24 = '[' . $ipv4[0] . '.' . $ipv4[1] . '.' . $ipv4[2] . ']';
                $result[$ip24] = 1;
            }
        }
        return implode("\n", array_keys($result));
    }


    /**
     * $_SERVER情報を整理する
     * @return array
     */
    static public function getServerPrint(): array {
        $server = $_SERVER;
        // ほぼ共通の項目　精査不要なため削除
        $clears = [
            'USER', 'HOME', 'SCRIPT_NAME', 'QUERY_STRING', 'REQUEST_METHOD', 'SERVER_PROTOCOL', 'GATEWAY_INTERFACE', 'REMOTE_PORT',
            'SCRIPT_FILENAME', 'SERVER_ADMIN', 'CONTEXT_DOCUMENT_ROOT', 'CONTEXT_PREFIX', 'REQUEST_SCHEME', 'DOCUMENT_ROOT',
            'REQUEST_URI', 'REMOTE_ADDR', 'SERVER_PORT', 'SERVER_ADDR', 'SERVER_NAME', 'SERVER_SOFTWARE', 'SERVER_SIGNATURE', 'PATH',
            'HTTP_X_AMZN_TRACE_ID', 'HTTP_HOST', 'HTTP_X_FORWARDED_PORT', 'HTTP_X_FORWARDED_PROTO', 'UNIQUE_ID', 'FCGI_ROLE', 'PHP_SELF', 'REQUEST_TIME_FLOAT', 'REQUEST_TIME',

            'MIBDIRS', 'MYSQL_HOME', 'PHP_PEAR_SYSCONF_DIR', 'OPENSSL_CONF', 'PHPRC', 'TMP', 'SystemRoot'
        ];
        $clears = array_merge($clears, array_keys($_ENV));
        foreach ($clears as $key) {
            unset($server[$key]);
            unset($server["REDIRECT_{$key}"]);
        }
        $server['COOKIES'] = count($_COOKIE);
        return $server;
    }


    static public function checkServerPrint() {

        $print = Limiter::getServerPrint();
        $evil = 0;
        if (empty($print['HTTP_ACCEPT_LANGUAGE'])) {
            $evil += 50;
        } else {
            if (!str_contains($print['HTTP_ACCEPT_LANGUAGE'], 'ja')) $evil += 50;
        }

        if (empty($print['COOKIES'])) {
            $evil += 50;
        } else {
            if ($print['COOKIES'] < 1) $evil += 50;
        }


        if ($evil >= 100) {
            LogClass::notice($print);
            self::BadRequest();
        } else {
            LogClass::log($print);
        }


    }

    /**
     * IPアドレスでマークファイルを作成する
     * @return void
     */

    static public function markIPFile() {

        // デプロイサーバ以外では動作しない
        if (!file_exists(self::$RELEASE_CACHE_DIR)) return;

        try {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');

            // アクセス停止指名ファイル確認
            $stop_file = self::$TMP_MARK_DIR . "/block.{$ip}";
            if (file_exists($stop_file)) {
                if (filemtime($stop_file) + 300 > time()) self::tooManyRequests();
            };

            // アクセス記録
            $m_dir = self::$TMP_MARK_DIR . '/' . date('YmdHi');
            if (!file_exists($m_dir)) mkdir($m_dir, 0777, true);
            $ip_file = "{$m_dir}/{$ip}";

            if (!file_exists($ip_file)) {
                touch($ip_file);
            } else {
                $fp = fopen($ip_file, 'a');
                fwrite($fp, "1");
                fclose($fp);
            }
        } catch (Throwable $exception) {
            return;
        }

    }

    /**
     * ディレクトリの整理とアクセス停止用ファイルの管理
     * @return void
     */

    static public function sweepIPFile() {
        // デプロイサーバ以外では動作しない
        if (!file_exists(self::$RELEASE_CACHE_DIR)) return;

        // 現在ディレクトリ
        $m_dir = self::$TMP_MARK_DIR . '/' . date('YmdHi');
        // 判定対象ディレクトリ取得・確認と削除
        foreach (glob(self::$TMP_MARK_DIR . "/*", GLOB_ONLYDIR) as $dir) {
            if ($m_dir != $dir) {
                // IPファイル精査
                foreach (glob("{$dir}/*") as $ipfile) {
                    if (filesize($ipfile) > 30) {
                        // 1分間に30リクエスト超  // 停止ファイル作成
                        $ip = basename($ipfile);
                        touch(self::$TMP_MARK_DIR . "/block.{$ip}");
                        echo date('YmdHi') . " stop: {$ip}\n";
                    }
                    // 削除
                    unlink($ipfile);
                }
                // ディレクトリ削除
                rmdir($dir);
            }
        }

        // 停止ファイル調査
        foreach (glob(self::$TMP_MARK_DIR . "/block*") as $stop_file) {
            if (filemtime($stop_file) + 300 < time()) {
                unlink($stop_file);
            }
        }

    }



}