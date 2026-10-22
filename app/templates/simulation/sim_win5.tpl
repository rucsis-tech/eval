{* P4a WIN5 的中確率算出 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/simulation/sim_win5.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-win5 pc-grid">

        <div class="g-full">
            <div class="page-head">
                <span class="w5b">WIN5</span>
                <h1>的中確率算出</h1>
                <span class="en">WIN5 SIMULATOR</span>
            </div>
        </div>

        <div class="g-left">
            <div class="deadline"><span style="white-space:nowrap">締切 14:50</span><small>（1レース目の発走前まで）・対象5レースの1着馬をすべて的中で払戻</small></div>
            <div class="w5-notice"><i class="ic">i</i>
                <p>WIN5は発売締切の時点で、後半のレースがまだ最終補正（各レース発走約30分前）を迎えていません。そのため<b>全5レースとも最終補正前の勝率（前日公開値）で計算</b>します。最終確定した確率ではない点にご注意ください。</p>
            </div>
            <div class="stepper" id="stepper"></div>
        </div>

        <div class="g-main">
            <div id="raceView">
                <div class="race-info" id="raceInfo"></div>
                <div class="status-row">
                    <span class="status-badge pre">確率 最終補正前</span>
                    <span class="status-badge live"><span class="live-dot"></span>期待値 LIVE</span>
                    <span class="status-note">14:35時点・5分毎更新</span>
                </div>
                <div class="sel-wrap">
                    <table class="sel-t">
                        <thead>
                            <tr>
                                <th style="width:34px">馬番</th>
                                <th style="width:30px">印</th>
                                <th>馬名（騎手）</th>
                                <th style="width:64px">勝利確率</th>
                                <th style="width:66px">単勝期待値<span class="th-live"><span class="live-dot"></span>LIVE</span></th>
                                <th style="width:44px">選択</th>
                            </tr>
                        </thead>
                        <tbody id="selBody"></tbody>
                    </table>
                </div>
                <p class="sel-note">※ 1レースにつき1頭以上選択してください。印はTOPの出馬表で入力した予想印。妙味＝単勝期待値100%超。</p>
                <div class="nav-btns">
                    <a class="ghost" href="#" id="prevBtn">◀ 前のレース</a>
                    <a class="primary" href="#" id="nextBtn">次のレースへ ▶</a>
                </div>
            </div>

            <div id="sumView" hidden>
                <div class="sum-list" id="sumList"></div>
                <div class="sum-total">
                    <span class="lb">組数</span><span class="val" id="sumPts">−</span>
                </div>
                <div class="sum-hit">
                    <p class="lb">WIN5 的中確率</p>
                    <p class="val" id="sumHit">−</p>
                    <p class="sub">各レースで選択した馬の勝利確率の合計を5レース分掛け合わせた参考値です（<b style="color:var(--text)">全レース最終補正前の勝率で計算</b>。各レースの好走確率は発走約30分前に最終補正されるため、最終確定値とは異なる場合があります）。<br>レース行をタップすると選び直せます。</p>
                </div>
                <div class="btm-btns">
                    <a class="ghost" href="#">この買い目を保存する</a>
                    <a class="plain" href="/simulation/sim_top">単レースのシミュレーションへ</a>
                    <a class="plain" href="/">TOPへ戻る</a>
                </div>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/simulation/sim_win5.js"></script>
{/block}
