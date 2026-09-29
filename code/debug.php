<?php

use JetBrains\PhpStorm\NoReturn;

/**
 * デバッグ表示用
 * @param $value
 * @param null $title
 * @throws Exception
 */

function pre($value, $title = null): void {
    $isCli = (php_sapi_name() == 'cli');
    static $num = 0;
    $num++;
    if (config('APP_ENV') == 'RELEASE') {
        return;
    }

    // 変数内容展開
    $height = 10;
    if (is_object($value)) {
        $name = get_class($value);
        $value = json_decode(json_encode($value), true);
        $texts = explode("\n", print_r($value, true));
        $texts[0] = "Class $name";
        $value = implode("\n", $texts);

    } else if (is_array($value)) {
        $value = print_r($value, true);
    } else if ($value === null) {
        $height = 2;
        $value = "(NULL)";
    } else {
        $height = 2;
        $type = gettype($value);
        if ($type == 'boolean') $value = $value ? 'TRUE' : 'FALSE';
        $value = "($type) $value";
    }


    // 出力ファイルの箇所抽出
    $file = "";
    $traces = debug_backtrace();
    foreach ($traces as $trace) {
        if (!str_contains($trace['file'], 'debug.php')) {
            $file = $trace['file'] . " :" . $trace['line'] . "\n";
            break;
        }
    }

    // 出力
    if ($isCli) {
        echo "{$file}{$title}{$value}\n";
    } else {
        echo '<pre id="debug-val-view' . $num . '" ondblclick="this.style.height=\'auto\';" '
            . ' style="border-top:solid 2px gray;white-space:pre-wrap;margin:1rem 0;padding:0.5rem;color:white;background-color:#444;height:' . $height . 'rem;overflow:auto;">';
        if ($file) echo "$file";
        if ($title) echo "<b>$title: </b>";
        echo "$value\n</pre>";
    }

}

/**
 * デバッグ表示用
 * @param $text
 * @param null $title
 * @throws Exception
 */

#[NoReturn] function prex($text, $title = null): void {
    $isCli = (php_sapi_name() == 'cli');
    if (!$isCli) echo '<html><body>';
    pre($text, $title);
    if (!$isCli) echo "</body></html>";
    exit;
}

/**
 * @return string
 */
function getPeakMem(): string {
    return floor(memory_get_peak_usage() / (1024 * 1024)) . " MB";
}

