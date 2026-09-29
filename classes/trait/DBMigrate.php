<?php

namespace classes\trait;

use Exception;

trait DBMigrate {
    // ---------------------------------------------------------
    // マイグレーション補助用

    /**
     * DBマイグレーションメソッド一覧を返す add で始まるメソッド
     * @return array
     */
    private function getMigrationMethods(): array {
        $result = [];
        $list = get_class_methods($this);
        foreach ($list as $item) {
            if (str_starts_with($item, 'migration')) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 最新のDB Migrateが実施されているかチェック
     * @return mixed
     */

    function checkMigration(): mixed {

        $methods = $this->getMigrationMethods();
        if (isset($methods[0])) {
            $method = $methods[0];
            $migration = $this->$method(false);
            return $migration['state'];
        } else {
            return true;
        }
    }


    /**
     * マイグレーションの確認か実施
     * @param bool $go マイグレーション実施フラグ
     * @return array
     * @throws Exception
     */

    function migrate(bool $go = false): array {

        $mgs = [];

        $methods = $this->getMigrationMethods();
        foreach ($methods as $method) {
            $mg = $this->$method($go);
            $mg['name'] = $method;
            array_unshift($mgs, $mg);
        }

        $results = [];
        foreach ($mgs as $key => $migration) {
            if (!$migration['state']) {
                if ($go) {
                    try {
                        $res = $this->sql($migration['sql']);
                        $migration['check'] = $res;
                    } catch (Exception $e) {
                        $migration['check'] = $e->getMessage();
                    }
                } else {
                    $migration['check'] = 'NOT';
                }
            } else {
                $migration['check'] = 'OK';
            }
            array_unshift($results, $migration);
        }
        return $results;
    }
}