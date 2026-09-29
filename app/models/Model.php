<?php

use classes\DBClass;

class Model extends DBClass {

    protected static array $database = [];



    static
    public function isLocal(): bool {
        return App::isLocal();
    }

    static
    public function isDevelop(): bool {
        return App::isDevelop();
    }

    static
    public function isStatging(): bool {
        return App::isStaging();
    }

    static
    public function isRelease(): bool {
        return App::isRelease();
    }

    /**
     * @param $table
     * @return \classes\DBQueryClass
     * @throws Exception
     */
    static
    public function fromLog($table): \classes\DBQueryClass {
        return self::from(App::logDB($table));
    }

    static
    public function toYYYYMMDD($day): string {
        return date('Ymd', strtotime($day));
    }

    static
    public function toDay($YYYYMMDD): string {
        $Y = substr($YYYYMMDD, 0, 4);
        $M = substr($YYYYMMDD, 4, 2);
        $D = substr($YYYYMMDD, 6, 2);
        return "{$Y}-{$M}-{$D}";
    }

    static function getJsonPair($key, $value) {
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } else if (is_int($value)) {
            $value = (int)$value;
        } else if (is_string($value)) {
            $value = "\"{$value}\"";
        } else if (is_null($value)) {
            $value = "null";
        } else {
            $value = (string)$value;
        }

        return "%\"{$key}\":{$value}%";
    }
}