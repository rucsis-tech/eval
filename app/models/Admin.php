<?php

use classes\auth\AuthMailClass;
use classes\auth\MyAuthClass;
use classes\DBClass;

/**
 * app\models\Admin
 *
 * @property int $id
 * @property string $uid 認証ID
 * @property string $admin_name
 * @property string $role
 * @property string $tel
 * @property string $login_at
 *
 * @property string $name
 * @property string $email
 * @property string $link_state
 * @property string $developer
 * @property string $disabled_at
 * @property string $created_at
 *

 */
class Admin extends Model {

    protected static string $tableName = 'admin_condition';

    const MemberList = 'MemberList';
    const UserList = 'UserList';
    const UserAdd = 'UserAdd';
    const UserUpdate = 'UserUpdate';
    const UserDelete = 'UserDelete';
    const UserReset = 'UserReset';
    const UserHistory = 'UserHistory';
    const UserLogin = 'UserLogin';


    // 権限
    const DIVISIONS = [
        'member' => [
            'label' => '会員検索',
            'auth' => [
                'MemberExec' => ['label' => '会員検索',],
                'MemberMod' => ['label' => '会員修正',],
                'MemberList' => ['label' => '全会員一覧取得',],
            ],
        ],
        'narrow' => [
            'label' => '会員絞込み',
            'auth' => [
                'NarrowExec' => ['label' => '会員絞込み',],
                'PresetAdd' => ['label' => 'プリセット保存',],
                'PresetDel' => ['label' => 'プリセット削除',],
                'NarrowToMember' => ['label' => '会員絞込会員反映',],
                'NarrowCheckAdd' => ['label' => 'チェックフラグ付与',],
                'NarrowCheckDel' => ['label' => 'チェックフラグ削除',],
                'CheckEdit' => ['label' => 'チェック名編集',],
                'GivePoints' => ['label' => 'ポイント付与',],
                'GiveScore' => ['label' => 'スコア付与',],
            ],
        ],
        'cms' => [
            'label' => 'CMS表示管理',
            'auth' => [
                'CmsList' => ['label' => '表示内容一覧',],
                'CmsAdd' => ['label' => '表示内容登録',],
                'CmsMod' => ['label' => '表示内容修正',],
                'CmsDel' => ['label' => '表示内容削除',],
            ],
        ],
        'content' => [
            'label' => 'CMS記事管理',
            'auth' => [
                'ContentList' => ['label' => '記事一覧',],
                'ContentAdd' => ['label' => '記事登録',],
                'ContentMod' => ['label' => '記事修正',],
                'ContentDel' => ['label' => '記事削除',],
                'ReturnPoints' => ['label' => 'ポイント特別付与',],
                'ContentPack' => ['label' => '商品パック管理',],
            ],
        ],
        'windex' => [
            'label' => 'Windex管理',
            'auth' => [
                'WindexList' => ['label' => 'Windex一覧',],
                'WindexAdd' => ['label' => 'Windex登録',],
                'WindexMod' => ['label' => 'Windex修正',],
                'WindexDel' => ['label' => 'Windex削除',],
                'ReturnPoints' => ['label' => 'ポイント特別付与',],
            ],
        ],
        'payment' => [
            'label' => '商品管理',
            'auth' => [
                'PaymentItem' => ['label' => '商品一覧',],
                'PaymentItemAdd' => ['label' => '商品登録',],
                'PaymentItemMod' => ['label' => '商品修正',],
                'PaymentItemDel' => ['label' => '商品削除',],
            ],
        ],
        'sales' => [
            'label' => '売上管理',
            'auth' => [
                //'Approval' => ['label' => '決済承認',],
                //'ApprovalAdd' => ['label' => '未決済承認追加',],
                'Sales' => ['label' => '売上・取引履歴',],
                'SalesCb' => ['label' => '取引履歴CBコピー',],
                'SalesToMember' => ['label' => '取引履歴会員検索反映',],

                'Subscription' => ['label' => 'サブスク履歴',],

                'PointPurchased' => ['label' => 'Pt購入・付与履歴',],
                'PointSpend' => ['label' => 'Pt消費履歴',],
                'PointHistory' => ['label' => 'Pt増減集計',],
            ],
        ],
        'advertise' => [
            'label' => '広告管理',
            'auth' => [
                'Advertise' => ['label' => '広告コード一覧',],
                'AdvertiseAdd' => ['label' => '広告コード登録',],
                'AdvertiseMod' => ['label' => '広告コード修正',],
                'AdvertiseDel' => ['label' => '広告コード削除',],
                'Media' => ['label' => '媒体一覧',],
                'MediaAdd' => ['label' => '媒体追加',],
                'MediaMod' => ['label' => '媒体修正',],
                'MediaDel' => ['label' => '媒体削除',],
                'Aggregate' => ['label' => '広告集計',],
            ],
        ],
        'marketing' => [
            'label' => 'マーケティング',
            'auth' => [
                'Kpi' => ['label' => 'KPI',],
                'KpiCbCopy' => ['label' => 'KPIクリップボードコピー',],
            ],
        ],
        'mail' => [
            'label' => 'メール管理',
            'auth' => [
                'Mail' => ['label' => 'メール一覧',],
                'MailAdd' => ['label' => 'メール登録',],
                'MailMod' => ['label' => 'メール修正',],
                'MailDel' => ['label' => 'メール削除',],

                'From' => ['label' => '差出人一覧',],
                'FromAdd' => ['label' => '差出人追加',],
                'FromMod' => ['label' => '差出人修正',],
                'FromDel' => ['label' => '差出人削除',],
            ],
        ],
        'distribute' => [
            'label' => '配信管理',
            'auth' => [
                'Distribute' => ['label' => '配信予約一覧',],
                'DistributeAdd' => ['label' => '配信予約',],
                'DistributeMod' => ['label' => '配信予約修正',],
                'DistributeDel' => ['label' => '配信予約取消',],
                'Banner' => ['label' => 'バナー管理',],
                'BannerEdit' => ['label' => 'バナー編集',],
                'Topic' => ['label' => 'トピック管理',],
                'TopicEdit' => ['label' => 'トピック編集',],
            ],
        ],
        'user' => [
            'label' => '管理者',
            'auth' => [
                self::UserList => ['label' => '管理者一覧',],
                self::UserAdd => ['label' => '管理者新規追加',],
                self::UserUpdate => ['label' => '管理者情報修正',],
                self::UserDelete => ['label' => '管理者削除',],
                self::UserReset => ['label' => '管理者パスワード再発行',],
                self::UserHistory => ['label' => '操作履歴',],
                self::UserLogin => ['label' => 'ログイン情報',],
            ],
        ],
    ];


