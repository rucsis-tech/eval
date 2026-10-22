{* S1 運営会社（表型・S1 モック key: company／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/static.css">
{/block}

{block name="content"}
        <div class="page-head">
            <h1>運営会社</h1>
        </div>

        <div class="kv-wrap">
            <table class="kv">
                <tr><th>会社名</th><td>株式会社◯◯◯◯<small>（仮）</small></td></tr>
                <tr><th>所在地</th><td>〒000-0000 東京都◯◯区◯◯ 0-0-0</td></tr>
                <tr><th>代表者</th><td>代表取締役 ◯◯ ◯◯</td></tr>
                <tr><th>設立</th><td>20◯◯年◯月</td></tr>
                <tr><th>事業内容</th><td>競馬予想情報サービス「Eval MODE」の企画・開発・運営</td></tr>
                <tr><th>公式X</th><td>@master_eval</td></tr>
                <tr><th>お問い合わせ</th><td>お問い合わせフォームより受け付けています</td></tr>
            </table>
        </div>
        <p class="note">※ 内容はすべて仮（プレースホルダ）です。正式な表記は法務確認後に差し替えます。</p>
{/block}
