<?php

// 基本設定 タイムゾーン
date_default_timezone_set("Asia/Tokyo");

// -----------------------------------------------------------------------
// オートローダ composer　autoload
require_once(__DIR__ . "/../vendor/autoload.php");

define('SYSTEM_START_MICRO_TIME', microtime(true));

/**
 * ENV 取得関数
 * @param $key
 * @param $default
 * @return mixed
 * @throws Exception
 */

function config($key, $default = null): string {

    if (isset($_ENV[$key])) {
        // ENV設定
        return $_ENV[$key];
    } elseif (defined($key)) {
        // define設定
        return constant($key);
    } elseif ($default !== null) {
        // デフォルト値
        return $default;
    }

    // 設定無し・おそらく名称指定ミス
    throw new Exception("NO ENV $key");

}


