<?php

class Info extends Model {
    protected static string $tableName = 'info';

    const TYPE_NEWS = 'news';
    const TYPE_INFO = 'info';

    const TYPE_TOPIC = 'topic';
    const TOPIC_CATEGORIES = ['WIN5攻略', '危険な人気馬', 'ラップ分析', '馬具情報', 'コラム', 'ニュース'];


    public function datalize(): self {
        if ($this->type == self::TYPE_NEWS) $this->datalizeNews();
        if ($this->type == self::TYPE_INFO) $this->datalizeInfo();
        if ($this->type == self::TYPE_TOPIC) $this->datalizeTopics();
        return $this;
    }

    public function store($post) {
        if ($this->type == self::TYPE_NEWS) $this->storeNews($post);
        if ($this->type == self::TYPE_INFO) $this->storeInfo($post);
        if ($this->type == self::TYPE_TOPIC) $this->storeTopics($post);
    }
    //---------------------------------------------------------------------
    //---------------------------------------------------------------------

    private function datalizeNews() {
        $this->link_url = $this->work('link_url');
        $this->text = $this->work('text', '');
        $this->disabled = ($this->priority < 1) ? 1 : 0;
        $this->priority = abs($this->priority);
    }

    private function storeNews($post) {
        $this->priority = $post['priority'] * ($post['disabled'] ? -1 : 1);
        $this->work([
            'link_url' => trim($post['link_url']),
            'text' => trim($post['text']),
        ]);

        $this->save([
            'category' => trim($post['category']),
            'title' => trim($post['title']),
            'start_at' => empty($post['start_at']) ? null : $post['start_at'],
            'end_at' => empty($post['end_at']) ? null : $post['end_at'],
            'priority' => $this->priority,
            'work' => $this->work,
            'closed' => trim($post['closed'] ?? '0'),
        ]);
    }

    //---------------------------------------------------------------------
    //---------------------------------------------------------------------

    private function datalizeInfo() {
        $this->content = $this->work('content');
        $this->disabled = ($this->priority < 1) ? 1 : 0;
        $this->priority = abs($this->priority);

        // new 判定
        $this->new = (strtotime(!empty($this->start_at) ? $this->start_at : '2025-01-01') > strtotime("-22 hour")) ? 1 : 0;
    }

    private function storeInfo($post) {
        $this->priority = $post['priority'] * ($post['disabled'] ? -1 : 1);
        $this->work([
            'content' => trim($post['content']),
        ]);

        $this->save([
            'title' => trim($post['title']),
            'start_at' => empty($post['start_at']) ? null : $post['start_at'],
            'end_at' => empty($post['end_at']) ? null : $post['end_at'],
            'priority' => $this->priority,
            'work' => $this->work,
            'closed' => trim($post['closed'] ?? '0'),
        ]);
    }

    //---------------------------------------------------------------------
    //---------------------------------------------------------------------

    private function datalizeTopics(): void {
        $this->content = $this->work('content');
        $this->disabled = ($this->priority < 1) ? 1 : 0;
        $this->priority = abs($this->priority);
    }

    private function storeTopics($post): void {
        $this->priority = $post['priority'] * ($post['disabled'] ? -1 : 1);
        $this->work([
            'content' => trim($post['content']),
        ]);

        $this->save([
            'title' => trim($post['title']),
            'start_at' => empty($post['start_at']) ? null : $post['start_at'],
            'end_at' => empty($post['end_at']) ? null : $post['end_at'],
            'priority' => $this->priority,
            'work' => $this->work,
            'closed' => trim($post['closed'] ?? '0'),
        ]);
    }

}