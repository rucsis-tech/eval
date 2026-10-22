{* P20 ホーム画面追加ガイド *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/home_guide.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-guidehome w-narrow">

        <div class="page-head">
            <h1>ホーム画面に追加する方法</h1>
            <span class="en">ADD TO HOME SCREEN</span>
            <p class="lead">Eval MODEはアプリのインストール不要。ホーム画面に追加すると、<b>アイコンからワンタップで全画面起動</b>でき、発走直前の確率チェックがすばやくなります。</p>
        </div>

        <div class="merits">
            <div class="m"><span class="mi">⚡</span><span>ワンタップで<br>すぐ起動</span></div>
            <div class="m"><span class="mi">🖥</span><span>ブラウザ枠なしの<br>全画面表示</span></div>
            <div class="m"><span class="mi">🔔</span><span>お知らせ通知に<br>対応（予定）</span></div>
        </div>

        <div class="os-tabs" id="osTabs">
            <a href="#" data-os="ios"><span class="ot-ico"></span>iPhone</a>
            <a href="#" data-os="android"><span class="ot-ico"></span>Android</a>
            <a href="#" data-os="pc"><span class="ot-ico"></span>パソコン</a>
        </div>
        <p class="detect" id="detect"></p>

        <div class="os-pane" data-pane="ios">
            <p class="pane-lead"><b>Safari</b>で開いている場合の手順です（iOS 16.4以降はChromeでも同じ手順で追加できます）。</p>
            <ol class="steps">
                <li class="step">
                    <span class="no">1</span>
                    <div class="s-body">
                        <p class="s-t">画面下の<b>共有ボタン</b>をタップ</p>
                        <p class="s-d">Safari下部のツールバー中央にある、四角から矢印が出たアイコンです。</p>
                        <div class="illu bar">
                            <span class="ib">‹</span><span class="ib">›</span>
                            <span class="ib hl"><svg viewBox="0 0 24 24"><path d="M12 3v12"/><path d="M8 7l4-4 4 4"/><path d="M6 11v9h12v-9"/></svg></span>
                            <span class="ib">▢</span><span class="ib">⧉</span>
                        </div>
                    </div>
                </li>
                <li class="step">
                    <span class="no">2</span>
                    <div class="s-body">
                        <p class="s-t">メニューを下にスクロールし<b>「ホーム画面に追加」</b>をタップ</p>
                        <p class="s-d">見当たらない場合は、メニュー最下部の「アクションを編集」から追加できます。</p>
                        <div class="illu menu">
                            <span class="mr">コピー<i>⧉</i></span>
                            <span class="mr">ブックマークを追加<i>📖</i></span>
                            <span class="mr hl">ホーム画面に追加<i class="plus">＋</i></span>
                        </div>
                    </div>
                </li>
                <li class="step">
                    <span class="no">3</span>
                    <div class="s-body">
                        <p class="s-t">右上の<b>「追加」</b>をタップして完了</p>
                        <p class="s-d">名前は「Eval MODE」のままでOK。ホーム画面にアイコンが追加されます。</p>
                        <div class="illu confirm">
                            <span class="cf-l">キャンセル</span><span class="cf-c">ホーム画面に追加</span><span class="cf-r hl">追加</span>
                        </div>
                    </div>
                </li>
                <li class="step done">
                    <span class="no">✓</span>
                    <div class="s-body">
                        <p class="s-t">ホーム画面の<b>Eval MODEアイコン</b>から起動</p>
                        <div class="illu home"><span class="app"><b>E</b></span><span class="app-n">Eval MODE</span></div>
                    </div>
                </li>
            </ol>
        </div>

        <div class="os-pane" data-pane="android" hidden>
            <p class="pane-lead"><b>Chrome</b>で開いている場合の手順です。</p>
            <ol class="steps">
                <li class="step">
                    <span class="no">1</span>
                    <div class="s-body">
                        <p class="s-t">画面右上の<b>︙（メニュー）</b>をタップ</p>
                        <div class="illu bar top">
                            <span class="url">eval-mode.jp</span><span class="ib">⧉</span><span class="ib hl">︙</span>
                        </div>
                    </div>
                </li>
                <li class="step">
                    <span class="no">2</span>
                    <div class="s-body">
                        <p class="s-t"><b>「ホーム画面に追加」</b>または<b>「アプリをインストール」</b>をタップ</p>
                        <p class="s-d">端末・Chromeのバージョンにより表記が異なります。どちらでも同じです。</p>
                        <div class="illu menu">
                            <span class="mr">新しいタブ</span>
                            <span class="mr">ブックマーク</span>
                            <span class="mr hl">ホーム画面に追加<i class="plus">＋</i></span>
                        </div>
                    </div>
                </li>
                <li class="step">
                    <span class="no">3</span>
                    <div class="s-body">
                        <p class="s-t">確認画面で<b>「追加」</b>（または「インストール」）をタップ</p>
                        <div class="illu confirm"><span class="cf-l">キャンセル</span><span class="cf-c">Eval MODE</span><span class="cf-r hl">追加</span></div>
                    </div>
                </li>
                <li class="step done">
                    <span class="no">✓</span>
                    <div class="s-body">
                        <p class="s-t">ホーム画面／アプリ一覧の<b>Eval MODEアイコン</b>から起動</p>
                        <div class="illu home"><span class="app"><b>E</b></span><span class="app-n">Eval MODE</span></div>
                    </div>
                </li>
            </ol>
        </div>

        <div class="os-pane" data-pane="pc" hidden>
            <p class="pane-lead"><b>Chrome／Edge</b>で開いている場合の手順です（Safari・Firefoxは非対応のため、ブックマークをご利用ください）。</p>
            <ol class="steps">
                <li class="step">
                    <span class="no">1</span>
                    <div class="s-body">
                        <p class="s-t">アドレスバー右端の<b>インストールアイコン</b>をクリック</p>
                        <p class="s-d">モニターに下向き矢印が付いたアイコンです。見当たらない場合はメニュー（︙）→「キャスト、保存、共有」→「ページをアプリとしてインストール」。</p>
                        <div class="illu bar top pc">
                            <span class="url">eval-mode.jp</span><span class="ib hl"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="12" rx="1.5"/><path d="M8 20h8"/><path d="M12 7v6"/><path d="M9.5 10.5L12 13l2.5-2.5"/></svg></span><span class="ib">☆</span>
                        </div>
                    </div>
                </li>
                <li class="step">
                    <span class="no">2</span>
                    <div class="s-body">
                        <p class="s-t">表示された確認で<b>「インストール」</b>をクリック</p>
                        <div class="illu confirm"><span class="cf-l">キャンセル</span><span class="cf-c">アプリをインストールしますか？</span><span class="cf-r hl">インストール</span></div>
                    </div>
                </li>
                <li class="step done">
                    <span class="no">✓</span>
                    <div class="s-body">
                        <p class="s-t">デスクトップ／スタートメニュー（Mac：Launchpad）から起動</p>
                        <p class="s-d">※ 現在のEval MODEはスマートフォン向け表示です（PC版は今後対応予定）。</p>
                    </div>
                </li>
            </ol>
        </div>

        <div class="sec-title"><h2>よくある質問</h2><span class="en">FAQ</span></div>
        <div class="faq">
            <details><summary>「ホーム画面に追加」が見つかりません</summary>
                <p>iPhoneはSafari（iOS 16.4以降はChromeも可）、AndroidはChromeでページを開いてください。LINEやXのアプリ内ブラウザでは追加できません。右上・右下の「ブラウザで開く」から開き直してください。</p></details>
            <details><summary>追加後、もう一度ログインが必要ですか？</summary>
                <p>iPhoneでは、ホーム画面のアイコンから開いたEval MODEはSafariとログイン状態が別管理のため、初回のみログインが必要です。メールアドレス認証コードまたはLINE等ですぐにログインできます。</p></details>
            <details><summary>容量や料金はかかりますか？</summary>
                <p>アプリストアからのインストールではないため、追加による料金は一切かからず、端末の容量もほとんど使いません。更新も自動で反映されます。</p></details>
            <details><summary>削除したい場合は？</summary>
                <p>ホーム画面のアイコンを長押しして「削除」（Androidは「アンインストール」）を選んでください。会員情報・ポイントは消えません。</p></details>
        </div>

        <a class="back-top" href="/">TOPへ戻る</a>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages/home_guide.js"></script>
{/block}
