{* P3 商品詳細 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_tools_detail.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-item pc-grid" data-tool-id="{$tool_id|escape}">

        <div class="g-full">
            <div class="back-bar"><a href="/tools/tools_top"><span class="ar">‹</span> おすすめ攻略ツール一覧へ</a></div>

            <div class="mock-ctrl">
                <span class="m-lbl">会員状態：</span>
                <a href="#" data-st="guest">非会員</a>
                <a href="#" data-st="free" class="on">無料会員</a>
                <a href="#" data-st="sub_full">フル会員</a>
                <span class="sep"></span>
                <span class="m-lbl">商品タイプ：</span>
                <a href="#" data-ty="pt" class="on">pt商品</a>
                <a href="#" data-ty="subsc">サブスク対象</a>
                <a href="#" data-ty="freebie">無料</a>
                <span class="m-lbl" style="margin-left:6px;">状態：</span>
                <a href="#" id="mcOwned">購入済み</a>
            </div>
        </div>

        <div class="g-main">
            <div class="prod-head">
                <span class="p-cat" id="catLabel">まとめ・時短</span><span class="p-new" id="prodNew">NEW</span>
                <h1 id="prodName">推奨馬リスト（8/8 中央）</h1>
                <div class="p-meta">
                    <span>提供：<b>Eval公式</b></span>
                    <span>公開：<b>8/7 20:00</b></span>
                    <span>対象：<b>8/8（土）中央 24R</b></span>
                </div>
            </div>

            <div class="price-band" id="priceBand"></div>

            <p class="desc" id="prodDesc">Evalの好走確率に、<b>解析が拾えない前日情報・調教評価・オッズの歪み</b>を上乗せして絞り込んだ、その日の中央全レースからの推奨馬リストです。各馬に<b>推奨理由の短評</b>付き。</p>
            <div class="desc-points">
                <p class="dp-t">この商品でわかること</p>
                <ul id="prodPoints">
                    <li>本日の推奨馬（5〜10頭・レース横断で厳選）</li>
                    <li>各馬の勝利確率・単勝期待値（公開時点）</li>
                    <li>推奨理由の短評（データ＋定性情報）</li>
                </ul>
            </div>

            <div class="sec-title"><h2 id="contentTitle">推奨馬リスト</h2><span class="en">CONTENTS</span><span class="fresh">8/7 20:00公開</span></div>
            <div id="contentArea"></div>
        </div>

        <div class="g-side">
            <div class="cta-wrap" id="ctaWrap"></div>
        </div>

        <div class="g-full g-after">
            <div class="sec-title"><h2>あわせて使われている商品</h2><span class="en">RELATED</span></div>
            <div class="rel-list" id="relList">
                <a class="rel-card" href="/tools/tools_detail?id=t101">
                    <span class="t-ico"><svg viewBox="0 0 24 24"><path d="M4 21V4"/><path d="M4 5h13l-2.5 3.5L17 12H4"/></svg></span>
                    <span><span class="r-name">前走不利情報（8/8分）</span><br><span class="r-cat">解析が拾えない情報</span></span>
                    <span class="r-price">300pt</span><span class="r-chev">›</span>
                </a>
                <a class="rel-card" href="/tools/tools_detail?id=t201">
                    <span class="t-ico"><svg viewBox="0 0 24 24"><path d="M3 17l5-6 4 3 6-8"/><path d="M21 21H3V3"/></svg></span>
                    <span><span class="r-name">ラップ分析情報（8/8分）</span><br><span class="r-cat">ファクター特化</span></span>
                    <span class="r-price">300pt</span><span class="r-chev">›</span>
                </a>
            </div>

            <p class="note">※ 情報商品の性質上、<b>購入後の返金はできません</b>。内容は公開時点のデータに基づきます（確率・期待値はTOPページで最新値をご確認ください）。日付ものの商品は当日レース終了まで閲覧できます。</p>

            <div class="sheet-ov" id="sheetOv">
                <div class="sheet" id="sheet"></div>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_tools_detail.js"></script>
{/block}
