<?php

use classes\DBClass;

/**
 * @property int $id
 * @property int $uid 認証ID
 * @property int $segment セグメント
 * @property int $flags フラグ
 * @property string $updated_at 更新日時
 * @property string $created_at 作成日時
 */
class MemberFlag extends Model {

    const SEGMENT_MAX = 8;

    protected static string $tableName = 'member_flag';

    /**
     * インポートして設定する
     * @param $filepath
     * @return void
     * @throws Exception
     */
    public static function import($filepath): void {
        $handle = fopen($filepath, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $uid = intval($data[0]);
            $flag = $data[1] ?? '';
            $Member = Member::where('uid', $uid)->first();
            if ($Member) {
                $Member->setFlag($flag);
                self::isCli("$uid: set $flag");
            } else {
                self::isCli("$uid: NOT FOUND");
            }
        }
        fclose($handle);
    }

    /**
     * 指定番号フラグのON
     * @param $uid
     * @param $number
     * @return void
     * @throws Exception
     */
    static
    public function turnOnFlag($uid, $number): void {
        // $number からセグメント割り出し
        $index = $number - 1;
        $segment = intval($index / 32) + 1;
        $flag = 1 << ($index % 32);
        MemberFlag::orFlag($uid, $segment, $flag);
    }

    /**
     * 指定番号フラグのOFF
     * @param $uid
     * @param $number
     * @return void
     * @throws Exception
     */
    static
    public function turnOffFlag($uid, $number): void {
        // $number からセグメント割り出し
        $index = $number - 1;
        $segment = intval($index / 32) + 1;
        $flag = 1 << ($index % 32);
        MemberFlag::subFlag($uid, $segment, $flag);
    }

    /**
     * 指定フラグのON/OFF確認
     * @param $uid
     * @param $number
     * @return string "0"/"1"
     * @throws Exception
     */
    static
    public function checkFlag($uid, $number): string {

        // $number からセグメント割り出し
        $index = $number - 1;
        $segment = intval($index / 32) + 1;
        $MemberFlag = self::where('uid', $uid)->where('segment', $segment)->first();
        // 01バイナリ文字列
        $bin = str_pad(decbin($MemberFlag->flag ?? 0), 32, '0', STR_PAD_LEFT);
        $bit = 31 - $index % 32;

        return substr($bin, $bit, 1);
    }


    /**
     * 会員のフラグ情報をまとめて取得
     * @param $uid
     * @return array
     * @throws Exception
     */
    static
    public function getFlags($uid): array {
        $flags = [];
        for ($seg = 1; $seg <= self::SEGMENT_MAX; $seg++) {
            $MemberFlag = self::where('uid', $uid)->where('segment', $seg)->first();
            $flags[$seg] = $MemberFlag->flag ?? 0;
        }
        return $flags;
    }


    /**
     * 特定フラグのセットされている全レコード取得
     * @param $number
     * @return array
     * @throws Exception
     */
    static
    public function all($number): array {
        // $number からセグメント割り出し
        $index = $number - 1;
        $segment = intval($index / 32) + 1;
        $flag = 1 << ($index % 32);
        return self::where('segment', $segment)->where("flag & $flag = $flag")->all();
    }

    /**
     * @param $uid
     * @param $segment
     * @param $flag
     * @return bool
     * @throws Exception
     */
    public static function confirmFlag($uid, $segment, $flag): bool {
        $MemberFlag = self::where('uid', $uid)->where('segment', $segment)->first();
        if (empty($MemberFlag)) {
            return empty($flag);
        } else {
            return ($MemberFlag->flag == $flag);
        }
    }

    /**
     * フラグセグメントをそのまま書き込み
     * @param $uid
     * @param $segment
     * @param $flags
     * @return void
     * @throws Exception
     */
    public static function setFlag($uid, $segment, $flags): void {
        $MemberFlag = self::where('uid', $uid)->where('segment', $segment)->first();
        if (empty($MemberFlag)) {
            // 新規作成
            if ($flags) {
                self::insert(['uid' => $uid, 'segment' => $segment, 'flag' => $flags]);
            }

        } else if ($MemberFlag->flag != $flags) {
            // 更新
            $MemberFlag->save(['flag' => $flags]);
        }
    }

    /**
     * フラグセグメントにＯＲ書き込み
     * @param $uid
     * @param $segment
     * @param $flags
     * @return void
     * @throws Exception
     */
    public static function orFlag($uid, $segment, $flags): void {
        $MemberFlag = self::where('uid', $uid)->where('segment', $segment)->first();
        if (empty($MemberFlag)) {
            // 新規作成
            if ($flags) {
                self::insert(['uid' => $uid, 'segment' => $segment, 'flag' => $flags]);
            }

        } else {
            $new = $MemberFlag->flag | $flags;
            if ($MemberFlag->flag != $new) {
                // 更新
                $MemberFlag->save(['flag' => $new]);
            }
        }

    }

    /**
     * フラグセグメントから指定フラグセットをリセット
     * @param $uid
     * @param $segment
     * @param $flags
     * @return void
     * @throws Exception
     */

    public static function subFlag($uid, $segment, $flags): void {
        $MemberFlag = self::where('uid', $uid)->where('segment', $segment)->first();
        if (empty($MemberFlag)) {
            // 新規作成しない

        } else {
            $new = $MemberFlag->flag & (~$flags);
            if ($MemberFlag->flag != $new) {
                // 更新
                $MemberFlag->save(['flag' => $new]);
            }
        }
    }

}