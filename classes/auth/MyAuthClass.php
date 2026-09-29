<?php

namespace classes\auth;

use classes\CryptClass;
use classes\DBClass;
use Exception;


/**
 * classes\auth\MyAuthClass
 *
 * @property string $gid
 * @property string $email メールアドレス
 * @property string $profile
 * @property string $refresh
 * @property string $updated_at 更新日時
 */
class MyAuthClass extends DBClass {

    protected static string $tableName = 'auth_base';
    protected static string $primaryKey = 'gid';

    const SALT = '4847918231';
    const TYPE_ENTRY = 'entry';
    const TYPE_RESET = 'reset';
    const TYPE_CHANGE = 'change';
    const TYPE_LOGIN_CODE = 'login-code';
    const TYPE_TOKEN = 'token';
    const TYPE_TOW_STEP = 'tow-step';
    const EXPIRES_IN = 2592000;     // トークン有効期限
    const EXPIRES_REGISTER = 86400; // 登録メール期限

    /**
     * 連携ログイン用パラメータのs取得とcookie保存
     * @return array
     */
    static
    public function checkAuthCookies(): array {
        $client_id = $_GET['client_id'] ?? ($_COOKIE['client_id'] ?? '');
        $state = $_GET['state'] ?? ($_COOKIE['state'] ?? '');
        $scope = $_GET['scope'] ?? ($_COOKIE['scope'] ?? '');
        $redirect_url = $_REQUEST['redirect_url'] ?? ($_COOKIE['redirect_url'] ?? '');
        $ad = $_GET['ad'] ?? ($_COOKIE['ad'] ?? '');
        setcookie('client_id', $client_id, time() + 86400, '/auth');
        setcookie('state', $state, time() + 86400, '/auth');
        setcookie('scope', $scope, time() + 86400, '/auth');
        setcookie('redirect_url', $redirect_url, time() + 86400, '/auth');
        setcookie('ad', $ad, time() + 864000, '/auth');

        // todo $client_id と $redirect_url の整合性チェック

        return [
            'client_id' => $client_id,
            'state' => $state,
            'scope' => $scope,
            'redirect_url' => $redirect_url,
            'ad' => $ad,
        ];
    }


    /**
     * hash tmp の書き込み
     * @param $type
     * @param $email
     * @param $work
     * @return string
     * @throws Exception
     */

    static
    protected function saveTmp($type, $email, $work): string {
        $hash = self::makeHash();
        $work['type'] = $type;
        unset($work['email']);
        $work = json_encode($work);

        if ($type === self::TYPE_CHANGE) {
            // メール変更のときのみログアウトしたら困るので TYPE_CHANGE 以外を上書きしない
            $tmp = self::table('auth_tmp')->where('email', $email)->where('type', $type)->first();
        } else {
            // 自分のモノ再利用
            $tmp = self::table('auth_tmp')->where('email', $email)->where('created_at', '>', self::date(-86400 * 30))->first();
        }

        if (empty($tmp)) {
            // 一ヶ月以上前は誰のでも再利用
            $tmp = self::table('auth_tmp')->where('created_at', '<', self::date(-86400 * 30))->first();
        }

        if ($tmp) {
            // 再利用
            $now = self::date();
            $sql = "UPDATE `auth_tmp` SET `hash`=? , `type`=? ,`email`=? , `work`=? , created_at = ? WHERE `hash`=? ";
            self::sql($sql, [$hash, $type, $email, $work, $now, $tmp->hash]);
        } else {
            // 新規
            $sql = "INSERT INTO `auth_tmp` ( `hash`,`type`,`email`,`work`) VALUES ( ?, ?, ?, ?)";
            self::sql($sql, [$hash, $type, $email, $work]);
        }

        return $hash;
    }

