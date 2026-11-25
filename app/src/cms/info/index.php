<?php

$Smarty = new SmartyCMSClass();


// --------------------------------------------------
// 新規　
if (isset($_POST['insert'])) {
    $Smarty->auth('CmsAdd','info');
    $id = Info::insert([
        'type' => Info::TYPE_INFO,
        'title' => '新規'
    ]);
    $Smarty->location('/cms/info/edit?id=' . $id);
}
// --------------------------------------------------
// 削除
if (isset($_REQUEST['closed'])) {
    $Smarty->auth('CmsDel','info');
    $Info = Info::first($_REQUEST['closed']);
    $Info->save(['closed' => 1]);
}

// --------------------------------------------------
// 一覧表示
$Smarty->auth('CmsList','info');
$Infos = Info::where('type', Info::TYPE_INFO)
    ->orderBy('start_at', 'DESC')
    ->where('closed', 0)
    ->all();
foreach ($Infos as $Info) {
    $Info->datalizeInfo();
}

$Smarty->assign('Infos', $Infos);
$Smarty->displayCMS("info/index.tpl");

