<?php

class Enquete extends Model {
    protected static string $tableName = 'enquete_master';

    const STATUS_READY = 'ready';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSING = 'closing';
    const STATUS_END = 'end';
    const STATUSES = [
        //self::STATUS_READY => '停止中',
        //self::STATUS_OPEN => '回答可能',
        //self::STATUS_CLOSING => '結果表示',
        //self::STATUS_END => '終了',
        self::STATUS_OPEN => '表示',
        self::STATUS_END => '非表示',
    ];

    static
    public function getDefaultQuery() {
        return self::query()
            ->where('closed', 0)
            ->select(['enquete_master.*']);
    }

    static
    public function getCurrentQuery() {
        $now = self::date();
        return self::getDefaultQuery()
            ->where('disabled', 0)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now);
    }

    //-----------------------------------------------------------------------------------------------------------

    public function datalize(): self {
        $this->questions = $this->json('questions');
        $this->score = $this->work('score', 0);
        $this->publish = $this->work('publish', 0);
        $this->description = $this->work('description', '');

        $now_time = time();
        $this->now_event = ($this->start_at && $now_time >= strtotime($this->start_at)) ? 1 : 0;
        $this->end_event = ($this->end_at && $now_time >= strtotime($this->end_at)) ? 1 : 0;

        return $this;
    }

    public function store($jsonData) {

        $work = [
            'score' => $jsonData['score'],
            'publish' => $jsonData['publish'],
            'description' => $jsonData['description'],
        ];
        $questions = $jsonData['questions'];

        $this->save([
            'status' => $jsonData['status'],
            'title' => $jsonData['title'],
            'start_at' => $jsonData['start_at'],
            'end_at' => $jsonData['end_at'],
            'work' => json_encode($work, JSON_UNESCAPED_UNICODE),
            'questions' => json_encode($questions, JSON_UNESCAPED_UNICODE),

        ]);
    }

    //-----------------------------------------------------------------------------------------------------------

    /**
     * 回答受付
     * @param $uid // varchar(10)
     * @param $enquete_id
     * @param $question_id
     * @param $answers
     * @return bool|int
     * @throws Exception
     */
    static public function answer($uid, $enquete_id, $question_id, $answers): bool|int {
        // 投票済み確認
        $table = App::logDB('enquete');
        $Exists = self::table($table)
            ->where('uid', $uid)
            ->where('enquete_id', $enquete_id)
            ->where('question_id', $question_id)
            ->first();

        // 投票済み
        if ($Exists) return false;

        // 投票記録
        $work = json_encode(['answers' => $answers], JSON_UNESCAPED_UNICODE);
        $result = count($answers) == 1 ? intval($answers[0]) : 0;
        $id = self::insert(['uid' => $uid, 'enquete_id' => $enquete_id, 'question_id' => $question_id, 'result' => $result, 'work' => $work], $table);

        return $id;
    }

}