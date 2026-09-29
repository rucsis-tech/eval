<?php

namespace classes\auth;

use classes\CryptClass;
use classes\MailClass;
use Exception;

class MyAuthMailClass extends MyAuthClass {

    private MailClass|null $Mail;

    public function __construct($Mail = null) {

        if ($Mail) {
            $this->Mail = $Mail;
            $this->Mail->setFrom(config('EMAIL_FROM_ADDRESS'), config('EMAIL_FROM_NAME'));
        }
    }

    public function setMail($Mail) {
        $this->Mail = $Mail;
    }


    // =====================================================================================================

    /**
     * 仮登録を行いメール送信
     * @param $email
     * @param $password
     * @param $work
     * @return bool|array
     * @throws \Exception
     */
    public function entryMail($email, $password, $work): bool|array {
        if (empty($email)) throw new Exception("メールアドレスがありません", 1);
        if (empty($password)) throw new Exception("パスワードがありません", 1);

        if (self::first('email', $email)) {
            // 登録済み
            // throw new Exception(" Already Mail " . $params['mail']);

            // 登録済み送信
            $work['to'] = $email;
            $work['res'] = $this->Mail->sendMail('auth/EntryAlready', $work);
            $work['send'] = 'alreadyEntryMailBody';
        } else {
            // ------------------------------
            // TMP仮登録

            $work['pass_hash'] = password_hash($password, PASSWORD_DEFAULT);
            $work['checked'] = CryptClass::encrypt($password, $email);
            $hash = self::saveTmp('entry', $email, $work);

            // ------------------------------
            // 仮登録メール送信
            $work['to'] = $email;
            $work['url'] = BASE_URL . "auth/register?hash=$hash&email=" . rawurlencode($email);
            $work['res'] = $this->Mail->sendMail('auth/Entry', $work);

        }

        return $work;

    }

    // =====================================================================================================


    /**
     * remindメール送信
     * @param $email
     * @return mixed
     * @throws Exception
     */
    public function remindMail($email): mixed {
        if (empty($email)) throw new Exception("メールアドレスがありません", 1);
        $base = self::first('email', $email);
        $work = ['email' => $email];
        if (empty($base)) {
            // メールアドレスが存在しない場合、存在しないメール送信
            $work['res'] = $this->Mail->sendMail('auth/RemindNone', $work);
            $work['send'] = 'unknownRePassMailBody';
            return $work;
        }

        // 照合用ハッシュ保存
        $hash = $this->saveTmp('reset', $email, $work);

        // メール送信処理

        $work['to'] = $email;
        $work['url'] = BASE_URL . "auth/register?hash=$hash&email=" . rawurlencode($email);
        $work['login_url'] = $work['url'];
        $work['res'] = $this->Mail->sendMail('auth/Remind', $work);

        return $work;

    }

    // =====================================================================================================

    /**
     * アドレス変更メール送信
     * @param $params
     * @return array|bool
     * @throws Exception
     */

    public function changeMail($params): array|bool {

        $base = self::first('email', $params['email']);
        if (!$base) {
            throw new Exception("NO user " . $params['email']);
        }

        $params['old_email'] = $params['email'];
        $params['email'] = $params['new_email'];

        //  変更メールアドレス検索
        $exists = self::first('email', $params['email']);
        if ($exists) {
            // アドレスが存在する場合、存在するよメール送信
            $params['res'] = $this->Mail->sendMail('auth/ChangeComplete', $params);
            $params['send'] = 'ChangeComplete';
            return $params;
        }

        // 照合用ハッシュ作成・保存
        $profile = ['email' => $params['old_email'], 'new_email' => $params['email']];
        $hash = $this->saveTmp('change', $params['old_email'], $profile);

        // メール送信処理
        $params['url'] = BASE_URL . "auth/register?hash=$hash&email=" . rawurlencode($params['email']);
        $params['res'] = $this->Mail->sendMail('auth/Change', $params);
        return $params;
    }


}
