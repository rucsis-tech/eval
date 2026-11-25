<?php

use classes\ChatworkClass;
use classes\LogClass;

class AppLog extends LogClass {


    static
    public function location($url) {
        header("Location: " . $url);
        exit();
    }


    /**
     * 2025-06-25
     * ･id付きURL=10万件メール対象の広告コードがws002になるようにしてもらう
     * ･アクセスのあったidをデータベースに保存してもらう
     * @param $id
     * @return void
     * @throws Exception
     */
    static
    public function about_pr($id) {
        $message = [
            'ip' => self::getUIP(),
            'uaid' => self::getUAID(),
        ];
        self::insertLog('about_pr', $id, $message);
    }

    /**
     * エラーモードの特定
     *
     * @param $msg
     * @param $kind
     * @param string $message
     * @return string
     */

    public static function getErrorMode($msg, $kind, $message = ''): string {
        if (str_contains($kind, 'checkdns')) return 'domain';
        if (str_contains($kind, 'file_get_contents') && str_contains($message, 'api2.winsight')) return 'api';
        if (str_contains($message, 'card_declined')) return 'card_dcl';
        if (str_contains($message, 'amount_limit_exceeded')) return 'card_limit';
        if (str_contains($message, 'unlink') && str_contains($message, 'block')) return 'block';

        return parent::getErrorMode($msg, $kind);
    }

    /**
     * １日分のエラーログのチェック
     * @param int $sec
     * @return array
     * @throws Exception
     */
    static
    public function checkErrorLog(int $time): array {

        // $sec 以内のエラーログ
        $table = App::logDB('error_log');
        $Errors = Model::table($table)
            ->where('created_at', '>', Model::date($time))
            ->orderBy('id', 'desc')
            ->all();

        $counts = [];
        foreach ($Errors as $error) {
            $mode = $error->mode;
            $counts[$mode] = ($counts[$mode] ?? 0) + 1;
        }

        return [
            'counts' => $counts,
            'Errors' => $Errors,
        ];
    }

    /**
     * Chatworkにてエラーリポート
     * @return void
     * @throws Exception
     */

    static function report($errors) {
        $ENV = config('APP_ENV');
        $title = "[WS-{$ENV}]エラー件数 " . json_encode($errors['counts']);
        $messages = [];
        foreach ($errors['Errors'] as $Error) {
            if ($Error->mode === 'error') {
                $data = [
                    substr($Error->created_at, 5),
                    $Error->mode,
                    $Error->uniq,
                    $Error->kind,
                    str_replace('\/', '/', substr($Error->message, 0, 200) . " ..."),
                ];
                $messages[] = implode(" ", $data);
            }
            if (count($messages) > 5) {
                $messages[] = "and more ..";
                break;
            }
        }

        if ($messages) {
            $text = implode("\n", $messages);
            $Chatwork = new ChatworkClass(config('CHATWORK_TOKEN'));
            $room_id = config('CHATWORK_ERROR_ROOM' , '164952859');
            $Chatwork->postRoomsMessages($room_id, "{$title}\n[code]{$text}[/code]");
            Model::isCli($text);
        }
    }

}