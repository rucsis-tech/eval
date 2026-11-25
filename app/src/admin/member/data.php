<?php

// 絞り込み用各種情報の取得

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth('NarrowExec');
    $result = ['request' => $request];

    // ---------------------------------------------------------------------------------------------------------------
    // アンケート情報

    $enquete_id = $request['enquete_id'] ?? null;
    if ($enquete_id) {
        $Enquete = Enquete::first($enquete_id);
        if ($Enquete) $Enquete->datalize();
        $result['Enquete'] = $Enquete;
    }

    // ---------------------------------------------------------------------------------------------------------------
    // 記事著者情報

    if ($request['authors'] ?? null) {
        //$Authors = ContentAuthor::getDefaultQuery()->where('content_author.work','LIKE','%"attribute":"1"%')->all();
        //foreach ($Authors as $Author) $Author->datalize();
        $result['Authors'] = [];//$Authors;
    }


    return $result;

});