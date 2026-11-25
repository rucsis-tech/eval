<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    // フラグチェックボックス情報

    $sql = '';

    // 更新
    if (isset($request['names'])) {
        $API->auth('CheckEdit', $request);
        foreach ($request['names'] as $id => $name) {
            Model::insertOnDuplicateUpdate(['id' => $id, 'name' => $name], 'flag_master');
        }
    }

    // 空配列作成
    $list = [];
    for ($index = 1; $index <= MemberFlag::SEGMENT_MAX * 32; $index++) {
        $list[] = [
            'check_num' => $index,
            'name' => '',
        ];
    }
    // 取得書き込み
    $names = Model::from('flag_master')->orderBy('id', 'ASC')->rows();
    foreach ($names as $value) {
        $check_num = $value['id'];
        $list[$check_num - 1] = [
            'check_num' => $check_num,
            'name' => $value['name'],
        ];
    }

    return [
        'check_names' => $list ?? [],
        'sql' => $sql
    ];

});
