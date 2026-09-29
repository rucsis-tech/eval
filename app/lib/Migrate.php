<?php

// 簡易DBマイグレーション

use classes\trait\DBMigrate;
use classes\trait\DBTableTrait;

class Migrate extends Model {

    use DBTableTrait;
    use DBMigrate;

    // -------------------------------------------------------------------------------------------------------------
    // DBマイグレーション関数　この下に migration*** メソッドを追加していく


}