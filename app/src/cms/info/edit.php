<?php

$Smarty = new SmartyCMSClass();
$Smarty->action(function ($Smarty) {
    // --------------------------------------------------
    // 対象アイテム
    $id = $_REQUEST['id'] ?? 0;
    $Info = Info::where('type', Info::TYPE_INFO)
        ->where('id', $id)
        ->first();
    if (empty($Info)) $Smarty->location('index');


    // --------------------------------------------------
    // 更新
    if (isset($_POST['id'])) {
        $Smarty->auth('CmsMod', 'info');
        $Info->store($_POST);
        $Smarty->location('/cms/info/index');
    }

    // --------------------------------------------------
    // 表示
    $Info->datalize();
    $Smarty->assign('Info', $Info);
    $Smarty->displayCMS("info/edit.tpl");

});




