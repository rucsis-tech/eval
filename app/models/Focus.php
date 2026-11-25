<?php

class Focus extends Model {
    protected static string $tableName = 'focus_preset';


    /**
     * 見込み人数取得
     * @param $id
     * @return int
     * @throws Exception
     */

    public static function getProspectiveCount($id) {
        $Preset = Focus::first($id);
        if ($Preset) {
            $condition = $Preset->getCondition();
            return MemberSearch::distributeQuery($condition)
                ->count('COUNT(DISTINCT member_master.uid)');
        }
        return 0;
    }


    //-----------------------------------------------------------------------

    public function getCondition() {
        try {
            return json_decode($this->condition, true);
        } catch (Exception $e) {

        }
        return [];
    }


}