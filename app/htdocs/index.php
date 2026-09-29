<?php

require_once __DIR__ . '/../lib/Limiter.php';
Limiter::loadStartBlock();

//------------------------------

require __DIR__ . "/../../bootstrap.php";

// DBマイグレーションチェック
App::checkMigration();

// 適切な src を include
Route::include($_SERVER['REQUEST_URI']);

