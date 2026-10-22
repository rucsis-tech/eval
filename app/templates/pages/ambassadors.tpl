{* P21 アンバサダー *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/ambassadors.css">
{/block}

{block name="content"}
    <div class="pc-wrap w-narrow pc-amb">

        <div class="page-head">
            <h1>アンバサダー</h1>
            <span class="en">AMBASSADOR</span>
        </div>
        <p class="amb-lead">Eval MODEの確率を使いこなすアンバサダーたち。それぞれの視点での活用法や的中報告をXで発信しています。</p>

        <section class="amb-page">
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

                <div class="amb-list-h"><b>アンバサダー</b><span id="ambCount"></span></div>
                <div class="amb-filter" id="ambFilter"></div>
                <ul class="amb-list" id="ambGrid"></ul>
                <p class="amb-note">※ 各アンバサダーの投稿内容は個人の見解です。行をタップするとXのプロフィールが開きます。</p>
            </div>
        </section>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages/ambassadors.js"></script>
{/block}
