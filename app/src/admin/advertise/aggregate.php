<?php

$ApiAdmin = new ApiAdmin();
$ApiAdmin->action(function (ApiAdmin $API, $request) {

    function setEvent($list, $binds, $events) {
        foreach ($events as $event) {
            $index = $binds[$event['ad_code']] ?? null;
            if ($index !== null) {
                if ($event['type'] == 2) {
                    $list[$index]['empty_mail'] = $event['CNT'];
                } else if ($event['type'] == 3) {
                    $list[$index]['form_send'] = $event['CNT'];
                } else if ($event['type'] == 4) {
                    $list[$index]['reg_member'] = $event['CNT'];
                }
            }
        }
        return $list;
    }


    // --------------------------
    $API->auth('Aggregate');

    // ----------------------------------------------------------------------
    $term = $request['term'];
    $since = ($request['since'] ?? '2001-01-01') . ' 00:00:00';
    $until = ($request['until'] ?? '2099-01-01') . ' 23:59:59';

    if ($term == 0) {
        $WHERE = '1';
    } else {
        $WHERE = " created_at BETWEEN '$since' AND '$until' ";
    }
    // ----------------------------------------------------------------------
    // ADコード
    $list = Model::sql("SELECT ad_master.* FROM ad_master ");
    $binds = []; // ad_code と index の関連付け
    foreach ($list as $key => $ad) {
        $binds[$ad['ad_code']] = $key;
    }

    // ----------------------------------------------------------------------

    /*
      2 empty_mail
      3 form_send
      4 reg_member
      5 leave_member
    */

    $logDB = App::logDB();

    // 2 3 空メールとフォームメール送信者で　mail/ad ユニークで created_at が早い方を取り出し
    // を ad_code,type　でグループ化してカウント
    $sendEvents = Model::sql(
        "SELECT ad_code,type,count(*) AS CNT FROM {$logDB}.ad_log WHERE not exists (
                    SELECT 1 from {$logDB}.ad_log AS sub WHERE sub.type in (2,3) AND {$logDB}.ad_log.uniq = sub.uniq AND {$logDB}.ad_log.ad_code = sub.ad_code AND {$logDB}.ad_log.created_at > sub.created_at
                    )
                    AND {$logDB}.ad_log.type in (2,3) AND $WHERE                            
                GROUP BY ad_code,type
         ");
    $list = setEvent($list, $binds, $sendEvents);

    // ----------------------------------
    // 登録者
    $registerEvents = Model::sql(
        "SELECT ad_code,type,count(*) AS CNT FROM {$logDB}.ad_log 
                WHERE type = 4 AND NOT(uniq LIKE 'LINE%') AND $WHERE      
                GROUP BY ad_code,type
         ");
    $list = setEvent($list, $binds, $registerEvents);

    // ----------------------------------
    // LINE登録者
    $lineEvents = Model::sql(
        "SELECT ad_code,type,count(*) AS CNT FROM {$logDB}.ad_log 
                WHERE type = 4 AND uniq LIKE 'LINE%' AND $WHERE      
                GROUP BY ad_code,type
         ");
    foreach ($lineEvents as $event) {
        $index = $binds[$event['ad_code']] ?? null;
        if ($index !== null) {
            $list[$index]['reg_line'] = $event['CNT'];
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------
    // 売上
    if ($term == 0) {
        $WHERE = '1';
    } else {
        $WHERE = " payment_history.created_at BETWEEN '$since' AND '$until' ";
    }
    $sales = Model::sql("SELECT ad_code,sum(amount) AS SUM FROM member_master "
        . " INNER JOIN payment_history ON payment_history.uid = member_master.uid AND (NOT ISNULL(payment_history.purchased_at)) "
        . " WHERE $WHERE GROUP BY ad_code "
    );
    foreach ($sales as $sale) {
        $index = $binds[$sale['ad_code']] ?? null;
        if ($index !== null) {
            $list[$index]['ad_sales'] = $sale['SUM'];
            if (!empty($list[$index]['price'])) {
                $list[$index]['recov_rate'] = round($sale['SUM'] / $list[$index]['price'] * 100, 2);// 回収率
            }
        }
    }


    return [
        'list' => $list ?? [],
        'history' => $history ?? [],
        'sales' => $sales ?? [],
        'request' => $request,
    ];

});
