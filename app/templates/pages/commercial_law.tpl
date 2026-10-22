{* S4 特定商取引法に基づく表示（表型・S1 モック key: tokusho／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/static.css">
{/block}

{block name="content"}
        <div class="page-head">
            <h1>特定商取引法に基づく表示</h1>
        </div>

        <div class="kv-wrap">
            <table class="kv">
                <tr><th>販売事業者</th><td>株式会社◯◯◯◯<small>（仮）</small></td></tr>
                <tr><th>運営責任者</th><td>◯◯ ◯◯</td></tr>
                <tr><th>所在地</th><td>〒000-0000 東京都◯◯区◯◯ 0-0-0</td></tr>
                <tr><th>お問い合わせ</th><td>フォームより受付（電話番号は請求があれば遅滞なく開示します）</td></tr>
                <tr><th>販売価格</th><td>各商品・プランの申込画面に表示（すべて税込）</td></tr>
                <tr><th>支払方法</th><td>クレジットカード／Apple Pay／Google Pay／キャリア決済</td></tr>
                <tr><th>提供時期</th><td>決済完了後、直ちに利用できます</td></tr>
                <tr><th>返品・キャンセル</th><td>デジタルコンテンツの性質上、決済完了後の返金はできません。サブスクはいつでも解約でき、期間満了まで利用できます</td></tr>
                <tr><th>動作環境</th><td>推奨環境ページをご確認ください</td></tr>
            </table>
        </div>
        <p class="note">※ 内容はすべて仮（プレースホルダ）です。正式な表記は法務確認後に差し替えます。</p>
{/block}
