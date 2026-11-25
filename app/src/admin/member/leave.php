<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $API->auth('MemberMod');

    $uid = $request['uid'];

    try {
        User::begin();
        User::lock($uid);
        User::commit();

    } catch (Exception $e) {
        User::rollback();
        throw $e;
    }


    $Member = Member::getDetail($uid);
    return [
        'member' => $Member ?? null,
        'request' => $request,
    ];


});