    /**
     * 管理者作成
     * @param $email
     * @param $password
     * @return Admin
     * @throws Exception
     */
    static
    public function create($email, $password): Admin {
        $Exists = MyAuthClass::first('email', $email);
        if (!$Exists) {
            $work = [
                'pass_hash' => password_hash($password, PASSWORD_DEFAULT)
            ];

            $Auth = MyAuthClass::create($email, $work);
            $guid = AuthMailClass::makeGUID($Auth->gid);
            // 新規ユーザー
            $User = User::create($guid, AuthMailClass::$provider, $email);
            $User->save([
                'name' => $email,
                'developer' => User::DEVELOPER_MEMBER
            ]);
        } else {
            $User = User::getByGUID(AuthMailClass::makeGUID($Exists->gid));
            $User->save(['developer' => User::DEVELOPER_MEMBER]);
        }

        // パスワード設定
        $Auth = \classes\auth\MyAuthClass::first('email', $email);
        $Auth->setPassword($email);

        // 新規 admin_condition
        $AdminCondition = self::first('uid', $User->uid);
        if (!$AdminCondition) {
            self::insert([
                'uid' => $User->uid,
                'admin_name' => $email,
                'role' => '',
                'tel' => '',
            ], 'admin_condition');
        }

        return self::getByUID($User->uid);
    }

    static function getAdmin($User) {
        if (empty($User->uid) || $User->developer == User::DEVELOPER_NONE) {
            return null;
        }
        $Admin = self::getByUID($User->uid);
        if (empty($Admin)) {
            // 新規 admin_condition
            $profile = $User->profile();
            $email = $profile['email'] ?? '@';
            self::insert([
                'uid' => $User->uid,
                'admin_name' => $email,
                'role' => '',
                'tel' => '',
            ], 'admin_condition');
            $Admin = self::getByUID($User->uid);
        }
        return $Admin;
    }

    static
    public function defaultSelect() {
        return self::select(['user_master.*', 'admin_condition.admin_name', 'admin_condition.role', 'admin_condition.tel', 'admin_condition.login_at', 'user_auth.email']);
    }

