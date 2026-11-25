<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth(Admin::UserList);

    $userList = Admin::defaultSelect()
        ->select(['user_master.*', 'admin_condition.*', 'user_auth.email'])
        ->from('user_master')
        ->where('developer', '>', User::DEVELOPER_NONE)
        ->where('disabled_at IS NULL')
        ->leftJoin('admin_condition', 'admin_condition.uid=user_master.uid')
        ->leftJoin('user_auth', 'user_auth.uid=user_master.uid')
        ->rows();

    /*
        foreach ($userList as $user) {
            if ($user['admin_name']===null) {
                // 新規 admin_condition
                Admin::insert([
                    'uid' => $user['uid'],
                    'admin_name' => $user['email'],
                    'role' => '',
                    'tel' => '',
                ], 'admin_condition');
            }
        }
    */

    return [
        'userList' => $userList,
        'request' => $request,
    ];


});