<?php

/* Rucsisサーバでのメール送信用クラス */

use classes\MailClass;

class MailRucsisClass extends MailClass {

    protected string $proxyURL = '';//http://18.182.77.39:1080/api/mail/proxy';
    private string $mailURL = 'https://api.winsight.jp/keiba/mail/bridge_mailsend.php';
    private string $distributeURL = 'https://api.winsight.jp/keiba/mail/bridge_douhousend.php';


    public function __construct() {
        $this->setFrom(config('EMAIL_FROM_ADDRESS'), config('EMAIL_FROM_NAME'));
    }

    protected function proxyURL() {
        if (App::isLocal()) {
            return 'http://neouma.local/api/mail/proxy';
        }
        return $this->proxyURL;
    }

    /**************************************************************************
     * 送信
     ***************************************************************************/

    /**
     * @param $to_list
     * @param $subject
     * @param $message
     * @param array $options
     * @return array
     */

    public function sender($to_list, $subject, $message, $options = []) {
        try {
            $from = $options[self::OPTION_FROM] ?? $this->fromAddress;
            $name = $options[self::OPTION_NAME] ?? $this->fromName;
            $content_type = $options[self::OPTION_CONTENT_TYPE] ?? self::CONTENT_TYPE_PLAIN;
            $plain_text = $options[self::OPTION_PLAIN_TEXT] ?? strip_tags($message);
            $return_path = $options[self::OPTION_RETURN_PATH] ?? $this->retAddress;
            if (is_array($to_list)) $to_list = implode(',', $to_list);

            $mail_server_url = $this->mailURL;
            if (strlen($to_list) > 100) {
                // 多数配信向け
                $mail_server_url = $this->distributeURL;
            }

            $postData = [
                'to' => $to_list,
                'subject' => $subject,
                'body' => $message,
                'from' => $from,
                'fromname' => $name,
                'ret' => $from,
                'retname' => $name,
            ];
            if ($content_type == 'html') {
                $postData['body'] = $plain_text;
                $postData['html'] = $message;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_URL, $mail_server_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            $json = json_decode($response);

        } catch (Exception $e) {
            $values = ['response' => $response ?? null, 'info' => $info ?? null];
            AppLog::except($e, $values);
            $values['result'] = false;
            $values['error'] = $e->getMessage();
            return $values;
        }

        return ['result' => true, 'response' => $json, 'info' => $info];
    }


    /**
     * 配信用宛先配列の作成
     * @param $list
     * @return array
     */

    public static function makeToList($list): array {
        $toList = [];
        foreach ($list as $item) {
            // 配信用個別情報
            $id = $item['uid'];
            $nick = empty($item['nickname']) ? $id : $item['nickname'];
            $nick = rawurlencode($nick);
            $mail = rawurlencode($item['email']);

            $to = $item['email'] . "?id=$id&nick=$nick&mail=$mail";
            $toList[$id] = $to;
        }
        return $toList;
    }


}
