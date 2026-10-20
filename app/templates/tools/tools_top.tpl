{* P2 おすすめ攻略ツール *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_tools.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-tools pc-grid">

        <div class="g-full">
            <div class="page-head">
                <h1>おすすめ攻略ツール</h1>
                <span class="en">STRATEGY TOOLS</span>
            </div>
        </div>

        <div class="g-left">
            <div class="concept">
                <p class="lead">Eval MODEの土台は<b>全出走馬の好走確率</b>。そこにあなたのスタンスに合わせたファクターを上乗せして、自分だけの予想を組み立てるためのツール群です。</p>
                <div class="stack" id="stack"></div>
                <p class="note">どれを上乗せするかは自由。普段お使いの予想法・他サービスと組み合わせてもEvalの確率は土台として機能します。</p>
            </div>
        </div>

        <div class="g-main">
            <div id="genreSections"></div>

            <section>
                <div class="sec-title"><h2>アンバサダー</h2><span class="en">AMBASSADOR</span></div>
                <div class="amb-intro">
                    <div class="amb-card">
                        <span class="amb-av"><img src="" alt="" id="ambOfficialIcon"></span>
                        <div class="body">
                            <div class="head">
                                <span class="who">Eval</span>
                                <span class="xid">@master_eval</span>
                                <span class="badge-a">公式アンバサダー</span>
                            </div>
                            <p class="txt">確率で競馬を捉えるEvalの公式アカウント。パドック気配、穴馬の視点、的中報告まで、レース当日のリアルタイムな思考をXで発信中。</p>
                            <a class="follow" href="https://x.com/master_eval" target="_blank" rel="noopener">𝕏 フォローして最新ポストを見る ↗</a>
                        </div>
                    </div>
                    <a class="amb-entry" href="/pages/ambassadors">
                        <span class="stackav" id="ambStack"></span>
                        <span class="t"><b>アンバサダー一覧を見る</b><small id="ambCount"></small></span>
                        <span class="go">›</span>
                    </a>
                </div>
            </section>

            <div class="coexist">
                <p>Eval MODEは、他の予想法や予想サービスを置き換えるものではありません。<b>あらゆる予想の「土台」となるマスターデータ</b>として、お使いの手法と組み合わせてご活用ください。</p>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_tools_top.js"></script>
{/block}
