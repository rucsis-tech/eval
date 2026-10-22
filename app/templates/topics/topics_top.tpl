{* P14/P15/P16 トピックス・速報・お知らせ一覧 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/topics/topics.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-topics" data-initial-tab="{$topics_tab|escape}">

    <!-- ===== 一覧ビュー ===== -->
    <div id="listView">
    <div class="page-head">
    <h1 id="pageTitle">トピックス</h1>
    <span class="en" id="pageEn">TOPICS</span>
    </div>
    <div class="c-tabs">
    <a href="#" class="on" data-tab="topics">トピックス</a>
    <a href="#" data-tab="flash">速報</a>
    <a href="#" data-tab="info">お知らせ</a>
    </div>

    <!-- トピックス：カテゴリ絞り込み＋記事カード -->
    <div id="tabTopics">
    <div class="cat-row" id="catRow"></div>
    <ul class="card-list" id="topicsList"></ul>
    <a class="read-more">もっと見る ▼</a>
    </div>

    <!-- 速報：画像なし・日付グルーピング -->
    <div id="tabFlash" hidden></div>

    <!-- お知らせ：テキスト中心・重要ピン留め -->
    <div id="tabInfo" hidden></div>
    </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/topics/topics_common.js"></script>
    <script src="/js/topics/topics_top.js"></script>
{/block}
