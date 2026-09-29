<?php

class Tools {

    static function changeRaceCode($old, $new) {
        // race_codeカラム書き換え
        $tables = [
            'content_condition', 'content_kaime', 'content_mark',
            'everyone_master',
            'my_mark',
            'uma_comment',
            //'windex_kaime', 'windex_race',
            'windex_uma',
            App::logDB('paddock_log'),
        ];
        foreach ($tables as $table) {
            $res = Model::sql("UPDATE {$table} SET race_code=? WHERE race_code=?", [$new, $old]);
            Model::isCli("change:{$table}: {$res}");
        }

        // 置き換え
        $replaces = [
            //'windex_day' => 'work',
            App::logDB('chat') => 'room'
        ];
        foreach ($replaces as $table => $col) {
            $res = Model::sql("UPDATE {$table} SET {$col}=REPLACE({$col}, ?, ?) WHERE {$col} LIKE '%{$old}%'", [$old, $new]);
            Model::isCli("replace:{$table}.{$col}: {$res}");
        }

    }

}