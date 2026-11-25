<?php

use classes\DBClass;
use classes\DBQueryClass;

class ModelLog extends Model {
    protected static string $DBName = '';

    /**
     * テーブルにパーティション作成
     * @param string $table
     * @param array $options
     * @return void
     * @throws Exception
     */
    static function partitioning(string $table = '', array $options = []): void {

        if (empty($table)) $table = static::$tableName;
        $log_table = App::logDB($table);

        $open = $options['open'] ?? strtotime('2025-05-01');
        $current_at = date('Y-m-d', $options['current'] ?? time());
        $term = $options['term'] ?? 'year';

        if ($term === 'day') {
            $identifier = 'Ymd';
            $unit = 'day';
            $format = 'Y-m-d';
        } else if ($term === 'month') {
            $identifier = 'Ym';
            $unit = 'month';
            $format = 'Y-m-01';
        } else {
            $identifier = 'Y';
            $unit = 'year';
            $format = 'Y-01-01';
        }

        $res = self::sql("SHOW CREATE TABLE {$log_table}");
        $create_table_sql = $res[0]['Create Table'];
        $partition_sql = '';

        self::isCli("{$create_table_sql}");
        self::isCli("-------------------");


        // そもそもパーティションがあるか確認
        $min_at = date($format, $open);
        $target_at = date('Y-m-d', strtotime("{$current_at} +1 {$unit}"));
        self::isCli("create partition:{$min_at} to {$target_at}");

        // Targetまで作成
        $partitions = [];
        $current_at = $min_at;
        while ($current_at <= $target_at) {
            $partition = 'p' . date($identifier, strtotime($current_at)) . 'unit';
            $current_at = date('Y-m-d', strtotime("{$current_at} +1 {$unit}"));
            if (!str_contains($create_table_sql, $partition)) {
                // 作成対象が存在しない場合
                $partitions[] = "PARTITION {$partition} VALUES LESS THAN ('{$current_at}')";
            }
        }

        if (!str_contains($create_table_sql, 'PARTITION')) {
            $partition_sql = "
            ALTER TABLE {$log_table} DROP PRIMARY KEY, ADD PRIMARY KEY(id, created_at);\n
            ALTER TABLE {$log_table} PARTITION BY RANGE columns(created_at) ( "
                . implode(',', $partitions)
                . " ENGINE = InnoDB );\n";
        } else if (count($partitions)) {
            $partition_sql .= "ALTER TABLE {$log_table} ADD PARTITION ( "
                . implode(',', $partitions)
                . " ENGINE = InnoDB );\n";
        }

        if ($partition_sql) {
            $res = self::sql($partition_sql);
            self::isCli("{$partition_sql}\n");
        }

    }

    /**
     * @param $table
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function table($table): DBQueryClass {
        $query = static::query();
        return $query->from(self::logDBTable($table));
    }

    static
    public function query(): DBQueryClass {
        $query = parent::query();
        $table = self::logDBTable(static::$tableName);
        $query->from($table);
        return $query;
    }

    public
    static function logDBTable($table = null) {

        static::$DBName = config('DB_LOG_NAME');
        if ($table) {
            if (str_contains($table, '.')) return $table;
            return self::$DBName . '.' . $table;
        } else {
            return static::$DBName;
        }
    }

    static
    public function insert($params, $table = null, $onDuplicateUpdate = false): mixed {
        $query = self::query();
        if (!$table) $table = static::$tableName;
        return $query->insert($params, self::logDBTable($table), $onDuplicateUpdate);
    }
}