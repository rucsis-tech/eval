<?php

use classes\LogClass;

global $error_loop_check;
$error_loop_check = 0;

error_reporting(E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR | E_WARNING);

set_error_handler(
/**
 * @throws ErrorException
 */
    function ($error_no, $error_msg, $error_file, $error_line, $error_vars = null) {
        if (error_reporting() === 0) {
            return;
        }
        throw new ErrorException($error_msg, 0, $error_no, $error_file, $error_line);
    });

set_exception_handler(function ($throwable) {
    send_error_log($throwable);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error === null) {
        return;
    }
    // fatal error の場合はすでに何らかの出力がされているはずなので、何もしない
    send_error_log(new ErrorException($error['message'], 0, 0, $error['file'], $error['line']));

});


ini_set('display_errors', 'Off');

/**
 * @param Throwable $throwable
 * @return void
 * @throws Exception
 */
function send_error_log(Throwable $throwable): void {

    global $error_loop_check;
    if ($error_loop_check) {
        // 多重エラー
        output_error_report($throwable);
    }
    $error_loop_check++;


    try {
        $filename = $throwable->getFile();
        $src = file_get_contents($filename);
        $line = $throwable->getLine();
        $messages = [
            'msg' => $throwable->getMessage(),
            'file' => "$filename :line $line",
            'line' => $line,
            'src' => $src,
            'trace' => $throwable->getTraceAsString(),
        ];

        // DB:
        $success = LogClass::except($throwable);

        // FILE:
        if (!$success) {
            // DB書き込み失敗でファイル書き出し
            $log = date("Y-m-d H:i:s") . "\n" . implode("\n", $messages) . "\n\n";
            try {
                file_put_contents(LOGFILE, $log, FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
                $messages[] = $e->getMessage();
            }
        }

        // 開発環境ならエラーログを標準出力出す
        if (Model::isCli()) {
            echo $throwable;
        } else {
            output_error_report($messages);
        }

    } catch (Exception $e) {
        output_error_report($e->getMessage());
    }
}

function output_error_report($messages): void {

    // --------------------------------------------------------------------------------
    // 本番非デバッグ時は固定の error.html を出力
    if (!config('APP_DEBUG', true)) {
        header("HTTP/1.1 500 Internal Server Error");
        echo file_get_contents(APP_ROOT . 'htdocs/error.html');
        return;
    }

    // --------------------------------------------------------------------------------
    // デバッグ時は各種情報を出力

    if (is_array($messages)) {
        $text = '';
        $target_line = 0;
        foreach ($messages as $key => $message) {
            $style = "margin-bottom:10px;";
            if ($key == 'file') {
                $style .= "color:#00F;margin:0 0;";
            }
            if ($key == 'line') {
                $target_line = $message;
                continue;
            }
            if ($key == 'src') {
                $style .= "color:#666;background-color:#EEE;padding:0.2rem;font-size:1.0em;";
                $rows = explode("\n", htmlspecialchars($message));
                $message = "";
                foreach ($rows as $line => $row) {
                    if (abs($line - $target_line) < 5) {
                        $message .= ($line + 1) . ": $row<br>\n";
                    }
                }
                $message = "\n<pre>\n$message</pre>";
            }
            if ($key == 'trace') {
                $style .= "color:#66F;";
                $message = "<h3 style=\"margin:0;\">Trace:</h3>\n" . str_replace("\n", "<br>\n", $message);
            }
            $text .= "<div style='$style'>$message</div>\n";
        }
        $messages = $text;
    }
    if (!is_string($messages)) {
        $messages = print_r($messages, true);
    }

    // 環境情報
    $SITE = config('APP_NAME', '-');
    $APP_ENV = config('APP_ENV', '-');
    $datetime = date('Y-m-d H:i:s');
    $uniq = LogClass::$UNIQ;
    $ip = LogClass::getUIP();
    $HTTP = (isset($_SERVER['http_x_forwarded_proto']) && $_SERVER['http_x_forwarded_proto'] === 'https') ? 'https' : 'http';
    $HTTP_HOST = $_SERVER['HTTP_HOST'] ?? '-';
    $REQUEST_URI = $_SERVER['REQUEST_URI'] ?? '-';
    $QUERY_STRING = $_SERVER['QUERY_STRING'] ?? '-';
    $SERVER_ADDR = $_SERVER['SERVER_ADDR'] ?? '-';

    // 出力
    http_response_code(500);
    echo "<html lang=\"ja\"><body style=\"box-sizing: border-box;width:100%;margin:0;padding:0 0.5rem;\">\n";
    echo "<div style=\"margin-bottom:0;font-family:'Arial Black';\">$SITE $datetime $APP_ENV($SERVER_ADDR) $uniq($ip) <br>$HTTP://$HTTP_HOST$REQUEST_URI </div>\n";
    echo "<h3 style=\"color:red;margin: 0;\">Error:</h3>\n";
    echo $messages;
    echo "</body></html>";
    exit;
}
