{* P19 キャンペーンLP *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/lp/campaign.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-campaign w-narrow">

        <div class="hero">
            <span class="h-tag">タイアップ限定キャンペーン</span><span class="h-until">〜8/17(月) 23:59</span>
            <h1>タカシさんのフォロワー限定<br><span class="lm">フルプラン初月無料</span>＋<br>2ヶ月目以降も特別価格</h1>
            <p class="h-sub">X（旧Twitter）でおなじみ、<b>タカシ@回収率改善中</b>さんとのタイアップ企画。このページ経由の申込に限り、招待コード特典が適用されます。</p>
            <div class="h-partner">
                <span class="av">タ</span>
                <span><span class="pn">タカシ@回収率改善中</span><br><span class="pd">@taka_keiba ・ Evalアンバサダー</span></span>
                <span class="px">𝕏</span>
            </div>
        </div>

        <div class="offer">
            <p class="o-t">🎟 このキャンペーンの特典</p>
            <div class="o-main">
                <span class="o-code">EVALW</span>
                <span class="o-desc">フルプラン<span class="r">初月無料</span><br>2ヶ月目以降 <s style="color:#9aa3b2;">¥9,800</s> → <span class="r">¥7,800/月（仮）</span></span>
            </div>
            <p class="o-note">下のボタンから進むと、プラン選択画面に<b>コードが自動で適用</b>されます（入力不要）。既にアカウントをお持ちの方もご利用いただけます（おひとり様1回限り）。</p>
        </div>

        <div class="sec-title"><h2>はじめかた（30秒）</h2><span class="en">3 STEPS</span></div>
        <ul class="steps">
            <li class="step">
                <span class="s-no">1</span>
                <span><span class="s-t">無料登録</span><br><span class="s-d">LINE・Apple・Google・Xまたはメールで<b>約30秒</b>。パスワードは不要です。</span></span>
            </li>
            <li class="step">
                <span class="s-no">2</span>
                <span><span class="s-t">コード適用済みのプランを確認</span><br><span class="s-d">プラン選択画面で<b>EVALWが自動適用</b>。特典内容を確認して申込。</span></span>
            </li>
            <li class="step">
                <span class="s-no">3</span>
                <span><span class="s-t">今日のレースからすぐ使える</span><br><span class="s-d">全レースの好走確率と<b>リアルタイム期待値（LIVE期待値）</b>が開放されます。</span></span>
            </li>
        </ul>

        <div class="sec-title"><h2>Evalの実績</h2><span class="en">PROOF</span></div>
        <div class="pf-band">
            <span>昨日 8/8(土)：<b>的中18R／32R</b>・最高払戻 <span class="hot">68,930円</span><br>直近7日間の最高払戻 <span class="hot">86,880円</span>（3連単・札幌11R）</span>
            <a class="pf-link" href="/results/results_top">実績を見る ›</a>
        </div>

        <p class="note">※ キャンペーン内容・期間・特典はタイアップごとに異なります（本LPはバックエンド管理のテンプレート）。
        特典は<b>おひとり様1回限り</b>・他キャンペーンとの併用不可。サブスクはいつでも解約できます（無料期間中の解約は料金がかかりません）。
        価格はすべて仮です。確率・期待値は的中や利益を保証するものではありません。</p>

        <div class="sticky-cta">
            <a class="sc-btn" href="/settle/settle_subsc" id="campaignCta">特典つきではじめる（コード自動適用） ›</a>
            <p class="sc-sub">登録は約30秒・いつでも解約OK・8/17(月)まで</p>
        </div>

    </div>
{/block}

{block name="footer"}
    {include file="partials/app_back.tpl"}
{/block}

{block name="scripts"}
    <script src="/js/lp/campaign.js"></script>
{/block}
