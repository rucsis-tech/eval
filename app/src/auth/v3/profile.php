<?php
/**
 * アプリケーションとは独立したユーザー管理機構用API
 * ユーザー情報取得
 */

use classes\auth\MyAuthClass;

App::checkInternal();
header("Content-Type: application/json; charset=utf-8");


(function () {
    $result = [];
    try {

        if (isset($_REQUEST['access_token'])) {
            $Auth = MyAuthClass::getFromToken($_REQUEST['access_token']);
            $result = $Auth->getProfile();

        } else {
            throw new Exception('no access token');
        }

    } catch (Exception $e) {
        http_response_code($e->getCode() ?: 200);
        $result['error'] = true;
        $result['message'] = $e->getMessage();
    }

    echo json_encode($result);
})();


