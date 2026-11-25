<?php

$Smarty = new SmartyCMSClass();

// ID指定
$id = $_REQUEST['id'] ?? '';
$Item = Enquete::getDefaultQuery()->first('id',$id);
if (empty($Item)) $Smarty->location('index.php');

// --------------------------------------------------
// 更新

$jsonData = $Smarty->getPayload();
if ($jsonData) {
    $Smarty->auth('ContentMod','Enquete');
    $Item->store($jsonData);
    $Smarty->location('/cms/everyone_setting/index');
}

// --------------------------------------------------
// 表示



$Item->datalize();


$Smarty->assign('EnqueteMaster', $Item);


$Smarty->displayCMS("enquete/edit.tpl");
