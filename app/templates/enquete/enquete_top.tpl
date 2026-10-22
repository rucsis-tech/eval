{* P9 アンケート一覧 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/enquete/enquete.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-survey">

        <div class="page-head">
            <h1>アンケート</h1>
            <span class="en">SURVEY</span>
            <p class="lead">回答すると<b>🎁ポイント</b>がもらえます。あなたの声がEval MODEを育てます。</p>
        </div>
        <div class="c-tabs">
            <a href="#" class="on" data-tab="open">開催中</a>
            <a href="#" data-tab="closed">終了・結果</a>
        </div>
        <div class="sv-list" id="svList"></div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/enquete/enquete_common.js"></script>
    <script src="/js/enquete/enquete_top.js"></script>
{/block}
