<?php
//TOP（確率テーブル）

//--------------------------------------------------
// 引数のチェック
//--------------------------------------------------
define ("ERROR_NONE", 0 );
define ("ERROR_PARAM_FORMAT", 1 );
define ("ERROR_NO_KAISAI_DATA", 10 );
define ("ERROR_NO_BACHU_DATA", 11 );

define ("TAB_MODE_AUTO", 0 );
define ("TAB_MODE_SHUTUBA", 1 );
define ("TAB_MODE_KEKKA", 2 );  // 引数としては受け付けない

function validateRaceCode( $rc )
{
    if (strlen($rc) != 16 || !ctype_digit($rc)){
        return ERROR_PARAM_FORMAT;
    }else{
        $year  = substr($rc, 0, 4);
        $month = substr($rc, 4, 2);
        $day   = substr($rc, 6, 2);
        if( !checkdate((int)$month, (int)$day, (int)$year) ){
            return ERROR_PARAM_FORMAT;   // 書式エラー
        }
        if( $year < 2026 ) return ERROR_PARAM_FORMAT;

        $jyo  = (int)substr($rc, 8, 2);
        if( $jyo < 1 || $jyo > 10 ) return ERROR_PARAM_FORMAT;

        $kai  = (int)substr($rc, 10, 2);
        if( $kai > 12 ) return ERROR_PARAM_FORMAT;

        $nichi  = (int)substr($rc, 12, 2);
        if( $kai > 13 ) return ERROR_PARAM_FORMAT;

        $r  = (int)substr($rc, 14, 2);
        if( $r < 1 || $r > 12 ) return ERROR_PARAM_FORMAT;
    }
    return ERROR_NONE;
}


