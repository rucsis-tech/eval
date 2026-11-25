<?php
$Smarty = new ApiApp();

$response = ['POST' => $_POST, 'FILES' => $_FILES];
if (!empty($_FILES['file'])) {//送信ボタンが押された場合
    try {
        $tmp = uniqid(mt_rand(), true);//ファイル名をユニーク化
        $tmp .= '.' . substr(strrchr($_FILES['file']['name'], '.'), 1);//アップロードされたファイルの拡張子を取得

        $response['tmp'] = $tmp;
        $response['origin'] = $_FILES['file']['name'];

        if (!empty($_FILES['file']['tmp_name'])) {
            if (!file_exists(APP_ROOT . 'htdocs/images/tmp')) {
                mkdir(APP_ROOT . 'htdocs/images/tmp/', 0777, true);
            }
            move_uploaded_file($_FILES['file']['tmp_name'], APP_ROOT . 'htdocs/images/tmp/' . $tmp);//imagesディレクトリにファイル保存
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
}


echo json_encode($response);