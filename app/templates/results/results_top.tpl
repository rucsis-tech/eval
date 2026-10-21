{* P5 的中実績 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_results.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-results pc-grid">

        <div class="g-full">
            <div class="page-head">
                <h1>的中実績</h1>
                <span class="en">HIT RECORD</span>
                <p class="lead">Evalの<b>好走確率上位6頭ボックス</b>（馬連・馬単・3連複・3連単）を毎レース購入した場合の的中実績です。本日の的中は<b>レース確定ごとにリアルタイム反映</b>されます。</p>
            </div>

            <div class="c-tabs">
                <a href="#" class="on" data-scope="total">総合</a>
                <a href="#" data-scope="jra">中央</a>
                <a href="#" data-scope="local">地方</a>
            </div>

            <div class="date-nav" id="dateNav"></div>
        </div>

        <div class="g-side">
            <div class="sum-head">
                <span class="t" id="sumTitle">総合サマリー</span>
                <span class="state" id="sumState"></span>
            </div>
            <div class="stat-grid" id="statGrid"></div>
            <p class="basis">※ 的中基準：Eval好走確率上位6頭のボックス購入（<b>馬連15点・馬単30点・3連複20点・3連単120点</b>／各100円）。
            ボックスの性質上、馬連・馬単／3連複・3連単はそれぞれ同時に的中します。<b>上位6頭は発走約30分前の最終補正で確定した好走確率で判定</b>（公開時の値から変わることがあるため、判定基準を最終値に固定）。数値はモック用の仮データです。</p>
        </div>

        <div class="g-main">
            <div class="sec-title"><h2 id="listTitle">的中レース</h2><span class="en">RACE BY RACE</span></div>
            <ul class="race-list" id="raceList"></ul>
            <p class="list-note" id="listNote"></p>
        </div>

        <div class="sheet-ov" id="sheetOv">
            <div class="sheet" id="sheet"></div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_results_top.js"></script>
{/block}
