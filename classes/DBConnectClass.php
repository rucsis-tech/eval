<?php

namespace classes;

use PDO;

class DBConnectClass {
    private static array $Connections = [];
    private ?PDO $primaryPdo = null;
    private ?PDO $secondaryPdo = null;
    private array $database = [];
    private bool $isPrimary = false;

    public string $last_query = '';
    public array $last_params = [];

    /**
     * 接続オブジェクトを返す
     * @param $database
     * @return self
     */
    static
    public function getConnection($database): self {

        $index = serialize($database);
        if (!isset(self::$Connections[$index])) {
            self::$Connections[$index] = new DBConnectClass($database);
        }
        return self::$Connections[$index];

    }

    static
    public function flush() {
        self::$Connections = [];
    }


    function __construct($database) {
        $this->database = $database;
        $this->isPrimary = false;
        $this->secondary(true);
    }

    /**
     * 接続してPDOを返す
     * @param $setting
     * @return PDO
     */

    private function connect($setting): PDO {
        $server = $setting['host'];
        $name = $setting['database'];
        $user = $setting['user'];
        $pass = $setting['password'];

        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

        //DB接続
        $pdo = new PDO('mysql:host=' . $server . ';dbname=' . $name, $user, $pass, $options);
        //エラーをスロー
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;

    }

    /**
     * primaryPdo secondaryPdo 現在有効ないずれかを返す
     * @return PDO
     */
    private function pdo(): PDO {
        if ($this->isPrimary) {
            return $this->primaryPdo;
        } else {
            return $this->secondaryPdo;
        }
    }

    /**
     * primaryPdo　に切り替え
     * @param bool $force
     * @return void
     */
    public function primary(bool $force = false): void {
        if (!$this->isPrimary || $force) {
            if (empty($this->primaryPdo || $force)) {
                $this->primaryPdo = $this->connect($this->database['primary']);
            }
            $this->isPrimary = true;
        }
    }

    /**
     * secondaryPdo に切り替え
     * @param bool $force
     * @return void
     */
    public function secondary(bool $force = false): void {
        if ($this->isPrimary || $force) {
            if (empty($this->secondaryPdo) || $force) {
                $this->secondaryPdo = $this->connect($this->database['secondary']);
            }
            $this->isPrimary = false;
        }
    }

    /**
     * @param $sql
     * @return false|\PDOStatement
     */
    public function prepare($sql, $params): false|\PDOStatement {
        $this->last_query = $sql;
        $this->last_params = $params;
        $statement = $this->pdo()->prepare($sql);

        for ($i = 0; $i < count($params); $i++) {
            if (!is_string($params[$i])) {
                $statement->bindParam($i + 1, $params[$i], PDO::PARAM_INT);
            } else {
                $statement->bindParam($i + 1, $params[$i]);
            }
        }

        return $statement;
    }

    /**
     * @return false|string
     */
    public function lastInsertId(): false|string {
        return $this->pdo()->lastInsertId();
    }

    /**
     * クオートエスケープ
     * @param $sql
     * @return string
     */
    public function quote($sql): string {
        return $this->pdo()->quote($sql);
    }

    // ---------------------------------------------------------
    // トランザクション

    /**
     * 開始
     * @return void
     */
    public function begin(): void {
        $this->pdo()->beginTransaction();
    }

    /**
     * コミット
     * @return void
     */
    public function commit(): void {
        $this->pdo()->commit();
    }

    /**
     * ロールバック
     * @return void
     */
    public function rollback(): void {
        $this->pdo()->rollback();
    }


}
