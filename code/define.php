<?php
// URL/ディレクトリ定数

const SYS_ROOT = __DIR__ . '/../';
const APP_ROOT = SYS_ROOT . 'app/';

try {

    define('BASE_URL_ENV', config('BASE_URL', ''));

    $http = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
    $port = $_SERVER['SERVER_PORT'] ?? '';
    if (empty($port) || $port == '80' || $port == '443') {
        $port = '';
    } else {
        $port = ":$port";
    }

    if (isset($_SERVER['SERVER_NAME'])) {
        $url = $http . $_SERVER['SERVER_NAME'] . $port . "/";
    } else {
        $url = config('BASE_URL');
    }
    define('BASE_URL', $url);

    $INTERNAL = BASE_URL;

    if (!Model::isLocal()) {
        $INTERNAL = config('INTERNAL_URL', $INTERNAL);
    }
    define('AUTH_API_URL', $INTERNAL . 'auth/');

    define('TMP_DIR', config('TMP_DIR', __DIR__ . '/../cache/'));
    define('LOGFILE', config('TMP_DIR') . config('APP_NAME') . ".log");
} catch (Exception $e) {
    echo "define env error: " . $e->getMessage();
    exit;
}
