<?php

class MemberProfile extends Model {
    protected static string $tableName = 'member_profile';

    const CATEGORY_PURCHASED_COUNT = 'purchased_count'; // 購入回数
    const CATEGORY_PAYMENT_SUM = 'payment_sum'; // 購入総額
    const CATEGORY_POINT_USED_COUNT = 'point_used_count'; // ポイント使用回数

    // 配信許諾詳細
    // 生年月日


    static public function write($uid, $name, $data, $value = 0): string {
        $result = '';
        if (is_string($data)) {
            $data = strip_tags($data);
            $result = $data;
        } else if (is_array($data)) {
            foreach ($data as &$datum) {
                $datum = strip_tags($datum);
            }
            $result = implode(" ", $data);
        } else {
            $data = null;
        }

        $profile = [
            'uid' => $uid,
            'category' => $name,
            'value' => $value,
            'work' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ];

        $Profile = self::where('uid', $uid)->where('category', $name)->first();
        if ($Profile) {
            $Profile->save($profile);
        } else {
            self::insert($profile);
        }

        return $result;
    }

    static public function getAllProfiles($uid) {
        $All = self::where('uid', $uid)->where('disabled', 0)->all();
        $result = [];
        foreach ($All as $item) {
            if ($item->work !== 'null') {
                $result[$item->category] = json_decode($item->work);
            }
        }
        return $result;
    }

    /**
     * 購入回数の記録
     * @param $uid
     * @return void
     * @throws Exception
     */

    static public function tallyPurchasedCount($uid): void {
        $count = PaymentHistory::where('uid', $uid)
            ->where('purchased_at IS NOT NULL')
            ->count();
        if ($count)
            self::write($uid, self::CATEGORY_PURCHASED_COUNT, [], $count);

        $sum = PaymentHistory::select('SUM(amount) AS SUM')->where('uid', $uid)
            ->where('purchased_at IS NOT NULL')
            ->first();

        if ($sum->SUM)
            self::write($uid, self::CATEGORY_PAYMENT_SUM, [], $sum->SUM ?? 0);;
    }

    /**
     * ポイント使用回数の記録
     * @param $uid
     * @return void
     * @throws Exception
     */

    static public function addPointUsedCount($uid): void {
        $Profile = self::where('uid', $uid)->where('category', self::CATEGORY_POINT_USED_COUNT)->first();
        if ($Profile) {
            $Profile->save(['value' => $Profile->value + 1]);
        } else {
            self::insert(['uid' => $uid, 'category' => self::CATEGORY_POINT_USED_COUNT, 'value' => 1, 'work' => '[]']);
        }
    }

    static public function tallyPointUsedCount($uid): void {
        $count = self::from(App::logDB('point_log'))
            ->where('uid', $uid)
            ->where('kind', Point::KIND_SPENDING)
            ->leftJoin('point_origin', 'point_origin.id=point_log.origin_id')
            ->count();

        if ($count)
            self::write($uid, self::CATEGORY_POINT_USED_COUNT, [], $count);;
    }
}