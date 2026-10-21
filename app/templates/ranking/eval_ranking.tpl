{* P6 本日の好走確率ランキング *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_ranking.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-ranking pc-grid">

        <div class="g-full">
            <div class="page-head">
                <h1>本日の好走確率ランキング</h1>
                <span class="en">TODAY'S PROBABILITY RANKING</span>
                <p class="lead">本日の全出走馬を好走確率順に掲載するデイリーページ。発走後も馬は残り、着順が流れ込みます（答え合わせ表）。行タップで出馬表（発走後は結果・払戻し）へ移動できます。</p>
            </div>

            <div class="day-nav">
                <button type="button" class="dn-btn" id="dayPrev">◀ 7/22(月)</button>
                <span class="dn-cur" id="dayCur">7/23(火)・本日</span>
                <button type="button" class="dn-btn" id="dayNext" disabled>7/24(水) ▶</button>
            </div>

            <div class="filter">
                <div class="f-row"><span class="f-lbl">対象</span>
                    <button type="button" class="chip on" data-area="all">すべて</button>
                    <button type="button" class="chip" data-area="jra">中央</button>
                    <button type="button" class="chip" data-area="local">地方</button>
                </div>
                <div class="f-row"><span class="f-lbl">指標</span>
                    <button type="button" class="chip on" data-mode="win">勝利確率</button>
                    <button type="button" class="chip" data-mode="fuku">複勝確率</button>
                </div>
                <p class="update-note" id="updateNote"><span class="live-dot"></span>15:15時点　<span class="lg-fin">最終</span>＝発走約30分前の最終補正で確定</p>
            </div>
        </div>

        <div class="g-side">
            <div class="day-sum" id="daySum"></div>
            <p class="sum-note" id="sumNote" hidden>※ 圏内は発走前の確率変動で入れ替わることがあります（NEW＝新規ランクイン）。成績は<b>各レースの発走時点で圏内だった馬</b>のみで集計し、発走後に遡って変動しません。</p>
            <p class="arc-open" id="arcOpen" hidden>✓ 過去のランキングは確定版として全公開中（会員登録不要で全て見られます）</p>
        </div>

        <div class="g-main">
            <ul class="rk-list" id="rkList"></ul>
            <p class="rk-note" id="rkNote"></p>

            <div id="ctaWrap"></div>

            <div class="demo-switch">
                <span class="d-lbl">モック確認用：</span>
                <button type="button" class="on" data-st="guest">非ログイン</button>
                <button type="button" data-st="free">無料会員</button>
                <button type="button" data-st="sub_chuo">中央限定</button>
                <button type="button" data-st="sub_chiho">地方限定</button>
                <button type="button" data-st="sub_full">フル会員</button>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_eval_ranking.js"></script>
{/block}
