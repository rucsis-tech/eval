<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    // --------------------------
    $API->auth('Advertise');

    if (isset($request['type'])) {
        $id = $request['id'] ?? 0;

        switch ($request['type']) {
            case 'ADD':
                $API->auth('AdvertiseAdd');
                Model::into('ad_master')->insert([
                    'ad_code' => $request['ad_code'],
                    'media_code' => $request['media_code'],
                    'ad_child_code' => $request['ad_child_code'],
                    'media_id' => $request['media_id'],
                    'name' => $request['name'],
                    'ad_date' => $request['ad_date'],
                    'price' => $request['price'],
                    'notes' => $request['notes'],
                ]);
                break;
            case 'MOD':
                $API->auth('AdvertiseMod');
                Model::into('ad_master')
                    ->where('id', $id)
                    ->update([
                        'ad_code' => $request['ad_code'],
                        'media_code' => $request['media_code'],
                        'ad_child_code' => $request['ad_child_code'],
                        'media_id' => $request['media_id'],
                        'name' => $request['name'],
                        'ad_date' => $request['ad_date'],
                        'price' => $request['price'],
                        'notes' => $request['notes'],
                    ]);
                break;
                break;
            case 'DEL':
                $API->auth('AdvertiseDel');
                Model::from('ad_master')
                    ->where('id', $id)->update(['disable' => 1]);
                break;
        }
    }

    $list = Model::select(['ad_master.*', 'category'])
        ->from('ad_master')
        ->where('ad_master.disable', 0)
        ->join('ad_media_master', 'ad_media_master.id=ad_master.media_id')
        ->rows();


    return [
        'list' => $list ?? [],
        'request' => $request,
    ];

});
