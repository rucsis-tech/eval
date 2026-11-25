<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $API->auth('GivePoints');

    $reason = $request['reason'];
    $point = $request['point'];
    $days = $request['days'];
    $origin_count = Point::sql('SELECT MAX(id) AS id FROM point_origin');
    $origin_count = $origin_count[0]['id'];
    $origin_id = Point::giftOrigin($origin_count + 1, "{$point}-{$days}", $reason);

    $success = [];
    foreach ($request['members'] as $member) {
        try {
            Point::begin();
            Point::give($member['uid'], $origin_id, $point, $days);
            Point::commit();
            $success[] = $member['uid'];
        } catch (Exception $e) {
            Point::rollback();
        }
    }

    return ['success' => $success];

});