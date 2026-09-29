<?php

namespace trait;
trait Pagination {

    public $count;
    public $max;

    public $page;
    public $limit = 50;
    private $Query;
    private $cookie_path = null;


    public function setPagenationQuery(\classes\DBQueryClass $Query) {
        $this->count = $Query->clone()->count();
        $this->Query = $Query;

        $this->page = $this->requestOrCookieAssign('page');
        $this->limit = $this->requestOrCookieAssign('limit');
        if (empty($this->limit)) $this->limit = 50;

        $this->max = intval(($this->count - 1) / $this->limit) + 1;
        $jump = $_REQUEST['jump'] ?? '';
        if ($jump === '-1') $this->page--;
        if ($jump === '+1') $this->page++;
        if ($this->page > $this->max || $jump == 'max') {
            $this->page = $this->max;
        }
        if ($this->page < 1 || $jump == 'min') {
            $this->page = 1;
        }
        $this->set('page', $this->page);

    }

    public function pageAll() {
        $numbers = [$this->page];
        if ($this->max < 20) {
            for ($i = 1; $i <= $this->max; $i++) $numbers[] = $i;
        } else {
            for ($i = 1; $i <= 20; $i++) $numbers[] = intval(($this->max / 20) * $i);
        }
        $numbers = array_unique($numbers);
        sort($numbers);


        $this->assign('Pages', (object)[
            'count' => $this->count,
            'max' => $this->max,
            'page' => $this->page,
            'limit' => $this->limit,
            'numbers' => $numbers,
        ]);
        return $this->Query
            ->offset($this->limit * ($this->page - 1))
            ->limit($this->limit)
            ->all();
    }

    public function requestOrCookieAssign($key, $default = null, $life_sec = 86400 * 30) {
        $value = isset($_REQUEST[$key]) ? ($_REQUEST[$key]) : (isset($_COOKIE[$key]) ? $_COOKIE[$key] : null);
        if ($value === null) {
            $value = ($default === null) ? '' : $default;
        }
        $this->set($key, $value, $life_sec);
        return $value;
    }

    private function setCookiePath() {
        if (empty($this->cookie_path)) {
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            if (str_ends_with($path, '/index')) $path = substr($path, 0, strlen($path) - 5);
            $this->cookie_path = $path;
        }
    }

    private function set($key, $value, $life_sec = 86400 * 30): void {
        $this->setCookiePath();
        setcookie($key, $value, time() + $life_sec, $this->cookie_path);
        $this->assign($key, $value);
    }


}