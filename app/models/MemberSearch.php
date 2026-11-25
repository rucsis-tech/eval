<?php

use classes\DBQueryClass;

class MemberSearch {

    protected static string $tableName = 'member_master';

    public static array $CONDITIONS = [];

    /**
     * 旧形式を新形式に変換する
     * @param $request
     * @return array $array
     */

    public static function convertNewCondition($request): array {
        // 新形式分
        if (isset($request['combination'])) {
            $condition = [
                'key' => $request['key'] ?? 'BIND',
                'combination' => $request['combination'],
                'conditions' => $request['conditions'],
            ];
        } else {
            $condition = [
                'key' => 'BIND',
                'combination' => 'AND',
                'conditions' => []
            ];
        }

        // 旧形式分
        foreach ($request as $key => $item) {
            if (is_array($item) && $key !== 'conditions') {
                $item['key'] = $key;
                $condition['conditions'][] = $item;
            }
        }
        $condition = self::checkReclusive($condition);
        return $condition;
    }

    /**
     * Enableフラグのチェック
     * @param $condition
     * @return array
     */
    private static function checkReclusive($condition): array {

        if (!isset($condition['enabled'])) {
            $condition['enabled'] = true;
        }

        if (isset($condition['conditions'])) {
            foreach ($condition['conditions'] as $key => $item) {
                $condition['conditions'][$key] = self::checkReclusive($item);
            }
        }
        return $condition;
    }

    public static function narrow2($request): DBQueryClass {
        // 基本クエリ
        $query = Member::select(['DISTINCT',
            'member_master.*',
            'member_master.id AS mid',
            'user_master.*',
            'mail_status',
            'member_condition.accessed_at', 'member_condition.locked_at', 'member_condition.staff_flag'
        ])
            ->orderBy('user_master.created_at', 'ASC')
            ->join('user_master', 'user_master.uid=member_master.uid')
            ->leftJoin('member_state', 'member_state.uid=member_master.uid')
            ->leftJoin('member_condition', 'member_condition.uid=member_master.uid')
            ->leftJoin('member_distribute', 'member_distribute.uid=member_master.uid');

        $array = self::convertNewCondition($request);
        $conditions = self::condition($array['conditions'], 0);
        //var_dump($conditions);

        self::$CONDITIONS = $conditions;

        foreach ($conditions['wheres'] as $where) {
            $query->where($where[0], $where[1]);
        }
        foreach ($conditions['joins'] as $key => $join) {
            $query->leftJoin($key, $join);
        }

        return $query;
    }

