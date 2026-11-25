<?php

$Smarty = new SmartyCMSClass();
$Smarty->auth('WindexAdd', 'upload');

$tmp_dir = TMP_DIR . 'tmp';
if (!file_exists($tmp_dir)) {
    mkdir($tmp_dir, 0777, true);
}

$files = $_FILES ?? [];
$write = false;

// 更新の場合、一時保存したファイルを$_FILE形式に転換
if (isset($_POST['confirmed'])) {
    $write = true;
    foreach ($_POST as $keyword => $value) {
        if (str_contains($keyword, '-')) {
            list($key, $const) = explode('-', $keyword);
            $files[$key][$const] = $value;
        }
    }
}

$resources = [];
$date = date("Ymd.His");

foreach ($files as $key => $file) {
    if (!empty($file['tmp_name'])) {
        $name = $file['name'];

        $csv = file_get_contents($file['tmp_name']);
        $tmp_name = "{$tmp_dir}/{$key}.{$date}.csv";
        if (!$write) file_put_contents($tmp_name, $csv);


        $resources[$key] = [
            'key' => $key,
            'name' => $name,
            'tmp_name' => $tmp_name,
        ];

        try {

            if ($key === 'local_race') {
                $resources[$key]['data'] = WindexLocal::convToRace($csv, $write);
            }

            if ($key === 'local_uma') {
                $resources[$key]['data'] = WindexLocal::convToUma($csv, $write);
            }

            if ($key === 'local_windex') {
                $resources[$key]['data'] = WindexLocal::convToWindex($csv, $write);
            }

        } catch (Exception $e) {
            if ($e->getCode() === 0) {
                throw $e;
            } else {
                $resources[$key]['error'] = ['filename' => $name, 'type' => $key, 'message' => $e->getMessage()];
            }
        }

    }
}

////pre($files);
//pre($resources);
if (!empty(WindexLocal::$TargetYYYYMMDD)) {
    $target_day = WindexLocal::toDay(WindexLocal::$TargetYYYYMMDD);
    WindexLocal::getWindexDay($target_day);
}

if ($write) $Smarty->location("index?target_day={$target_day}");

$Smarty->assign('target_day', $target_day);
$Smarty->assign('resources', $resources);
$Smarty->displayCMS("windex_local/upload.tpl");


