<?php

namespace classes;

use classes\trait\DBTableTrait;
use Exception;
use PDO;
use PDOStatement;

class DBQueryClass {

    use DBTableTrait;

    protected string $tableName = '';

    public static bool $isCliMode;

    private ?DBConnectClass $DBConnect;

    private string $resultClassName = 'stdClass';

    private string $from = '';
    private array $selects = [];
    public array $wheres = [];
    private array $orders = [];
    private array $joins = [];
    private array $groups = [];

    private string $sql = '';
    private array $params = [];

    private int $offset = 0;
    private int $limit = 0;


    const ERROR_NO_TABLE = 'NO TABLE';

    function __construct(DBConnectClass $connect = null) {
        $this->DBConnect = $connect;
        self::$isCliMode = (php_sapi_name() == 'cli');
    }

    /**
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     * @throws Exception
     */
    private function statement(string $sql, array $params = []): PDOStatement {

        if (!is_array($params)) {
            throw new Exception(" (SQL: $sql [$params])");
        }

        try {
            $statement = $this->DBConnect->prepare($sql , $params);

            if (!$statement->execute()) {
                throw new Exception("statement::execute false");
            }

        } catch (Exception $e) {
            $params = json_encode($params, JSON_UNESCAPED_UNICODE);
            throw new Exception($e->getMessage() . " (SQL: $sql [$params])");
        }

        return $statement;

    }

    public function testBuild(): array {

        $this->buildQuery();
        return $this->getRequestQuery();

    }


    /**
     * @return PDOStatement
     * @throws Exception
     */
    private function buildQuery(): PDOStatement {

        $Select = $this->buildSelect();
        $From = $this->buildFrom();
        $Join = $this->buildJoin();
        $Where = $this->buildWhere();
        $Order = $this->buildOrderBy();
        $Limit = $this->buildLimit();
        $Group = $this->buildGroupBy();

        $this->sql = $Select->SQL . $From->SQL . $Join->SQL . $Where->SQL . $Group->SQL . $Order->SQL . $Limit->SQL;
        $this->params = array_merge($Select->PARAMS, $From->PARAMS, $Join->PARAMS, $Where->PARAMS, $Group->PARAMS, $Order->PARAMS, $Limit->PARAMS);


        return $this->statement($this->sql, $this->params);
    }

    // --------------------------------------------------------
    // SELECT

    /**
     * @param $columns
     * @return $this
     * @throws Exception
     */
    public function select($columns): static {
        if (empty($columns)) {
            // リセットする
            $this->selects = [];
            return $this;

        } else if (is_string($columns)) {
            $columns = explode(',', $columns);
        } else if (!is_array($columns)) {
            throw new Exception('Fail SELECT' . serialize($columns));
        }


        foreach ($columns as $key => $column) {
            $column = self::formatKey($column);
            if (!is_int($key)) {
                $key = self::formatKey($key);
                $column = $column . " AS $key";
            }
            if (!in_array($column, $this->selects)) {
                $this->selects[] = $column;
            }
        }
        return $this;
    }

    private function buildSelect(): object {
        $DISTINCT = '';
        if (empty($this->selects)) {
            $cols = "*";
        } else {
            $cols = '';
            $selects = [];
            foreach ($this->selects as $select) {
                if (strtoupper($select) === '`DISTINCT`') {
                    $DISTINCT = 'DISTINCT';
                } else {
                    $selects[] = $select;
                }
            }
            $cols = implode(',', $selects);
        }

        return (object)['SQL' => " SELECT $DISTINCT $cols ", 'PARAMS' => []];
    }

    // --------------------------------------------------------

    /**
     * @param $table_name
     * @return $this
     * @throws Exception
     */
    public function from($table_name): static {
        if (is_string($table_name)) {
            $this->from = $table_name;
        } else {
            throw new Exception('Fail FROM table' . serialize($table_name));
        }
        return $this;
    }

    public function table($table_name): static {
        return $this->from($table_name);
    }

    /**
     * @return object
     * @throws Exception
     */
    private function buildFrom(): object {
        $table_name = ($this->from ?: $this->tableName);
        if (empty($table_name)) {
            throw new Exception(self::ERROR_NO_TABLE);
        }
        return (object)['SQL' => " FROM $table_name ", 'PARAMS' => []];
    }

