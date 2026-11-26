<?php

$Smarty = new SmartyCMSClass();
$Smarty->action(function ($Smarty) {

    // 取得
    $Smarty->auth('WindexList', 'windex');

    $WindexDay = null;
    $WindexRaces = [];


    $target_day = $_GET['target_day'] ?? BaseData::day(time());
    $time = strtotime($target_day);
    $Target = null;
    $days = [];
    for ($i = -4; $i <= 2; $i++) {
        //$Kaisai = Race::getKaisaiList($day);
        $day = date('Y-m-d', $time + $i * 86400);
        $days[$day] = ['day' => App::date("m月d日(ww)", strtotime($day))];
        $days[$day]['Kaisai'] = [];
        if (empty($target_day)) $target_day = $day;
        // windex_day 確認
        $Day = BaseData::getWindexDay($day);
        $days[$day]['Windex'] = $Day;
        if (isset($_REQUEST['start_at']) && ($target_day === $day)) {
            $Day->save(['start_at' => $_REQUEST['start_at']], BaseData::TABLE_DAY);
        }
    }

    //prex($days);


    $Smarty->assign('days', json_decode(json_encode($days), true));
    $Smarty->assign('target_day', $target_day);
    $Smarty->assign('day', App::date("m月d日(ww)", strtotime($target_day)));
    $Smarty->assign('Target', $Target);


    // 対象日の情報取得
    $YYYYMMDD = BaseData::toYYYYMMDD($target_day);
    //$Target = $Kaisai;
    $WindexDay = BaseData::getWindexDay($target_day);
    if (isset($_REQUEST['weekend_at'])) {
        $WindexDay->save(['weekend_at' => $_REQUEST['weekend_at']], BaseData::TABLE_DAY);
    }
    $win5 = $WindexDay->work()['win5'] ?? [];
    // レース一覧
    $WindexRaces = BaseData::defaultQuery($target_day)->all();
    foreach ($WindexRaces as $WindexRace) {
        $WindexRace->place = substr($WindexRace->race_code, 8, 2);
        // ポイント消費
        $WindexRace->point_count = Point::table('point_origin')
            ->where('category', 'IN', [Point::CATEGORY_WINDEX_RACE, Point::CATEGORY_CONTENT_CANCEL])
            ->where('identifier', $WindexRace->race_code)
            ->count();

    }


    $Smarty->assign('WindexDay', $WindexDay);
    $Smarty->assign('WindexRaces', $WindexRaces);
    $Smarty->assign('PLACES', Race::PLACES);
    $Smarty->assign('REVIEWS', []);


    $Smarty->displayCMS("basedata/index.tpl");

    pre($WindexRaces, '$WindexRaces');
});