    /**
     * テンポラリリセット
     * @param $type
     * @param $email
     * @return void
     * @throws Exception
     */
    static
    public function resetTmp($type, $email) {
        $sql = "UPDATE `auth_tmp` SET created_at = '2000-01-01' WHERE `type`=? AND `email`=? ";
        self::sql($sql, [$type, $email]);
    }


    /**
     * hash tmp の読み込み
     * @param $type
     * @param $hash
     * @return null|array
     * @throws Exception
     */
    static
    protected function loadTmpWork($type, $hash): ?array {
        $Tmp = self::table('auth_tmp')->where('type', $type)->first('hash', $hash);
        if ($Tmp) {
            $work = $Tmp->json('work');
            if (!isset($work['type']) || $work['type'] != $type) return null;   // type違い

            $work['email'] = $Tmp->email;
            unset($work['type']);
            return $work;
        } else {
            return null;
        }
    }

    // =====================================================================================================

    /**
     * 本登録
     * @param $email
     * @param $hash
     * @param null $password
     * @return mixed
     * @throws Exception
     */
    static
    public function register($email, $hash, $password = null) {

        // 整合性確認
        $work = self::loadTmpWork(self::TYPE_ENTRY, $hash);
        if (empty($work)) throw new Exception('存在しません');
        if (!isset($work['email']) || $work['email'] != $email) throw new Exception('存在しません');


        // 既に存在する $email
        $exists = self::first('email', $email);
        if ($exists) throw new Exception("登録済みです($email)");

        // Auth(mail)会員作成
        $Auth = self::create($email, $work);
        if ($password) $Auth->setPassword($password);

        $work['state'] = AuthMailClass::getLoginState(0);
        $work['code'] = self::saveTmp(self::TYPE_LOGIN_CODE, $email, []);
        $work['gid'] = $Auth->gid;

        return $work;
    }

