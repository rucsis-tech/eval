{* P11 会員登録（入力）（P11 モック signupView／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_auth.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-signup w-form">

        <div class="wrap" id="signupView">
            <h1 class="a-title"><b>30秒</b>で無料登録</h1>
            <p class="a-lead">登録した瞬間から、<b>好走確率とリアルタイム期待値（LIVE期待値）</b>が見られます。パスワード設定は不要です。</p>
            <div class="value-row">
                <span class="v-chip">好走確率データ</span>
                <span class="v-chip">リアルタイム期待値・妙味チップ</span>
                <span class="v-chip">的中確率シミュレーション</span>
                <span class="v-chip">🎁登録特典100pt</span>
            </div>

            <div class="social-col">
                <a class="s-btn s-line" href="#" data-social="LINE"><span class="s-ico">💬</span>LINEで登録<span class="s-badge">最短10秒</span></a>
                <a class="s-btn s-apple" href="#" data-social="Apple"><span class="s-ico"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M17.05 12.54c-.03-2.89 2.36-4.27 2.47-4.34-1.35-1.97-3.44-2.24-4.18-2.27-1.78-.18-3.47 1.05-4.37 1.05-.9 0-2.29-1.02-3.77-1-1.94.03-3.72 1.13-4.72 2.86-2.01 3.49-.51 8.66 1.45 11.49.96 1.39 2.1 2.94 3.6 2.88 1.44-.06 1.99-.93 3.73-.93 1.74 0 2.23.93 3.76.9 1.56-.03 2.54-1.41 3.49-2.8 1.1-1.61 1.55-3.17 1.58-3.25-.03-.02-3.03-1.16-3.06-4.59zM14.16 4.06c.79-.96 1.33-2.3 1.18-3.63-1.14.05-2.53.76-3.35 1.72-.73.85-1.38 2.21-1.21 3.51 1.28.1 2.58-.65 3.38-1.6z"/></svg></span>Appleで登録</a>
                <a class="s-btn s-google" href="#" data-social="Google"><span class="s-ico"><span class="g-mark"></span></span>Googleで登録</a>
                <a class="s-btn s-x" href="#" data-social="X"><span class="s-ico">𝕏</span>Xで登録</a>
            </div>

            <div class="divider">または メールアドレスで登録</div>
            <div class="mail-form">
                <input class="in-text" id="mailInput" type="email" placeholder="メールアドレスを入力" autocomplete="email">
                <button class="btn-main disabled" id="mailBtn" type="button">認証コードを受け取る ▶</button>
                <p class="mail-note"><b>パスワード不要。</b>メールに届く6桁コードを入力するだけで登録完了です。</p>
            </div>

            <div class="trust-row">
                <span>メールアドレスのみでOK</span><span>退会はいつでも可能</span>
            </div>
            <p class="switch-link">アカウントをお持ちの方は <a id="toLogin" href="/auth/login">ログイン ▶</a></p>
            <p class="legal-note">登録により<a href="/pages/rules">利用規約</a>・<a href="/pages/privacy">プライバシーポリシー</a>に同意したものとみなされます。</p>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script>
    (function () {
        var input = document.getElementById('mailInput');
        var btn = document.getElementById('mailBtn');
        if (!input || !btn) return;
        input.addEventListener('input', function () {
            btn.classList.toggle('disabled', !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(input.value));
        });
    })();
    </script>
{/block}
