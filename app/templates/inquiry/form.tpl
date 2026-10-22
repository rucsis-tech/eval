{* S6 お問い合わせ（フォーム型・S1 モック key: contact／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/static.css">
{/block}

{block name="content"}
        <div class="page-head">
            <h1>お問い合わせ</h1>
            <p class="meta">通常2〜3営業日以内に返信します</p>
        </div>

        <form class="form" id="inquiryForm" action="#" method="post" novalidate>
            <div>
                <label for="inquiryType">お問い合わせ種別<small>必須</small></label>
                <select id="inquiryType" name="type" required>
                    <option value="">選択してください</option>
                    <option value="account">アカウント・ログイン</option>
                    <option value="plan">プラン・決済</option>
                    <option value="point">ポイント</option>
                    <option value="data">データ・確率について</option>
                    <option value="other">その他</option>
                </select>
            </div>
            <div>
                <label for="inquiryEmail">メールアドレス<small>必須</small></label>
                <input type="email" id="inquiryEmail" name="email" placeholder="mail@example.com" required autocomplete="email">
            </div>
            <div>
                <label for="inquiryBody">お問い合わせ内容<small>必須</small></label>
                <textarea id="inquiryBody" name="body" placeholder="できるだけ具体的にご記入ください" required></textarea>
            </div>
            <p class="form-note">※ ログイン中は会員情報が自動で添付されます。返信は登録メールアドレス宛にお送りします。</p>
            <button type="submit" class="send">送信する</button>
        </form>
{/block}

{block name="scripts"}
    <script src="/js/inquiry/form.js"></script>
{/block}
