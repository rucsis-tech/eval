<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    // 会員リストのチェックデータ更新

    if ($request['mode'] == 'ADD') {
        $API->auth('NarrowCheckAdd');
    } else {
        $API->auth('NarrowCheckDel');
    }


    $flags = [];
    for ($seg = 1; $seg <= MemberFlag::SEGMENT_MAX; $seg++) {
        $flags[$seg] = bindec($request["check$seg"]);
    }

    foreach ($request['uidList'] as $uid) {
        for ($seg = 1; $seg <= MemberFlag::SEGMENT_MAX; $seg++) {
            if ($request['mode'] == 'ADD') {
                MemberFlag::orFlag($uid, $seg, $flags[$seg]);
            } else {
                MemberFlag::subFlag($uid, $seg, $flags[$seg]);
            }
        }

    }

    return ['request' => $request,];

});
