<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $type = $request['type'] ?? '';

    if ($type == 'ADD') {
        // 追加登録
        $API->auth('DistributeAdd', $request);
        Distribute::add($request['name'], $request['focus_preset_id'], $request['mail_id'], $request['distribute_at']);

    } else if ($type == 'MOD') {
        $API->auth('DistributeMod', $request);
        Distribute::modify($request['id'], $request['name'], $request['focus_preset_id'], $request['mail_id'], $request['distribute_at']);

    } else if ($type == 'DEL') {
        // キャンセル
        $API->auth('DistributeDel', $request);
        Distribute::cancel($request['id']);

    } else {
        // リスト抽出
        $API->auth('Distribute', $request);

        $since = $request['since'];
        $until = $request['until'] . " 23:59:59";

        $query = Distribute::select([
            'distribute_book.*',
            'distribute_mail_body.name AS mail_name',
            'distribute_mail_body.subject AS mail_subject',
            'distribute_mail_body.body AS mail_body',
            'focus_preset.name AS preset_name'
        ])
            ->where('distribute_book.disable', 0)
            ->where('distribute_book.updated_at', 'BETWEEN', [$since, $until])
            ->leftJoin('distribute_mail_body', 'distribute_mail_body.id=distribute_book.mail_id')
            ->leftJoin('focus_preset', 'focus_preset.id=distribute_book.focus_preset_id');

        /*
        // 指定フィルタ
        if (!empty($request['uid'])) {
            $query->where('payment_history.uid', $request['uid']);
        }
        if (!empty($request['slip'])) {
            $query->where('payment_history.slip', $request['slip']);
        }
        */

        $list = $query->rows();
    }


    return [
        'distributeList' => $list ?? [],
    ];

});