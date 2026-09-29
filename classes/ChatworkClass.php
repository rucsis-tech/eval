<?php

namespace classes;
class ChatworkClass {
    private $token;
    private $url = '';

    public function __construct($token) {
        $this->token = $token;
    }

    private function getHeaders() {
        return [
            "Content-Type: application/x-www-form-urlencoded",
            "X-ChatWorkToken: " . $this->token,
        ];
    }

    /*
     *  $token = '0c8ace6f2b31681159c17350760093aa';
     *  $rid = '169900232';
     *
     * */

    public function postRoomsMessages($rid, $message) {
        $data = ['body' => $message];
        return $this->request("https://api.chatwork.com/v2/rooms/$rid/messages", $this->getHeaders(), http_build_query($data));
    }

    public function getRoomsMessages($rid, $force = 0) {
        return $this->request("https://api.chatwork.com/v2/rooms/$rid/messages?force=$force", $this->getHeaders(), null);
    }

    public function postRoomsFile($rid, $message, $file_path, $file_name) {
        $mimeType = mime_content_type($file_path) ?: 'application/octet-stream';
        $file_name = basename($file_name);
        $cFile = curl_file_create($file_path, $mimeType, $file_name);

        $headers = [
            "Content-Type: multipart/form-data",
            "X-ChatWorkToken: " . $this->token,
        ];

        // 3. 送信データの組み立て (multipart/form-data)
        $postData = ['file' => $cFile];
        if (!empty($message)) $postData['message'] = $message;

        return $this->request("https://api.chatwork.com/v2/rooms/$rid/files", $headers, $postData);
    }




    // --------------------------------------------------------
    /*
     * curlでPOSTリクエスト
     */

    /**
     * @throws \Exception
     */
    public function request($url, $headers, $postData) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        if ($postData) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
        );
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            // cURL自体のエラー（通信失敗など）
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            if ($httpCode === 200) {
                // レスポンス（JSON）をパースして表示
                $result = json_decode($response, true);
                // $result['file_id'] でアップロードされたファイルのIDが取得できます
            } else {
                // Chatwork API側からエラーが返ってきた場合
                throw new \Exception("APIエラーが発生しました（ステータスコード: {$httpCode}）\n" . $response);
            }
        }

        curl_close($ch);

        return $result;
    }
}