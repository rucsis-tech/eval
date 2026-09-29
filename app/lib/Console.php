<?php

use classes\LogClass;

class Console {

    public array $argv;
    public array $commands = [];

    public $memory_limit = 100 * 1024 * 1024;   // 100MB
    public $runtime_limit = 60;   // 60sec
    public $dryrun = false;

    function __construct(array $argv, int $memory_limit = null, int $runtime_limit = null) {

        $this->argv = $argv;
        $this->getCommandOptions();
        $this->dryrun = in_array('dryrun', $this->argv) || isset($this->commands['dryrun']);

        if ($memory_limit) $this->memory_limit = $memory_limit;
        if ($runtime_limit) $this->runtime_limit = $runtime_limit;

        echo "-- " . date("Y-m-d H:i:s") . " ----------------------------\n";

    }

    /**
     * 現在の実行経過秒数の取得
     * @return int
     */
    public function getExecutionSec(): int {
        return intval(LogClass::getPassTime());
    }

    /**
     * @param $function
     * @return mixed
     */
    public function action($function): void {
        AppLog::start();

        $result = null;
        try {
            $result = $function($this);

        } catch (Exception $e) {
            echo $e->getMessage();
            AppLog::except($e);
        }

        AppLog::watch('終了');


        // 使用メモリ
        $bytes = memory_get_peak_usage(true);
        $mBytes = $bytes / 1024 / 1024;

        // 実行時間
        $sec = $this->getExecutionSec();

        echo "---------------------------------------------------\n";
        echo "runtime $sec sec / memory_get_peak_usage $mBytes MB (" . number_format($bytes) . ") \n";

        // メモリオーバー？
        if ($bytes > $this->memory_limit)
            LogClass::error("memory_limit over {$mBytes}MB", number_format($bytes));

        // 時間オーバー？
        if ($sec > $this->runtime_limit)
            LogClass::error("sec_limit over {$sec}sec", LogClass::$workTimes);

        // コマンドライン受け取りログ
        LogClass::bat(['runtime' => $sec, 'result' => $result, 'argv' => $this->argv]);


    }


    /**
     * コマンド引数列
     * @return array
     */

    function getCommands() {
        return $this->commands;
    }

    /**
     * オプション列
     * @param array $options 値引数のあるオプション名の配列
     * @return array
     */

    private function getCommandOptions(): array {
        for ($i = 1; $i < count($this->argv); $i++) {
            $value = $this->argv[$i];
            $top = substr($value, 0, 1);
            if ($top == '-' || $top == '/') {
                // オプションコマンド
                $command = substr($value, 1);
                $str = isset($this->argv[$i + 1]) ? $this->argv[$i + 1] : '';
                if ($str && substr($str, 0, 1) == '"') {
                    // ダブルクオート対応
                    $str = substr($str, 1, strlen($str) - 2);
                }
                $this->commands[$command] = $str;
            }
        }
        return $this->commands;
    }


    /**
     * 本日、あるいは指定日
     * @param $index
     * @return string
     */
    function getDay($index = 1) {
        $argv = $this->commands;
        if (empty($argv[$index])) {
            return date('Y-m-d', time() - 60 * 60 * 24);
        } else {
            return date('Y-m-d', strtotime($argv[$index]));
        }
    }


}