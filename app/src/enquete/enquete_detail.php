<?php
//アンケート詳細（回答）

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

    // アンケートID　仮でデフォルト1にしておく
    $id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
    if ($id === '') {
        $id = '1';
    }

    $Smarty->assign([
        'survey_id' => $id,
        'is_login' => true,
        'member_type' => 'free',
        'user_point' => 7777,
        'today_hit_count' => 14,
        'mypage_active' => false,
    ]);

    $title = "アンケート詳細（回答）";
    $title_url = $home_url . "enquete/enquete_detail";
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);
    $Smarty->display("enquete/enquete_detail.tpl");

})();