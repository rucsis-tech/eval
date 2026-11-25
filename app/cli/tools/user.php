<?php

// 任意ユーザーの追加その他
// php user.php [email] [password]

use classes\auth\AuthMailClass;
use classes\auth\MyAuthClass;
use classes\CryptClass;

require_once __DIR__ . "/../../../bootstrap.php";

$Console = new Console($argv);
$Console->action(function (Console $Console) {

    $email = trim($Console->argv[1] ?? '');
    if (empty($email)) throw new Exception('emailアドレスを指定してください');

    $commands = $Console->commands;
    pre($commands);

    // ----------------------------------------------------------------
    // Adminユーザー追加
    // php user.php flatwork76@gmail.com -n
    if (isset($commands['n'])) {
        $password = trim($commands['n']);
        echo "ADD USER $email $password\n";

        if (empty($password)) throw new Exception();

        $Admin = Admin::create($email, $password);

    }

    // ----------------------------------------------------------------
    // 全権限追加
    // php user.php flatwork76@gmail.com -a
    if (isset($commands['a'])) {
        echo "DIVISION $email -a\n";
        $Base = MyAuthClass::first('email', $email);
        if ($Base) {
            $guid = AuthMailClass::makeGUID($Base->gid);
            echo "guid:$guid\n";
            $Admin = Admin::getByGUID($guid);
            if ($Admin) {
                $Admin->addAllAuth();
            } else {
                throw new Exception("見つかりません:$guid");
            }

        } else {
            throw new Exception("見つかりません:$email");
        }
        exit;
    }

    // ----------------------------------------------------------------
    // password 変更
    // php add_user.php flatwork76@gmail.com -p ****

    if (isset($commands['p'])) {
        echo "CHANGE PASS $email -p\n";
        $password = $commands['p'];
        $Base = MyAuthClass::first('email', $email);
        if ($Base) {
            $Base->setPassword($password);
        } else {
            throw new Exception("見つかりません:$email");
        }
        exit;
    }

    // ----------------------------------------------------------------
    // password 抽出
    // php user.php flatwork76@gmail.com -r
    if (isset($commands['r'])) {
        $Base = MyAuthClass::first('email', $email);
        if ($Base) {
            $checked = $Base->json('profile', 'checked');
            $password = CryptClass::decrypt($checked, $Base->gid);
            echo "GET USER $email\nPASS $password\n";
        }
    }
});








