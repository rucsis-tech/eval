<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    // 更新
    if (!empty($request['conditionRoot'])) {
        $API->auth('PresetAdd', $request);
        $id = $request['id'];
        $name = $request['name'];
        $condition = json_encode($request['conditionRoot']);
        if ($id) {
            Model::from('focus_preset')->where('id', $id)->update(['name' => $name, 'condition' => $condition]);
        } else {
            Model::insert(['name' => $name, 'condition' => $condition], 'focus_preset');
        }
    }

    // 非表示
    if (!empty($request['disable_preset_id'])) {
        $API->auth('PresetDel', $request);
        $disable_preset_id = $request['disable_preset_id'];
        Model::from('focus_preset')->where('id', $disable_preset_id)->update(['disable' => 1]);
    }

    // 取得
    $sets = Model::from('focus_preset')->where('disable', 0)->orderBy('id', 'ASC')->rows();
    $list = [];
    foreach ($sets as $id => $value) {
        $list[] = [
            'id' => $value['id'],
            'name' => $value['name'],
            'condition' => MemberSearch::convertNewCondition(json_decode($value['condition'], true)),
        ];
    }


    return [
        'presetList' => $list ?? [],
        'request' => $request,
    ];

});
