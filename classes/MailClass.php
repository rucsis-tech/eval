<?php

namespace classes;

use Exception;

class MailClass {

    protected string $proxyURL = 'http://neouma.local/api/mail/proxy';
    const CONTENT_TYPE_PLAIN = 'plain';
    const CONTENT_TYPE_HTML = 'html';

    // メール送信オプション指定用
    const OPTION_FROM = 'from';
    const OPTION_NAME = 'name';
    const OPTION_CONTENT_TYPE = 'content_type';
    const OPTION_PLAIN_TEXT = 'plain_text';
    const OPTION_RETURN_PATH = 'return_path';

    protected string $fromAddress = 'company@SITE-NAME.com';        // 送信元アドレス
    protected string $fromName = 'SITE-NAME';    // 送信元名称
    protected string $retAddress = 'company@SITE-NAME.com';        // 返信先アドレス
    protected string $retName = 'SITE-NAME';    // 返信先名称

    const MAIL_TEXT_DIR = APP_ROOT . 'data/mailtext/';
    const MAIL_SIGNATURE = 'Signature';

    public function setFrom($address, $name) {
        $this->fromAddress = $address;
        $this->fromName = $name;
    }

    public function setReturn($address, $name) {
        $this->retAddress = $address;
        $this->retName = $name;
    }


    /**
     * メールテキスト取得
     * @param $filename
     * @param $params
     * @return array
     */

    public function getMailContent($filename, $params): array {

        $to = $params['to'] ?? $params['email'];
        $texts = explode("\n", file_get_contents(self::MAIL_TEXT_DIR . "$filename.txt"));
        $subject = '';
        $body = '';
        $mode = '';
        foreach ($texts as $text) {
            if (mb_strpos($text, '件名：') !== false) {
                $subject = trim(mb_substr($text, 3));
                $mode = 'subject';
            } elseif (mb_strpos($text, '本文：') !== false && $mode != 'body') {
                $mode = 'body';
            } elseif ($mode == 'subject') {
                $subject .= trim($text);
            } else {
                $body .= trim($text) . "\n";
            }
        }
        $subject = trim($subject);

        // 共通シグネチャ
        if (str_contains($body, "%%signature%%")) {
            $signature = file_get_contents(self::MAIL_TEXT_DIR . self::MAIL_SIGNATURE . ".txt");
            $body = str_replace("%%signature%%", $signature, $body);
        }

        foreach ($params as $key => $value) {
            if (!is_array($value) && $value) {
                $subject = str_replace("{%%$key%%}", $value, $subject);
                $subject = str_replace("%%$key%%", $value, $subject);
                $body = str_replace("{%%$key%%}", $value, $body);
                $body = str_replace("%%$key%%", $value, $body);
            }
        }

        if (isset($params['from_name'])) $this->fromName = $params['from_name'];
        if (isset($params['from_address'])) $this->fromAddress = $params['from_address'];

        return array(
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
        );

    }

    // -------------------------------------------------------------------------------------


    protected function proxyURL() {
        return $this->proxyURL;
    }
    // -------------------------------------------------------------------------------------

    /**
     * API送信プロキシへ送る
     * @param $filename
     * @param $params
     * @return array
     */
    public function sendProxy($filename, $params) {

        $params['filename'] = $filename;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_URL, $this->proxyURL());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            $http_code = $info['http_code'];
            $content_type = $info['content_type'];

