{* P11 会員登録（コード認証）（P11 モック codeView／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/auth/auth.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-signup w-form">

        <div class="wrap" id="codeView">
            <h1 class="a-title">認証コードを入力</h1>
            <p class="a-lead"><b id="codeMail">{$register_email|escape}</b> に6桁のコードを送信しました。届いたコードを入力してください。</p>
            <div class="code-row">
                <input class="code-box" maxlength="1" inputmode="numeric" autocomplete="one-time-code" aria-label="認証コード 1桁目">
                <input class="code-box" maxlength="1" inputmode="numeric" aria-label="認証コード 2桁目">
                <input class="code-box" maxlength="1" inputmode="numeric" aria-label="認証コード 3桁目">
                <input class="code-box" maxlength="1" inputmode="numeric" aria-label="認証コード 4桁目">
                <input class="code-box" maxlength="1" inputmode="numeric" aria-label="認証コード 5桁目">
                <input class="code-box" maxlength="1" inputmode="numeric" aria-label="認証コード 6桁目">
            </div>
            <div class="mail-form mail-form--code">
                <button class="btn-main" id="codeBtn" type="button">認証して登録を完了する ▶</button>
            </div>
            <p class="resend">コードが届かない場合は <a href="#">再送信</a>（迷惑メールフォルダもご確認ください）</p>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script>
    (function () {
        var boxes = document.querySelectorAll('.code-box');
        boxes.forEach(function (box, i) {
            box.addEventListener('input', function () {
                if (box.value && i < boxes.length - 1) {
                    boxes[i + 1].focus();
                }
            });
        });
    })();
    </script>
{/block}
