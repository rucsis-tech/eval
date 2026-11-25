<?php

use classes\SessionClass;

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $guid = SessionClass::$GUID;
    if (!$guid) {
        throw new Exception('ログインtoken取得失敗');
    }
    $Response = (object)[
        'token' => SessionClass::getToken()
    ];
    $API->status = 'OK';
    $API->message = 'ログインしました';

    $Response->userInfo = Admin::getByGUID($guid);
    $Response->divisions = $Response->userInfo->getDivisions();
    $Response->authDefine = Admin::DIVISIONS;
    $Response->consts = [
        'build' => filemtime(SYS_ROOT . '/admin/dist/index.html'),
        'flags' => [
            'segment' => MemberFlag::SEGMENT_MAX,
            'count' => MemberFlag::SEGMENT_MAX * 32,
        ],
    ];

    return $Response;
});
