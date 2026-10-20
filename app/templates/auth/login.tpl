{* P12 ログイン（P11 モック loginView／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_auth.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-signup w-form">

        <div class="wrap" id="loginView">
            <h1 class="a-title">ログイン</h1>
            <p class="a-lead">登録に使った方法でログインしてください。</p>

            <div class="social-col">
                <a class="s-btn s-line" href="#" data-social="LINE"><span class="s-ico">💬</span>LINEでログイン</a>
                <a class="s-btn s-apple" href="#" data-social="Apple"><span class="s-ico"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.05 12.54c-.03-2.89 2.36-4.27 2.47-4.34-1.35-1.97-3.44-2.24-4.18-2.27-1.78-.18-3.47 1.05-4.37 1.05-.9 0-2.29-1.02-3.77-1-1.94.03-3.72 1.13-4.72 2.86-2.01 3.49-.51 8.66 1.45 11.49.96 1.39 2.1 2.94 3.6 2.88 1.44-.06 1.99-.93 3.73-.93 1.74 0 2.23.93 3.76.9 1.56-.03 2.54-1.41 3.49-2.8 1.1-1.61 1.55-3.17 1.58-3.25-.03-.02-3.03-1.16-3.06-4.59zM14.16 4.06c.79-.96 1.33-2.3 1.18-3.63-1.14.05-2.53.76-3.35 1.72-.73.85-1.38 2.21-1.21 3.51 1.28.1 2.58-.65 3.38-1.6z"/></svg></span>Appleでログイン</a>
                <a class="s-btn s-google" href="#" data-social="Google"><span class="s-ico"><span class="g-mark"></span></span>Googleでログイン</a>
                <a class="s-btn s-x" href="#" data-social="X"><span class="s-ico">𝕏</span>Xでログイン</a>
            </div>

            <div class="divider">または メールアドレスでログイン</div>
            <div class="mail-form">
                <input class="in-text" id="mailInputL" type="email" placeholder="メールアドレスを入力" autocomplete="email">
                <button class="btn-main disabled" id="mailBtnL" type="button">ログインコードを受け取る ▶</button>
                <p class="mail-note">メールに届く6桁コードでログインします（パスワード不要）。</p>
            </div>

            <p class="switch-link">アカウントがまだの方は <a id="toSignup" href="/auth/entry">無料登録（30秒） ▶</a></p>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script>
    (function () {
        var input = document.getElementById('mailInputL');
        var btn = document.getElementById('mailBtnL');
        if (!input || !btn) return;
        input.addEventListener('input', function () {
            btn.classList.toggle('disabled', !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(input.value));
        });
    })();
    </script>
{/block}
