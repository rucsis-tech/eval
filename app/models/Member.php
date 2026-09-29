<?php

use classes\auth\MyAuthMailClass;

class Member extends Model {

    protected static string $tableName = 'member_master';

    const CONDITION_LAST_LOGIN_AT = 'last_login_at';
    const CONDITION_ACCESSED_AT = 'accessed_at';
    const CONDITION_PAYMENTED_AT = 'paymented_at';
    const CONDITION_LOCKED_AT = 'locked_at';
    const CONDITION_POINT_USED_AT = 'point_used_at';
    const CONDITION_POINTED_AT = 'pointed_at';
    const CONDITION_SCORED_AT = 'scored_at';

    const RANKS = [
        1 => array("rank_num" => 1, "need_score" => 100, "rank_name" => "レギュラー",),
        2 => array("rank_num" => 2, "need_score" => 150, "rank_name" => "ブロンズ　☆",),
        3 => array("rank_num" => 3, "need_score" => 300, "rank_name" => "ブロンズ　☆☆",),
        4 => array("rank_num" => 4, "need_score" => 600, "rank_name" => "ブロンズ　☆☆☆",),
        5 => array("rank_num" => 5, "need_score" => 900, "rank_name" => "シルバー　☆",),
        6 => array("rank_num" => 6, "need_score" => 1200, "rank_name" => "シルバー　☆☆",),
        7 => array("rank_num" => 7, "need_score" => 1500, "rank_name" => "シルバー　☆☆☆",),
        8 => array("rank_num" => 8, "need_score" => 2000, "rank_name" => "ゴールド　☆",),
        9 => array("rank_num" => 9, "need_score" => 2500, "rank_name" => "ゴールド　☆☆",),
        10 => array("rank_num" => 10, "need_score" => 3000, "rank_name" => "ゴールド　☆☆☆",),
        11 => array("rank_num" => 11, "need_score" => 4000, "rank_name" => "プラチナ　☆",),
        12 => array("rank_num" => 12, "need_score" => 5000, "rank_name" => "プラチナ　☆☆",),
        13 => array("rank_num" => 13, "need_score" => 6000, "rank_name" => "プラチナ　☆☆☆",),
        14 => array("rank_num" => 14, "need_score" => 7000, "rank_name" => "プラチナ　☆☆☆☆",),
        15 => array("rank_num" => 15, "need_score" => 8000, "rank_name" => "プラチナ　☆☆☆☆☆",),
        16 => array("rank_num" => 16, "need_score" => 10000, "rank_name" => "ダイヤモンド　☆",),
        17 => array("rank_num" => 17, "need_score" => 15000, "rank_name" => "ダイヤモンド　☆☆",),
        18 => array("rank_num" => 18, "need_score" => 20000, "rank_name" => "ダイヤモンド　☆☆☆",),
        19 => array("rank_num" => 19, "need_score" => 25000, "rank_name" => "ダイヤモンド　☆☆☆☆",),
        20 => array("rank_num" => 20, "need_score" => 25000, "rank_name" => "ダイヤモンド　☆☆☆☆☆",),
        ];

    static
    public function currentQuery($uid = null) {
        $Query = self::select(['member_master.*', 'member_master.id AS member_id',
            'member_condition.last_login_at', 'member_condition.accessed_at',
            'member_distribute.accept', 'member_distribute.mail_status',
            'member_state.*','member_master.id AS id',
        ])
            ->innerJoin('member_state', 'member_state.uid = member_master.uid')
            ->innerJoin('member_distribute', 'member_distribute.uid = member_master.uid')
            ->leftJoin('member_condition', 'member_condition.uid = member_master.uid');

        if ($uid) $Query->where('member_master.uid', $uid);

        return $Query;
    }

    /**
     * CRM 詳細取得
     * @param $uid
     * @return self|null
     * @throws Exception
     */

    static
    public function getDetail($uid): self|null {
        return self::cast(self::currentQuery($uid)
            ->select(['member_condition.*', 'member_distribute.*', 'member_master.id AS mid'])
            ->first());
    }

    static public function conditionAt($uid, $field) {
        self::table('member_condition')
            ->where('uid', $uid)
            ->update([$field => self::date()]);
    }


