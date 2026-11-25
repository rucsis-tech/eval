<?php

// ログイン

use classes\auth\AuthMailClass;
use classes\auth\MyAuthClass;

(function () {
    $Smarty = new SmartyClass(false);
    $Smarty->resetMessage();
    $Smarty->addMessage(['auth' => 'ログイン']);

    $ENV = config('APP_ENV');
    $Smarty->assign('ENV', $ENV);


    //アプリからのアクセス判定
    $app_mode = 0;
    $app_kind = "";
    if (isset($_REQUEST["appKind"])) { //アクセス時にappコードがあれば
        $app_mode = 1;
        $app_kind = $_REQUEST["appKind"];
    }
    $Smarty->assign('app_mode', $app_mode);
    $Smarty->assign('app_kind', $app_kind);


    if (!empty($Smarty->Member->uid)) {
        $Smarty->location('/index');
        exit();
    }


    $Smarty->method('POST', function (SmartyClass $Smarty) {

        $email = trim($_POST['email'] ?? ($_POST['userid'] ?? ''));
        $password = $_POST['password'] ?? '';
        $params = MyAuthClass::checkAuthCookies();

        try {
            $code = MyAuthClass::checkin($email, $password);
            $redirect_url = empty($params['redirect_url']) ? BASE_URL : $params['redirect_url'];
            $state = empty($params['state']) ? AuthMailClass::getLoginState(0) : $params['state'];
            setcookie('redirect_url', '');
            $Smarty->location("$redirect_url" . (str_contains($redirect_url, '?') ? '&' : '?') . "state=$state&code=$code");

        } catch (Exception $e) {
            $code = $e->getCode();
            $em = \classes\CryptClass::encrypt("{$code}:{$email}:{$password}");
            AppLog::notice($em, AppLog::$ERROR_LOGIN);
            $Smarty->location("/cms/", ['auth.err' => $e->getMessage()]);
        }

    });


})();
