{* P7 ポイント購入 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/settle/settle_point.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-points pc-grid">

        <div class="g-full">
            <div class="page-head">
                <h1>ポイント購入</h1>
                <span class="en">POINT SHOP</span>
                <p class="lead">ポイントは<b>攻略ツールの購入</b>などに使えます。1pt＝1円としてご利用いただけます。</p>
            </div>

            <div class="mock-ctrl">
                <span class="m-lbl">モック確認用：</span>
                <a href="#" id="mcMember" class="on">会員</a>
                <a href="#" id="mcGuest">非会員</a>
                <a href="#" id="mcOnce">初回パック購入済み</a>
                <a href="#" id="mcCampOff">キャンペーン非表示</a>
            </div>
        </div>

        <div class="g-side">
            <div class="bal-card" id="balCard"></div>
            <a class="reg-cta" id="regCta" href="/auth/entry" hidden>
                <span class="r-txt">ポイントの購入には無料登録が必要です<small>登録は30秒・メールまたはソーシャルログイン</small></span>
                <span class="r-btn">無料登録</span>
            </a>
        </div>

        <div class="g-main">
            <div class="sec-title" id="campTitle"><h2>キャンペーンパック</h2><span class="en">CAMPAIGN</span></div>
            <div class="camp-list" id="campList"></div>

            <div class="sec-title"><h2>ポイントパック</h2><span class="en">LINEUP</span></div>
            <ul class="pack-list" id="packList"></ul>

            <p class="note">※ ポイントの有効期限は<b>購入日から180日</b>です（キャンペーンポイントは期限が異なる場合があります。各パックの表記をご確認ください）。
            購入後の払い戻しはできません。詳細は<a href="#">資金決済法に基づく表示</a>・<a href="/pages/commercial_law">特定商取引法に基づく表示</a>をご確認ください。
            アプリ版では購入方法・価格が異なる場合があります。</p>

            {include file="partials/sheet_point.tpl"}
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/settle/settle_point.js"></script>
{/block}
