<?php


use classes\LogClass;
use classes\SessionClass;

class ApiAdmin {

    public string $status = 'OK';
    public string $message = 'OK';
    private array $payload;

    public Admin $Admin;

    /**
     * @throws Exception
     */
    function __construct() {
        header("Access-Control-Allow-Origin: *");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // preflight の場合即終了
            echo "OK";
            exit;
        };

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            // POST以外は受けない
            exit;
        };

        header("Content-Type: application/json; charset=utf-8");

        $this->payload = $this->getPayload();

        // tokenあればログイン処理
        $token = $this->payload['token'] ?? ($_GET['token'] ?? '');
        if (!empty($token)) {
            SessionClass::checkSessionToken($token);
        }
    }

    /**
     * POSTされたJSONデータを取得
     * @throws Exception
     */

    public function getPayload() {

        $json = file_get_contents('php://input');
        if ($json) {
            $payload = json_decode($json, true);
        } else {
            $payload = $_REQUEST;
        }

        return $payload;
    }

    /**
     * 権限チェックとログ記録
     * @throws Exception
     */
    public function auth($auth, $for_log_params = null): void {

        // そもそもログインしているか確認
        $Admin = Admin::getByGUID(SessionClass::$GUID);
        if (empty($Admin)) {
            throw new Exception("ユーザー認証できていません(auth:$auth)" , 401);
        }

        $operation = $for_log_params ? $for_log_params : $this->payload;
        unset($operation['password']);
        unset($operation['token']);

        $Admin->authLog($auth , $operation);

        $this->Admin = $Admin;
    }

    /**
     * @param $function
     * @return void
     */
    public function action($function): void {

        try {
            $response = $function($this, $this->payload);
        } catch (Exception $e) {
            $response = [];
            $this->status = 'NG';
            $this->message = $e->getMessage() . "\n" . $e->getTraceAsString();
            AppLog::except($e);
        }

        $result = [
            'status' => $this->status,
            'message' => $this->message,
            'response' => $response,
        ];

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }




}