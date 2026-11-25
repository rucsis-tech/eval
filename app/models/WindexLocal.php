<?php

use classes\DBQueryClass;
use trait\KaimeTrait;

class WindexLocal extends Model {

    use KaimeTrait;
    static $CANCEL_ENABLE = false;    // キャンセル馬の扱い

    protected static string $tableName = 'windex_local_race';
    public static string $TargetYYYYMMDD = '';

    public const TABLE_DAY = 'windex_local_day';
    public const TABLE_RACE = 'windex_local_race';
    public const TABLE_UMA = 'windex_local_uma';


    /**
     * レース閲覧権限の購入
     * @param $uid
     * @param $race_code
     * @return string
     * @throws Exception
     */
    public
    static function buy($uid, $race_code) {
        $Race = self::first('race_code', $race_code);
        if (empty($Race)) throw new Exception("{$race_code}が存在しません");
        $log_id = Point::spend($uid, $Race->need_points, Point::CATEGORY_WINDEX_RACE, $Race->id, $race_code, $Race->created_at);
        return $log_id;
    }

    /**
     * @param $day // YYYY-MM-DD
     * @param $place // 指定なければ null
     * @param $uid // 指定なければ null
     * @return DBQueryClass
     * @throws Exception
     */
    public static function defaultQuery($day, $place = null, $uid = null): DBQueryClass {
        $YYYYMMDD = self::toYYYYMMDD($day);

        // レース一覧
        $query = WindexLocal::select(['windex_local_race.*'])
            ->where('windex_local_race.race_code', 'LIKE', "{$YYYYMMDD}%")
            ->orderBy('windex_local_race.race_code');

        if ($place) $query->where('race_info.place', $place);
        if ($uid) {
            $ORIGIN_CATEGORY = Point::CATEGORY_WINDEX_RACE;
            $since = self::date(strtotime($day) - 86400 * 10);
            $pointLogTable = App::logDB('point_log');
            $query->select(["{$pointLogTable}.created_at AS licensed_at"])
                ->leftjoin('point_origin', "point_origin.category='{$ORIGIN_CATEGORY}' AND point_origin.rid=windex_local_race.id AND point_origin.identifier=windex_local_race.race_code")
                ->leftjoin($pointLogTable, "{$pointLogTable}.origin_id=point_origin.id AND {$pointLogTable}.uid='{$uid}' AND {$pointLogTable}.created_at>'{$since}'");
        }

        return $query;
    }

    // ---------------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------------

    /**
     * 開催日情報の取得
     * @param $target_day
     * @return self
     * @throws Exception
     */

    static
    public function getWindexDay($target_day): self {
        $Day = self::table(WindexLocal::TABLE_DAY)->where('day', $target_day)->first();
        if (empty($Day)) {
            $start_at = self::date(strtotime($target_day) - 3600 * 6);
            self::table(WindexLocal::TABLE_DAY)->insert([
                'day' => $target_day,
                'start_at' => $start_at,
            ]);
            $Day = self::table(WindexLocal::TABLE_DAY)->where('day', $target_day)->first();
        }
        return $Day;
    }

    /**
     * CSVの分解
     * @param $csv
     * @param $indexes
     * @return array
     * @throws Exception
     */
    static
    private function extract($csv, $indexes) {
        $list = [];
        $rows = explode("\n", $csv);
        for ($i = 1; $i < count($rows); $i++) {
            $row = trim($rows[$i]);
            if (empty($row)) continue;

            $item = [];
            $cols = explode(",", $row);
            $index = 0;
            if (in_array('*', $indexes)) $index = 1;
            else {
                // 可変長でなければ項目数チェック
                if (count($cols) != count($indexes)) throw new Exception("項目数が合いません(line {$i}: {$row})", 1);
            }

            for ($j = 0; $j < count($cols); $j++) {
                $key = $indexes[$j];
                if ($key === '*') $key = $index++;
                $item[$key] = trim($cols[$j]);
            }
            $list[] = $item;
            // 日付チェック
            if (isset($item['race_code'])) {
                $YYYYMMDD = substr($item['race_code'], 0, 8);
                if (empty(self::$TargetYYYYMMDD)) self::$TargetYYYYMMDD = $YYYYMMDD;
                if ($YYYYMMDD != self::$TargetYYYYMMDD) throw new Exception("対象レースの日付[" . self::$TargetYYYYMMDD . "/{$YYYYMMDD}]が異なります({$i}:{$row})");
            }
        }
        return $list;
    }

