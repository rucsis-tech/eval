<?php

$Smarty = new SmartyCMSClass();

// 取得
$Smarty->auth('WindexList', 'windex');

$race_code = $_REQUEST['race_code'] ?? '';


$WindexRace = WindexLocal::where('race_code', $race_code)->first();
$place = substr($WindexRace->race_code,8,2);
$Smarty->assign('WindexRace', $WindexRace);
$Smarty->assign('PLACES', Race::PLACES);
$Smarty->assign('place', $place);
//pre($WindexRace);

$WindexUmas = WindexLocal::table(WindexLocal::TABLE_UMA)
    ->where('race_code', $race_code)
    ->orderBy('umaban')
    ->all();
foreach ($WindexUmas as $WindexUma) {
    $WindexUma->datalizeUma();
}
$Smarty->assign('WindexUmas', $WindexUmas);


// --------------------------------------------------
// ポイントでの購入

$CATEGORY_CONTENT_CANCEL = Point::CATEGORY_CONTENT_CANCEL;
$PointLogs = Point::from('point_origin')
    ->select(['point_origin.id AS oid', 'point_log.*', 'CANCEL.work AS reason'])
    ->where('point_origin.category', Point::CATEGORY_WINDEX_RACE)
    ->where('point_origin.identifier', $race_code)
    ->leftJoin(App::logDB('point_log'), 'point_log.origin_id=point_origin.id')
    ->leftJoin('point_origin AS CANCEL', "CANCEL.category='{$CATEGORY_CONTENT_CANCEL}' AND CANCEL.rid=point_origin.id")
    ->all();

if ($PointLogs) {
    $reason = $PointLogs[0]->json('reason', 'reason');
    $origin_id = $PointLogs[0]->oid;
}
$Smarty->assign('PointLogs', $PointLogs);
$Smarty->assign('reason', $reason ?? '');
$Smarty->assign('origin_id', $origin_id ?? 0);



$Smarty->displayCMS("windex_local/race.tpl");