    /**
     * ユーザーレコード作成
     * @param $email
     * @param $profile
     * @return string
     * @throws Exception
     */
    static
    public function create($email, $profile): self {

        $gid = self::makeGid();

        if (isset($work['checked'])) {
            // パスワードcryptがあれば復号して$gidで再度encrypt
            $password = CryptClass::decrypt($profile['checked'], $email);
            $profile['checked'] = CryptClass::encrypt($password, $gid);
            $profile['pass_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // DB登録
        if (empty($gid) || empty($email) || empty($profile['pass_hash'])) {
            throw new Exception('パラメータが足りません');
        }
        // DB登録
        unset($profile['password']);
        unset($profile['state']);
        unset($profile['scope']);
        unset($profile['redirect_url']);

        $sql = "INSERT INTO `auth_base` ( `gid`,`email`,`profile`,`refresh`) VALUES ( ?, ?, ?, ? )";
        self::sql($sql, [$gid, $email, json_encode($profile), self::makeHash(20)]);

        $Created = self::first('gid', $gid);

        return $Created;
    }


    /**
     * パスワード認証
     * @param $email
     * @param $password
     * @return array|false
     * @throws Exception
     */

    static
    public function checkin($email, $password): false|array|string {

        $Base = self::first('email', $email);
        if (!$Base) {
            throw new Exception("一致するユーザーが見つかりません($email)[0]",401);
        }
        $profile = $Base->json('profile');
        if (!password_verify($password, $profile['pass_hash'])) {
            throw new Exception("一致するユーザーが見つかりません($email)[1]",401);
        }

        $code = self::saveTmp(self::TYPE_LOGIN_CODE, $email, []);

        return $code;

    }

    static
    public function force($email) {
        $code = self::saveTmp(self::TYPE_LOGIN_CODE, $email, []);
        $state = AuthMailClass::getLoginState();
        $query = "state=$state&code=$code";
        return [
            'code' => $code,
            'state' => $state,
            'query' => $query,
        ];
    }


    /**
     * codeからアクセストークン取得
     * @param $code
     * @return array
     * @throws Exception
     */
    static
    public function getAccessTokenFromCode($code): array {
        $work = self::loadTmpWork(self::TYPE_LOGIN_CODE, $code);
        if (empty($work)) throw new Exception('error code');
        $access_token = self::saveTmp(self::TYPE_TOKEN, $work['email'], []);

        $Base = self::first('email', $work['email']);
        if (empty($Base)) throw new Exception('error base');

        return [
            'access_token' => $access_token,
            'refresh_token' => $Base->refresh,
            'expires_in' => self::EXPIRES_IN,
        ];
    }

    static
    public function getAccessTokenFromRefresh($refresh_token): array {

        $Base = self::first('refresh_token', $refresh_token);
        if (empty($Base)) throw new Exception('error base');
        $access_token = self::saveTmp(self::TYPE_TOKEN, $Base['email'], []);

        return [
            'access_token' => $access_token,
            'refresh_token' => $Base->refresh,
            'expires_in' => self::EXPIRES_IN,
        ];
    }

    /**
     * $emailのみによる単純ログイン処理
     * @param $email
     * @return false|string
     * @throws Exception
     */

    static
    public function logon($email) {
        if (!$email) return false;
        // Codeトークン等作成
        $code = self::saveTmp('login', $email, []);
        return $code;
    }

    /**
     * gid作成
     * @return string
     */
    static
    public function makeGid() {
        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * トークンから　AuthBase特定
     * @param $token
     * @return MyAuthClass
     * @throws Exception
     */

    static
    public function getFromToken($token): MyAuthClass {
        $sql = "SELECT * FROM `auth_tmp` WHERE `type` = ? AND `hash` = ? ";
        $res = self::sql($sql, [self::TYPE_TOKEN, $token]);
        if (count($res) != 1) {
            throw new Exception("Unmatched access_token");
        }
        $email = $res[0]['email'];

        return self::first('email', $email);
    }



    // =====================================================================================================

    /**
     * メール変更
     * @param $params
     * @return bool
     * @throws Exception
     */

    public function change($hash) {
        // ハッシュから取得
        $tmp = $this->getAuthTmp($hash);
        $email = $tmp['email'];
        $work = json_decode($tmp['work'], true);
        $new_email = $work['new_email'];

        // DB登録
        $sql = "UPDATE `auth_base` SET `email` = ? WHERE `email` = ?;";
        self::sql($sql, [$new_email, $email]);

        // ハッシュ削除
        self::sql("DELETE FROM `auth_tmp` WHERE `hash` = ?", [$hash]);

        return $new_email;
    }

    /**
     * パスワードを保存
     * @throws Exception
     */
    public function setPassword($password): void {
        $this->json('profile', [
            'checked' => CryptClass::encrypt($password, $this->gid),
            'pass_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        // DB保存
        $this->save();
    }


    /**
     * AUTHプロファイル取得
     * @return array
     * @throws Exception
     */

    public function getProfile(): array {
        $profile = $this->json('profile');

        $profile['userId'] = $this->gid;
        $profile['email'] = $this->email;
        $profile['displayName'] = $profile['nickname'] ?? '';
        $profile['pictureUrl'] = $profile['pictureUrl'] ?? '';

        return $profile;
    }


    /**
     * 仮登録ハッシュ照合
     * @param $hash
     * @return array
     * @throws Exception
     */

    public function getAuthTmp($hash): array {
        //期限切れ処理
        $this->TmpClean();

        $sql = "SELECT * FROM `auth_tmp` WHERE `hash` = ? AND created_at > ?";
        $res = self::sql($sql, [$hash, $this->date(time() - 86400 * 30)]);
        if (count($res) == 0) {
            throw new Exception('No Auth Tmp');
        }

        return $res[0];
    }


    // 有効期限切れ処理
    protected function TmpClean() {
        $sql = "DELETE FROM `auth_tmp` WHERE `created_at` < ? ";
        $res = self::sql($sql, [$this->date(time() - 86400 * 30)]);
    }


}
