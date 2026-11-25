<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    // --------------------------

    if (isset($request['type'])) {
        $id = $request['id'] ?? 0;

        switch ($request['type']) {
            case 'ADD':
                $API->auth('MediaAdd');
                $id = Model::into('ad_media_master')->insert([
                    'code' => $request['code'],
                    'name' => $request['name'],
                    'category' => $request['category'],
                    'url' => $request['url'],
                    'note' => $request['note'],
                ]);
                break;
            case 'MOD':
                $API->auth('MediaMod');
                Model::into('ad_media_master')
                    ->where('id', $id)
                    ->update([
                        'code' => $request['code'],
                        'name' => $request['name'],
                        'category' => $request['category'],
                        'url' => $request['url'],
                        'note' => $request['note'],
                    ]);
                break;
                break;
            case 'DEL':
                $API->auth('MediaDel');
                Model::from('ad_media_master')
                    ->where('id', $id)->update(['disable' => 1]);
                break;
        }
    }

    $API->auth('Media');
    $list = Model::from('ad_media_master')
        ->where('disable', 0)
        ->rows();


    return [
        'list' => $list ?? [],
        'request' => $request,
    ];

});
