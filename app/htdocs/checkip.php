<?php
// IPアドレスを返す
echo $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');