<?php

class Race extends Model {

    const PLACES = [
        '01' => '札幌',
        '02' => '函館',
        '03' => '福島',
        '04' => '新潟',
        '05' => '東京',
        '06' => '中山',
        '07' => '中京',
        '08' => '京都',
        '09' => '阪神',
        '10' => '小倉',
        '30' => ' 門別',
        '35' => '盛岡',
        '36' => '水沢',
        '42' => '浦和',
        '43' => '船橋',
        '44' => '大井',
        '45' => '川崎',
        '46' => '金沢',
        '47' => '笠松',
        '48' => '名古屋',
        '50' => '園田',
        '51' => '姫路',
        '54' => '高知',
        '55' => '佐賀',
    ];
    const WEEK_STARTS = [
        '2025-02-10' => ['adjust' => +1],
        '2025-09-15' => ['adjust' => +1],
        '2025-10-13' => ['adjust' => +1],
        '2025-11-24' => ['adjust' => +1],
        '2026-01-05' => ['adjust' => +1],
        '2026-01-12' => ['adjust' => +1],
        '2026-02-09' => ['adjust' => +2],
        '2026-09-21' => ['adjust' => +2],
        '2026-10-12' => ['adjust' => +1],
        '2026-11-23' => ['adjust' => +1],
    ];

    // ------------------------------------------------------------------------
    // 更新用

    /**
     * 開催情報書き込み
     * @param $KaisaiData
     * @param $info
     * @return void
     * @throws Exception
     */
    static public function writeKaisai($KaisaiData, $info) {
        $Exists = self::table('race_kaisai')->where($KaisaiData)->first();
        $json = json_encode($info, JSON_UNESCAPED_UNICODE);
        if ($Exists) {
            $Exists->save(['info' => $json], 'race_kaisai');
        } else {
            $KaisaiData['info'] = $json;
            $KaisaiData['start_at'] = $KaisaiData['day'];
            self::insert($KaisaiData, 'race_kaisai');
        }
    }

    /**
     * レース情報書き込み
     * @param $RaceData
     * @param $info
     * @return void
     * @throws Exception
     */
    static public function writeRace($RaceData, $info) {
        $Exists = self::table('race_info')->where($RaceData)->first();
        $json = json_encode($info, JSON_UNESCAPED_UNICODE);
        if ($Exists) {
            $Exists->save(['info' => $json], 'race_info');
        } else {
            $RaceData['info'] = $json;
            self::insert($RaceData, 'race_info');
        }
    }

    /**
     * 馬情報書き込み
     * @param $UmaData
     * @param $info
     * @return void
     * @throws Exception
     */
    static public function writeUma($race_code, $umaban, $registration, $info) {
        $Exists = self::table('race_uma')
            ->where('race_code', $race_code)
            ->where('registration', $registration)
            ->first();
        $json = json_encode($info, JSON_UNESCAPED_UNICODE);
        $UmaData = [
            'race_code' => $race_code,
            'umaban' => $umaban,
            'registration' => $registration,
            'info' => $json,
        ];
        if ($Exists) {
            $Exists->save($UmaData, 'race_uma');
        } else {
            self::insert($UmaData, 'race_uma');
        }
    }
    // ------------------------------------------------------------------------
    // 取得用

    static public function getKaisaiList($day) {
        $result = [];
        if (empty(strtotime($day))) return $result;

        $list = self::from('race_kaisai')->where('day', $day)->all();
        foreach ($list as $item) {
            $kaisai_code = $item->kaisai_code;
            $item->info = json_decode($item->info, true);
            $Races = self::from('race_info')->where('race_code', 'LIKE', "{$kaisai_code}%")->orderBy('race_code')->all();
            foreach ($Races as $Race) {
                $Race->info = json_decode($Race->info, true);
            }
            $item->races = $Races;

            $item->work = json_decode($item->work, true);

            $result[$kaisai_code] = $item;
        }
        return $result;

    }

    static public function getUmaList($raceCode) {
        $list = self::from('race_uma')
            ->where('race_code', $raceCode)
            ->where('umaban', '>', '-1')
            ->orderBy('umaban')
            ->all();
        $result = [];
        foreach ($list as $item) {
            $result[] = json_decode($item->info, true);
        }
        return $result;

    }


    // ------------------------------------------------------------------------
    //

    static public function getEnabledDays($days) {
        $days = self::select(['DISTINCT', 'day'])
            ->from('race_kaisai')
            ->where('day', 'IN', $days)
            ->rows();
        return array_column($days, 'day');
    }

    /**
     * 指定週の日にちを返す
     * @param $time
     * @return array
     */

    static function getWeekDays($time = null) {
        if (empty($time)) $time = time();

        $info = self::getWeekInfo($time);
        $result = [];
        for ($tm = $info['start_day_time']; $tm < $info['next_day_time']; $tm += 86400) {
            $result[] = self::day($tm);
        }
        return $result;
    }

    static function getKaisaiDays($time = null) {
        $days = [];
        $week_days = Race::getWeekDays($time);
        foreach ($week_days as $day) {
            $Kaisai = Race::getKaisaiList($day);
            if ($Kaisai) $days[] = $day;
        }
        return $days;
    }

    static function getWeekInfo($day_time = 0) {
        if (empty($day_time)) $day_time = time();
        $day_time = strtotime(date('Y-m-d', $day_time));

        for ($i = 0; $i < 10; $i++) {
            // 通常月曜が週初め
            $wn = (intval(date('w', $day_time)) + 6) % 7;
            $start_time = $day_time - $wn * 86400;
            // 次回は7日後
            $next_time = $start_time + 86400 * 7;
            $next_day = date('Y-m-d', $next_time);
            // ラベルはズレなし
            $type_label = date('Ymd', $start_time);

            // 調整設定確認
            $day = date('Y-m-d', $start_time);
            if (isset(self::WEEK_STARTS[$day])) {
                $start_time += self::WEEK_STARTS[$day]['adjust'] * 86400;
            }

            if (isset(self::WEEK_STARTS[$next_day])) {
                $next_time += self::WEEK_STARTS[$next_day]['adjust'] * 86400;
            }

            if ($day_time < $start_time) {
                $day_time -= 86400;
                continue;
            }
            if ($next_time <= $day_time) {
                $day_time += 86400;
                continue;
            }

            break;
        }
        $end_time = $next_time - 86400;

        return [
            'target_day' => self::day($day_time),
            'week_label' => $type_label,
            'start_day_time' => $start_time,
            'end_day_time' => $end_time,
            'next_day_time' => $next_time,
            'start_day' => self::day($start_time),
            'end_day' => self::day($end_time),
            'next_day' => self::day($next_time),
        ];
    }

    /**
     * 場所込みのレース名を取得
     * @param $race_code
     * @return string
     * @throws Exception
     */

    public function getRaceName($race_code) {
        $RaceInfo = Race::table('race_info')->where('race_code', $race_code)->first();
        if ($RaceInfo) {
            $place = self::PLACES[$this->place];
            $kyosomei = $RaceInfo->json('info', 'kyosomei');
            $race_bango = $RaceInfo->json('info', 'race_bango');
            return "{$place} {$race_bango}R {$kyosomei}";
        } else {
            return '';
        }
    }
}
