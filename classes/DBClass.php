<?php

namespace classes;

use classes\trait\DBTableTrait;
use classes\trait\StringUtils;
use Exception;
use PDO;
use stdClass;

class DBClass extends stdClass {

    use StringUtils;
    use DBTableTrait;

    protected static string $tableName = '';
    protected static string $primaryKey = 'id';
    private static DBConnectClass $DBConnect;
    public static DBQueryClass $Query;

    protected static array $database = [];

    // カラムフィールド
    protected static array $fields = [];
    private static string $echo = '';

    static
    public function getDatabase(): array {
        if (empty(static::$database)) {
            $database = config('DB_NAME');
            $user = config('DB_USER');
            $password = config('DB_PASS');
            $host = config('DB_PRIMARY');

            static::$database = [
                'primary' => [
                    'database' => $database,
                    'user' => $user,
                    'password' => $password,
                    'host' => $host,
                ],
                'secondary' => [
                    'database' => $database,
                    'user' => config('DB_SECONDARY_USER', $user),
                    'password' => config('DB_SECONDARY_PASS', $password),
                    'host' => config('DB_SECONDARY', $host),
                ],
            ];
        }
        return static::$database;
    }

    static
    public function setDatabase($database) {
        static::$database = $database;
    }

    static public function getLastQuery() {
        try {
            return [self::$DBConnect->last_query, self::$DBConnect->last_params];
        } catch (Exception $e) {
        }
        return [];
    }

    // --------------------------------------------------------

    static
    public function DBConnect(): DBConnectClass {
        if (empty(self::$DBConnect)) {
            $database = self::getDatabase();
            self::$DBConnect = DBConnectClass::getConnection($database);
        }
        return self::$DBConnect;
    }

    /**
     * 自身にモデル型キャスト
     * @param $object
     * @return static|null
     */
    static
    public function cast($object): static|null {
        return $object;
    }

