<?php

function ifEmpty($value, $text) {
    if (empty($value)) return $text;
    return $value;
}

function emptyToNull($value) {
    if (empty($value)) return null;
    return $value;
}

/**
 * 三項演算子代わり
 * @param $object
 * @param $property
 * @param $default
 * @return void
 */
function ifTertiary($object, $property, $default = '') {
    if (empty($object)) {
        return $default;
    } else if (is_array($object)) {
        return $object[$property] ?? $default;
    } else {
        return $object->$property ?? $default;
    }
}

function appHtmlspecialchars($text) {
    if (empty($text)) {
        return '';
    } else {
        return htmlspecialchars($text);
    }
}

/**
 * 表示用にテキストを変換
 * @param $text
 * @return string
 */
function toHtml($text) {
    return nl2br(htmlspecialchars($text));
}

/**
 * ファイル更新日時の付与
 * @param $file
 * @return string
 */
function updatelink($file) {
    $time = filemtime(SYS_ROOT . 'htdocs/' . $file);
    return $file . (strpos($file, '?') ? '&' : '?') . $time;
}

/**
 * UNIXTIEM Date書式化
 * @param null $time
 * @return false|string
 */
function timetostr($time = null) {
    if (empty($time)) {
        $time = time();
    }
    return date('Y-m-d H:i:s', $time);
}

/**
 * 数の符号を調べます。
 * $value が正の場合に 1、 $value が負の場合に -1、そして $value がゼロの場合に 0 を返します。
 * @param $value
 * @return int
 */

function sign($value) {
    if ($value < 0) {
        return -1;
    } elseif ($value > 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 改行タグ付与
 * @param $html
 * @return string|string[]
 */

function br($html) {

    if (empty($html)) {
        return "<br>";
    }

    $html = str_replace("\n", "<br>", $html);
    return $html;

}

/**
 * POSTリクエストラッパー
 * @param $url
 * @param $post
 * @return false|string
 */

function file_post_contents($url, $post) {
    // BASIC認証対策
    if (!empty(config('API_BASIC_USERPWD'))) {
        $url = str_replace('https://', 'https://' . config('API_BASIC_USERPWD') . '@', $url);
        $url = str_replace('http://', 'http://' . config('API_BASIC_USERPWD') . '@', $url);
    }

    // ストリームコンテキストのオプションを作成
    $options = array(
        // HTTPコンテキストオプションをセット
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($post, "", "&") // URLエンコードされたクエリ文字列を生成
        )
    );
    //送信
    return file_get_contents($url, false, stream_context_create($options));
}


/**
 * メッセージ遷移
 * @param $msg
 * @param $title
 * @param $btn
 * @param $link
 * @return void
 */
function locationMessage($mcode, $error = '', $message = '') {
    header("Location: " . BASE_URL . "message?mcode=$mcode");
    exit();
}


/**
 * ログインへ遷移
 */

function locationLogin() {
    header("Location: " . BASE_URL . "login/login");
    exit();
}

/**
 * 現在時間文字列、デバッグ用指定考慮
 */

function getNowDate() {
    //デバッグ用偽装時間指定
    $apo_date = isset($_GET["apo_date"]) ? $_GET["apo_date"] : "";
    if (empty($apo_date)) {
        $nowDate = date('Y-m-d H:i:s');
    } else {
        $nowDate = $apo_date;
    }
    return $nowDate;
}

/**
 * Cookie取得
 * @param $key
 * @param $default
 * @return ?string
 */
function getcookie($key, $default = null, $delete = false): ?string {
    if (isset($_COOKIE[$key])) {
        $value = $_COOKIE[$key];
        setcookie($key, '', time() - 86400);
        return $value;
    } else {
        return $default;
    }
}

/**
 * Cookie削除
 * @param $key
 * @return void
 */

function delcookie($key) {
    setcookie($key, '', time() - 86400);
}


function array_to_string($array) {
    if (is_string($array)) {
        return $array;
    } elseif (is_array($array)) {

        foreach ($array as $key => $item) {
            $array[$key] = $key . " " . array_to_string($item);
        }
        return implode("\n", $array);
    } else {
        return print_r($array, true);
    }
}

function ToUTC($datetime): string {
    if (is_int($datetime)) {
        $time = $datetime;
    } else {
        $time = strtotime($datetime);
    }

    return gmdate("Y/m/d H:i:s", $time);
}

function UTCToDate($utc_date): string {
    $finish_time = strtotime($utc_date);
    // UTCのはずなので9Hプラス
    return date("Y-m-d H:i:s", $finish_time + 60 * 60 * 9);
}

function base64url_encode($v) {
    $v = base64_encode($v);
    $v = preg_replace("/[=]+$/", "", $v);
    return str_replace(array('+', '/'), array('-', '_'), $v);
}

function base64url_decode($v) {
    $v = str_replace(array('-', '_'), array('+', '/'), $v);

    for ($i = 0; $i < strlen($v) % 4; $i++) {
        $v .= "=";
    }

    return base64_decode($v);
}

function milli_sleep($milli_sec){
    usleep($milli_sec * 1000);
}

/**
 * gzip圧縮でURLコンテンツを取得
 * @param $url
 * @return false|string
 */

function gzip_get_contents_0($url): false|string {
    return  file_get_contents($url);
}

function isGzipResponse($headers): bool {
    foreach ($headers as $header) {
        if (stristr($header, 'content-encoding') and stristr($header, 'gzip')) {
            return true;
        }
    }
    return false;
}
function gzip_get_contents($url): false|string {


    $context = stream_context_create(array('http' => ['method' => "GET", 'header' => implode("\r\n", array('Accept-Encoding: gzip,deflate'))]));
    $content = file_get_contents($url, false, $context);
    if (isGzipResponse($http_response_header)) {
        return gzdecode($content);
    } else {
        return $content;
    }
}