(function () {


    $Smarty = new SmartyClass();

    $ENV = config('APP_ENV');
    $Smarty->assign('ENV', $ENV);

    $error_code = 0;


    //--------------------------------------------------
    // レース情報取得
    //--------------------------------------------------

    $current_date = "";
    $current_jyo = "";		    // 01:札幌 02:函館 03:福島 04:新潟 05:東京 06:中山 07:中京 08:京都 09:阪神 10:小倉
    $current_raceNo = "";           // RR：レース番号
    $raceCode = "";         // YYYYMMDDBBKKNNRR:年月日場回日レースの16文字
    $kaisai_list = [];      // 日付、場所のドロップダウンリスト用
    $race_list = [];
    $bachu_race_joho = [];  // 馬柱上段のレース情報表示用
    $bachu_uma_joho = [];   // 馬柱の馬情報
    $chakujun_list = [];    // 結果・払戻の着順リスト

    $week_days = Race::getWeekDays();
    $kaisai_days = Race::getKaisaiDays();


    if( empty($_GET['rc']) ){
        //レースコードが無い場合にデフォルトのレースコードを採用する
    }else{
        $raceCode = $_GET['rc'];
        $error_code =  validateRaceCode( $raceCode );
    }

    // レースコードからカレントデートやその他の情報をセット
    if( $error_code != ERROR_NONE  ){
        // レースコードのフォーマットエラー
    }else{
        $current_date = substr($raceCode, 0, 8 );
        $current_jyo = substr($raceCode, 8, 2 );
        $current_raceNo = substr($raceCode, 14, 2 );
    }

    $tab_mode = TAB_MODE_AUTO;      // 0:自動、1:出馬表
    if( !empty($_GET['t']) ){
        $tab_mode = $_GET['t'];
        if (strlen($tab_mode) != 1 || !ctype_digit($tab_mode)){
            $tab_mode = TAB_MODE_AUTO;
        }else{
            if( $tab_mode != TAB_MODE_AUTO && $tab_mode != TAB_MODE_SHUTUBA ){
                $tab_mode = TAB_MODE_AUTO;
            }
        }
    }


    // 開催リスト取得
    //$day = $kaisai_days[0];
    $day = "2026-11-14"; //デバッグ用ダミー
    $kaisai_list = Race::getKaisaiList($day);
    //var_dump($kaisai_list);

    $Smarty->assign('kaisai_list', $kaisai_list );      // 日付、場所のドロップダウンリスト用



    $kaisai_code = "";
    if ( empty($raceCode) ) {
        $kaisai_code = array_key_first($kaisai_list);
        $raceCode = $kaisai_code . "01";
        $current_date = $kaisai_code;
        $current_jyo = substr($kaisai_code, 8, 2 );
        $current_raceNo = "01";
    }else{
        $kaisai_code = substr($raceCode, 0, 14 );
    }
    $Smarty->assign('current_kaisaiCode', $kaisai_code );


    $race_list = $kaisai_list[$kaisai_code]->races;
    $current_raceCode = $raceCode;

    $Smarty->assign('race_list', $race_list );          // レース番号のドロップダウンリスト用
    $Smarty->assign('current_date', $current_date );
    $Smarty->assign('current_jyo', $current_jyo );
    $Smarty->assign('current_raceNo', $current_raceNo );
    $Smarty->assign('current_raceCode', $current_raceCode );

    //var_dump($race_list);


    // 東西裏の情報を別の変数に
    //どのように取得するか相談
    //$tozaiura = $kaisai_list[$current_date]['tozai'];
    //$Smarty->assign('tozaiura', $tozaiura );

    //この一連のデータが必要かどうか？一旦保留
    /*

    $bangumi = $Keiba->getBangumiJoho( $current_date );   // 対象日の番組を取得
    if( is_null($bangumi) ){ $error_code = ERROR_NO_KAISAI_DATA; goto _999; };
    if( empty( $bangumi[$kaisai_code]['race']) ){
        // 引数の開催場所が無い場合先頭の開催場所にする。
        $first = null;
        foreach ($tozaiura as $v) {
            if ($v !== "") {
                $kaisai_code = $v;
                break;
            }
        }
    }
    if( empty( $bangumi[$kaisai_code]['race']) ){ $error_code = ERROR_NO_KAISAI_DATA; goto _999; };
    $race_list = $bangumi[$kaisai_code]['race'];
    if( is_null($race_list) ){ $error_code = ERROR_NO_KAISAI_DATA; goto _999; };

    $race_result_count = 0;
    $race_keys = [];
    foreach ($bangumi[$kaisai_code]['race'] as $key => $value) {
        if( $value['commit'] != 9 ) $race_keys[] = $key;
        if( $value['commit'] >= 5 && $value['commit'] <= 7 ){
            $race_result_count++;
        }
    }
    $Smarty->assign('race_result_count', $race_result_count );      // 日付、場所のドロップダウンリスト用
//echo "レース結果数：" . $race_result_count . "<br>";
//echo "<pre>" . json_encode($bangumi, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "</pre>";
    */



    if( empty( $race_list[$current_raceNo+0]) || $race_list[$current_raceNo+0]->info['commit'] == 9 ){
        $error_code = ERROR_NO_BACHU_DATA;
        //競争中止や該当するレース番号がなかった場合の対応
        goto _999;
    }


    //出走馬情報取得

    //var_dump($current_raceCode);
    $race_uma_list = "";
    $race_uma_list = Race::getUmaList($current_raceCode);
    //var_dump($race_uma_list);

    $Smarty->assign('race_uma_list', $race_uma_list );




    // 結果・払戻データで使用する着順のデータを作成
    /*一旦保留
    $result_open = 0;
    $raceNo = $bachu_race_joho['race_bango'];
    $Smarty->assign('current_raceNo', $raceNo );
    $race_num =(int)$raceNo;
    if( !empty($race_list[$race_num]) ){
        if( $race_list[$race_num]['commit'] >= 5 && $race_list[$race_num]['commit'] <= 7 ){
            $chakujun_list = $bachu_uma_joho;
            usort($chakujun_list, function ($a, $b) {
              return intval($a['chakujun']) - intval($b['chakujun']);
            });
            $result_open = 1;
        }
    }
    if( $result_open == 0 ) $tab_mode = TAB_MODE_SHUTUBA;
    else if( $tab_mode == TAB_MODE_AUTO && $result_open == 1  ) $tab_mode = TAB_MODE_KEKKA;

    $Smarty->assign('tab_mode', $tab_mode );
    $Smarty->assign('result_open', $result_open );
    $Smarty->assign('chakujun_list', $chakujun_list);
    */


//データない時は、nullを返します。
_999:


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