    // --------------------------------------------------------

    /**
     * *1pram
     * where(条件)
     *
     * *2param
     * where(値1,値2)
     * where(条件STR,値配列)
     *
     * *3param
     * where(値１,'演算子',値2)
     *
     * @param $p1
     * @param $p2
     * @param $p3
     * @return $this
     * @throws Exception
     */
    public function where($p1, $p2 = null, $p3 = null): static {

        $whereOrders = self::getWhereOrders($p1, $p2, $p3);
        $this->addWhere($whereOrders);

        return $this;
    }

    /**
     * whereIn専用
     * @param $key
     * @param $array
     * @return $this
     * @throws Exception
     */
    public
    function in($key, $array): static {
        $this->addWhere(['IN', $key, $array]);
        return $this;
    }

    /**
     * between専用
     * @param $key
     * @param $since
     * @param $until
     * @return $this
     * @throws Exception
     */
    public
    function between($key, $since, $until, $not_option = null): static {
        if ($since !== null && $until !== null) {
            $this->where([$key, 'BETWEEN', [$since, $until]], $not_option);
        } else if ($since === null) {
            $this->where([$key, '<=', $until], $not_option);
        } else {
            $this->where([$key, '>=', $since], $not_option);
        }

        return $this;
    }

    /**
     * 条件式配列追加
     * @param $key
     * @return void
     */

    private
    function addWhere(array $array): void {
        $this->wheres[] = $array;
    }

    static private function is_assoc($data): bool {

        if (!is_array($data))
            return false;

        $keys = array_keys($data);
        $range = range(0, count($data) - 1);
        //var_dump($keys);
        //var_dump($range);
        foreach ($keys as $i => $value) {
            if (!is_int($value) || $value !== $range[$i]) {
                return true;
            }
        }

        return false;
    }

    /**
     * where指示を条件配列に構成しなおす
     * @param $p1
     * @param $p2
     * @param $p3
     * @return array|array[]
     * @throws Exception
     */

    static
    private function getWhereOrders($p1, $p2 = null, $p3 = null): array {


        if ($p2 === null) {
            // *1pram ----------------------------------------------------
            if (is_string($p1)) {
                // RAW文 where("colum = 1")
                return ['RAW', $p1, null];

            } elseif (self::is_assoc($p1)) {
                // 条件連想配列　where(['key1'=>$value,'key'=>$value,...])
                $result = [];
                foreach ($p1 as $key => $value) {
                    $result[] = [$key, '=', $value];
                }
                return $result;

            } elseif (is_array($p1)) {

                if (is_string($p1[0]) || is_string($p1[1] ?? null) || is_string($p1[2] ?? null)) {
                    // where([ 'a' , '=' , 'b' ])
                    return self::getWhereOrders($p1[0], $p1[1] ?? null, $p1[2] ?? null);

                } else {
                    // 条件配列 where([[条件],[条件],[条件],...]
                    $result = [];
                    foreach ($p1 as $key => $value) {
                        $result[] = $value;
                    }
                    return $result;
                }

            } else {
                throw new Exception("WHERE 1 PARAM ERROR " . json_encode($p1));
            }

        } else if ($p3 === null) {
            // *2pram ----------------------------------------------------
            if (is_string($p1) && is_array($p2)) {
                // where("文字列命令" , [条件配列])
                $OPERATION = strtoupper($p1);
                $ARRAY = $p2;
                return self::get2ParamOrder($OPERATION, $ARRAY);

            } elseif (!is_array($p1) && !is_array($p2)) {
                // where( 値1 , 値2 )
                // 値1 = 値2
                return [$p1, '=', $p2];

            } elseif (is_array($p1) && !is_array($p2)) {
                // where( [条件配列] , "文字列命令")
                $OPERATION = strtoupper($p2);
                $ARRAY = $p1;
                return self::get2ParamOrder($OPERATION, $ARRAY);

            } else {
                throw new Exception("WHERE 2 PARAM ERROR " . json_encode([$p1, $p2]));
            }

        } else {
            // *3param ----------------------------------------------------
            // where( ？ , "OPERATION" , ？)
            $OPERATION = strtoupper($p2);

            if ($OPERATION == 'IN') {
                // where("key" ,  "IN" , [値配列] )
                return ["IN", $p1, $p3];
            } else if ($OPERATION == 'BETWEEN') {
                // where("key" ,  "BETWEEN" , [ since , last ] )
                if ($p3[0] === null) return [$p1, '<=', $p3[1]];
                if (count($p3) === 1 || $p3[1] === null) return [$p1, '>=', $p3[0]];
                return ["BETWEEN", $p1, $p3];
            } else if ($OPERATION == 'OR' || $OPERATION == 'AND') {
                // where([条件1] ,  "OR" , [条件2])
                $order1 = self::getWhereOrders($p1);
                $order2 = self::getWhereOrders($p3);
                return [$OPERATION, $order1, $order2];

            } else if (!is_array($p1) && is_string($p2) && !is_array($p3)) {
                //　where(値 , "文字" , 値)
                return [$p1, $p2, $p3];

            } else {
                throw new Exception("WHERE 3 PARAM ERROR " . json_encode([$p1, $p2, $p3]));
            }
        }


        // 連想配列判定


        // オーダー判別


    }

