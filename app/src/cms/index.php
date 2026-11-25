<?php

$Smarty = new SmartyCMSClass();

$Errors = [];
if ($Smarty->Admin->developer === User::DEVELOPER_ADMIN) {
    $Errors = ModelLog::from(App::logDB('error_log'))->orderBy('id', 'DESC')->limit(10)->all();
}

$Smarty->assign('Errors', $Errors);
$Smarty->displayCMS('common/top.tpl');

