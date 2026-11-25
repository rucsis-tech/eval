<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {
    $API->auth('MemberMod');
    $json = [];
    $uid = $request['uid'];
    $User = User::where('uid', $request['uid'])->first();
    $Member = User::from('member_master')->where('uid', $request['uid'])->first();
    $Distribute = User::from('member_distribute')->where('uid', $request['uid'])->first();
    $Condition = User::from('member_condition')->where('uid', $request['uid'])->first();
    $State = User::from('member_state')->where('uid', $request['uid'])->first();

    $new_mail = $request['email'];
    $Exists = User::table('user_auth')
        ->where('email', $new_mail)
        ->where('uid', '<>', $User->uid)
        ->first();
    if ($Exists) {
        echo json_encode([
            'status' => 'NG',
            'message' => "既に存在します($new_mail)"
        ]);
        exit;
    }

    //if ($User->mail != $new_mail) {
    //    $json = file_post_contents(AUTH_API_URL . 'v2/update.php', ['mail' => $User->mail, 'new_mail' => $new_mail]);
    //    $User->save(['mail' => $new_mail,]);
    //}

    $Member->save([
        'nickname' => $request['nickname'],
        //   'tel' => $request['tel'],
        'ad_code' => $request['ad_code'],
        //   'pref' => $request['pref'],
        //   'city' => $request['city'],
        //   'zip' => $request['zip'],
        //   'staff_flag' => $request['staff_flag'],
    ], 'member_master');

    $Distribute->save([
        'mail_status' => $request['mail_status'],
    ], 'member_distribute');

    $Condition->save([
        'notes1' => $request['notes1'],
        'notes2' => $request['notes2'],
        'notes3' => $request['notes3'],
        'staff_flag' => $request['staff_flag'],
        //    'bonus_priority' => $request['bonus_priority'],
    ], 'member_condition');

    // デバッグ用サブスク
    if ($State->subscription_id <= 0) {
        $subscription_id = empty($request['debug_subscription']) ? 0 : -1;
        Member::table('member_state')->where('uid', $uid)->update(['subscription_id' => $subscription_id]);
    }


    for ($seg = 1; $seg <= MemberFlag::SEGMENT_MAX; $seg++) {
        MemberFlag::setFlag($uid, $seg, bindec($request["check$seg"]));
    }

    $Member = Member::getDetail($User->uid);

    return [
        'member' => $Member ?? null,
        'request' => $request,
        'json' => $json,
    ];

});
