<?php

use classes\SessionClass;

(new ApiApp())->action(function (ApiApp $API, $request) {

    $API->auth('WindexMod', 'review point');

    $result = ['request' => $request];

    $target = $request['target'];

    //-------------------------------------------------------------------------
    //

    if ($target == 'race') {
        $race_code = $request['race_code'];
        $status = $request['status'];
        $point = $request['point'];

        $WindexRace = WindexLocal::where('race_code', $race_code)->first();
        $WindexRace->save(['status' => $status, 'need_points' => $point]);
    }

    return $result;
});

