<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $API->auth('GiveScore');

    $score = $request['score'];
    $rid = $request['rid'];

    $success = [];
    foreach ($request['members'] as $member) {
        try {
            $uid = $member['uid'];
            Score::begin();
            Score::add($uid, App::TARGET_CAMPAIGN, $rid, $score);
            Score::commit();
            $success[] = $member['uid'];
        } catch (Exception $e) {
            Point::rollback();
        }
    }

    return ['success' => $success];

});