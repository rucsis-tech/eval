<?php


use classes\LogClass;
use classes\SessionClass;

class ApiApp {

    public int $http_code = 200;
    public string $status = 'OK';
    public string $message = 'OK';
    private array $payload;
    private bool $loggingEnabled = false;

    /**
     * @throws Exception
     */
    function __construct(bool $sessionEnabled = true, $loggingEnabled = false) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // preflight の場合即終了
            echo "OK";
            exit;
        };

        if ($sessionEnabled) {
            // セッションログイン処理
            SessionClass::checkWebSession();
        }

        header("Content-Type: application/json; charset=utf-8");

        $this->payload = $this->getPayload();

        $this->loggingEnabled = $loggingEnabled;
        if ($loggingEnabled) $this->logRequest();

        App::setCookieUID(null);

    }

    /**
     * @param $function
     * @return void
     */
    public function action($function): void {

        try {
            $response = $function($this, $this->payload);
            $type = 'response';
        } catch (Exception $e) {
            http_response_code($e->getCode());
            $response = [];
            $this->status = 'NG';
            $this->http_code = $e->getCode();
            $this->message = $e->getMessage();
            if ($this->http_code === 401) {
                LogClass::notice($this->message);
            } else {
                AppLog::except($e);
            }

            $type = 'error';
            if (App::isDebug()) {
                $response = [
                    'files' => $e->getTrace(),
                    'get' => $_GET,
                    'post' => $_POST,
                    'json' => $this->payload,
                ];
            }
        }

        $result = [
            'status' => $this->status,
            'http_code' => $this->http_code,
            'message' => $this->message,
            'response' => $response,
        ];

        $log = json_encode($result, JSON_UNESCAPED_UNICODE);
        $this->logging($type, $log);

        http_response_code($this->http_code);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * POSTされたJSONデータを取得
     * @throws Exception
     */

    private function getPayload() {

        $payload = [];
        $json = file_get_contents('php://input');
        if ($json) {
            $payload = json_decode($json, true);
        }
        return empty($payload) ? [] : $payload;
    }


    public function logRequest(): void {
        try {
            $text = "\n"
                . "--getallheaders---------------------------------\n"
                . print_r(getallheaders(), true)
                . "--_POST-----------------------------------------\n"
                . print_r($_POST, true)
                . "--_FILES-----------------------------------------\n"
                . print_r($_FILES, true)
                . "--_GET------------------------------------------\n"
                . print_r($_GET, true)
                . "--GetRequestJson--------------------------------\n"
                . print_r($this->payload, true)
                . "--_SERVER---------------------------------------\n"
                . print_r(array_diff($_SERVER, $_ENV), true);
            $this->Logging('access', $text);

        } catch (Exception $e) {

        }
    }


    public function logging($type, $log) {
        if ($this->loggingEnabled) {
            try {
                // log-path
                $log_dir = TMP_DIR . 'api/' . date('Ym/d');
                if (!file_exists($log_dir)) mkdir($log_dir, 0777, true);

                $paths = parse_url($_SERVER['REQUEST_URI']);
                $basename = str_replace('/', '.', substr($paths['path'], 1));

                $mSecs = explode('.', $_SERVER["REQUEST_TIME_FLOAT"]);
                $mSec = str_pad($mSecs[1] ?? '0000', 4, '0', STR_PAD_LEFT);

                $filename = "{$log_dir}/{$basename}." . date("Ymd-His.") . "{$mSec}.{$type}.log";

                // Write
                file_put_contents($filename, $log);
                chmod($filename, 0777);

            } catch (Exception $e) {
            }
        }

    }


    /**
     * CMSでの認証
     * @param $auth
     * @param $action
     * @return void
     * @throws Exception
     */
    public function auth($auth, $action): void {

        $operation = array_merge(['action' => $action], $_REQUEST, $this->payload , $_FILES);
        unset($operation['password']);
        unset($operation['token']);

        // そもそもログインしているか確認
        $Admin = Admin::getByGUID(SessionClass::$GUID);
        if (empty($Admin)) {
            throw new Exception("ユーザー認証できていません(auth:$auth)", 401);
        }

        $Admin->authLog($auth, $operation);

    }


}