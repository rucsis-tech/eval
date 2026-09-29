<?php

namespace classes\trait;

use Exception;

trait DBTableTrait {

    // ---------------------------------------------------------
    // 補助用


    /**
     * カラム名がドットを含んでいる場合を考慮して``で囲む処置
     * @param $key
     * @return string
     */
    static function formatKey($key): string {
        // 既に囲われている
        if (str_contains($key, '`')) {
            return $key;
        }

        // 複数、あるいは関数利用
        if (str_contains($key, ' ') || str_contains($key, '(')) {
            return $key;
        }

        if (str_contains($key, '.')) {
            // DB/テーブル指定込のキー
            $keys = explode('.', $key);
            foreach ($keys as $i => $key) {
                if ($key != '*') $keys[$i] = "`$key`";
            }
            return implode('.', $keys);
        } else {
            // 単体キー
            if ($key != '*') $key = "`$key`";
            return $key;
        }
    }

    /**
     * フィールドカラム情報取得
     * @throws Exception
     */
    static function getField($TableName, $name) {
        try {
            $TableName = self::formatKey($TableName);
            $res = self::sql("SHOW COLUMNS FROM $TableName LIKE '$name'");
        } catch (Exception $e) {
            return null;
        }
        if ($res) {
            return $res[0];
        } else {
            return null;
        }
    }

    static function getFieldType($TableName, $name) {
        $info = self::getField($TableName, $name);
        return $info['Type'] ?? '';
    }

    static function hasField($TableName, $name) {
        return !empty(self::getField($TableName, $name));
    }

    /**
     * テーブルの存在確認
     * @param $TableName
     * @return array
     * @throws Exception
     */

    static function hasTable($TableName): array {
        return self::sql("SHOW TABLES LIKE '$TableName' ");
    }

    /**
     * インデックスの存在確認
     * @param $TableName
     * @param $name
     * @return bool
     * @throws Exception
     */
    static function hasIndex($TableName, $name): bool {
        $res = self::sql("SHOW INDEX FROM `$TableName` ");
        foreach ($res as $index) {
            if ($index['Key_name'] == $name) {
                return true;
            }
        }
        return false;
    }

    static function getFullColumns($TableName): array {

        $TableName = self::formatKey($TableName);
        $fields = self::sql("SHOW FULL COLUMNS FROM $TableName");
        $list = [];
        foreach ($fields as $field) {
            $field = array_change_key_case($field);
            $list[$field['field']] = $field;
        }
        return $list;
    }

    static function propertyList($TableName): array {
        $fields = self::getFullColumns($TableName);
        $list = [];
        foreach ($fields as $field) {
            $name = $field['Field'];
            $type = $field['Type'];
            $comment = $field['Comment'];
            $null = $field['Null'];
            if (str_contains($type, 'int')) $tp = 'int';
            if (str_contains($type, 'char')) $tp = 'string';
            if (str_contains($type, 'text')) $tp = 'string';
            if (str_contains($type, 'date')) $tp = 'string';

            $list[] = "* @property $tp \$$name $comment<br>";
        }

        return ['comments' => $list];
    }

}