<?php
//TOP（確率テーブル）

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
        'show_pc_top' => true,          // true: nav_pc.tpl（ホーム追加バナー・速報ティッカー・アンケート帯）
        'show_campaign_modal' => true,  // true: partials/modal.tpl（キャンペーン告知モーダル）
        'show_reg_banner' => true,      // true: 下部ナビ上の無料登録追従バナー（非ログイン向け）
        'show_footer' => true,          // layout/footer.tpl（非表示時はキーごと省略）
        'member_type' => 'guest',       // ドロワー内アカウント欄: guest | free | subsc
        'is_login' => false,            // false: ヘッダー右「ログイン」／true: 「マイページ」＋/mypage へ
        'user_point' => 7777,           // ヘッダーの保有ポイント表示（未ログイン時は 0 想定）
        'today_hit_count' => 14,        // 下部ナビ「的中実績」の .hit-badge 件数
        'mypage_active' => false,       // true: ヘッダー .icon-mypage に is-active（マイページ画面など）
    ]);

    $title = "TOP（確率テーブル）";
    $title_url = $home_url;
    $Smarty->assign('title', $title);
    $Smarty->assign('title_url', $title_url);
    $Smarty->display("index.tpl");

})();