<?php

class SmartRss {

    static $ContentDoc;

    /**
     * 最新コンテンツからRSS用のXMLを取得
     * @return false|string
     * @throws Exception
     */

    static function make() {

        // コンテンツを取得
        $not_rss_json = Model::getJsonPair('not_rss', true);
        $subscription_only = Model::getJsonPair('subscription_only', true);

        $Conditions = ContentCondition::getCurrentQuery()
            ->where('content.category', 'IN', ['ニュース', '競馬コラム'])
            ->where("content_condition.work NOT LIKE '{$not_rss_json}'") // RSS除外フラグの判定
            ->where("content_condition.work NOT LIKE '{$subscription_only}'") // サブスク専用フラグの判定
            ->orderBy('content_condition.start_at', 'desc')
            ->limit(20)
            ->all();

        // XML雛形
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->load(APP_ROOT . 'data/rss/winsight_smartnews.xml');

        $channel = $doc->getElementsByTagName('channel')[0];
        $channel->getElementsByTagName('pubDate')[0]->nodeValue = self::getGMT(time());

        $origin = $channel->getElementsByTagName('item')[0];
        $channel->removeChild($origin);

        // 各アイテム処理
        foreach ($Conditions as $Condition) {
            $Condition->datalize();
            if (empty(Content::first('posts_id', $Condition->posts_id))) continue;

            $Content = Content::getItem($Condition->posts_id);
            $Condition->tagList = array_column($Content->tags, 'name');
            $item = $origin->cloneNode(true);
            $channel->appendChild($item);
            self::setContentItem($item, $Condition);
            // todo
            //break;
        }

        //var_dump($origin);
        $doc->formatOutput = TRUE;
        return $doc->saveXML();
    }

    /**
     * 出力されたXMLの調整
     * @param $xml
     * @return string
     */
    static function make_after($xml): string {
        $sep = strpos($xml, '<channel>');
        $head = substr($xml, 0, $sep);
        $body = substr($xml, $sep);

        $body = str_replace(' xmlns:content="http://purl.org/rss/1.0/modules/content/"', '', $body);
        $body = str_replace(' xmlns:dc="http://purl.org/dc/elements/1.1/"', '', $body);
        $body = str_replace(' xmlns:media="http://search.yahoo.com/mrss/"', '', $body);
        $body = str_replace(' xmlns:snf="http://www.smartnews.be/snf"', '', $body);

        $xml = $head . $body;

        if (!App::isRelease()) {
            $xml = str_replace('https://winsight.jp/', BASE_URL, $xml);
        }

        return $xml;
    }

    /**
     * 各コンテンツ内容をitemノードに反映
     * @param $item
     * @param $Content
     * @return mixed
     */
    static function setContentItem($item, $Content) {
        $url = BASE_URL . "column/column_page?id={$Content->posts_id}";

        self::setCDATA($item->getElementsByTagName('title')[0], $Content->title);
        $item->getElementsByTagName('link')[0]->nodeValue = $url;
        $item->getElementsByTagName('guid')[0]->nodeValue = $url;
        self::setCDATA($item->getElementsByTagName('description')[0], $Content->description);
        $item->getElementsByTagName('pubDate')[0]->nodeValue = self::getGMT(strtotime($Content->start_at));
        self::setCDATA($item->getElementsByTagName('encoded')[0], self::makeContent($Content));
        $item->getElementsByTagName('category')[0]->nodeValue = implode(',', $Content->tagList);
        //var_dump($Content);exit;
        $item->getElementsByTagName('creator')[0]->nodeValue = $Content->author_name;


        $thumbnail = $item->getElementsByTagName('thumbnail')[0];
        $thumbnail->setAttribute('url', SmartyExtensions::cf($Content->eyecatch_path));

        $relatedLink = $item->getElementsByTagName('relatedLink')[0];
        try {
            if ($Content->links) {
                foreach (explode(',', $Content->links) as $posts_id) {
                    $posts_id = trim($posts_id);
                    $LinkContent = ContentCondition::getCurrentQuery()->where('content.posts_id', $posts_id)->first();
                    if (empty($LinkContent)) continue;
                    $Link = $relatedLink->cloneNode(true);
                    $Link->setAttribute('link', BASE_URL . "column/column_page?id={$posts_id}");
                    $Link->setAttribute('title', $LinkContent->title);
                    $Link->setAttribute('thumbnail', SmartyExtensions::cf($LinkContent->eyecatch_path));
                    $item->insertBefore($Link, $relatedLink);
                }
            }
        } catch (Exception $e) {
            //var_dump($e);exit;
        }
        $item->removeChild($relatedLink);


        //$item->getElementsByTagName('description')[0]->nodeValue = $Content->description;
        return $item;
    }