    // ---------------------------------------------------------------------------------------------------------
    // ---------------------------------------------------------------------------------------------------------

    /**
     * レースごとの情報
     * @param $csv
     * @param $write
     * @return array
     * @throws Exception
     */

    static public function convToRace($csv, $write): array {
        $TABLE = self::TABLE_RACE;
        $list = [];
        // CSVデータの展開
        $data = self::extract($csv, ['race_code', 'start_at', 'grade_code', 'conditions', 'race_name', 'course', 'entrants']);
        foreach ($data as $datum) {
            $time = strtotime(self::toDay($datum['race_code']) . " " . $datum['start_at']);
            $datum['start_at'] = self::date($time);
            if ($write) {
                // DBカキコミ
                $Exists = self::table($TABLE)
                    ->where('race_code', $datum['race_code'])
                    ->first();
                if ($Exists) {
                    // 上書き
                    $Exists->save($datum, $TABLE);
                } else {
                    // 新規
                    $datum['need_points'] = 100;
                    $datum['work'] = '';
                    self::insert($datum, $TABLE);
                }
            }
            $list[] = $datum;
        }

        return ['list' => $list];

    }


    /**
     * UMA情報
     * @param $csv
     * @param $write
     * @return array
     * @throws Exception
     */
    static public function convToUma($csv, $write): array {
        $TABLE = self::TABLE_UMA;
        $list = [];
        $write_ids = [];
        // CSVデータの展開
        $data = self::extract($csv, ['race_code', 'wakuban', 'umaban', 'name', 'age', 'weight', 'jockey', 'trainer', 'chakujun', 'status']);
        foreach ($data as $datum) {
            $works = [
                'name' => $datum['name'],
                'age' => $datum['age'],
                'weight' => $datum['weight'],
                'jockey' => $datum['jockey'],
                'trainer' => $datum['trainer'],
            ];
            $cols = [
                'race_code' => $datum['race_code'],
                'wakuban' => $datum['wakuban'],
                'umaban' => $datum['umaban'],
                'chakujun' => $datum['chakujun'],
                'status' => $datum['status'],
                'work' => json_encode($works, JSON_UNESCAPED_UNICODE),
            ];

            if ($write) {
                // DBカキコミ
                $Exists = self::cast(self::table($TABLE)->where('race_code', $datum['race_code'])->where('umaban', $datum['umaban'])->first());
                if ($Exists) {
                    // 上書き
                    $Exists->save($cols, $TABLE);
                } else {
                    // 新規
                    self::insert($cols, $TABLE);
                }
            }
            $list[] = $datum;
        }


        return ['list' => $list];
    }

    public function datalizeUma() {
        $this->name = $this->work('name');
        $this->age = $this->work('age');
        $this->weight = $this->work('weight');
        $this->jockey = $this->work('jockey');
        $this->trainer = $this->work('trainer');
    }


    /**
     * WINDEX指数
     * @param $csv
     * @param $write
     * @return array
     * @throws Exception
     */

    static public function convToWindex($csv, $write): array {
        $TABLE = self::TABLE_UMA;
        $list = [];
        // CSVデータの展開
        $data = self::extract($csv, ['race_code', 'wakuban', 'umaban', 'basic_ability', 'pedigree', 'running_style_and_development', 'final_time', 'jockeys_and_trainers']);
        foreach ($data as $datum) {
            if ($write) {
                // レコード取得
                $Exists = self::table($TABLE)->where('race_code', $datum['race_code'])->where('umaban', $datum['umaban'])->first();
                if ($Exists) {
                    // 上書き
                    $Exists->save($datum, $TABLE);
                } else {
                    // 新規のレコード作成
                    self::insert($datum, $TABLE);
                }
            }

            $list[] = $datum;
        }

        return ['list' => $list];

    }


}