<?php
//会員登録（コード認証）

(function () {

    $Smarty = new SmartyClass();

    $ENV = config('APP_ENV');
    $Smarty->assign('ENV', $ENV);

    // フルパス記述用トップURL取得
    $protocol = (empty($_SERVER['HTTPS']) ? 'https://' : 'https://'); //取得できていないので必ずhttpsにする
    $host = $_SERVER['HTTP_HOST'];
    $home_url = $protocol . $host . "/";
    $Smarty->assign('home_url', $home_url);

	//パンくず情報
	$first_name = "";
	$first_url = "";
	$second_name = "";
	$second_url = "";
	$third_name = "";
	$third_url = "";
	$fourth_name = "";
	$fourth_url = "";

    $Smarty->assign('first_name', $first_name);
    $Smarty->assign('second_name', $second_name);
    $Smarty->assign('third_name', $third_name);
    $Smarty->assign('fourth_name', $fourth_name);
    $Smarty->assign('first_url', $first_url);
    $Smarty->assign('second_url', $second_url);
    $Smarty->assign('third_url', $third_url);
    $Smarty->assign('fourth_url', $fourth_url);

    // コード送信先メール（未指定時はモック表示用）
    $email = isset($_GET['email']) ? trim((string) $_GET['email']) : '';
    if ($email === '') {
        $email = 'mail@example.com';
    }

    $Smarty->assign([
        'focus_header_type' => 'auth',              // header_focus.tpl: auth（P11/P12・右「× 閉じる」）| exit（P13/P19・右✕）| static（S2 等・左‹戻る）
        'register_email' => $email,                 // コード送信先メール（未指定時はモック表示用）
        // 'close_url' => '/path',                  // 任意: 閉じる／戻るの遷移先URL。未指定時は common.js で履歴戻り
    ]);

    $title = "会員登録（コード認証）";
    $title_url = $home_url . "auth/register";
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);

    $Smarty->display("auth/register.tpl");  // ※テンプレ確認用に追加。実装後はこの行とregister.tplを削除する

})();