    /**
     * @param $filepath
     * @return void
     */
    static
    public function importCSV($filepath): void {
        $handle = fopen($filepath, "r");
        $index = null;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (empty($index)) {
                $index = $data;
                continue;
            }
            $import = [];
            for ($i = 0; $i < count($index); $i++) {
                $import[$index[$i]] = $data[$i];
            }
            self::insert($import);
        }
        fclose($handle);
    }

    /**
     * @param $table
     * @return void
     */
    static
    public function setTableName($table): void {
        self::$tableName = $table;
    }

    static
    public function getFields(): array {
        if (empty(static::$fields)) {
            static::$fields = self::getFullColumns(static::$tableName);
        }
        return static::$fields;
    }


    // --------------------------------------------------------
    // クエリメソッド


    /**
     * SQLの直接実行
     * @param $sql
     * @param array $param
     * @return array
     * @throws Exception
     */
    static
    public function sql($sql, array $param = []): array|null|int {
        $query = new DBQueryClass(self::DBConnect());
        return $query->execute($sql, $param);
    }

    /**
     * クエリクラスの生成
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function query(): DBQueryClass {
        $query = new DBQueryClass(self::DBConnect());
        $query->from(static::$tableName);
        $query->setResultClassName(static::class);
        self::$Query = $query;
        return $query;
    }

    /**
     * @param $p1
     * @param $p2
     * @param $p3
     * @return static|null
     * @throws Exception
     */
    static
    public function first($p1, $p2 = null, $p3 = null): static|null {
        if ($p3 !== null) {
            // first('id','=',1)
            return self::where($p1, $p2, $p3)->first();
        } else if ($p2 !== null) {
            // first('id',1)
            return self::where($p1, '=', $p2)->first();
        } else {
            // $p1のみ
            if (is_array($p1)) {
                // first(['lqid' => $lqid, 'own' => 0])
                return static::query()->where($p1)->first();

            } else {
                // first(1)
                return self::where(static::$primaryKey, $p1)->first();
            }
        }
    }


    /**
     * @param $key
     * @param $p2
     * @param $p3
     * @return int
     * @throws Exception
     */
    static
    public function count($key = null, $p2 = null, $p3 = null): int {
        $query = static::query();
        if ($key) {
            $query = $query->where($key, $p2, $p3);
        }
        return $query->count();
    }


    // ---------------------------------------------------------
    // クエリクラスへのエイリアス

    /**
     * @throws Exception
     */
    static
    public function select($cols): DBQueryClass {
        $query = static::query();
        return $query->select($cols);
    }

    /**
     * @param $table
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function from($table): DBQueryClass {
        $query = static::query();
        return $query->from($table);
    }

    /**
     * @param $table
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function into($table): DBQueryClass {
        $query = static::query();
        return $query->from($table);
    }

    /**
     * @param $table
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function table($table): DBQueryClass {
        $query = static::query();
        return $query->from($table);
    }

    /**
     * @param $p1
     * @param $p2
     * @param $p3
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function where($p1, $p2 = null, $p3 = null): DBQueryClass {
        $query = static::query();
        return $query->where($p1, $p2, $p3);
    }

    /**
     * @param $p1
     * @param $p2
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function orderBy($p1, $p2 = null): DBQueryClass {
        $query = static::query();
        return $query->orderBy($p1, $p2);
    }

    /**
     * @param $p1
     * @param $p2
     * @return DBQueryClass
     * @throws Exception
     */
    static
    public function limit($p1, $p2 = null): DBQueryClass {
        $query = static::query();
        return $query->limit($p1, $p2);
    }

    // ---------------------------------------------------------

    static
    public function primary(): void {
        self::DBConnect()->primary();
    }

    static
    public function secondary(): void {
        self::DBConnect()->secondary();
    }

    // ---------------------------------------------------------
    // トランザクションの開始
    /**
     * @param null $id1
     * @param null $id2
     * @return DBClass|null
     * @throws Exception
     */
    static
    public function begin($id1 = null, $id2 = null): ?static {
        self::DBConnect()->primary();
        self::DBConnect()->begin();
        if ($id2) {
            return static::forUpdate($id1, $id2);
        } else if ($id1) {
            return static::forUpdate('id', $id1);
        } else {
            return null;
        }
    }

    /**
     * コミット
     * @return void
     */
    static
    public function commit(): void {
        self::DBConnect()->commit();
    }

    /**
     * ロールバック
     * @return void
     */
    static
    public function rollback(): void {
        self::DBConnect()->rollback();
    }

    /**
     * @param $key
     * @param $value
     * @param null $table
     * @return DBClass|null
     * @throws Exception
     */
    static
    public function forUpdate($key, $value, $table = null): ?static {
        if (empty($table)) {
            $table = static::$tableName;
        }
        $List = static::query()->execute("SELECT * FROM $table WHERE `$key`=? FOR UPDATE", [$value], PDO::FETCH_CLASS);
        return $List[0] ?? null;
    }


    // --------------------------------------------------------
    static
    public function insertOnDuplicateUpdate($prm, $tableName = null): mixed {
        $query = static::query();
        return $query->insert($prm, $tableName, true);
    }

    static
    public function insert($params, $table = null, $onDuplicateUpdate = false): mixed {
        $query = static::query();
        return $query->insert($params, $table, $onDuplicateUpdate);
    }

    /**
     * バルクインサート
     * @param $list
     * @param $table
     * @return int
     * @throws Exception
     */
    static
    public function insertBulk($list, $table = null): int {


        if (empty($table)) $table = static::$tableName;
        // 小分けにする
        $bulks = [[]];
        foreach ($list as $item) {
            $index = count($bulks) - 1;
            $bulks[$index][] = $item;
            if (count($bulks[$index]) >= 500) $bulks[] = [];
        }

        // SQL 作成
        $count = 0;
        $Query = self::query();
        foreach ($bulks as $bulk) {
            if (count($bulk) > 0) {
                $values = [];
                foreach ($bulk as $row) {
                    $cols = [];
                    foreach ($row as $col) $cols[] = self::$DBConnect->quote($col);
                    $values[] = '(' . implode(',', $cols) . ')';;
                    $count++;
                }
                $fields = implode(',', array_keys($bulk[0]));
                $sql = "INSERT INTO {$table}($fields) VALUES " . implode(',', $values) . ";";
            }
            $Query->execute($sql);
            //echo "{$sql}<br>";
        }

        return $count;

    }


    // --------------------------------------------------------
    // モデルオブジェクト更新用メソッド

    /**
     * 保存
     * @param $params
     * @param null $table
     * @return DBQueryClass
     * @throws Exception
     */

    public function save($params = null, $tableName = null): DBQueryClass {

        if (empty($params)) {
            // ->save()
            // 現在オブジェクトにセットされている該当カラムだけを全てUPDATEする
            $params = [];
            $fields = self::getFields();
            $properties = get_object_vars($this);
            foreach ($properties as $key => $value) {
                if ($key == 'updated_at' || $key == 'created_at') {
                    unset($params[$key]);
                } else {
                    if (isset($fields[$key])) {
                        $params[$key] = $value;
                    }
                }
            }
        }
        if (is_string($params)) {
            $params = [$params => $this->{$params}];
        } else {
            // ->save([''=>$value , ..])
            //  指定されたものをオブジェクトに代入しつつUPDATE
            foreach ($params as $key => $value) {
                $this->{$key} = $value;
            }
        }

        $query = static::query();
        $query->where(static::$primaryKey, $this->{static::$primaryKey})->update($params, $tableName);

        return $query;
    }

    /**
     * データが同じかどうかチェック
     * @param $data
     * @return bool
     */
    public function isSame($data): bool {

        foreach ($data as $key => $value) {
            if ($this->$key != $value) return false;
        }
        return true;
    }

    /**
     * 特定カラム($col_name)のJSON処理　取得・設定
     * @param $col_name
     * @param null $data
     * @param null $default
     * @return array|mixed
     */

    public function json($col_name, $data = null, $default = null): mixed {
        $work = [];
        try {
            $work = json_decode($this->$col_name, true);
        } catch (Exception $e) {

        }

        if (is_string($data)) {
            // 値取得
            $value = $work[$data] ?? null;
            if ($value === null) {
                $value = $default;
            }
            return $value;

        } else if (is_array($data)) {
            // 書き込み
            foreach ($data as $key => $datum) {
                $work[$key] = $datum;
            }
            $this->$col_name = json_encode($work, JSON_UNESCAPED_UNICODE);
        }

        return $work;
    }

    public function setJson($col_name, $data) {
        $this->$col_name = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 特定カラム work のJSON処理　取得・設定
     * 全配列取得 work()
     * 値取得 work('key' , $default)
     * 複数値設定 work(['key1'=> $value1 , 'key2'=> $value2 ,  ... ])
     * @param null $data
     * @param null $default
     * @return array|mixed
     */

    public function work($data = null, $default = null): mixed {
        $work = [];
        if (!empty($this->work)) {
            try {
                $work = json_decode($this->work, true);
            } catch (Exception $e) {
            }
        }

        if (is_string($data)) {
            // 値取得
            if (isset($work[$data])) {
                return $work[$data];
            } else {
                return $default;
            }

        } else if (is_array($data)) {
            // 書き込み
            foreach ($data as $key => $datum) {
                $work[$key] = $datum;
            }
            $this->work = json_encode($work, JSON_UNESCAPED_UNICODE);
        }

        return $work;

    }

    public function storeWork($property, $post, $default = ''): void {
        $value = $post[$property] ?? $default;
        $this->$property = $value;
        $this->work([$property => $value]);
    }

    public function restoreWork($property, $default = null): void {
        $value = $this->work($property, $default);
        $this->$property = $value;
    }

}