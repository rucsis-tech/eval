<?php
namespace classes;
/**
 * 管理画面用のテーブルレコード取得クラス
 * Class TABLEclass
 */

class TableClass {

    private $TableInfo = [];
    private $fetchTableName = '';

    /**
     *
     * @param $fetchTable
     */

    function setFetch($fetchTable) {
        if ($fetchTable && $_REQUEST['table'] == $fetchTable) {
            $this->fetchTableName = $fetchTable;
            $info['page'] = $_GET['page'] ? $_GET['page'] : '0';
            $info['rows'] = $_GET['rows'] ? $_GET['rows'] : '5';
            $info['page'] = $_GET['page'] ? $_GET['page'] : '0';
            $info['order'] = $_GET['order'] ? $_GET['order'] : 'id';
            $info['asc'] = $_GET['asc'] ? $_GET['asc'] : 'asc';
            setcookie($fetchTable, json_encode($info), time() + 86400 * 30, '/dev/soo/');
            $_COOKIE[$fetchTable] = json_encode($info);
        }
    }

    /**
     * テーブル情報の追加
     * @param $name
     * @param $info
     */

    function addTableInfo($name, $info) {
        if ($this->fetchTableName && $name != $this->fetchTableName) {
            // 指定テーブルがある場合はそれ以外追加しない
            return;
        } else {
            $this->TableInfo[$name] = $info;
        }
    }

    /**
     * テーブル情報取得
     * @param $guid
     * @param $TableInfo
     * @return mixed
     */


    function getTables($guid) {
        $TableInfo = $this->TableInfo;
        $INK = new INKclass();
        foreach ($TableInfo as $tableName => $item) {
            if (empty($fetchTable) || $fetchTable == $tableName) {
                if (isset($item['param'])) {
                    $param = $item['param'];
                } elseif ($guid) {
                    $param = ['guid' => $guid];
                } else {
                    $param = [];
                }

                $count = $INK->count($param, $tableName);
                if ($count) {
                    $rows = $this->getConf($tableName, 'rows', 5);
                    $page = $this->getConf($tableName, 'page', 0);
                    if ($rows == 'all') {
                        $page = 0;
                    } else {
                        $max = intval(($count - 1) / $rows);
                        if ($page > $max) {
                            $page = $max;
                        }
                        $offset = $rows * $page;
                        $param['LIMIT'] = "$offset,$rows";
                    }
                    $order = $this->getConf($tableName, 'order', 'id');
                    $asc = $this->getConf($tableName, 'asc', 'asc');
                    $param['ORDER'] = "$tableName.$order $asc";

                    $TableInfo[$tableName]['list'] = $INK->getAll($param, $tableName);
                    $TableInfo[$tableName]['count'] = $count;
                    $TableInfo[$tableName]['page'] = $page;
                    $TableInfo[$tableName]['rows'] = $rows;
                    $TableInfo[$tableName]['order'] = $order;
                    $TableInfo[$tableName]['asc'] = $asc;
                    $TableInfo[$tableName]['fields'] = $INK->getColumns($tableName);
                }

            }
        }
        return $TableInfo;
    }

    //---------------------------------------------
    //　cookieから設定取得
    function getConf($table, $key, $default) {
        $value = $default;
        $cookie = $_COOKIE[$table];
        if ($cookie) {
            $info = json_decode($cookie, true);
            if ($info[$key]) {
                $value = $info[$key];
            }
        }
        return $value;
    }


}