    /**
     * @throws Exception
     */
    static
    private function get2ParamOrder($OPERATION, $ARRAY): array {
        if ($OPERATION === '' || $OPERATION === 'NOT') {
            // where('NOT',[条件])
            // where('',[条件])
            $order = self::getWhereOrders($ARRAY[0], $ARRAY[1] ?? NULL, $ARRAY[2] ?? NULL);
            return [$OPERATION, $order];

        } elseif ($OPERATION === 'OR' || $OPERATION === 'AND') {
            // where('OR',[ [条件1] , [条件2] ])
            $ORS = [$OPERATION];
            foreach ($ARRAY as $key => $value) {
                $order = self::getWhereOrders($value);
                $ORS[] = $order;
            }
            return $ORS;

        } else {
            // where( "RAW条件" , [値配列] )
            return ['RAW', $OPERATION, $ARRAY];
        }
    }


    /**
     * 展開条件式取得
     * @return object
     */
    public
    function buildWhere(): object {

        try {
            $extends = self::extractWhere($this->wheres);
        } catch (Exception $e) {
            $orders = print_r($this->wheres, true);
            $trace = $e->getTraceAsString();
            throw new Exception("WHERES {$orders}\n{$trace}" . $e->getMessage());
        }


        $sql = $extends['sql'];
        $allParams = $extends['params'];
        if ($sql) {
            $sql = " WHERE $sql ";
        } else {
            $sql = " WHERE 1 ";
        }
        return (object)['SQL' => $sql, 'PARAMS' => $allParams];
    }


    /**
     * @param $num
     * @return array
     */

    private
    static function getReplaces($num): array {
        $result = [];
        for ($i = 0; $i < $num; $i++) {
            $result[] = '?';
        }
        return $result;
    }


