<?php

namespace classes\trait;

trait Pagenation {

    public $table_name = 'recipes';


    public function getPage($Model) {
        $condition = $this->setCondition();

        $where = " 1 ";
        if ($condition['enabled'] && $condition['disabled']) {

        } elseif ($condition['enabled']) {
            $where = ' enabled = 1 ';
        } else {
            $where = ' enabled = 0 ';
        }

        if ($condition['filter']) {
            $filter = $condition['filter'];
            $where .= " AND ( title LIKE '%$filter%' OR name LIKE '%$filter%' OR mail LIKE '%$filter%' ) ";
        }

        $condition['count'] = $Model->query("SELECT count(*) AS CNT FROM recipes WHERE $where ")[0]['CNT'];
        $offset = ($condition['number'] - 1) * $condition['rows'];
        $condition['offset'] = ($offset >= $condition['count']) ? 0 : $offset;
        $condition['max_number'] = intval(($condition['count'] - 1) / $condition['rows']) + 1;

        $sort = $condition['sort'];
        $order = $condition['order'];
        $params = [];
        $params[] = intval($condition['offset']);
        $params[] = intval($condition['rows']);
        $sql = "SELECT * FROM recipes WHERE $where ORDER BY $sort $order LIMIT ?,? ";
        $condition['list'] = $Model->query($sql, $params);
        return $condition;
    }

    public function setCondition() {

        $cookie_name = $this->table_name . "_conf";

        if (isset($_COOKIE[$cookie_name])) {
            $condition = json_decode($_COOKIE[$cookie_name], true);
        } else {
            $condition = ['number' => 1, 'sort' => 'id', 'order' => 'asc', 'filter' => '', 'rows' => 10, 'enabled' => '1', 'disabled' => '1'];
        }

        if (isset($_REQUEST['sort'])) {
            $order = $condition['order'] ?? 'asc';

            if ($_REQUEST['sort'] == $condition['sort']) {
                $order = ($order == 'asc') ? 'desc' : 'asc';
            }
            $condition['sort'] = $_REQUEST['sort'];
            $condition['order'] = $order;
        }

        $condition['enabled'] = $_REQUEST['enabled'] ?? $condition['enabled'];
        $condition['disabled'] = $_REQUEST['disabled'] ?? $condition['disabled'];
        $condition['number'] = $_REQUEST['number'] ?? $condition['number'];
        $condition['filter'] = $_REQUEST['filter'] ?? $condition['filter'];
        $condition['rows'] = $_REQUEST['rows'] ?? $condition['rows'];
        setcookie($cookie_name, json_encode($condition), time() + 86400);

        return $condition;
    }


}