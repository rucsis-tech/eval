<?php

use classes\auth\AuthClass;
use classes\CryptClass;
use classes\LogClass;
use classes\SessionClass;

/**
 * app\models\User
 *
 * @property int $id
 * @property string $uid 認証ID
 * @property string $name
 * @property string $email メールアドレス
 * @property string $icon
 * @property string $provider
 * @property int $developer 0:一般 1:管理 2:開発
 * @property string $disabled_at
 *
 * @property string $auth_email
 */
class User extends Model {

    const DEVELOPER_NONE = 0;
    const DEVELOPER_TEST1 = 1;
    const DEVELOPER_TEST2 = 2;
    const DEVELOPER_MEMBER = 10;
    const DEVELOPER_ADMIN = 20;

    public int $id = 0;
    public string $provider = '';

    private static ?User $Current = null;
    protected static string $tableName = 'user_master';

    /**
     * 現在ログイン中ユーザー取得
     * @return User
     * @throws Exception
     */
    static
    public function logined(): self {

        if (self::$Current == null) {
            self::$Current = self::getLoginUser();
        }
        return self::$Current;
    }

    /**
     * ユーザーレコード生成
     * @param $guid
     * @param $provider
     * @param string $email
     * @return User
     * @throws Exception
     */

    static
    public function create($guid, $provider, $email = ''): User {

        // 新規 user_master レコード
        for ($i = 0; $i < 100; $i++) {
            $uid = self::makeCode();
            $id = self::insert(['uid' => $uid, 'name' => $uid], 'user_master');
            if ($id) break;
        }

        $User = self::cast(self::where('id', $id)->first());

        // 新規 user_auth レコード
        self::insert([
            'uid' => $User->uid,
            'guid' => $guid,
            'provider' => $provider,
            'email' => $email,
            'access_token' => SessionClass::$ACCESS_TOKEN,
            'refresh_token' => SessionClass::$REFRESH_TOKEN,
            'expire_at' => empty(SessionClass::$EXPIRE_AT) ? (self::date(86400 * 30)) : SessionClass::$EXPIRE_AT,
        ], 'user_auth');

        return $User;

    }


    /**
     * ログイン時ユーザー取得
     * @return User
     * @throws Exception
     */
    static
    public function getLoginUser(): self {
        if (self::$Current && self::$Current->uid) {
            return self::$Current;
        }

        $guid = SessionClass::$GUID;
        $provider = SessionClass::getProvider();
        $state = SessionClass::$STATE ?? '';

        if (empty($provider)) {
            // ゲストユーザー
            $User = new User();
            $User->uid = 0;
            $User->name = 'GUEST';
            $User->developer = self::DEVELOPER_NONE;
            $User->disabled_at = null;
            $User->auth_email = '';
            return $User;
        }

        // ログインユーザーを認証情報guidから取得
        $User = self::getByGUID($guid);
        if (empty($User)) {
            // --------------------------------------------------
            // user_auth に連携なし

            // 他のAUTHから連携してログインしたか？
            if ($state) {
                $User = self::where('link_state', $state)->first();
                // 新規 user_auth レコードで連携
                self::insert(['uid' => $User->uid, 'guid' => $guid, 'provider' => $provider, 'email' => '',], 'user_auth');
            }
            // 未登録・新規作成
            if (empty($User)) {
                $User = self::create($guid, $provider);
            }
            $User = self::getByGUID($guid);
        }

        // $access_token がある場合はログイン直後なのでプロファイルチェック
        $works = SessionClass::getWorks();
        if (!empty($works['access_token'])) {
            $access_token = $works['access_token'] ?? '';
            $refresh_token = $works['refresh_token'] ?? '';

            $profile = $User->getProfile($provider, $access_token);
            // todo 期限切れ・認証不可など
            $email = $profile['email'] ?? '';
            // メールアドレス更新
            if ($email && $email != $User->auth_email) {
                self::table('user_auth')->where('guid', $guid)->update(['email' => $email]);
                $User->auth_email = $email;
            }
            // $access_token $refresh_token 更新
            self::from('user_auth')->where('guid', $guid)->update([
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'expire_at' => self::date(86400 * 30),
            ]);
            // セッションから削除
            SessionClass::setWorks(NULL);

            $ua_id = AppLog::class::getUAID();
            self::sql("UPDATE member_condition SET ua_id=$ua_id , last_login_at = NOW() WHERE uid='{$User->uid}'");
        }

        self::$Current = $User;
        LogClass::$UNIQ = $User->uid;

        return $User;
    }