    /**
     * 条件配列展開　再帰
     * @param $array // 条件配列
     * @return array
     */
    private
    static function extractWhere($wheres): array {
        if (empty($wheres)) {
            return ['sql' => '', 'params' => []];
        }

        $formulas = [];
        $allParams = [];

        $w1 = $wheres[0] ?? NULL;

        if (is_array($w1)) {
            // 再帰構造 -----------------------------------------------
            // [ [条件配列1], [条件配列2] , [条件配列3] ]

            foreach ($wheres as $where) {
                $extract = self::extractWhere($where);
                $formulas[] = $extract['sql'];
                $allParams = array_merge($allParams, $extract['params']);
            }

        } else if ($w1 === '') {
            // 指定ない -----------------------------------------------
            // [ "", [条件配列1] ]
            $extract = self::extractWhere($wheres[1]);
            $formulas[] = $extract['sql'];
            $allParams = array_merge($allParams, $extract['params']);
        } else if ($w1 == 'NOT') {
            // NOT指定 -----------------------------------------------
            // [ "NOT", [条件配列1] ]
            $extract = self::extractWhere($wheres[1]);
            $formulas[] = "(NOT " . $extract['sql'] . ")";
            $allParams = array_merge($allParams, $extract['params']);
        } else if ($w1 == 'OR' || $w1 == 'AND') {
            // AND/OR指定 -----------------------------------------------
            // [ "OR", [条件配列1] , [条件配列2] , .... ]
            $conditions = [];
            for ($i = 1; $i < count($wheres); $i++) {
                $extract = self::extractWhere($wheres[$i]);
                $conditions[] = $extract['sql'];
                $allParams = array_merge($allParams, $extract['params']);
            }
            $formulas[] = '(' . implode($w1, $conditions) . ')';

        } else if ($w1 == 'IN') {
            // IN -----------------------------------------------
            // [ "IN", "key" , [VALUE配列] ]
            $key = self::formatKey($wheres[1]);
            $params = $wheres[2];
            $placeholder = join(',', self::getReplaces(count($params)));
            $formulas[] = "($key IN ($placeholder))";
            $allParams = array_merge($allParams, $params);
        } else if ($w1 == 'BETWEEN') {
            // BETWEEN -----------------------------------------------
            // [ "BETWEEN", "key" , [ since , last ] ]
            $key = self::formatKey($wheres[1]);
            $params = $wheres[2];
            $formulas[] = "($key BETWEEN ? AND ?)";
            $allParams = array_merge($allParams, $params);
        } else if ($w1 == 'RAW') {
            // RAW SQL -----------------------------------------------
            // [ 'RAW' , 'SLQ' ,  [VALUE配列] ]
            $raw = $wheres[1];
            $params = $wheres[2] ?? null;
            $formulas[] = "($raw)";
            if ($params) $allParams = array_merge($allParams, $params);
        } else if (!is_array($w1)) {
            // [KEY,演算子,VALUE] ---------------------------------------
            $key = self::formatKey($wheres[0]);
            $operator = $wheres[1];
            $formulas[] = "($key $operator ?)";
            $allParams = array_merge($allParams, [$wheres[2]]);
        } else {
            throw new Exception("WHERE PARAM ERROR: " . json_encode($wheres));
        }

        $sql = implode(' AND ', $formulas);
        return ['sql' => (empty($sql) ? '' : "($sql)"), 'params' => $allParams];
    }

// --------------------------------------------------------
// JOIN


    public
    function join(string $table, $on1, $on2 = null, $on3 = null): static {
        if ($on2 == null) {
            $this->joins[] = ['INNER JOIN', $table, $on1, null];
        } else if (is_array($on2)) {
            $this->joins[] = ['INNER JOIN', $table, $on1, $on2];
        } else {
            $this->joins[] = ['INNER JOIN', $table, "$on1 $on2 $on3", null];
        }
        return $this;
    }

    public
    function innerJoin(string $table, $on1, $on2 = null, $on3 = null): static {
        return $this->join($table, $on1, $on2, $on3);
    }

    public
    function leftJoin(string $table, $on1, $on2 = null, $on3 = null): static {
        if ($on2 == null) {
            $this->joins[] = ['LEFT JOIN', $table, $on1, null];
        } else if (is_array($on2)) {
            $this->joins[] = ['LEFT JOIN', $table, $on1, $on2];
        } else {
            $this->joins[] = ['LEFT JOIN', $table, "$on1 $on2 $on3", null];
        }
        return $this;
    }

    private
    function buildJoin(): object {
        $sql = '';
        $params = [];
        foreach ($this->joins as $key => $join) {
            $joinType = $join[0];
            $table = $join[1];
            $on = $join[2];
            $param = $join[3];

            $sql .= " $joinType $table ON $on ";
            if (is_array($param)) {
                $params = array_merge($params, $param);
            }
        }

        return (object)['SQL' => $sql, 'PARAMS' => $params];
    }

// --------------------------------------------------------
// Group

    /**
     * @param $groups
     * @param $having
     * @return $this
     */

    public
    function groupBy($groups, $having = null): static {

        $this->groups = [self::formatKey($groups), $having];

        return $this;
    }

    /**
     * @return object
     */
    private
    function buildGroupBy(): object {
        $sql = '';
        if (isset($this->groups[0])) {
            $sql = " GROUP BY " . $this->groups[0] . " ";
        }
        if (isset($this->groups[1])) {
            $sql .= " HAVING " . $this->groups[1] . " ";
        }
        return (object)['SQL' => $sql, 'PARAMS' => []];
    }

// --------------------------------------------------------
// order


