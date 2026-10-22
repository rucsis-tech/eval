{* P9 アンケート回答後 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/enquete/enquete.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-survey" data-survey-id="{$survey_id|escape}" data-variant="{$thanks_variant|escape}">

        <div id="thanksView">
        <div class="tk-wrap">
            <div class="tk-card">
                <div class="tk-check">✓</div>
                <p class="tk-title">回答ありがとうございました！</p>
                <p class="tk-sub" id="tkSub"></p>
                <span class="tk-pt">🎁 <span id="tkPt"></span>pt 獲得</span>
            </div>
        </div>

        <div class="pr-slot" id="prSlot" hidden>
            <span class="pr-tag">PR</span>
            <a class="pr-banner" href="#">
                <p class="pb-main">オッズパーク新規登録で<br><b>最大3,000円分</b>プレゼント中</p>
                <span class="pb-btn">詳しく見る ▶</span>
            </a>
        </div>

        <div class="lead-card" id="leadHigh" hidden>
            <p class="stars">★★★★★</p>
            <p class="lc-main">Eval MODEを気に入っていただけたなら、<br>ストアレビューで応援をお願いします！</p>
            <p class="lc-sub">30秒で完了します（レビュー後に🎁20ptプレゼント）</p>
            <a class="lc-btn" href="#">ストアでレビューを書く ▶</a>
        </div>

        <div class="lead-card alt" id="leadLow" hidden>
            <p class="lc-main">貴重なご意見をありがとうございます。<br>いただいた内容はサービス改善に活用します。</p>
            <p class="lc-sub">お困りの点は、お問い合わせから直接ご連絡いただけます</p>
            <a class="lc-btn" href="#">改善要望・お問い合わせ ▶</a>
        </div>

        <div class="tk-btns">
            <a class="ghost" href="/enquete/enquete_top">アンケート一覧へ戻る</a>
            <a class="plain" href="/">TOPへ戻る</a>
        </div>

        <p class="mock-note" id="tkMockNote"></p>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/enquete/enquete_common.js"></script>
    <script src="/js/enquete/enquete_thanks.js"></script>
{/block}
