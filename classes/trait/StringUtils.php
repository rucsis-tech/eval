<?php

namespace classes\trait;

use Exception;

trait StringUtils {

    /**
     * CLI（コマンドライン）モードかの判定
     * @param null $echo
     * @return bool
     */

    public
    static function isCli($echo = null): bool {
        $isCLI = (php_sapi_name() == 'cli');
        if ($echo) {
            $echo .= "\n";
            if ($isCLI) {
                echo $echo;
            } else {
                self::$echo .= $echo;
            }
        }
        return $isCLI;
    }


    /**
     * YYYY-mm-dd hh:ii:ss 形式の日時文字列を返す
     * @param $tm
     * @return string Y-m-d H:i:s
     */
    public
    static function date($tm = null): string {
        if (empty($tm)) {
            // 指定なしなら現在値
            $tm = time();
        } else if ($tm < 86400 * 365 * 10) {
            // 10年分以下の秒数値なら現在timeに加算
            $tm += time();
        }
        // Y-m-d H:i:s の形で返す
        return date('Y-m-d H:i:s', $tm);
    }

    /**
     * @param $tm
     * @return string
     */

    public
    static function day($tm = null): string {
        if (empty($tm)) {
            $tm = time();
        }
        return date('Y-m-d', $tm);
    }


    /**
     * @param $f
     * @param $c
     * @return string
     */

    public
    static function decimal($f, $c = 0): string {
        if (!$c) {
            return sprintf('%f', $f);
        } else {
            return sprintf('%f', round($f, $c));
        }
    }

    /**
     * hash作成
     * @param int $len
     * @return string
     */
    public
    static function makeHash(int $len = 64): string {
        return substr(hash('sha256', microtime()) . md5(microtime()), 0, $len);
    }

    /**
     * ユーザーコード生成 特定文字のみの組み合わせ
     * @param int $len
     * @param string $prefix
     * @return string
     */
    public
    static function makeCode(int $len = 8, string $prefix = ''): string {

        $CODES = '1235678ABDEFGHIJKLMNPRTWXYZ';

        $code = $prefix;
        $max = strlen($CODES) - 1;
        try {
            for ($j = 0; $j < $len; $j++) {
                $code .= $CODES[random_int(0, $max)];
            }
        } catch (Exception $e) {
            for ($j = 0; $j < $len; $j++) {
                $code .= $CODES[rand(0, $max)];
            }
        }
        return $code;
    }

    /**
     * @param $string
     * @return string
     */

    public static function pascalize($string): string {
        $string = strtolower($string);
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        return str_replace(' ', '', $string);
    }

    /**
     * @param $string
     * @return string
     */

    public static function camelize($string): string {
        $string = self::pascalize($string);
        $string[0] = strtolower($string[0]);
        return $string;
    }

    /**
     * @param $string
     * @return string
     */
    public static function snake($string): string {
        $string = preg_replace('/([A-Z])/', '_$1', $string);
        $string = strtolower($string);
        return ltrim($string, '_');
    }

}