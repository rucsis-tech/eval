<?php
//利用規約

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

    $Smarty->assign([
        'focus_header_type' => 'static',  // header_focus.tpl: auth（P11/P12・右「× 閉じる」）| exit（P13/P19・右✕）| static（S** 静的ページ・左‹戻る）
        'static_page_layout' => true,     // base_focus: pc-wrap pc-static w-narrow で本文＋フッターを包む
        'show_footer' => true,            // layout/footer.tpl（非表示時はキーごと省略）
        // 'close_url' => '/path',        // 任意: 閉じる／戻るの遷移先URL。未指定時は common.js で履歴戻り /
    ]);

    $title = "利用規約";
    $title_url = $home_url . "pages/rules";
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);
    $Smarty->display("pages/rules.tpl");

})();