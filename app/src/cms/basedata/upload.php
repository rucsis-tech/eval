<?php

$Smarty = new SmartyCMSClass();
$Smarty->action(function ($Smarty) {

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

                if ($key === 'basedata_uma') {
                    $resources[$key]['data'] = BaseData::convToUma($csv, $write);
                    if ($write) {
                        $list = $resources[$key]['data']['list'];
                        foreach ($list as $key => $item) {
                            $race_code = $item['race_code'];
                            $BaseRace = BaseData::from('basedata_race')->where('race_code', $race_code)->first();
                            if (empty($BaseRace)) BaseData::insert(['race_code' => $race_code], 'basedata_race');
                        }

                    }
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
    if (!empty(BaseData::$TargetYYYYMMDD)) {
        $target_day = BaseData::toDay(BaseData::$TargetYYYYMMDD);
        BaseData::getWindexDay($target_day);
    }

    if ($write) $Smarty->location("index?target_day={$target_day}");

    $Smarty->assign('target_day', '');
    $Smarty->assign('resources', $resources);
    $Smarty->displayCMS("basedata/upload.tpl");


});