    /**
     * メンバーの取得
     * @throws Exception
     */
    static
    public function getMember($uid): self {

        // 規定外のUID
        if (empty($uid) || !preg_match('/^[A-Z0-9]{8}$/', $uid)) {
            $Member = new Member();
            $Member->uid = '0';
            $Member->nickname = 'GUEST';
            $Member->email = '';
            $Member->icon = '';
            $Member->rank_name = 'レギュラー';
            return $Member;
        }

        // 会員レコードの取得
        $Member = self::cast(self::currentQuery($uid)->first());
        if (empty($Member) || empty($Member->last_login_at)) {
            // 存在しなければ会員レコードの作成
            if (empty(User::first('uid', $uid))) throw new Exception("Not Found uid:[{$uid}]"); // user_masterがない！

            self::create($uid);
            $Member = self::cast(self::currentQuery($uid)->first());
        }

        // Email設定の確認
        if (empty($Member->email)) {
            $Auth = self::table('user_auth')->where('uid', $uid)->orderBy('updated_at', 'DESC')->first();
            $Member->save(['email' => $Auth->email]);;
        }

        // ランク名設定
        $Member->next_score = 0;
        if (isset(self::RANKS[$Member->rank])) {
            $Member->rank_name = self::RANKS[$Member->rank]['rank_name'];
            $Member->next_rank_name = isset(self::RANKS[$Member->rank + 1]) ? self::RANKS[$Member->rank + 1]['rank_name'] : '';
            $Member->need_score = self::RANKS[$Member->rank]['need_score'];

        } elseif ($Member->rank > count(self::RANKS)) {
            $Member->rank_name = self::RANKS[array_key_last(self::RANKS)]['rank_name'];
            $Member->next_rank_name = '';
            $Member->need_score = 0;

        } else {
            $Member->rank_name = 'レギュラー';
            $Member->next_rank_name = 'ブロンズ　☆';
            $Member->need_score = 0;
        }

        return $Member;
    }

    /**
     * 新規作成
     * @param $uid
     * @return self
     * @throws Exception
     */
    static function create($uid): self {

        try {
            self::insert(['uid' => $uid, 'nickname' => $uid], 'member_master');
        } catch (Exception $e) {
        }
        try {
            self::insert(['uid' => $uid, 'rank' => 1, 'score' => 0,], 'member_state');
        } catch (Exception $e) {
        }
        try {
            self::insert(['uid' => $uid], 'member_distribute');
        } catch (Exception $e) {
        }
        try {
            self::insert(['uid' => $uid, 'ua_id' => 0,], 'member_condition');
        } catch (Exception $e) {
        }

        return self::first('uid', $uid);
    }

    /**
     * 会員情報の更新
     * @param $updates
     * @return void]
     */
    public function update($updates) {
        foreach ($updates as $key => $value) {
            if ($key == 'nickname') {
                $value = strip_tags($value);
                if (!empty($value)) {
                    $this->save(['nickname' => $value]);
                    AppLog::log($value, 'nickname');
                }
            }
            if ($key == 'pass') {
                $email = $this->email;
                $Auth = \classes\auth\MyAuthClass::first('email', $email);
                $Auth->setPassword($value);
            }
            if ($key == 'mail') {
                $UserAuth = User::table('user_auth')->where('uid', $this->uid)->where('provider', 'MAIL')->first();
                $Auth = MyAuthMailClass::first('gid', str_replace('MAIL-', '', $UserAuth->guid));
                $Auth->setMail(App::mail());
                $Auth->changeMail(['email' => $UserAuth->email, 'new_email' => $value]);
            }
            if ($key == 'mail_accept') {
                $value = $value ? 1 : 0;
                self::sql("UPDATE `member_distribute` SET `accept` = ? WHERE `uid` = ?", [$value, $this->uid]);
                $this->accept = $value;
            }
            if ($key == 'concierge_id') {
                self::sql("UPDATE `member_state` SET `concierge_id` = ? WHERE `uid` = ?", [$value, $this->uid]);
                $this->concierge_id = $value;
            }
        }
    }

    /**
     * ランク名をセット
     * @return void
     */
    public function setRankName() {
        if (isset(self::RANKS[$this->rank])) {
            $this->rank_name = self::RANKS[$this->rank]['rank_name'];
        } else {
            $this->rank_name = '-';
        }
    }
}