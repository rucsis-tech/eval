<?php

/**
 * アプリケーションとは独立したユーザー管理機構用API
 * ユーザー特定・アクセストークン取得
 */


use classes\auth\MyAuthClass;

App::checkInternal();
header("Content-Type: application/json; charset=utf-8");

(function () {
    $result = [];
    try {
        $client_id = $_REQUEST['client_id'] ?? '';
        if ($client_id != config('CLIENT_ID_AUTH', 'APP')) throw new Exception('client_id error');
        $client_secret = $_REQUEST['client_secret'] ?? '';
        if ($client_secret != config('CHANNEL_SECRET_AUTH', 'SECRET')) throw new Exception('client_secret error');

        $grant_type = $_REQUEST['grant_type'] ?? 'authorization_code';
        if ($grant_type == 'refresh_token') {
            $refresh_token = $_REQUEST['refresh_token'] ?? '';
            $result = MyAuthClass::getAccessTokenFromRefresh($refresh_token);
        } else {
            $code = $_REQUEST['code'] ?? '';
            if (str_contains($code, '..')) {
                // Mailで送られた6桁認証コード  "{$email}..{$verify_code}"　分解する
                $codes = explode('..', $code);
                $email = $codes[0] ?? '';
                $verify_code = $codes[1] ?? '';

                $result = MyAuthClass::getAccessTokenFromVerifyCode($email, $verify_code);

            } else {
                // HASH code から直接特定
                $result = MyAuthClass::getAccessTokenFromCode($code);
            }
        }

    } catch (Exception $e) {
        http_response_code($e->getCode() ?: 200);
        $result['error'] = [$e->getMessage(), $e->getTraceAsString()];
    }

    echo json_encode($result);

})();