    /**
     * @param $condition string  'AND' or 'OR'
     * @param $array
     * @return array
     * @throws Exception
     */
    private static function condition($array, $uniq = 0): array {
        $wheres = [];
        $joins = [];

        foreach ($array as $item) {
            $uniq++;
            if (empty($item['enabled'])) continue;
            $key = $item['key'];
            if ($key === 'BIND') {
                $combination = $item['combination'];
                $conditions = self::condition($item['conditions'], $uniq);
                $uniq = $conditions['uniq'];
                $wheres[] = [[$combination, $conditions['wheres']], ''];
                foreach ($conditions['joins'] as $key => $join) {
                    $joins[$key] = $join;
                }
                continue;
            }

            $not_option = ($item['mode'] == 2) ? 'NOT' : '';

            // ---------------------------
            // 有効パラメータの存在確認
            unset($value);
            unset($sub);
            unset($term);
            unset($min);
            unset($max);

            $param = false;
            if (array_key_exists('check1', $item)) {
                $param = true;
            }
            if (array_key_exists('value', $item)) {
                $value = $item['value'];
                $param = true;
            }
            if (array_key_exists('sub', $item)) {
                $sub = $item['sub'];
                $param = true;
            }
            if (array_key_exists('term', $item)) {
                $term = $item['term'];
                if (!empty($term[0]) || !empty($term[1])) $param = true;
                if (count($term) === 1) $term[1] = null;
                // クライアントからUTC文字列で来るので変換をかける
                if (!empty($term[0])) $term[0] = date('Y-m-d H:i:s', (strtotime($term[0])));
                if (!empty($term[1])) $term[1] = date('Y-m-d H:i:s', (strtotime($term[1])));
            }
            if (array_key_exists('min', $item)) {
                $min = $item['min'];
                $max = $item['max'];
                if ($min !== null || $max !== null) $param = true;
            }

            if (!$param) continue;
            // ---------------------------

            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            // 条件１
            //-----------------------------------------------------------------------------------------------------------------------------------------------------

            if ($key == 'uid') {
                if (is_array($value)) {
                    $wheres[] = [['member_master.uid', 'in', $value], $not_option];
                } else if ($value) {
                    $wheres[] = [['member_master.uid', '=', $value], $not_option];
                } else {
                    $wheres[] = [['member_master.uid', 'BETWEEN', [$min, $max]], $not_option];
                }
            }

            if ($key == 'uid_list') {
                if (empty($value)) {
                } else {
                    $value = trim($value);
                    $value = str_replace("\t", ",", $value);
                    $value = str_replace("\n", ",", $value);
                    $value = str_replace(" ", ",", $value);
                    $values = array_filter(explode(',', $value));

                    $wheres[] = [["member_master.uid", "IN", $values], $not_option];
                }
            }

            if ($key == 'mid') {
                if (isset($value)) $wheres[] = [['member_master.id', $value], $not_option];
                else $wheres[] = [['member_master.id', 'BETWEEN', [$min, $max]], $not_option];
            }

            if ($key == 'staff_flag') {
                // そもそも択一の条件ではない
                if ($value == 0) {
                    // 一般会員: スタッフではない会員 かつ 退会していない
                    $wheres[] = [["member_condition.staff_flag=0 AND member_condition.locked_at IS NULL"], $not_option];
                }
                if ($value == 1) {
                    // スタッフ: スタッフフラグがONの会員
                    $wheres[] = [["member_condition.staff_flag=1"], $not_option];
                }
                if ($value == 2) {
                    // 退会者: スタッフではない会員 かつ 退会している
                    $wheres[] = [["member_condition.staff_flag=0 AND member_condition.locked_at IS NOT NULL"], $not_option];
                }
            }

            if ($key == 'created_at') {
                $wheres[] = [['user_master.created_at', 'BETWEEN', $term], $not_option];
            }
            if ($key == 'accessed_at') {
                $wheres[] = [['member_condition.accessed_at', 'BETWEEN', $term], $not_option];
            }
            if ($key == 'last_login_at') {
                $wheres[] = [['member_condition.last_login_at', 'BETWEEN', $term], $not_option];
            }
            if ($key == 'locked_at') {
                $wheres[] = [['member_condition.locked_at', 'BETWEEN', $term], $not_option];
            }


            if ($key == 'email') {
                if (empty($value)) {
                    $wheres[] = [["member_master.email = ''"], $not_option];
                } else {
                    $wheres[] = [["member_master.email LIKE '%$value%'"], $not_option];
                }
            }
            if ($key == 'nickname') {
                if (empty($value)) {
                    $wheres[] = [["member_master.nickname = ''"], $not_option];
                } else {
                    $wheres[] = [["member_master.nickname LIKE '%$value%'"], $not_option];
                }
            }
            if ($key == 'code') {
                $wheres[] = [["member_master.code = '$value'"], $not_option];
            }


            if ($key == 'mail_status') {
                $wheres[] = [['member_distribute.mail_status', '=', $item['value']], $not_option];
            }
            if ($key == 'mail_accept') {
                $wheres[] = [['member_distribute.accept', '=', $item['value']], $not_option];
            }

            if ($key == 'ad_code') {
                $wheres[] = [['member_master.ad_code', '=', $value], $not_option];
            }

            if ($key == 'notes1') {
                $wheres[] = [["member_condition.notes1 LIKE '%$value%'"], $not_option];
            }
            if ($key == 'notes2') {
                $wheres[] = [["member_condition.notes2 LIKE '%$value%'"], $not_option];
            }
            if ($key == 'notes3') {
                $wheres[] = [["member_condition.notes3 LIKE '%$value%'"], $not_option];
            }

            // アンケート回答者
            if ($key == 'enquete') {
                $ENQ_TABLE = App::logDB('enquete');
                $UNQ_ANSWER = "UNQ_ANSWER_{$uniq}";
                // 指定があれば分割
                $values = explode('-', $value);
                $eid = $values[0];
                $AND_qid = (count($values) > 1) ? " AND {$UNQ_ANSWER}.question_id={$values[1]} " : '';
                $AND_result = (count($values) > 2) ? " AND {$UNQ_ANSWER}.result={$values[2]} " : '';

                $joins["$ENQ_TABLE AS {$UNQ_ANSWER}"] = "{$UNQ_ANSWER}.uid=member_master.uid AND {$UNQ_ANSWER}.enquete_id={$eid} {$AND_qid} {$AND_result}";
                if (empty($not_option))
                    $wheres[] = [["{$UNQ_ANSWER}.enquete_id", '=', $eid],''];
                else
                    $wheres[] = [["{$UNQ_ANSWER}.enquete_id IS NULL"],''];

            }


            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            // 条件２
            //-----------------------------------------------------------------------------------------------------------------------------------------------------

            // 購入
            if ($key == 'purchased_at' || $key == 'paymented_at') {
                $wheres[] = [['member_condition.paymented_at', 'BETWEEN', $term], $not_option];
            }
            if ($key == 'purchased_count') { // 購入回数
                $PRF_PC_CNT = "PRF_PC_CNT_{$uniq}";
                $joins["member_profile AS {$PRF_PC_CNT}"] = "{$PRF_PC_CNT}.uid=member_master.uid AND {$PRF_PC_CNT}.category='" . MemberProfile::CATEGORY_PURCHASED_COUNT . "'";
                $wheres[] = [["{$PRF_PC_CNT}.value", 'BETWEEN', [$min, $max]], $not_option];
            }
            if ($key == 'payment_sum') { // 購入総額
                $PRF_PAY_SUM = "PRF_PAY_SUM_{$uniq}";
                $joins["member_profile AS {$PRF_PAY_SUM}"] = "{$PRF_PAY_SUM}.uid=member_master.uid AND {$PRF_PAY_SUM}.category='" . MemberProfile::CATEGORY_PAYMENT_SUM . "'";
                $wheres[] = [["{$PRF_PAY_SUM}.value", 'BETWEEN', [$min, $max]], $not_option];
            }


            if ($key == 'payment_point') { // ポイント購入回数
                $WHERE_SINCE = !empty($term[0]) ? " AND created_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
                $WHERE_UNTIL = !empty($term[1]) ? " AND created_at<='" . date("Y-m-d H:i:s", strtotime($term[1])) . "' " : '';  // 終了日指定

                $COUNT_SELECT = "SELECT count(*) AS CNT , PP.uid FROM point_purchased AS PP WHERE PP.type=0 {$WHERE_SINCE} {$WHERE_UNTIL} GROUP BY PP.uid";
                $POINT_COUNT = "POINT_COUNT_{$uniq}";
                $joins["({$COUNT_SELECT}) AS {$POINT_COUNT}"] = "{$POINT_COUNT}.uid = member_master.uid";

                if ($min !== null) $wheres[] = [["IFNULL({$POINT_COUNT}.CNT,0)", ">=", $min], $not_option];
                if ($max !== null) $wheres[] = [["IFNULL({$POINT_COUNT}.CNT,0)", "<=", $max], $not_option];
            }

            if ($key == 'payment_item') { // 商品別購入経験
                $PAYMENT_ITEM_HISTORY = "PAYMENT_ITEM_HISTORY_{$uniq}";

                $WHERE_SINCE = !empty($term[0]) ? " AND {$PAYMENT_ITEM_HISTORY}.purchased_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
                $WHERE_UNTIL = !empty($term[1]) ? " AND {$PAYMENT_ITEM_HISTORY}.purchased_at<='" . date("Y-m-d H:i:s", strtotime($term[1])) . "' " : '';  // 終了日指定
                $WHERE_PAY_METHOD = '';// 決済手段
                if ($item['sub'] === 'CREDIT') $WHERE_PAY_METHOD = " AND {$PAYMENT_ITEM_HISTORY}.method like '%" . PaymentHistory::METHOD_CREDIT . "%' ";
                if ($item['sub'] === 'BANK') $WHERE_PAY_METHOD = " AND {$PAYMENT_ITEM_HISTORY}.method like '%" . PaymentHistory::METHOD_AOZORA . "%' ";

                $joins["payment_history AS {$PAYMENT_ITEM_HISTORY}"] = "{$PAYMENT_ITEM_HISTORY}.uid = member_master.uid AND {$PAYMENT_ITEM_HISTORY}.purchased_at IS NOT NULL $WHERE_SINCE $WHERE_UNTIL $WHERE_PAY_METHOD";

                $where = "{$PAYMENT_ITEM_HISTORY}.item_id={$value}";

                // サブスク新規加入のみ
                if (!empty($item['checked'])) {
                    $Item = PaymentItem::first($value);
                    if ($Item->type === PaymentItem::TYPE_SUBSC) {
                        //$KIND = SubscriptionEx::KIND_YOSO;
                        //$TYPE_EXTEND = SubscriptionEx::TYPE_EXTEND;
                        $PAY_SUBSCRIPTION = "PAY_SUBSCRIPTION_{$uniq}";
                        $PRE_SUBSCRIPTION = "PRE_SUBSCRIPTION_{$uniq}";
                        $joins["subscription AS {$PAY_SUBSCRIPTION}"] = "{$PAY_SUBSCRIPTION}.uid = member_master.uid AND {$PAY_SUBSCRIPTION}.payment_id = {$PAYMENT_ITEM_HISTORY}.id";
                        $joins["subscription AS {$PRE_SUBSCRIPTION}"] = "{$PRE_SUBSCRIPTION}.uid = member_master.uid AND {$PRE_SUBSCRIPTION}.kind={$PAY_SUBSCRIPTION}.kind AND {$PRE_SUBSCRIPTION}.created_at < {$PAY_SUBSCRIPTION}.created_at";
                        $where .= " AND {$PRE_SUBSCRIPTION}.id IS NULL ";
                    }
                }

                $wheres[] = [[$where], $not_option];

            }

            // ポイント
            if ($key == 'point_used_at') {
                $wheres[] = [['member_condition.point_used_at', 'BETWEEN', $term], $not_option];
            }
            if ($key == 'point_used_count') { // 消費回数
                $PRF_PU_CNT = "PRF_PU_CNT_{$uniq}";
                $joins["member_profile AS {$PRF_PU_CNT}"] = "{$PRF_PU_CNT}.uid=member_master.uid AND {$PRF_PU_CNT}.category='" . MemberProfile::CATEGORY_POINT_USED_COUNT . "'";
                $wheres[] = [["{$PRF_PU_CNT}.value", 'BETWEEN', [$min, $max]], $not_option];
            }
            if ($key == 'point') { // 総ポイント
                $wheres[] = [['member_state.premium_point + member_state.gift_point', 'BETWEEN', [$min, $max]], $not_option];
            }
            if ($key == 'gift') { // ギフト
                $wheres[] = [['member_state.gift_point', 'BETWEEN', [$min, $max]], $not_option];
            }
            if ($key == 'premium') { // 有償
                $wheres[] = [['member_state.premium_point', 'BETWEEN', [$min, $max]], $not_option];
            }


            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            // 条件３
            //-----------------------------------------------------------------------------------------------------------------------------------------------------

            $KIND = $item['sub'] ?? 'all';

            if ($key == 'payment_subscription') { // サブスク購入回数
                $WHERE_SINCE = !empty($term[0]) ? " AND start_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
                $WHERE_UNTIL = !empty($term[1]) ? " AND start_at<='" . date("Y-m-d H:i:s", strtotime($term[1])) . "' " : '';  // 終了日指定
                $KIND_CONDITION = ($KIND == 'all') ? '1' : "kind='{$KIND}' ";
                $WHERE_NOT_CONTINUE = ($item['checked']) ? " AND type<>'EXTEND' " : ''; // 継続排除

                $COUNT_SELECT = "SELECT count(*) AS CNT , SB.uid FROM subscription AS SB WHERE {$KIND_CONDITION} {$WHERE_NOT_CONTINUE} {$WHERE_SINCE} {$WHERE_UNTIL} GROUP BY SB.uid";
                $SUBSC_COUNT = "SUBSC_COUNT_{$uniq}";
                $joins["({$COUNT_SELECT}) AS {$SUBSC_COUNT}"] = "{$SUBSC_COUNT}.uid = member_master.uid";

                if ($min !== null) $wheres[] = [["IFNULL({$SUBSC_COUNT}.CNT,0)", ">=", $min], $not_option];
                if ($max !== null) $wheres[] = [["IFNULL({$SUBSC_COUNT}.CNT,0)", "<=", $max], $not_option];
            }

            // -------------------------------------------
            // ・現サブスク加入状態会員
            //　※期間指定無し、絞り込みを行った時点での会員を抽出
            //　※決済種別(ALL:全て or CREDIT:クレジット or BANK:銀行振込)でのサブ条件を加えたい
            $joinSubscription = false;
            $SUBSC_JOIN = "SUBSC_JOIN_{$uniq}";
            $SUBSC_PAYMENT = "SUBSC_PAYMENT_{$uniq}";
            $KIND_CONDITION = ($KIND == 'all') ? '' : "{$SUBSC_JOIN}.kind='{$KIND}' AND";

            if ($key == 'subscription') {
                $joinSubscription = true;
                if ($value === 'ALL') $wheres[] = [["{$SUBSC_PAYMENT}.method", 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT, PaymentHistory::METHOD_AOZORA]], $not_option];
                if ($value === 'CREDIT') $wheres[] = [["{$SUBSC_PAYMENT}.method", 'IN', [PaymentHistory::METHOD_CREDIT, PaymentHistory::METHOD_ONFILE_CREDIT]], $not_option];
                if ($value === 'BANK') $wheres[] = [["{$SUBSC_PAYMENT}.method", 'IN', [PaymentHistory::METHOD_AOZORA]], $not_option];
                if ($value === 'NONE') $wheres[] = [["{$SUBSC_PAYMENT}.method IS NULL"], $not_option];
            }

            //・自動継続状態会員
            //　※期間指定無し、絞り込みを行った時点での会員を抽出
            //　※ON: をデフォルトとして、OFF:状態でのサブ条件を加えたい
            if ($key == 'subscription_continue') {
                $joinSubscription = true;
                if ($value == 'ON') $wheres[] = [["{$SUBSC_JOIN}.mode", '=', SubscriptionEx::MODE_NONE], $not_option];
                if ($value == 'OFF') $wheres[] = [["{$SUBSC_JOIN}.mode", '=', SubscriptionEx::MODE_CANCEL], $not_option];
            }

            if ($joinSubscription) {
                $NOW = SubscriptionEx::date();
                $joins["subscription AS {$SUBSC_JOIN}"] = "{$KIND_CONDITION} {$SUBSC_JOIN}.uid=member_state.uid AND {$SUBSC_JOIN}.expire_at>'{$NOW}'";
                $joins["payment_history AS {$SUBSC_PAYMENT}"] = "{$SUBSC_PAYMENT}.id={$SUBSC_JOIN}.payment_id";
            }
            // -------------------------------------------

            //・サブスク有効期間対象者
            //　※期間設定(Y-m-d)～(Y-m-d)を指定して、その期間にサブスクの有効期間が切れる会員を抽出
            if ($key == 'subscription_expire') {

                $SUBSCRIPTION_EXPIRE = "SUBSCRIPTION_EXPIRE_{$uniq}";
                $joins["subscription AS {$SUBSCRIPTION_EXPIRE}"] = "{$SUBSCRIPTION_EXPIRE}.uid = member_state.uid";
                $KIND = $item['value'] ?? 'all';
                $KIND_CONDITION = ($KIND == 'all') ? '1' : "{$SUBSCRIPTION_EXPIRE}.kind='{$KIND}' ";
                $WHERE_SINCE = !empty($term[0]) ? " AND {$SUBSCRIPTION_EXPIRE}.expire_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
                $WHERE_UNTIL = !empty($term[1]) ? " AND {$SUBSCRIPTION_EXPIRE}.expire_at<='" . date("Y-m-d 23:59:59", strtotime($term[1])) . "' " : '';  // 終了日指定

                $PAYMENT_ITEM_HISTORY = "PAYMENT_ITEM_HISTORY_{$uniq}";
                $joins["payment_history AS {$PAYMENT_ITEM_HISTORY}"] = "{$PAYMENT_ITEM_HISTORY}.id = {$SUBSCRIPTION_EXPIRE}.payment_id";
                $WHERE_PAY_METHOD = '';// 決済手段
                if ($item['sub'] === 'CREDIT') $WHERE_PAY_METHOD = " AND {$PAYMENT_ITEM_HISTORY}.method like '%" . PaymentHistory::METHOD_CREDIT . "%' ";
                if ($item['sub'] === 'BANK') $WHERE_PAY_METHOD = " AND {$PAYMENT_ITEM_HISTORY}.method like '%" . PaymentHistory::METHOD_AOZORA . "%' ";

                $where = "{$KIND_CONDITION} {$WHERE_SINCE} {$WHERE_UNTIL} {$WHERE_PAY_METHOD}";

                $wheres[] = [[$where], $not_option];

            }

            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            // 条件４
            //-----------------------------------------------------------------------------------------------------------------------------------------------------

            if ($key == 'rank') { // ランク
                $wheres[] = [['member_state.rank', 'BETWEEN', [$min, $max]], $not_option];
            }

            // --------------------------------------------------
            // スコア
            if ($key == 'score') {

                if (empty($term[0]) && empty($term[1])) {
                    // 期間なし
                    $wheres[] = [['member_state.score', 'BETWEEN', [$min, $max]], $not_option];
                } else {
                    // 期間あり
                    $score_log_table = App::logDB('score_log');
                    $SCORE_LOG = "SCORE_LOG_{$uniq}";
                    $SCORE_SINCE = !empty($term[0]) ? " AND SLOG.created_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
                    $SCORE_UNTIL = !empty($term[1]) ? " AND SLOG.created_at<='" . date("Y-m-d H:i:s", strtotime($term[1])) . "' " : '';  // 終了日指定

                    $SCORE_SELECT = "SELECT sum(SLOG.acquire) AS ACQUIRED , SLOG.uid FROM {$score_log_table} AS SLOG WHERE 1 {$SCORE_SINCE} {$SCORE_UNTIL} GROUP BY SLOG.uid";
                    $SCORE_SUM = "SCORE_{$uniq}";
                    $joins["({$SCORE_SELECT}) AS {$SCORE_SUM}"] = "{$SCORE_SUM}.uid = member_master.uid";

                    if ($min !== null) $wheres[] = [["IFNULL({$SCORE_SUM}.ACQUIRED,0)", ">=", $min], $not_option];
                    if ($max !== null) $wheres[] = [["IFNULL({$SCORE_SUM}.ACQUIRED,0)", "<=", $max], $not_option];
                }

            }

            // --------------------------------------------------
            // ポイント使用期間
            $log_table = App::logDB('point_log');
            $POINT_ORIGIN = "POINT_ORIGIN_{$uniq}";
            $POINT_LOG = "POINT_LOG_{$uniq}";

            $POINT_SINCE = !empty($term[0]) ? " AND {$POINT_LOG}.created_at>='" . date("Y-m-d H:i:s", strtotime($term[0])) . "' " : ''; // 開始日指定
            $POINT_UNTIL = !empty($term[1]) ? " AND {$POINT_LOG}.created_at<='" . date("Y-m-d H:i:s", strtotime($term[1])) . "' " : '';  // 終了日指定

            // ポイント記事
            if ($key == 'point_contents') {
                $CATEGORY = Point::CATEGORY_CONTENT;
                $joins["{$log_table} AS {$POINT_LOG}"] = "{$POINT_LOG}.uid = member_master.uid";
                $joins["point_origin AS {$POINT_ORIGIN}"] = "{$POINT_ORIGIN}.id = {$POINT_LOG}.origin_id";

                $WHERE = "{$POINT_ORIGIN}.id IS NOT NULL AND {$POINT_ORIGIN}.category='{$CATEGORY}'";
                if ($value) $WHERE .= " AND {$POINT_ORIGIN}.rid = '{$value}' ";
                if ($sub !== 'none') $WHERE .= " AND {$POINT_ORIGIN}.identifier LIKE '{$sub}%' ";

                $where = " {$WHERE} {$POINT_SINCE} {$POINT_UNTIL} ";
                $wheres[] = [[$where], $not_option];
            }

            // ポイント著者
            if ($key == 'point_author') {
                $CATEGORY = Point::CATEGORY_CONTENT;
                $CONTENT_CONDITION = "CONTENT_CONDITION_{$uniq}";
                $joins["{$log_table} AS {$POINT_LOG}"] = "{$POINT_LOG}.uid = member_master.uid";
                $joins["point_origin AS {$POINT_ORIGIN}"] = "{$POINT_ORIGIN}.id = {$POINT_LOG}.origin_id";
                $joins["content_condition AS {$CONTENT_CONDITION}"] = "{$CONTENT_CONDITION}.posts_id = {$POINT_ORIGIN}.rid AND {$CONTENT_CONDITION}.author_id='{$value}'";

                $WHERE = "{$POINT_ORIGIN}.id IS NOT NULL AND {$POINT_ORIGIN}.category='{$CATEGORY}'";
                if ($value) $WHERE .= " AND {$CONTENT_CONDITION}.author_id={$value} ";
                if ($sub !== 'none') $WHERE .= " AND {$POINT_ORIGIN}.identifier LIKE '{$sub}%' ";

                $where = " {$WHERE} {$POINT_SINCE} {$POINT_UNTIL} ";
                $wheres[] = [[$where], $not_option];
            }

            // ポイントスポイラー
            if ($key == 'point_spoiler') {
                $CATEGORY = Point::CATEGORY_UMA_COMMENT;
                $joins["{$log_table} AS {$POINT_LOG}"] = "{$POINT_LOG}.uid = member_master.uid";
                $joins["point_origin AS {$POINT_ORIGIN}"] = "{$POINT_ORIGIN}.id = {$POINT_LOG}.origin_id  AND {$POINT_ORIGIN}.category='{$CATEGORY}' AND {$POINT_ORIGIN}.identifier='spoiler'";;

                $WHERE = "{$POINT_ORIGIN}.id IS NOT NULL ";
                if ($value) $WHERE .= " AND {$POINT_ORIGIN}.rid = '{$value}' ";

                $where = " {$WHERE} {$POINT_SINCE} {$POINT_UNTIL} ";
                $wheres[] = [[$where], $not_option];
            }

            // ポイントWindex
            if ($key == 'point_windex') {
                $CATEGORY = Point::CATEGORY_WINDEX_RACE;
                $joins["{$log_table} AS {$POINT_LOG}"] = "{$POINT_LOG}.uid = member_master.uid";
                $joins["point_origin AS {$POINT_ORIGIN}"] = "{$POINT_ORIGIN}.id = {$POINT_LOG}.origin_id  AND {$POINT_ORIGIN}.category='{$CATEGORY}'";;

                $WHERE = "{$POINT_ORIGIN}.id IS NOT NULL ";
                if ($value) $WHERE .= " AND {$POINT_ORIGIN}.identifier = '{$value}' ";

                $where = " {$WHERE} {$POINT_SINCE} {$POINT_UNTIL} ";
                $wheres[] = [[$where], $not_option];
            }

            // ポイントパック
            if ($key == 'point_pack') {
                $CATEGORY = Point::CATEGORY_PACK;

                $joins["{$log_table} AS {$POINT_LOG}"] = "{$POINT_LOG}.uid = member_master.uid";
                $joins["point_origin AS {$POINT_ORIGIN}"] = "{$POINT_ORIGIN}.id = {$POINT_LOG}.origin_id AND {$POINT_ORIGIN}.category='{$CATEGORY}'";;

                $WHERE = "{$POINT_ORIGIN}.id IS NOT NULL ";
                if ($value) $WHERE .= " AND {$POINT_ORIGIN}.rid = '{$value}' ";
                if (!empty($sub) && $sub != 'all') $WHERE .= " AND {$POINT_ORIGIN}.identifier = 'pack_{$sub}' ";

                $where = " {$WHERE} {$POINT_SINCE} {$POINT_UNTIL} ";
                $wheres[] = [[$where], $not_option];
            }


            //-----------------------------------------------------------------------------------------------------------------------------------------------------
            // flag マスク処理
            if ($key == 'check_flag') {

                $flag_wheres = [];
                for ($seg = 1; $seg <= MemberFlag::SEGMENT_MAX; $seg++) {
                    $FLAG = "FLAG_{$uniq}_{$seg}";
                    if (isset($item["check$seg"])) {
                        $mask = bindec($item["checkMask$seg"] ?? $item["check$seg"]);
                        $check = bindec($item["check$seg"]);

                        $joins["member_flag AS {$FLAG}"] = "{$FLAG}.uid = member_master.uid AND {$FLAG}.segment=$seg";

                        if ($item['mode'] == 1) {
                            // 完全一致
                            $flag_wheres[] = "(IFNULL({$FLAG}.flag,0) & $mask = $check)";
                        } else {
                            // いずれか一致
                            $flag_wheres[] = "((~(IFNULL({$FLAG}.flag,0) ^ $check)) & $mask)";
                        }
                    }
                }
                if ($flag_wheres) {
                    if ($item['mode'] == 1) {
                        $where_str = implode('AND', $flag_wheres);
                    } else {
                        $where_str = implode('OR', $flag_wheres);
                    }
                    $wheres[] = [[$where_str, null, null], ''];
                }
            }
        }

        return ['wheres' => $wheres, 'joins' => $joins, 'uniq' => $uniq];

    }

    /**
     * 配信用絞り込み
     * @param $request
     * @return DBQueryClass
     * @throws Exception
     */
    public static function distributeQuery($request): DBQueryClass {
        return MemberSearch::narrow2($request)
            ->select(null)
            ->select(['member_master.uid', 'email', 'nickname'])
            ->where('member_distribute.mail_status', 0)
            ->where('member_distribute.accept', 1)
            ->where('member_master.email', '<>', '');
    }


}