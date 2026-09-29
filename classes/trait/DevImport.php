<?php

namespace trait;
trait DevImport {

    // --------------------------------------------------------
    // テーブルカラム情報取得・キャッシュ

    public function getFields($tableName) {
        if (!isset(self::$FieldArray[$tableName])) {
            $sql = "SHOW FULL COLUMNS FROM $tableName";
            self::$FieldArray[$tableName] = $this->select($sql);
        }
        return self::$FieldArray[$tableName];
    }

    public function getColumns($tableName) {
        $results = [];
        $fields = $this->getFields($tableName);
        foreach ($fields as $field) {
            $results[$field['Field']] = $field;
        }
        return $results;
    }


    // --------------------------------------------------------
    // TSVファイルインポート

    public function readTSV($txt, $diff = false) {

        $this->connectPrimary();
        $results = [];

        $rows = explode("\n", $txt);

        // TSVのカラム
        $cols = explode("\t", trim($rows[0]));

        // 次行からデータ
        for ($r = 1; $r < count($rows); $r++) {

            $res = $this->readRow($cols, $rows[$r]);
            if ($res) {
                if (is_string($res)) {
                    $line = $r + 1;
                    $res = "$line:$res\n";
                    if (self::$cliMode) {
                        echo $res;
                        return $results;
                    }
                }
                $results[] = $res;
            }
        }

        return $results;
    }

    public function readRow($cols, $row, $diff = false) {
        $values = explode("\t", $row);
        if (empty($values[0])) {
            if (trim(implode('', $values)) == "") {
                // からっぽ
                return;
            }
        }

        // 末尾の空データ切り捨て
        while (count($values) > count($cols)) {
            $cnt = count($values);
            if (!trim($values[$cnt - 1])) {
                unset($values[$cnt - 1]);
            } else {
                break;
            }
        }

        if (count($values) != count($cols)) {
            // カラム数がデータ数と不一致
            return "Not Match Column count";
        }
        // 連想配列の形に置き換え
        $rowData = [];
        for ($c = 0; $c < count($cols); $c++) {
            $key = $cols[$c];
            if ($key) {
                $rowData[$cols[$c]] = trim($values[$c]);
            }
        }

        // テーブルに合わせたValidate・整形
        $rowData = $this->validate($rowData);
        if ($rowData) {
            // 挿入・更新
            $res = $this->updateRow($rowData, $diff);
        }

    }

    // --------------------------------------------------------
    // 整形Validate 各テーブル対応クラスでオーバーライド

    protected function validate($data) {
        $table = $this->tableName;
        $fields = $this->getFields($table);
        // 不足埋め
        foreach ($fields as $field) {
            $key = $field['Field'];
            if (!isset($data[$key])) {
                $Null = $field['null'];
                $Default = $field['Default'];
                $Extra = $field['Extra'];
                $Type = $field['Type'];
                if ($Null == 'NO' && $Default == null) {
                    if (strpos($Extra, 'auto_increment') === false) {
                        if (strpos($Type, 'int') !== false) {
                            $data[$key] = 0;
                        } elseif (strpos($Type, 'double') !== false) {
                            $data[$key] = 0;
                        } elseif (strpos($Type, 'char') !== false) {
                            $data[$key] = '';
                        }
                    }
                }

            }
        }
        return $data;
    }

    // --------------------------------------------------------
    // 行更新もしくはインサート

    protected function updateRow($rowData, $diff = false) {

        $table = $this->tableName;
        $uniqkeys = $this->uniqkeys;

        // --------------------------------
        // 既存行確認

        $querys = [];
        // ユニークキーで特定
        foreach ($uniqkeys as $uniqkey) {
            $querys[$uniqkey] = $rowData[$uniqkey];
        }
        $exists = $this->get($querys, $table);
        if (is_string($exists)) {
            return $exists;
        }

        if (self::$cliMode) {
            // echo json_encode($querys) . "\r";
        }


        if ($exists) {
            // --------------------------------
            // UPDATE
            $modified = [];
            foreach ($rowData as $key => $value) {
                if ($exists[$key] != $value) {
                    $exists[$key] = $value;
                    $modified[$key] = true;
                }
            }
            if ($diff) {
                return ['row' => $exists, 'modified' => $modified];
            } else {
                if ($modified) {
                    $res = $this->save($exists, $table);
                    if (is_string($res)) {
                        return $res;
                    }
                }
            }

        } else {
            // --------------------------------
            // INSERT

            if ($diff) {
                return ['row' => $rowData, 'modified' => array_keys($rowData)];
            } else {
                $res = $this->insert($rowData, $table);
                if (is_string($res)) {
                    return $res;
                }
            }
        }
    }


    // --------------------------------------------------------
    // エクスポート用TSV作成

    protected function exportTsvFromList($rows) {
        $tsvRows = [];
        if (count($rows)) {
            // カラム名取り出し
            $row = $rows[0];
            $colmuns = [];
            foreach ($row as $key => $value) {
                $colmuns[] = $key;
            }
            // 一行目はカラム名
            $tsvRows[] = implode("\t", $colmuns);

            // データ抽出
            foreach ($rows as $row) {
                foreach ($row as $key => $value) {
                    $data[] = $value;
                }
                $tsvRows[] = implode("\t", $data);
            }
        }
        return implode("\n", $tsvRows);
    }


}