<?php

$Smarty = new SmartyCMSClass();
$Smarty->action(function ($Smarty) {

    // --------------------------------------------------
    // 対象アイテム
    $id = $_REQUEST['id'] ?? 0;
    $Topic = Info::where('type', Info::TYPE_NEWS)
        ->where('id', $id)
        ->first();
    if (empty($Topic)) $Smarty->location('index');


    // --------------------------------------------------
    // 更新
    if (isset($_POST['id'])) {
        $Smarty->auth('CmsMod', 'news');
        $Topic->store($_POST);
        $Smarty->location('/cms/news/index');
    }

    // --------------------------------------------------
    // 表示
    $Topic->datalize();
    $Smarty->assign('Topic', $Topic);
    $Smarty->displayCMS("news/edit.tpl");

});
