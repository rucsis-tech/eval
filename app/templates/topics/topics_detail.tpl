{* P17 トピックス記事詳細 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/topics/topics.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-topics" data-article-id="{$article_id|escape}">

        <div id="detailView">
            <div class="back-row"><a href="/topics/topics_top">◀ 一覧へ戻る</a></div>
            <div class="art" id="artWrap"></div>
            <div id="relWrap"></div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/topics/topics_common.js"></script>
    <script src="/js/topics/topics_detail.js"></script>
{/block}
