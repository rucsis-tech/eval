<?php

// -------------------------------------------------------------
// HTMLテンプレート表示用の SMARTY に セッション管理を追加したクラス
// -------------------------------------------------------------

use classes\SessionClass;
use trait\Pagination;
use Smarty\Smarty;

class SmartyCMSClass extends Smarty {

    use Pagination;

    public User|null $User = null;
    public string $path = '/';
    public $Admin = null;

    /**
     * @param $session_enable
     * @throws Exception
     */
    function __construct() {
        parent::__construct();

        // アクセス制限チェック
        App::restrictServerAccess();

        // ドメインURL（HTTPS）ではブラウザにセキュリティ制限(=>WP画像)があるので使えない
        if (str_contains(BASE_URL, 'winsight.jp')) {
            echo "Sorry, CMS is not available for winsight.jp URL";
            exit;
        }

        // セッションログイン処理
        SessionClass::checkWebSession();
        if (SessionClass::$REDIRECT_URL) {
            header("Location: " . SessionClass::$REDIRECT_URL);
            exit();
        }

        $this->template_dir = [APP_ROOT . 'src/cms/'];
        $this->compile_dir = TMP_DIR . 'smarty/';
        if (!file_exists($this->compile_dir)) {
            mkdir($this->compile_dir, 0777, true);
        }

        $User = User::logined();
        $Admin = Admin::getAdmin($User);
        $this->assign('User', $User);
        $this->assign('Admin', $Admin);

        SmartyExtensions::setExtensions($this);
        $this->assign('_REQUEST', $_REQUEST);
        $this->assign('_SERVER', array_diff($_SERVER, $_ENV));

        $head_color = '#666';
        if (str_contains($_SERVER['HTTP_HOST'], 'local')) $head_color = '#666';
        if (str_contains($_SERVER['HTTP_HOST'], '176.34')) $head_color = '#24A';
        if (str_contains($_SERVER['HTTP_HOST'], '13.112')) $head_color = '#282';
        $this->assign('head_color', $head_color);

        // UID記録
        App::setCookieUID(User::logined()->uid);

        if (empty($Admin)) {
            $this->displayCMS('common/login.tpl');
            exit;
        }

        $this->Admin = $Admin;

    }


    public function getRequest($key, $default = null) {
        return $_REQUEST[$key] ?? $default;
    }

    public function getPayload() {

        $payload = [];
        $json = file_get_contents('php://input');
        if ($json) {
            $payload = json_decode($json, true);
        }
        return empty($payload) ? [] : $payload;
    }

    /**
     * 管理画面出力
     * @param $template_file
     * @return void
     * @throws SmartyException
     */
    public function displayCMS($template_file = '') {

        if ($template_file) {
            $this->assign('template_file', $template_file);
        }
        $this->display('index.tpl');

    }

    public function outputCSV($filename, $list, $index) {
        // ヘッダーを設定してダウンロードを促す
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo pack('C*', 0xEF, 0xBB, 0xBF);

        //
        foreach ($list as $item) {
            if (empty($index)) {
                echo implode(',', $item);
            } else {
                $row = [];
                foreach ($index as $key) {
                    $row[] = $item[$key];
                }
                echo implode(',', $row);
            }

            echo "\n";
        }

    }

    /**
     * 遷移
     * @param $url
     * @param $message
     * @return void
     */

    #[NoReturn]
    public function location($url, $message = []) {
        if ($message) $this->addMessage($message);
        header("Location: " . $url);
        exit();
    }

    /**
     * @param $auth
     * @param $for_log_params
     * @return void
     * @throws Exception
     */
    public function auth($auth, $action): void {
        $operation = array_merge(['action' => $action], $_REQUEST, $_FILES);
        $this->Admin->authLog($auth, $operation);
    }
}
