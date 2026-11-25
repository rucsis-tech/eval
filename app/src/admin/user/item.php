<?php

use classes\auth\MyAuthClass;

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {


    // -------------------------------------------------------------------------------
    // 新規追加

    if ($request['mode'] == 'add') {
        $API->auth(Admin::UserAdd);

        $Admin = Admin::create($request['email'], $request['password']);
        $Admin->save([
            'admin_name' => $request['admin_name'],
            'role' => $request['role'],
            'tel' => $request['tel'],
        ]);
        $Admin->setDivisions($request['divisions']);

        return [
            'user' => $Admin,
        ];

    }

    $uid = $request['uid'];

    // -------------------------------------------------------------------------------
    // 情報取得
    if ($request['mode'] == 'detail') {
        $API->auth(Admin::UserList);

        $Admin = Admin::getByUID($uid);
        $divisions = $Admin->getDivisions();

        return [
            'user' => $Admin,
            'divisions' => $divisions,
            'request' => $request,
        ];
    }
    if ($request['mode'] == 'me') {
        // 自分の情報
        $API->auth(Admin::UserLogin);
        $divisions = $API->Admin->getDivisions();

        return [
            'user' => $API->Admin,
            'divisions' => $divisions,
            'request' => $request,
        ];
    }

    // -------------------------------------------------------------------------------
    // 更新
    if ($request['mode'] == 'update') {

        $API->auth(Admin::UserLogin);
        if ($uid !== $API->Admin->uid || isset($request['divisions'])) {
            $API->auth(Admin::UserUpdate);
        }

        $Admin = Admin::getByUID($uid);
        $old_email = $Admin->email;
        $new_email = $request['email'];
        // auth_base メルアド変更
        if ($old_email != $new_email) {
            $Exists = MyAuthClass::first('email', $new_email);
            if ($Exists) throw new Exception("既に存在します[$new_email]");

            User::from('auth_base')->where('email', $old_email)->update(['email' => $new_email]);
            User::from('user_master')->where('uid', $uid)->update(['email' => $new_email]);
        }
        // パスワード変更
        if (trim($request['password'])) {
            $Auth = \classes\auth\MyAuthClass::first('email', $new_email);
            $Auth->setPassword($request['password']);
        }
        $Admin->table('admin_condition')->where('uid', $Admin->uid)->update([
            'admin_name' => $request['admin_name'],
            'role' => $request['role'],
            'tel' => $request['tel'],
        ]);

        if (isset($request['divisions'])) {
            $Admin->setDivisions($request['divisions']);
        }

        $API->message = '更新しました';
        return [
            'user' => Admin::getByUID($uid),
        ];

    }
    // -------------------------------------------------------------------------------
    if ($request['mode'] == 'delete') {
        $API->auth(Admin::UserDelete);
        $Admin = Admin::getByUID($uid);
        User::where('uid', $uid)->update(['disabled_at' => User::date()]);
    }
    // -------------------------------------------------------------------------------
    if ($request['mode'] == 'reset') {
        $API->auth(Admin::UserReset);

    }


});