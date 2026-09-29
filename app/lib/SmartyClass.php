<?php

// -------------------------------------------------------------
// HTMLテンプレート表示用の SMARTY に セッション管理を追加したクラス
// -------------------------------------------------------------

use classes\SessionClass;
use Smarty\Smarty;

class SmartyClass extends Smarty {

    public array $const = [];
    public string $method = '';
    public array $messages = [];
    public Member|null $Member = null;
    public bool $isLogin = false;
    private string $cookie_name = 'app_msg';


    private static string $TRQ2PARAM = '';
    private static array $watches = [];

    /**
     * @throws Exception
     */
    function __construct($session_enable = true) {

        parent::__construct();

        // 連続アクセス制限
        // App::block();
        // アクセス制限チェック
        Limiter::markIPFile();
        App::restrictServerAccess();

        $this->method = $_SERVER["REQUEST_METHOD"] ?? '';
        // Message Cookie
        $this->messages = [];
        try {
            $messages = json_decode($_COOKIE[$this->cookie_name], true);
            if (!empty($messages)) $this->messages = $messages;
        } catch (Exception $e) {
            $this->setMessage([]);
        }

        // WEBセッション処理とユーザー認証
        if ($session_enable) {

            // セッションログイン処理
            SessionClass::checkWebSession();
            if (SessionClass::$REDIRECT_URL) {
                header("Location: " . SessionClass::$REDIRECT_URL);
                exit();
            }
            if (SessionClass::$WORK) {
                // アクセストークン等ありそうなので
                User::logined();
            }

            // --------------------------------------------------------------------------------

            // 必要あればリダイレクト
            if (SessionClass::$REDIRECT_URL) {
                $this->location(SessionClass::$REDIRECT_URL);
            }

            $this->Member = Member::getMember(User::logined()->uid);
            if ($this->Member->uid) {
                // EMAIL確認
                if (empty($this->Member->email)) {
                    $Auth = Model::table('user_auth')->where(['uid' => $this->Member->uid])->first();
                    $this->Member->save(['email' => $Auth->email ?? '']);
                }
                // 最終アクセス更新
                $accessed_time = strtotime($this->Member->accessed_at);
                $privacy_policy_update = strtotime(App::PRIVACY_POLICY_UPDATE);
                if (time() > $privacy_policy_update && $accessed_time < $privacy_policy_update) {
                    // privacy_policy_update の必要がありPOPUP表示、accessed_at を更新しない
                    $this->assign('privacy_policy_update', $privacy_policy_update);
                } else if ($accessed_time < time() - 60 * 10) {
                    // 毎回じゃなくていいので更新
                    Member::conditionAt($this->Member->uid, member::CONDITION_ACCESSED_AT);
                }
                //サブスク
                $this->Subscription = SubscriptionEx::currentQuery($this->Member->uid, SubscriptionEx::KIND_YOSO)->first();
                $this->Member->subscription_id = $this->Subscription->id ?? ($this->Member->subscription_id == -1 ? -1 : 0);
            }

            $this->assign("Member", $this->Member);

            // UID記録
            App::setCookieUID(User::logined()->uid);
        }

        $this->template_dir = [APP_ROOT . 'templates/'];
        $this->compile_dir = TMP_DIR . 'smarty/';
        if (!file_exists($this->compile_dir)) {
            mkdir($this->compile_dir, 0777, true);
        }
        SmartyExtensions::setExtensions($this);

        $this->assign('Smarty', $this);
        $this->assign('title', config('APP_NAME'));


    }

    /**
     * Cookieにメッセージ保存
     * @param $array
     * @return void
     */

    public function addMessage($array): void {
        $this->setMessage(array_merge($this->messages, $array));
    }

    public function setMessage($array): void {
        $this->messages = $array;
        setcookie($this->cookie_name, json_encode($this->messages), time() + 86400);
    }

    public function resetMessage(): array {
        $messages = $this->messages;
        setcookie($this->cookie_name, '', time() + 86400);
        $this->messages = [];
        return $messages;
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
     * 指定メソッドの場合実行する
     * @param $method
     * @param $function
     * @return ?bool
     */
    public function method($method, $function): ?bool {
        try {
            if (strtolower($this->method) == strtolower($method)) {
                return $function($this);
            }
            return false;
        } catch (Exception $e) {
            AppLog::except($e);
            throw $e;
        }
    }

    public function actionGET($function) {
        $this->method('GET', $function);
    }

    public function actionPOST($function) {
        $this->method('POST', $function);
    }

    /**
     * @param $function
     * @return mixed
     */
    public function action($function): mixed {
        return $function($this);
    }

    /**
     * API処理
     * @param $template_file
     * @return void
     */
    public function api($function) {
        header("Content-Type: application/json; charset=utf-8");
        // POST-JSONパラメータ受信処理
        try {
            $this->JSON = json_decode(file_get_contents('php://input'), true);
        } catch (Exception $e) {
            $this->JSON = [];
        }

        return $function($this);
    }

    /**
     * テンプレート出力
     * @param $tmplate
     * @return void
     * @throws SmartyException
     */

    public function admin($tmplate) {

        $this->assign('template', $tmplate);
        $this->display('admin/app.tpl');

    }

    public function display($template = null, $cache_id = null, $compile_id = null) {
        if (!empty(self::$watches)) {
            self::watch('display');
            $this->assign('debug_watches', self::$watches);
        }

        parent::display($template, $cache_id, $compile_id);
    }

    static public function watch($name) {
        $now = intval((microtime(true) - SYSTEM_START_MICRO_TIME) * 1000);
        if (count(self::$watches) > 0) {
            $pre = count(self::$watches) - 1;
            self::$watches[$pre]['ms'] = $now - self::$watches[$pre]['time'];
        }
        self::$watches[] = ['name' => $name, 'time' => $now, 'ms' => 0];

        return self::$watches;

    }


}
