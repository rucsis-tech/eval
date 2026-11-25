<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $API->auth(Admin::MemberList);

    $query = MemberSearch::narrow2($request);
    $list = $query->rows();

    return [
        'list' => $list ?? [],
        'request' => $request,
        'query' => isset($query) ? $query->getRequestQuery() : '',
    ];


});