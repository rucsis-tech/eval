{* P4 的中確率シミュレーション *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_sim.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-sim pc-grid">

        <div class="g-full">
            <div class="page-head">
                <h1>的中確率シミュレーション</h1>
                <span class="en">HIT PROBABILITY SIMULATOR</span>
            </div>
        </div>

        <div class="g-main">
            <div class="selector">
                <div class="tabs date">
                    <a href="#">7/12(日)</a><a class="on" href="#">7/14(火)</a><a href="#">7/15(水)</a>
                </div>
                <div class="tabs venue">
                    <a class="on" href="#">東京</a><a href="#">京都</a><a href="#">福島</a>
                    <a class="local" href="#">門別</a><a class="local" href="#">高知</a><a class="local" href="#">大井</a>
                    <a class="local" href="#">川崎</a><a class="local" href="#">名古屋</a><a class="local" href="#">佐賀</a>
                </div>
                <div class="tabs race">
                    <a class="done" href="#">1R</a><a class="done" href="#">2R</a><a href="#">3R</a><a href="#">4R</a>
                    <a href="#">5R</a><a href="#">6R</a><a href="#">7R</a><a href="#">8R</a>
                    <a href="#">9R</a><a href="#">10R</a><a class="on" href="#">11R</a><a href="#">12R</a>
                </div>
                <div class="race-info">
                    <b>東京11R</b> <b>ヴィクトリアマイル</b>
                    <span class="g1">GI</span><span class="win5">WIN5</span>
                    芝1600m <span class="going">良</span>
                    <span class="time">発走 15:40</span>
                </div>
            </div>

            <a class="btn-win5" id="win5Top" href="/simulation/sim_win5">
                <span class="w5b">WIN5</span>的中確率算出 ▶<small>本日15:00 販売締切</small>
            </a>

            <div class="sub-title"><h2>券種</h2><span class="en">BET TYPE</span></div>
            <div class="chip-row" id="kindChips">
                <button type="button" class="chip" data-kind="umaren">馬連</button>
                <button type="button" class="chip" data-kind="umatan">馬単</button>
                <button type="button" class="chip on" data-kind="wide">ワイド</button>
                <button type="button" class="chip" data-kind="sanfuku">3連複</button>
                <button type="button" class="chip" data-kind="santan">3連単</button>
            </div>

            <div class="sub-title"><h2>買い方</h2><span class="en">METHOD</span><span class="note" id="methodNote"></span></div>
            <div class="chip-row" id="methodChips"></div>
            <div class="ninki-row" id="ninkiRow" hidden>
                <span class="lb">表示：人気上位</span>
                <button type="button" class="chip" data-n="5">5点</button>
                <button type="button" class="chip on" data-n="10">10点</button>
                <button type="button" class="chip" data-n="20">20点</button>
                <button type="button" class="chip" data-n="30">30点</button>
            </div>

            <div class="mode-row" id="modeRow">
                <span class="lb">表示：</span>
                <button type="button" class="btn-mode on" id="modeWin">勝利確率</button>
                <button type="button" class="btn-mode" id="modeFuku">複勝確率</button>
            </div>

            <div class="sel-wrap" id="selWrap">
                <table class="sel-t" id="selTable">
                    <thead><tr id="selHead"></tr></thead>
                    <tbody id="selBody"></tbody>
                </table>
            </div>
            <p class="sel-note" id="selNote"></p>
        </div>

        <div class="g-side">
            <div class="count-bar" id="countBar">
                <span class="lb">選択中の買い目</span>
                <span class="pts zero" id="ptCount">0点</span>
                <button type="button" class="btn-calc disabled" id="calcBtn">的中確率を確認 ▶</button>
            </div>
        </div>

        <div class="g-full g-after">
            <div id="resultSec" hidden>
                <div class="sub-title"><h2>的中確率・期待値</h2><span class="en">RESULT</span><span class="note" id="resCount"></span></div>
                <table class="res-t">
                    <thead>
                        <tr id="resHead"></tr>
                    </thead>
                    <tbody id="resBody"></tbody>
                </table>
                <p class="res-note">※ 的中確率はEvalの勝利確率から算出した参考値（<b>計算時点の確率</b>に基づきます。好走確率は発走約30分前に最終補正され確定するため、確定後の再計算をおすすめします）。期待値は現在オッズ（5分毎更新）に基づくLIVE値（赤＝100%超）。実装時は30〜50件超でページ分割も可。</p>
            </div>

            <div class="demo-switch">
                <span class="d-lbl">モック確認用：WIN5販売時間</span>
                <button type="button" class="on" data-w="1">販売中（上部表示）</button>
                <button type="button" data-w="0">時間外（下部表示）</button>
            </div>

            <div class="btm-btns">
                <a class="ghost" href="#">買い目を保存して投票を続ける</a>
                <a class="primary" href="#">金額入力へ ▶</a>
                <a class="plain" id="win5Btm" href="/simulation/sim_win5" hidden>WIN5 的中確率算出</a>
                <a class="plain" href="/">TOPへ戻る</a>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_sim_top.js"></script>
{/block}
