<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    $type = $request['type'] ?? '';

    if ($type == 'ADD') {
        // 追加登録
        $API->auth('MailAdd', $request);
        MailBody::add($request['mail_name'], $request['mail_subject'], $request['mail_text'], $request['mail_notes']);

    } else if ($type == 'MOD') {
        // 更新
        $API->auth('MailMod', $request);
        MailBody::modify($request['id'], $request['mail_name'], $request['mail_subject'], $request['mail_text'], $request['mail_notes']);
    } else if ($type == 'TEST') {
        // TEST送信
        $API->auth('MailMod', $request);

        // 宛先会員一覧を取得
        $MAX = 10;
        $Preset = Focus::first($request['preset_id']);
        $list = MemberSearch::distributeQuery($Preset->getCondition())->rows();
        if (count($list) > $MAX) throw new Exception("{$MAX}名までしか送信できません。テスト配信先が多すぎます " . count($list) . '名');
        // 配信用配列を作成
        $toList = MailRucsisClass::makeToList($list);
        // 配信
        $Mail = new MailRucsisClass();
        $Mail->setFrom('magazine@winsight.jp', 'Winsight');
        $res = $Mail->sender($toList, $request['mail_subject'], $request['mail_text'], null);

        return ['res' => $res ?? [], 'list' => $list];

    } else if ($type == 'DEL') {
        // 無効化
        $API->auth('MailDel', $request);
        MailBody::disable($request['id']);

    } else {
        // リスト抽出
        $API->auth('Mail', $request);

        $since = $request['since'];
        $until = $request['until'] . " 23:59:59";

        $query = MailBody::select(['distinct distribute_mail_body.*', 'B0.status AS status0', 'B1.status AS status1', 'B2.status AS status2'])
            ->where('distribute_mail_body.disable', 0)
            ->where('distribute_mail_body.updated_at', 'BETWEEN', [$since, $until])
            ->leftJoin('distribute_book AS B0', 'B0.mail_id=distribute_mail_body.id AND B0.status=0')
            ->leftJoin('distribute_book AS B1', 'B1.mail_id=distribute_mail_body.id AND B1.status=1')
            ->leftJoin('distribute_book AS B2', 'B2.mail_id=distribute_mail_body.id AND B2.status=2');


        $list = $query->rows();
    }


    return [
        'mailList' => $list ?? [],
        'query' => isset($query) ? $query->getRequestQuery() : [],
    ];

});