    /**
     * @throws Exception
     */
    public static function getByGUID($guid): User|null {
        return self::select(['user_master.*', 'user_auth.email AS auth_email', 'user_auth.provider'])
            ->where('user_auth.guid', $guid)
            ->join('user_auth', 'user_auth.uid=user_master.uid')
            ->first();
    }

    public static function getByUID($uid): User|null {
        return self::select(['user_master.*', 'user_auth.email AS auth_email', 'user_auth.provider', 'user_auth.guid'])
            ->where('user_master.uid', $uid)
            ->join('user_auth', 'user_auth.uid=user_master.uid')
            ->first();
    }


    static
    public function uid() {
        return self::logined()->id;
    }

    /**
     * アカウント使用停止
     * @param $uid
     * @return void
     * @throws Exception
     */
    static
    public function lock($uid): void {
        $User = User::getByUID($uid);
        if ($User) {
            $email = $User->auth_email;
            $delCode = "(del." . date('Ymd-His', time()) . ")";
            $delete_email = $delCode . $email;

            // DBの「auth_base」から該当会員の「email」の冒頭に「delete_」を付ける
            Model::table('auth_base')->where('email', $email)->update(['email' => $delete_email]);

            // DBの「user_auth」から該当会員の「email」の冒頭に「delete_」を付ける
            Model::table('user_auth')->where('email', $email)->update(['email' => $delete_email]);

            // DBの「member_master」から該当会員の「email」の冒頭に「delete_」を付ける
            Model::table('member_master')->where('uid', $uid)->update(['email' => $delete_email]);

            // セッション削除
            SessionClass::deleteSession($User->guid);
        }

        // DBの「member_condition」から該当会員の「locked_at」に退会日時（処理時の日時）を入力
        Model::table('member_condition')->where('uid', $uid)->update(['locked_at' => Model::date()]);

        // member_distribute メール配信ステータスを「1:配信停止」に切り替えて「修正」
        Model::table('member_distribute')->where('uid', $uid)->update(['mail_status' => 1, 'accept' => 0]);

        // サブスクがあれば延長をキャンセルする
        SubscriptionEx::cancelAll($uid);

    }


    // -----------------------------------------------------------------------------------------------------------------


    public function profile() {
        if ($this->provider == \classes\auth\AuthMailClass::$provider) {
            $Auth = self::table('user_auth')
                ->where('user_auth.uid', $this->uid)
                ->where('user_auth.provider', $this->provider)
                ->first();
            return $this->getProfile($this->provider, $Auth->access_token);
        } else {
            return [];
        }
    }

    /**
     * 対応する認証システムよりプロファイルを取得
     * @param $provider
     * @param $access_token
     * @return array|null
     * @throws Exception
     */
    private function getProfile($provider, $access_token): ?array {
        if (empty($provider)) return [];

        $API = AuthClass::getAuthAPI($provider);
        $profile = $API->profile($access_token);

        if (isset($profile['error'])) {
            return [];
        }
        if (empty($this->name) && isset($profile['displayName'])) {
            $this->save(['name' => $profile['displayName']]);
        }
        return $profile;
    }

    /**
     * リフレッシュ(仮)
     * @param $session
     * @return array
     * @throws Exception
     */
    private
    static function refresh($session): array {

        $API = AuthClass::getAuthAPI($session['provider']);
        $refreshed = $API->refresh($session['refresh_token']);
        if (isset($refreshed['access_token'])) {
            $access_token = $refreshed['access_token'];
            $session['access_token'] = $access_token;
            $session['expire_at'] = self::date($refreshed['expires_in']);

            return $session;

        } else {
            return [];
        }
    }


    public function getPassword() {
        $Auth = self::table('user_auth')
            ->where('provider', $this->provider)
            ->where('uid', $this->uid)
            ->first();

        $profile = $this->getProfile($this->provider ?? '', $Auth->access_token ?? '');

        $password = !empty($profile['p']) ? $profile['p'] : '';
        if (empty($password) && !empty($profile['checked'])) $password = CryptClass::decrypt($profile['checked'], $profile['userId']);

        return $password;
    }
}