    /**
     * @param $col
     * @param string $order
     * @return $this
     */

    public
    function orderBy($col, string $order = 'ASC'): static {
        $this->orders[] = [self::formatKey($col), $order];
        return $this;
    }

    /**
     * @return object
     */
    private
    function buildOrderBy(): object {
        $sql = '';
        $orders = [];
        foreach ($this->orders as $order) {
            $orders[] = $order[0] . " " . $order[1];
        }
        if ($orders) {
            $sql = " ORDER BY " . join(',', $orders) . " ";

        }
        return (object)['SQL' => $sql, 'PARAMS' => []];
    }

// --------------------------------------------------------

    /**
     * @param $n1
     * @param $n2
     * @return $this
     */
    public
    function limit($n1, $n2 = null): static {
        if ($n2 == null) {
            $this->limit = $n1;
        } else {
            $this->offset = $n1;
            $this->limit = $n2;
        }
        return $this;
    }

    /**
     * @param $offset
     * @return $this
     */

    public
    function offset($offset): static {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return object
     */

    private
    function buildLimit(): object {
        $sql = '';
        $params = [];

        if ($this->limit) {
            $sql = ' LIMIT ?,? ';
            $params[] = $this->offset;
            $params[] = $this->limit;
        }

        return (object)['SQL' => $sql, 'PARAMS' => $params];
    }


// --------------------------------------------------------
// クラス取得

    /**
     * @param null $p1
     * @param null $p2
     * @param null $p3
     * @return mixed
     * @throws Exception
     */

    public
    function first($p1 = null, $p2 = null, $p3 = null): mixed {
        if ($p1) $this->where($p1, $p2, $p3);
        $this->limit(0, 1);

        $statement = $this->buildQuery();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->resultClassName);
        $item = $statement->fetch();
        return $item ? $item : null;
    }

    /**
     * @return array|false
     * @throws Exception
     */

    public
    function all($p1 = null, $p2 = null, $p3 = null): bool|array {
        if ($p1) $this->where($p1, $p2, $p3);

        $statement = $this->buildQuery();
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->resultClassName);
        return $statement->fetchAll();
    }

    /**
     * IDをindexにしてすべてを返す
     * @param $p1
     * @param $p2
     * @param $p3
     * @return bool|array
     * @throws Exception
     */
    public
    function idArrayAll($p1 = null, $p2 = null, $p3 = null): bool|array {
        $result = [];
        $all = $this->all($p1, $p2, $p3);
        foreach ($all as $item) {
            $result[$item->id] = $item;
        }
        return $result;
    }

    /**
     * @param $resultClassName
     * @return void
     */

    public
    function setResultClassName($resultClassName): void {
        $this->resultClassName = $resultClassName;
    }

// --------------------------------------------------------
// 配列取得
    /**
     * @param null $p1
     * @param null $p2
     * @param null $p3
     * @return mixed
     * @throws Exception
     */
    public
    function row($p1 = null, $p2 = null, $p3 = null): mixed {
        if ($p1) $this->where($p1, $p2, $p3);

        $statement = $this->buildQuery();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array|false
     * @throws Exception
     */
    public
    function rows($p1 = null, $p2 = null, $p3 = null): bool|array {
        if ($p1) $this->where($p1, $p2, $p3);

        $statement = $this->buildQuery();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public
    function IndexRows($index_name = 'id'): bool|array {
        $statement = $this->buildQuery();
        $array = $statement->fetchAll(PDO::FETCH_ASSOC);
        $rows = [];
        foreach ($array as $item) {
            $key = $item[$index_name];
            unset($item[$index_name]);
            if (count($item) == 1) $item = array_shift($item);
            $rows[$key] = $item;
        }
        return $rows;
    }


// --------------------------------------------------------
// カウント取得

    /**
     * @param null $column
     * @return int
     * @throws Exception
     */
    public
    function count($column = null): int {

        if (empty($column)) {
            $column = 'count(*)';
        } else {
            $column = self::formatKey($column);
        }

        $this->selects = [];
        $this->select("$column AS COUNT_NUM");
        $row = $this->first();
        return $row->COUNT_NUM ?? 0;
    }


// --------------------------------------------------------
// SQL直接実施

    /**
     * @throws Exception
     */
    public
    function execute(string $sql, array $params = [], $fetchMode = null): null|array|int {

        $sql_s = explode(' ', strtoupper($sql));
        if (in_array('UPDATE', $sql_s, true)
            || in_array('INSERT', $sql_s, true)
            || in_array('DELETE', $sql_s, true)
            || in_array('ALTER', $sql_s, true)
            || in_array('CREATE', $sql_s, true)
            || in_array('REPLACE', $sql_s, true)) {
            $this->DBConnect->primary();
        }

        $statement = $this->statement($sql, $params);
        if (in_array('UPDATE', $sql_s, true) && !in_array('SELECT', $sql_s, true)) { // FOR UPDATE は対象外
            return $statement->rowCount();
        } else if ($fetchMode == PDO::FETCH_CLASS) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->resultClassName);
            return $statement->fetchAll();
        } else {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }


    }

