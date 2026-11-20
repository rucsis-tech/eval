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


    if( empty($_GET['rc']) ){
        //レースコードが無い場合にデフォルトのレースコードを採用する
        $kaisai_days_base = Race::getKaisaiDays();
        $kaisai_list_base = Race::getKaisaiList($kaisai_days_base[0]);
        $raceCode = array_key_first($kaisai_list_base) . "01";
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
        $day = $current_date;
        $kaisai_days = Race::getKaisaiDays(strtotime($day));
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
    $kaisai_list = Race::getKaisaiList($day);
    $Smarty->assign('kaisai_list', $kaisai_list );
    //var_dump($kaisai_list);

    // 開催３タブリスト作成
    if ( count($kaisai_days) >= 3 ) { //3日開催か？
        $target_days = $kaisai_days;
    }else{
        $first_day = date('Y-m-d', strtotime('-7 day', strtotime($kaisai_days[1])));;
        $second_day = $kaisai_days[0];
        $third_day = $kaisai_days[1];
        $target_days = [$first_day, $second_day, $third_day];
    }
    //var_dump($target_days);
    $week = ['日', '月', '火', '水', '木', '金', '土'];
    $date01 = new DateTime($target_days[0]);
    $w01 = $date01->format('w');
    $display_date01 = $date01->format('n/j') . '(' . $week[$w01] . ')';
    $date_code01 = $date01->format('Ymd');

    $date02 = new DateTime($target_days[1]);
    $w02 = $date02->format('w');
    $display_date02 = $date02->format('n/j') . '(' . $week[$w02] . ')';
    $date_code02 = $date02->format('Ymd');

    $date03 = new DateTime($target_days[2]);
    $w03 = $date03->format('w');
    $display_date03 = $date03->format('n/j') . '(' . $week[$w03] . ')';
    $date_code03 = $date03->format('Ymd');

    $kaisai_date_list = array(
        array (
            "display_date" => $display_date01,
            "date_code" => $date_code01
        ),
        array (
            "display_date" => $display_date02,
            "date_code" => $date_code02
        ),
        array (
            "display_date" => $display_date03,
            "date_code" => $date_code03
        )
    );


    //テスト用ベタ置き（仮）
    /*
    $kaisai_date_list = array(
        array (
            "display_date" => "11/8(日)",
            "date_code" => "20261108"
        ),
        array (
            "display_date" => "11/14(土)",
            "date_code" => "20261114"
        ),
        array (
            "display_date" => "11/15(日)",
            "date_code" => "20261115"
        )
    );
    */

    //var_dump($kaisai_date_list);


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


    // 日付タブ用 ?rc=（場・レース番号維持、不可時は先頭有効レース）
    $current_day_ymd = $current_date !== '' ? substr($current_date, 0, 8) : substr($raceCode, 0, 8);
    $race_no_suffix = sprintf('%02d', (int)($current_raceNo !== '' ? $current_raceNo : 1));
    $prefer_jyo = $current_jyo;
    $load_day_ymd = str_replace('-', '', $day);
    foreach ($kaisai_date_list as &$date_tab) {
        $ymd = $date_tab['date_code'];
        $tab_day = substr($ymd, 0, 4) . '-' . substr($ymd, 4, 2) . '-' . substr($ymd, 6, 2);
        if ($ymd === $load_day_ymd && !empty($kaisai_list)) {
            $day_list = $kaisai_list;
        } else {
            $day_list = Race::getKaisaiList($tab_day);
        }
        $rc = '';
        if (!empty($day_list)) {
            $picked_code = null;
            foreach ($day_list as $kaisai) {
                if ((string)$kaisai->place === (string)$prefer_jyo) {
                    $picked_code = $kaisai->kaisai_code;
                    break;
                }
            }
            if ($picked_code === null) {
                $first = reset($day_list);
                $picked_code = $first->kaisai_code;
            }
            $races = $day_list[$picked_code]->races ?? [];
            $race_bango = (int)$race_no_suffix;
            $picked_race = null;
            foreach ($races as $race) {
                $info = is_array($race->info ?? null) ? $race->info : [];
                $bango = (int)($info['race_bango'] ?? 0);
                if ($bango === $race_bango) {
                    $picked_race = $race;
                    break;
                }
                if ($bango === 0 && !empty($race->race_code)
                    && substr((string)$race->race_code, -2) === $race_no_suffix) {
                    $picked_race = $race;
                    break;
                }
            }
            // 基本は開催場タブ同様 kaisai_code＋R番号。race_code は実在・非中止時のみ採用
            $candidate = $picked_code . $race_no_suffix;
            $commit = $picked_race !== null
                ? (int)(is_array($picked_race->info ?? null) ? ($picked_race->info['commit'] ?? 0) : 0)
                : null;
            if ($picked_race !== null && $commit !== 9 && !empty($picked_race->race_code)) {
                $candidate = $picked_race->race_code;
            } elseif ($picked_race !== null && $commit === 9) {
                foreach ($races as $race) {
                    $info = is_array($race->info ?? null) ? $race->info : [];
                    if ((int)($info['commit'] ?? 0) !== 9) {
                        $candidate = !empty($race->race_code)
                            ? $race->race_code
                            : $picked_code . sprintf('%02d', (int)($info['race_bango'] ?? 1));
                        break;
                    }
                }
            }
            $rc = $candidate;
        }
        $date_tab['href'] = $rc !== '' ? ('?rc=' . $rc) : '#';
        $date_tab['is_current'] = ($ymd === $current_day_ymd);
    }
    unset($date_tab);
    $Smarty->assign('kaisai_date_list', $kaisai_date_list);

    // 開催場タブ（表示中日の開催一覧・R 番号はキープ、race の存在は見ない）
    $venue_tab_day = substr($current_day_ymd, 0, 4) . '-'
        . substr($current_day_ymd, 4, 2) . '-'
        . substr($current_day_ymd, 6, 2);
    if ($current_day_ymd === $load_day_ymd && !empty($kaisai_list)) {
        $venue_day_list = $kaisai_list;
    } else {
        $venue_day_list = Race::getKaisaiList($venue_tab_day);
    }
    $kaisai_place_list = [];
    $venue_rows = !empty($venue_day_list) ? array_values($venue_day_list) : [];
    usort($venue_rows, static function ($a, $b) {
        $pla = (int)$a->place;
        $plb = (int)$b->place;
        $la = $pla >= 30;
        $lb = $plb >= 30;
        if ($la !== $lb) {
            return $la <=> $lb;
        }
        return $pla <=> $plb;
    });
    foreach ($venue_rows as $kaisai) {
        $place = (string)$kaisai->place;
        $info = is_array($kaisai->info ?? null) ? $kaisai->info : [];
        $label = $info['keibajo'] ?? (Race::PLACES[$place] ?? $place);
        $is_local = ((int)$place >= 30);
        $is_current = ($place === (string)$current_jyo);
        $class = trim(($is_local ? 'local' : '') . ($is_current ? ' on' : ''));
        $kaisai_place_list[] = [
            'label' => trim($label),
            'href' => '?rc=' . $kaisai->kaisai_code . $race_no_suffix,
            'is_current' => $is_current,
            'is_local' => $is_local,
            'class' => $class,
        ];
    }
    $Smarty->assign('kaisai_place_list', $kaisai_place_list);

    // レース番号タブ（現在の開催・?rc= で遷移）
    $current_race_bango = (int)$race_no_suffix;
    $race_tab_list = [];
    if (!empty($race_list) && is_iterable($race_list)) {
        foreach ($race_list as $race) {
            $info = is_array($race->info ?? null) ? $race->info : [];
            $bango = (int)($info['race_bango'] ?? 0);
            if ($bango < 1) {
                continue;
            }
            $commit = (int)($info['commit'] ?? 0);
            $suffix = sprintf('%02d', $bango);
            $rc = !empty($race->race_code) ? $race->race_code : ($kaisai_code . $suffix);
            $is_current = ($bango === $current_race_bango);
            // 発走済みグレーアウト: 結果確定のみ（commit 5-7）
            $is_done = ($commit >= 5 && $commit <= 7);
            $class = trim(($is_done ? 'done' : '') . ($is_current ? ' on' : ''));
            $race_tab_list[] = [
                'label' => $bango . 'R',
                'href' => '?rc=' . $rc,
                'class' => $class,
                'race_bango' => $bango,
            ];
        }
        usort($race_tab_list, static function ($a, $b) {
            return $a['race_bango'] <=> $b['race_bango'];
        });
    }
    $Smarty->assign('race_tab_list', $race_tab_list);

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



    $race_joho = [];
    $current_race = null;
    $race_bango_int = (int)$current_raceNo;
    foreach ($race_list as $race) {
        $rinfo = is_array($race->info ?? null) ? $race->info : [];
        if ((int)($rinfo['race_bango'] ?? 0) === $race_bango_int) {
            $current_race = $race;
            break;
        }
    }
    if ($current_race === null && !empty($race_list[$current_raceNo + 0])) {
        $current_race = $race_list[$current_raceNo + 0];
    }

    if ($current_race === null || empty($current_race->info) || (int)($current_race->info['commit'] ?? 0) === 9) {
        $error_code = ERROR_NO_BACHU_DATA;
        //競争中止や該当するレース番号がなかった場合の対応
        goto _999;
    }

    $ri = $current_race->info;
    $kaisai_info = is_array($kaisai_list[$kaisai_code]->info ?? null)
        ? $kaisai_list[$kaisai_code]->info
        : [];
    $jyo_name = trim($ri['keibajo'] ?? $kaisai_info['keibajo'] ?? (Race::PLACES[$current_jyo] ?? ''));
    $race_bango = (int)($ri['race_bango'] ?? $race_bango_int);
    $track = (string)($ri['track'] ?? '');
    $kyori = (int)($ri['kyori'] ?? 0);
    $course_label = $track . ($kyori > 0 ? $kyori . 'm' : '');
    $baba_labels = ['情報なし', '良', '稍重', '重', '不良'];
    $baba_code = (int)($ri['baba_code'] ?? 0);
    if ($baba_code === 0) {
        $baba_text = trim((string)($ri['baba'] ?? ''));
        if ($baba_text !== '') {
            $flip = array_flip($baba_labels);
            $baba_code = $flip[$baba_text] ?? 0;
        }
    }
    if ($baba_code === 0) {
        if ($track === '芝' && isset($kaisai_info['baba_shiba_code'])) {
            $baba_code = (int)$kaisai_info['baba_shiba_code'];
        } elseif (str_contains($track, 'ダ') && isset($kaisai_info['baba_dirt_code'])) {
            $baba_code = (int)$kaisai_info['baba_dirt_code'];
        } elseif ($track === '芝' && !empty($kaisai_info['baba_shiba'])) {
            $flip = array_flip($baba_labels);
            $baba_code = $flip[trim($kaisai_info['baba_shiba'])] ?? 0;
        } elseif (str_contains($track, 'ダ') && !empty($kaisai_info['baba_dirt'])) {
            $flip = array_flip($baba_labels);
            $baba_code = $flip[trim($kaisai_info['baba_dirt'])] ?? 0;
        }
    }
    $going = $baba_labels[$baba_code] ?? $baba_labels[0];
    $hasso = trim((string)($ri['hasso_jikoku'] ?? ''));
    if ($hasso !== '' && strlen($hasso) === 4 && ctype_digit($hasso)) {
        $hasso = substr($hasso, 0, 2) . ':' . substr($hasso, 2, 2);
    }
    $race_joho = [
        'jyo_name' => $jyo_name,
        'race_bango' => $race_bango,
        'kyosomei' => trim((string)($ri['kyosomei'] ?? '')),
        'course_label' => $course_label,
        'going' => $going,
        'hasso' => $hasso,
    ];


    //出走馬情報取得

    //var_dump($current_raceCode);
    $race_uma_list = Race::getUmaList($current_raceCode);
    if (!is_array($race_uma_list)) {
        $race_uma_list = [];
    }
  //var_dump($race_uma_list);

    // 結果ビュー用（中身は生の info 配列・着順で並べ替えのみ）
    $race_uma_list_by_chakujun = $race_uma_list;
    usort($race_uma_list_by_chakujun, static function ($a, $b) {
        return (int)($a['chakujun'] ?? 0) <=> (int)($b['chakujun'] ?? 0);
    });

    $Smarty->assign('race_uma_list', $race_uma_list);
    $Smarty->assign('race_uma_list_by_chakujun', $race_uma_list_by_chakujun);




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

    if (!isset($race_joho)) {
        $race_joho = [];
    }
    $Smarty->assign('race_joho', $race_joho);

    if (!isset($race_uma_list)) {
        $Smarty->assign('race_uma_list', []);
    }
    if (!isset($race_uma_list_by_chakujun)) {
        $Smarty->assign('race_uma_list_by_chakujun', []);
    }

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
        'show_campaign_modal' => false,  // true: partials/modal.tpl（キャンペーン告知モーダル）
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