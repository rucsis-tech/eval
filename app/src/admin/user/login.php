<?php

use classes\auth\AuthMailClass;
use classes\auth\MyAuthClass;
use classes\SessionClass;

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    // --------------------------
    // 通常登録

    if (empty($request['email']) || empty($request['password'])) {
        throw new Exception('メールアドレス・パスワードがありません');
    }

    $code = MyAuthClass::checkin($request['email'], $request['password']);
    if (empty($code)) {
        throw new Exception('ログイン失敗:' . $request['email']);
    }


    $state = AuthMailClass::getLoginState();
    $session = SessionClass::auth($state, $code);
    $guid = SessionClass::$GUID;
    if (!$guid) {
        throw new Exception("ログインtoken取得失敗" . json_encode($session));
    }
    $User = User::getLoginUser();
    //$API->auth(Admin::UserLogin);

    $Response = (object)[
        'guid' => $guid,
        'token' => SessionClass::getToken(),
    ];
    $Response->userInfo = Admin::getByGUID($guid);
    if (empty($Response->userInfo)) {
        throw new Exception("ログインuser取得失敗[$guid]");
    }
    if ($Response->userInfo->disabled_at != null) {
        throw new Exception("抹消アカウントです[$guid]" . $Response->userInfo->disabled_at);
    }

    $API->status = 'OK';
    $API->message = 'ログインしました';

    // ログイン日時
    Admin::table('admin_condition')
        ->where('uid', $Response->userInfo->uid)
        ->update(['login_at' => User::date()]);

    // 定数情報
    $Response->divisions = $Response->userInfo->getDivisions();
    $Response->authDefine = Admin::DIVISIONS;
    $Response->consts = [
        'build' => filemtime(SYS_ROOT . '/admin/dist/index.html'),
        'flags' => [
            'segment' => MemberFlag::SEGMENT_MAX,
            'count' => MemberFlag::SEGMENT_MAX * 32,
        ],
        'ranks' => Member::RANKS,
        'score_targets' => App::SCORE_TARGETS
    ];


    return $Response;

});
