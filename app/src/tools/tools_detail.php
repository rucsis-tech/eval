<?php
//商品詳細

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

    // ※商品ID
    $id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
    // ※表示確認用
    if ($id === '') {
        $id = 't303';
    }

    $Smarty->assign([
        'tool_id' => $id,
        'gnav_active' => 'tools',
        'member_type' => 'free',
        'is_login' => true,
        'user_point' => 7777,
        'today_hit_count' => 14,
        'mypage_active' => false,
    ]);

    $title = "商品詳細";
    $title_url = $home_url . "tools/tools_detail";
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);
    $Smarty->display("tools/tools_detail.tpl");

})();