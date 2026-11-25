<?php


(function () {
    $Smarty = new SmartyCMSClass();

    $days = Race::getWeekDays(time());
    $last_days = Race::getWeekDays(strtotime($days[0]) - 86400);
    $start_at = $Smarty->requestOrCookieAssign('start', $last_days[0], 3600);
    $end_at = $Smarty->requestOrCookieAssign('end', end($last_days), 3600);
    $endtime_at = Model::date(strtotime($end_at) + 86400);

    $logDB = App::logDB();

    $origins = Model::sql("
        SELECT count(*) AS cnt , ABS(premium_point+gift_point) AS point ,
               point_origin.category, point_origin.rid,  point_origin.identifier, point_origin.rid,
               content.title , content_author.name ,content_condition.race_code AS race_code, content_condition.work,
               uma_comment.registration AS comment_registration , uma_comment.race_code AS comment_race_code, uma_comment.work AS comment 
        FROM point_origin 
          LEFT JOIN content ON point_origin.category='content' AND content.posts_id=point_origin.rid
          LEFT JOIN content_condition ON content_condition.posts_id=content.posts_id
          LEFT JOIN content_author ON content_author.id=content_condition.author_id
            
          LEFT JOIN uma_comment ON point_origin.category='uma_comment' AND uma_comment.id=point_origin.rid
                    
          LEFT JOIN {$logDB}.point_log ON point_log.origin_id=point_origin.id
        WHERE kind=50 AND point_origin.created_at BETWEEN '{$start_at}' AND '{$endtime_at}'
        GROUP BY point_origin.id
");

    $list = [];
    $marks = ['◉', '◎', '◯', '▲', '★', '△1', '△2', '△3', '△'];
    foreach ($origins as $origin) {
        $rid = $origin['rid'];
        $title = $origin['title'];
        $registration = $origin['comment_registration'];
        $race_code = empty($origin['race_code']) ? $origin['comment_race_code'] : $origin['race_code'];

        if (empty($race_code)) $race_code = '';
        $identifier = $origin['identifier'];
        try {
            $work = json_decode($origin['work'], true);
            $payoff = $work['payoff'] ?? 0;
        } catch (Exception $e) {
            $payoff = 0;
        }

        if ($origin['category'] === Point::CATEGORY_WINDEX_RACE) {
            // --------------------------------------------------------------
            // Windex指数閲覧
            $pid = "レースコード:<br>{$identifier}";
            $title = 'WINDEX';
            $race_code = $identifier;
            $type = '指数閲覧';

        } elseif (($origin['category'] === Point::CATEGORY_PACK)) {
            // --------------------------------------------------------------
            // パック商品
            $pid = "パック商品ID: {$rid}";
            $type = Point::LICENSE_IDENTIFIERS[$identifier];
            $Pack = PointPack::first($rid);
            $title = $Pack->title;

        } elseif (isset(Point::LICENSE_IDENTIFIERS[$identifier])) {
            // --------------------------------------------------------------
            // 各種ライセンス購入

            if ($identifier === UmaComment::CATEGORY_SPOILER) {
                // スポイラー購入
                $pid = "コメントID: {$rid}";
            } else if ($identifier === UmaComment::CATEGORY_CHAT_COMMENT) {
                // チャットコメント購入
                $pid = "チャットID: {$rid}";
            } else {
                // 記事コンテンツ各種購入
                $pid = "記事ID: {$rid}";
            }
            $type = Point::LICENSE_IDENTIFIERS[$identifier];

        } elseif (str_contains($identifier, ContentCondition::LICENSE_MARK_N_)) {
            // --------------------------------------------------------------
            // 印購入
            $pid = "記事ID: {$rid}";
            $mark = str_replace(ContentCondition::LICENSE_MARK_N_, '', $identifier);
            $Mark = Model::table("content_mark")->select('registration')->where('posts_id', $origin['rid'])->where('mark', $mark)->first();
            $registration = $Mark->registration;
            $type = Point::LICENSE_IDENTIFIERS[ContentCondition::LICENSE_MARK_N_] . ($marks[$mark - 1] ?? '');

        } else {
            // --------------------------------------------------------------
            // 不明
            $pid = '';
            $type = '???';
        }

        $kaisai_code = substr($race_code, 0, 14);

        $data = [
            'pid' => $pid,
            'cnt' => $origin['cnt'],
            'point' => $origin['point'],
            'title' => $title,
            'identifier' => $identifier,
            'race_code' => $race_code,
            'kaisai_code' => $kaisai_code,
            'registration' => $registration,
            'type' => $type,
            'author' => $origin['name'],
            'payoff' => $payoff > 0 ? 1 : 0,
            'race' => '',
            'bamei' => '',
            'umaban' => '',
        ];
        $Race = Model::table("race_info")->select('info')->where('race_code', $race_code)->first();
        $Kaisai = Model::table("race_kaisai")->select('info')->where('kaisai_code', $kaisai_code)->first();
        if ($Race) {
            $kaisai_mei = $Kaisai ? $Kaisai->json('info', 'kaisaimei') : $kaisai_code;
            $data['race'] = $kaisai_mei . ' [' . $Race->json('info', 'race_bango') . ']' . $Race->json('info', 'kyosomei');
        }

        $Uma = Model::table("race_uma")->select(['umaban', 'info'])->where('race_code', $race_code)->where('registration', $registration)->first();
        if ($Uma) {
            $data['bamei'] = $Uma->json('info', 'bamei');
            $data['umaban'] = $Uma->umaban;
            if ($origin['category'] == Point::CATEGORY_UMA_COMMENT) {
                $data['title'] = '[' . $data['umaban'] . ']' . $data['bamei'];
            }
            $data['umaban'] = $Uma->umaban;
        }


        $list[] = $data;
    }


    $submit = $_REQUEST['submit'] ?? '';
    if ($submit === 'CSVダウンロード') {
        $filename = str_replace('/', '', "point-item-{$start_at}-{$end_at}.csv");
        $Smarty->outputCSV($filename, $list, ['pid', 'payoff', 'title', 'race', 'type', 'author', 'point', 'cnt']);
    } else {
        $Smarty->assign('list', $list);
        $Smarty->displayCMS("ranking/point.tpl");
    }


})();