// --------------------------------------------------------
// 更新 UPDATE

    /**
     * @param $prm1
     * @param null $prm2
     * @return PDOStatement
     * @throws Exception
     */
    public
    function update($prm1, $prm2 = null): PDOStatement {
        $params = $prm1;
        if ($prm2 == null) {
            $table = ($this->from ?: $this->tableName);
        } else {
            $table = $prm2;
        }

        if (empty($table)) {
            throw new \Exception(get_class($this) . ': Table name cannot be empty');
        }

        $columns = [];
        $values = [];
        $Where = $this->buildWhere();

        foreach ($params as $column => $value) {
            $columns[] = "`$column` = ? ";
            $values[] = $value;
        }
        $table = self::formatKey($table);
        $sql = "UPDATE $table "
            . "SET " . implode(',', $columns)
            . $Where->SQL;
        $values = array_merge($values, $Where->PARAMS);

        $this->sql = $sql;
        $this->params = $values;

        $this->DBConnect->primary();
        return $this->statement($sql, $values);
    }


// --------------------------------------------------------
// 挿入 INSERT


    /**
     * @param $prm1
     * @param $prm2
     * @param $prm3
     * @return mixed
     * @throws Exception
     */

    public
    function insert($prm1, $prm2 = null, $prm3 = null): mixed {

        if (is_string($prm2)) {
            $params = $prm1;
            $table = $prm2;
            $onDuplicateUpdate = $prm3;
        } else {
            $params = $prm1;
            $table = ($this->from ?: $this->tableName);
            $onDuplicateUpdate = $prm3;
        }

        $values = [];
        $holder = [];
        $columns = [];

        foreach ($params as $column => $value) {
            $columns[] = "`$column`";
            $values[] = $value;
            $holder[] = '?';
        }

        $column_str = implode(',', $columns);
        $holder_str = implode(',', $holder);
        $fmt_table = $this->formatKey($table);
        $sql = "INSERT " . " INTO $fmt_table ($column_str) VALUES($holder_str) ";

        if ($onDuplicateUpdate) {
            $sets = [];
            foreach ($params as $column => $value) {
                $sets[] = "`$column` = ? ";
                $values[] = $value;
            }
            $sql .= " ON DUPLICATE KEY UPDATE "
                . implode(',', $sets);
        }

        $this->DBConnect->primary();

        $statement = $this->statement($sql, $values);
        return $this->lastInsertId();

    }

    /**
     * @return mixed
     */

    public
    function lastInsertId(): mixed {
        return $this->DBConnect->lastInsertId();
    }

    /**
     * @return array
     */

    public
    function getRequestQuery(): array {
        $sql = $this->sql;
        foreach ($this->params as $param) {
            if (is_string($param)) {
                $param = "'$param'";
            }
            if ($param === null) {
                $param = 'null';
            }
            $sql = preg_replace("/\?/", $param, $sql, 1);
        }

        return [
            'params' => $this->params,
            'query' => $this->sql,
            'sql' => $sql,
        ];
    }

    public
    function clone(): DBQueryClass {
        return clone $this;
    }


}
