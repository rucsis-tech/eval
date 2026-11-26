<?php

// Windex地方版バッチアップロードAPI

use classes\ChatworkClass;

// テンポラリ領域
$tmp_dir = TMP_DIR . 'tmp';
if (!file_exists($tmp_dir)) mkdir($tmp_dir, 0777, true);

// 報告用　Chatwork　
if (App::isLocal()) {
    // Chatwork 送信しない
    $room_id = null;
} else if (App::isDevelop()) {
    // 開発 NEO開発
    $room_id = '164952859';
} else {
    // 本番 WINDEX地方版CSV納品用専用
    $room_id = '438927715';
}

$response = 'NG';
$files = $_FILES ?? [];
foreach ($files as $key => $file) {
    try {
        // APIキー認証
        if ($_POST['api_key'] != "03216549831032135633464642313") throw new Exception("Key Unauthorized");

        $name = $file['name'];
        $csv = file_get_contents($file['tmp_name']);

        if (str_contains($csv, 'レース名')) {
            BaseData::convToRace($csv, false); // 事前チェック
            BaseData::convToRace($csv, true); // 更新
            $response = 'OK';
        } else if (str_contains($csv, '着順')) {
            BaseData::convToUma($csv, false); // 事前チェック
            BaseData::convToUma($csv, true); // 更新
            $response = 'OK';
        } else if (str_contains($csv, '基礎')) {
            BaseData::convToWindex($csv, false); // 事前チェック
            BaseData::convToWindex($csv, true); // 更新
            $response = 'OK';
        } else {
            $response = "[{$name}] 判別できませんでした";
        }

        // Chatwork　にファイル添付
        if ($response == 'OK' && $room_id) {
            $Chatwork = new ChatworkClass(config('CHATWORK_TOKEN'));
            $Chatwork->postRoomsFile($room_id, '', $file['tmp_name'], $name);
        }

        // windex_local_day 作成
        if (!empty(BaseData::$TargetYYYYMMDD)) {
            $target_day = BaseData::toDay(BaseData::$TargetYYYYMMDD);
            BaseData::getWindexDay($target_day);
        }


    } catch (Throwable $e) {
        $response = $e->getMessage();
        AppLog::except($e);
        break;
    }
}

echo $response;




