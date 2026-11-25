<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('Sales');

    // ------------------------------------------------------------------------------------------------------
    // 期間
    $term = $request['term'];
    if ($term == 'ALL') {
        $since = "2025-05-01";
        $until = date("Y-m-d", time());
    } else {
        $since = $request['since'];
        $until = Model::date(strtotime($request['until']) + 86400);
    }

    // ------------------------------------------------------------------------------------------------------
    // リスト抽出
    $category = $request['category'];
    if ($category == 'DAY') $category = KPI::CATEGORY_DAY;
    if ($category == 'WEEK') $category = KPI::CATEGORY_WEEK;
    if ($category == 'MONTH') $category = KPI::CATEGORY_MONTH;


    // 期間データ取得
    $query = KPI::where('category', $category)->where('created_at', '>=', $since)
        ->where('created_at', '<', $until);
    $Kpis = $query->all();

    // ------------------------------------------------------------------------------------------------------
    // グラフ用データ

    // 項目作成
    $list = [];
    foreach (KPI::GROUPS as $TYPE => $infos) {
        foreach ($infos as $kind => $info) {
            $list["{$TYPE}{$kind}"]=[];
        }
    }

    $labels = [];
    foreach ($Kpis as $Item) {
        $name = $Item->name;

        $list[$name][] = $Item->value;
        $day = $Item->created_at;
        if ($category == KPI::CATEGORY_MONTH) $day = substr($day, 0, 7);
        $labels[$day] = 1;
    }
    $datasets = [];
    foreach ($list as $name => $data) {
        $dataset = KPI::GROUPS[substr($name, 0, 3)][substr($name, 3, 3)];
        $dataset['data'] = $data;
        if ($dataset['type'] == 'bar') {
            $dataset['stack'] = $dataset['kind'];
            if (str_contains($dataset['label'], '付与数')) $dataset['stack'] = 'give';
        }

        $dataset['yAxisID'] = $dataset['kind'] . 'Chart';
        $datasets[$name] = $dataset;

    }


    return [
        'request' => $request,
        'since' => $since,
        'until' => $until,
        'list' => array_keys($list),
        'labels' => array_keys($labels),
        'datasets' => $datasets,
        'sql' => $query->getRequestQuery(),
    ];

});