    /**
     * Sets or updates the CDATA section value of the provided node.
     *
     * @param DOMNode $node The node to which the CDATA value is to be set.
     * @param string $value The value to assign to the CDATA section.
     * @return void
     */
    static function setCDATA($node, $value) {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType == XML_CDATA_SECTION_NODE) {
                $child->nodeValue = $value;
                return;
            }
        }
    }

    /**
     * GMT時刻文字列を返す
     * @param $stamp
     * @return string
     */
    static function getGMT($stamp) {
        return gmdate('D, j M Y H:i:s', $stamp) . ' GMT';
    }

    /**
     * コンテンツ本文テキストをHTML解析して整形する
     * @param $html
     * @return array|string|string[]
     */
    static function makeContent($Content) {

        $html = $Content->contents;
        // 改行置き換えテスト
        //$html = str_replace("\n", "<br>", $html);
        //$html = str_replace("<br><", "<", $html);

        $title = $Content->title;
        $thumb = SmartyExtensions::cf($Content->eyecatch_path);

        $HEAD = '<html><head><meta charset="UTF-8"></head><body>';
        $PRE = '<figure><img src="' . $thumb . '" alt="' . $title . '" width="100%"><figcaption></figcaption>' . $title . '</figure><div style="white-space: pre-wrap;">';
        $AFTER = '</div>';
        $FOOT = '</body></html>';

        self::$ContentDoc = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = "{$HEAD}{$PRE}\n{$html}\n{$AFTER}{$FOOT}";
        self::$ContentDoc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = self::$ContentDoc->getElementsByTagName('body');
        self::checkContentNode($root[0]);

        $html = self::$ContentDoc->saveHTML();
        $html = str_replace($HEAD, '', $html);
        $html = str_replace($FOOT, '', $html);
        $html = html_entity_decode($html);

        return $html;
    }

    /**
     * HTMLタグ解析して調整 inclusive
     * @param DOMNode $node
     * @param int $lv
     * @return bool
     */
    static function checkContentNode($node, $lv = 0): bool {
        if ($node->childElementCount == 0) return true;
        $sp = str_pad('', $lv, '-', STR_PAD_LEFT);

        // 配下のノードをチェック
        $removables = [];
        $no_links = [];
        foreach ($node->childNodes as $key => $child) {

            if ($child->nodeType != XML_ELEMENT_NODE) continue;

            $name = $child->nodeName;
            $value = mb_substr($child->nodeValue, 0, 20);
            $classes = explode(" ", $child->getAttribute('class'));

            //echo "{$sp}{$name} {$value}\n";

            //・本文から「class="no_rss"」で囲われた部分を排除
            if (in_array('no_rss', $classes) !== false) {
                // 削除対象
                $removables[] = $child;
                continue;
            }
            //・本文から「class="no_rss_link"」で囲われた部分のリンクを排除（aタグをspanに置き換え）
            if (in_array('no_rss_link', $classes) !== false) {
                $no_links[] = $child;
            }

            self::checkContentNode($child, $lv + 1);
        }

        // 削除対象削除
        foreach ($removables as $child) $child->remove();
        // Aタグ置き換え
        foreach ($no_links as $child) self::replaceANodetoSpanNode($node, $child);

        return true;
    }

    /**
     * AタグをSPANに置き換える
     * @param $node
     * @param $child
     * @return void
     */
    static function replaceANodetoSpanNode($parent, $a) {
        $span = self::$ContentDoc->createElement('span');
        $span->setAttribute('class', $a->getAttribute('class'));
        $span->setAttribute('style', $a->getAttribute('style'));
        foreach ($a->childNodes as $child) $span->appendChild($child);
        $res = $parent->replaceChild($span, $a);
    }

}