<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $logDB = App::logDB();

    $API->auth(Admin::UserHistory);

    if (isset($request['log_id'])) {
        $log = Model::from("{$logDB}.admin_log")
            ->where("{$logDB}.admin_log.id", $request['log_id'])
            ->leftJoin('user_master', "user_master.uid={$logDB}.admin_log.operate_id")
            ->row();
        return [
            'log' => $log,
            'request' => $request,
        ];

    } else {

        $query = User::select(['admin_log.*', 'name'])
            ->from("{$logDB}.admin_log")
            ->join('user_master', 'user_master.uid=admin_log.uid')
            ->where('admin_log.created_at', 'BETWEEN', [$request['since'], $request['until'] . ' 23:59:59']);
        if (!empty($request['user_name'])) {
            if (intval($request['user_name'])) {
                $query->where('admin_log.uid', $request['user_name']);
            } else {
                $query->where('user_name', $request['user_name']);
            }
        }
        if (!empty($request['division'])) {
            $query->where('division', $request['division']);
        }
        $userList = $query->rows();

        return [
            'HistoryList' => $userList,
            'request' => $request,
        ];

    }

});