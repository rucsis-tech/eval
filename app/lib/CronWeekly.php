<?php

class CronWeekly {


    /**
     * スコアランキングの生成
     * @param $week_info
     * @return void
     * @throws Exception
     */
    static public function makeScoreRanking($week_info): void {
        // --------------------------------------------------------------
        // スコアランキング
        // 累計ランキング
        Ranking::scoreAllWrite(Ranking::SUBJECT_TOTAL_SCORE, strtotime('2025-04-01'), $week_info['start_day_time']);

        $table = App::logDB('ranking');
        // 前週ランキング
        $last_week_info = Race::getWeekInfo($week_info['start_day_time'] - 86400 * 2);        // 前週の情報
        Ranking::sql("UPDATE $table SET value=0 WHERE subject=?", [Ranking::SUBJECT_WEEKLY_SCORE]);
        Ranking::scoreAllWrite(Ranking::SUBJECT_WEEKLY_SCORE, $last_week_info['start_day_time'], $week_info['start_day_time']);

        // 特定週ランキング
        $special_info = Ranking::currentSpecialInfo();
        Ranking::sql("UPDATE $table SET value=0 WHERE subject=?", [$special_info['subject']]);
        Ranking::scoreAllWrite($special_info['subject'], strtotime($special_info['start_at']), $week_info['start_day_time']);
    }
}