            $json_decoded = json_decode($response, true);
            if ($http_code < 200 || 299 < $http_code) {
                $json_decoded['error'] = $http_code;
                $json_decoded['response'] = $response;
                $json_decoded['info'] = $info;
            }

        } catch (Exception $e) {
            $json_decoded = [
                'error' => 1,
                'message' => $e->getMessage(),
            ];
        }


        return $json_decoded;

    }

    /**
     * 受け取ったものを送信
     * @param $post
     * @return array
     */
    public function goProxy($post) {
        $filename = $post['filename'];
        $content = $this->getMailContent($filename, $post);

        $toAddress = $content['to'] ?? $content['email'];
        $mail_title = $content['subject'];
        $mail_body = $content['body'];

        return $this->sender($toAddress, $mail_title, $mail_body);
    }

    // -------------------------------------------------------------------------------------

    /**
     * メール送信
     * @param $filename
     * @param array $content
     * @return array
     */
    public function sendMail($filename, array $content): array {

        $content = $this->getMailContent($filename, $content);
        $toAddress = $content['to'] ?? $content['email'];
        $mail_title = $content['subject'];
        $mail_body = $content['body'];

        return $this->sender($toAddress, $mail_title, $mail_body);
    }

    // -------------------------------------------------------------------------------------

    /**
     * メール送信・詳細
     * @param $to_list
     * @param $subject
     * @param $message
     * @param $options
     * @return array
     */

    public function sender($to_list, $subject, $message, $options = []) {

        if (is_array($to_list)) $to_list = implode(',', $to_list);

        mb_language("japanese");
        mb_internal_encoding('UTF-8');

        $from = $options[self::OPTION_FROM] ?? $this->fromAddress;
        $name = $options[self::OPTION_NAME] ?? $this->fromName;
        $content_type = $options[self::OPTION_CONTENT_TYPE] ?? self::CONTENT_TYPE_PLAIN;
        $plain_text = $options[self::OPTION_PLAIN_TEXT] ?? strip_tags($message);
        $return_path = $options[self::OPTION_RETURN_PATH] ?? $this->retAddress;

        $CRLF = "\r\n";
        $subject = mb_encode_mimeheader($subject, 'utf-8', 'B', $CRLF);
        $header = "From: " . mb_encode_mimeheader($name) . "<$from>{$CRLF}";
        $body = $message;

        if ($content_type == 'html') {
            // マルチパートで送る

            $boundary = uniqid("BOUNDARY");

            //ヘッダー
            $header .= "MIME-Version: 1.0{$CRLF}";
            $header .= "Content-Type: multipart/alternative; boundary=\"$boundary\"{$CRLF}";

            // プレーンテキスト
            $body = "--{$boundary}{$CRLF}";
            $body .= "Content-Type: text/plain; charset=utf-8{$CRLF}";
            $body .= "Content-Transfer-Encoding: base64{$CRLF}";
            $body .= $CRLF;
            $body .= chunk_split(base64_encode($plain_text), 76, $CRLF);

            // ＨＴＭＬ
            $body .= "--{$boundary}{$CRLF}";
            $body .= "Content-Type: text/html; charset=utf-8{$CRLF}";
            $body .= "Content-Transfer-Encoding: base64{$CRLF}";
            $body .= $CRLF;
            $body .= chunk_split(base64_encode($message), 76, $CRLF);
            //$message .= "${body}${CRLF}";

            $body .= "--{$boundary}--{$CRLF}";
        } else {
            $header .= "MIME-Version: 1.0{$CRLF}";
            $header .= "Content-Type: text/plain; charset=utf-8{$CRLF}";
            $header .= "Content-Transfer-Encoding: 8bit{$CRLF}";
        }

        $res = mail($to_list, $subject, $body, $header, '-f' . $return_path);

        return ['result' => true, 'response' => $res];

    }

    /**
     * メールアドレスのチェック
     * @param $email
     * @return string
     */
    static public function checkAddress($email) {

        $email = trim($email);

        if (empty($email))
            return "入力されていません";


        // 使用文字チェック

        $messages = [];
        $alert = '';

        for ($i = 0; $i < mb_strlen($email, 'UTF-8'); $i++) {
            $msg = '';
            $lt = mb_substr($email, $i, 1, 'UTF-8');

            if (!preg_match("/[a-zA-Z0-9_.+@\-]/", $lt))
                $msg = "使用できない文字が含まれています";

            if (mb_strwidth($lt, 'UTF-8') === 2)
                $msg = "全角文字が含まれています";

            if ($lt === ' ')
                $msg = "スペースが含まれています";

            if ($msg) {
                $messages[$msg] = 1;
                $alert .= "<span style=\"background-color:yellow;color:red;font-weight: bold;\"> {$lt} </span> ";
            } else {
                $alert .= $lt;
            }

        }
        if ($messages) {
            $msg = implode("<br>", array_keys($messages));
            return "{$msg}<br>{$alert}";
        }

        if (!str_contains($email, '@'))
            return "@ がありません";

        list($local, $domain) = explode('@', $email);

        if (str_contains($local, '..'))
            return "連続したドット .. があります";

        if (str_starts_with($local, '.'))
            return "先頭に . があります";

        if (str_ends_with($local, '.'))
            return "最後に . があります";

        if (str_ends_with($domain, '.'))
            return "最後に . があります";




        if (!preg_match("/^[a-zA-Z0-9_+\-]+(.[a-zA-Z0-9_+\-]+)*@([a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/", $email)) {
            return "無効な形式のメールアドレスです<br>{$alert}";
        }

        // ドメインチェック

        if (!checkdnsrr($domain, "MX")) {
            $msg = '';
            // 失敗例
            $ex = [
                'docomo.co.jp' => 'docomo.ne.jp',
                'docomo.jp' => 'docomo.ne.jp',
                'docom.' => 'docomo',

                'ezweb.co.jp' => 'ezweb.ne.jp',
                'ezweb.jp' => 'ezweb.ne.jp',

                'gmail.co.jp' => 'gmail.com',
                'gmail.ne.jp' => 'gmail.com',
                'gmail.jp' => 'gmail.com',

                'icloud.jp' => 'icloud.com',

                'softdank' => 'softbank',
                'softbnk' => 'softbank',
                'softbank.jp' => 'softbank.ne.jp',

                '.co.ip' => 'co.jp',
                '.ne.ip' => 'ne.jp',
            ];
            foreach ($ex as $miss => $collect) {
                if (str_contains($domain, $miss)) $msg = "{$collect} ではありませんか";

            }

            return "ドメイン(@{$domain})が見つかりません<br>{$msg}";
        }

        // 有効
        return "OK";

    }
}