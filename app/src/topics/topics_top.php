<?php
//トピックス一覧

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

    // トピックスタブ　仮でデフォルトtopicsにしておく
    $tab = isset($_GET['tab']) ? trim((string) $_GET['tab']) : 'topics';
    if (!in_array($tab, ['topics', 'flash', 'info'], true)) {
        $tab = 'topics';
    }

    $Smarty->assign([
        'topics_tab' => $tab,
        'is_login' => true,
        'member_type' => 'free',
        'user_point' => 7777,
        'today_hit_count' => 14,
        'mypage_active' => false,
    ]);

    $title = "トピックス一覧";
    $title_url = $home_url . "topics/topics_top";
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);
    $Smarty->display("topics/topics_top.tpl");

})();