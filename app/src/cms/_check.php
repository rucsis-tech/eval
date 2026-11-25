<?php

use classes\SessionClass;

$Smarty = new ApiApp();

$Smarty->action(function ($Smarty, $payload) {
    // ログインしているか確認
    $Admin = Admin::getByGUID(SessionClass::$GUID);
    if (empty($Admin)) {
        throw new Exception("現在ログインしていません", 401);
    }
    return ['Admin' => $Admin];
});