    static
    public function getByGUID($guid): Admin|null {
        return self::defaultSelect()
            ->from('user_auth')
            ->where('user_auth.guid', $guid)
            ->innerJoin('user_master', 'user_master.uid=user_auth.uid')
            ->leftJoin('admin_condition', 'admin_condition.uid=user_auth.uid')
            ->first();
    }

    /**
     * @throws Exception
     */
    static
    public function getByUID($uid): Admin|null {
        return self::defaultSelect()
            ->from('user_master')
            ->where('user_master.uid', $uid)
            ->join('admin_condition', 'admin_condition.uid=user_master.uid')
            ->join('user_auth', 'user_auth.uid=user_master.uid')
            ->first();
    }

    // -------------------------------------------------------------------------------------------------

    /**
     * @return array
     * @throws Exception
     */
    public function getDivisions(): array {

        $list = self::from('admin_division')
            ->where('uid', $this->uid)
            ->rows();
        $result = [];
        foreach ($list as $item) {
            $result[$item['division']] = json_decode($item['auth'], true);;
        }
        foreach (self::DIVISIONS as $key => $def) {
            if (!isset($result[$key])) {
                $result[$key] = [];
            }
        }
        return $result;
    }

    /**
     * @throws Exception
     */

    public function setDivisions($params): void {
        foreach (self::DIVISIONS as $key => $DIVISION) {
            $this->setDivision($key, $params[$key]);
        }
    }


    /**
     * @throws Exception
     */
    public function setDivision($division_key, $auths): void {
        $uid = $this->uid;
        $json = json_encode($auths);
        $Division = self::from('admin_division')
            ->where('uid', $uid)
            ->where('division', $division_key)
            ->first();
        if ($Division) {
            self::into('admin_division')->where('id', $Division->id)->update(['auth' => $json]);
        } else {
            self::into('admin_division')->insert(['uid' => $uid, 'division' => $division_key, 'auth' => $json]);
        }
    }

    public function setAuth($division_key, $auth): void {
        $uid = $this->uid;
        $Division = self::from('admin_division')
            ->where('uid', $uid)
            ->where('division', $division_key)
            ->first();

        if ($Division) {
            $auths = json_decode($Division->auth, true);
        } else {
            $auths = [];
        }
        $auths[$auth] = 1;
        self::setDivision($division_key, $auths);
    }

    /**
     * 権限チェック
     * @param $auth_name
     * @return bool
     * @throws Exception
     */

    public function checkAuthDivision($auth_name): bool {
        $uid = $this->uid;
        $dName = self::findDivisionName($auth_name);
        if ($dName) {
            $division = self::from('admin_division')
                ->where('uid', $uid)
                ->where('division', $dName)
                ->first();
            if (empty($division)) {
                return false;
            }
            $auths = json_decode($division->auth, true);
            return !empty($auths[$auth_name]);
        }
        return false;
    }

    /**
     * AUTH名からDivision名を取得
     * @param $auth_name
     * @return string
     */

    static
    public function findDivisionName($auth_name): string {
        foreach (self::DIVISIONS as $key => $DIVISION) {
            foreach ($DIVISION['auth'] as $auth => $value) {
                if ($auth == $auth_name) return $key;
            }
        }
        return '';
    }


    /**
     * 全権限の追加
     * @return void
     * @throws Exception
     */

    public function addAllAuth() {
        foreach (self::DIVISIONS as $division_key => $DIVISION) {
            foreach ($DIVISION['auth'] as $auth => $item) {
                $this->setAuth($division_key, $auth);
            }
        }
    }

    /**
     * 管理ログ書き出し
     * @param $result
     * @param $division
     * @param $auth
     * @param $params
     * @return void
     * @throws Exception
     */

    public function log($result, $division, $auth, $params) {
        $logDB = App::logDB();
        $uid = $this->uid;
        $edit_id = $params['uid'] ?? ($params['id'] ?? '');

        self::into("{$logDB}.admin_log")->insert([
            'uid' => $uid,
            'result' => $result,
            'division' => $division,
            'auth' => $auth,
            'operate_id' => $edit_id,
            'params' => json_encode($params, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function authLog($auth, $operation) {
        // 権限確認
        $authority = $this->checkAuthDivision($auth);

        // 操作ログ
        $this->log($authority ? 'OK' : 'NG', Admin::findDivisionName($auth), $auth, $operation);

        // 権限なし
        if (!$authority) {
            throw new Exception("権限がありません(auth:$auth uid:{$this->uid}})", 401);
        }

    }
}