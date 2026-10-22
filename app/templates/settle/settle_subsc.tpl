{* P13 サブスク申込 *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/settle/settle_subsc.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-subscribe">

        <div class="page-head">
            <h1>プランを選ぶ</h1>
            <span class="en">SUBSCRIPTION</span>
            <p class="lead">サブスクで<b>全出走馬の好走確率とリアルタイム期待値（LIVE期待値）</b>を開放。いつでも解約できます。</p>
        </div>

        <div class="mock-ctrl">
            <span class="m-lbl">モック確認用：</span>
            <a href="#" data-st="guest">非会員</a>
            <a href="#" data-st="free" class="on">無料会員</a>
            <a href="#" data-st="sub_full">フル契約中</a>
            <a href="#" id="mcCamp" class="on">初月無料キャンペーン</a>
            <span class="m-lbl" style="margin-left:4px;">モーメント：</span>
            <a href="#" data-mo="live" class="on">開催中</a>
            <a href="#" data-mo="eve">開催前夜</a>
        </div>

        <a class="guest-bar" id="guestBar" href="/auth/entry" hidden>
            <span class="g-txt">プランの申込には無料登録が必要です<br><span style="font-size:9px;color:#aebe6e;">登録は30秒・登録後この画面に戻ります</span></span>
            <span class="g-btn">無料登録</span>
        </a>
        <div class="cur-bar" id="curBar" hidden>現在<b>全機能使い放題プラン</b>を契約中です（次回更新 9/8）。プランの変更は次回更新日から適用されます。</div>

        <div class="moment" id="momentBand"></div>

        <div class="invite-box" id="inviteBox">
            <p class="iv-t" id="ivTitle">🎟 招待コードをお持ちの方</p>
            <p class="iv-d">X（旧Twitter）やアンバサダーから受け取ったコードで特典が適用されます。</p>
            <div class="iv-row">
                <input id="ivInput" placeholder="例：EVALW" maxlength="12">
                <button type="button" class="iv-apply" id="ivApply">適用</button>
            </div>
            <p class="iv-msg" id="ivMsg" hidden></p>
        </div>

        <div class="plan-list" id="planList"></div>

        <div class="sec-title"><h2>プラン機能比較</h2><span class="en">COMPARE</span></div>
        <div class="cmp-wrap">
            <table class="cmp">
                <tr><th style="text-align:left;">機能</th><th>無料</th><th>中央限定</th><th>地方限定</th><th class="hl">フル</th></tr>
                <tr><td class="f">好走確率データ</td><td class="part">一部</td><td>中央のみ</td><td>地方のみ</td><td class="ok">✓</td></tr>
                <tr><td class="f">リアルタイム期待値（LIVE期待値）</td><td class="part">一部</td><td>中央のみ</td><td>地方のみ</td><td class="ok">✓</td></tr>
                <tr><td class="f">本日の好走確率ランキング</td><td class="part">10頭</td><td>中央全頭</td><td>地方全頭</td><td class="ok">全頭</td></tr>
                <tr><td class="f">的中確率シミュレーション</td><td class="part">一部</td><td>中央のみ</td><td>地方のみ</td><td class="ok">✓</td></tr>
                <tr><td class="f">WIN5的中確率シミュレーション</td><td class="no">−</td><td class="ok">✓</td><td class="no">−</td><td class="ok">✓</td></tr>
                <tr><td class="f">サブスク対象攻略ツール</td><td class="no">−</td><td class="part">対象分</td><td class="part">対象分</td><td class="ok">見放題</td></tr>
            </table>
        </div>

        <div class="sec-title"><h2>無料とフルの見え方の違い</h2><span class="en">TRY SLIDER</span></div>
        <div class="ba-wrap">
            <div class="ba-box" id="baBox" style="--pos:55%;">
                <div class="ba-pane">
                    <div class="ba-head-mini"><span class="h1c">出走馬</span><span class="h2c">勝利確率</span><span class="h2c">単勝期待値</span></div>
                    <div class="ba-row">
                        <span class="b-um" style="background:#e8c31e;color:#111;">7</span><span class="b-name">エヴァルライト</span>
                        <span class="b-p">31.2%</span><span class="b-lock">🔒 登録で表示</span>
                    </div>
                    <div class="ba-row blur34">
                        <span class="b-um" style="background:#d33;color:#fff;">3</span><span class="b-name">グリーンシグナル</span>
                        <span class="b-p">27.8%</span><span class="b-lock">🔒</span>
                    </div>
                    <div class="ba-row blur34">
                        <span class="b-um" style="background:#1a5dd6;color:#fff;">5</span><span class="b-name">マスターオッズ</span>
                        <span class="b-p">19.6%</span><span class="b-lock">🔒</span>
                    </div>
                </div>
                <div class="ba-pane full-pane">
                    <div class="ba-head-mini"><span class="h1c">出走馬</span><span class="h2c">勝利確率</span><span class="h2c">単勝期待値</span></div>
                    <div class="ba-row">
                        <span class="b-um" style="background:#e8c31e;color:#111;">7</span><span class="b-name">エヴァルライト</span>
                        <span class="b-p">31.2%</span><span class="b-ev hot">128%</span>
                    </div>
                    <div class="ba-row">
                        <span class="b-um" style="background:#d33;color:#fff;">3</span><span class="b-name">グリーンシグナル</span>
                        <span class="b-p">27.8%</span><span class="b-ev">112%</span>
                    </div>
                    <div class="ba-row">
                        <span class="b-um" style="background:#1a5dd6;color:#fff;">5</span><span class="b-name">マスターオッズ</span>
                        <span class="b-p">19.6%</span><span class="b-ev hot">121%</span>
                    </div>
                </div>
                <span class="ba-label l-full">フル会員の見え方</span>
                <span class="ba-label l-free">無料会員の見え方</span>
                <div class="ba-divider"></div>
                <input type="range" class="ba-slider" id="baSlider" min="8" max="92" value="55">
            </div>
            <p class="ba-note">スライダーを左右に動かすと、無料会員とフル会員の見え方を比較できます。フルでは全レース・全頭の確率とリアルタイム期待値（LIVE期待値）・妙味（赤表示）が開放されます。</p>
        </div>

        <div class="sec-title"><h2>よくある質問</h2><span class="en">FAQ</span></div>
        <div class="faq-list">
            <div class="faq open">
                <a href="#" class="fq-q"><span class="q-mk">Q</span>本当にいつでも解約できますか？<span class="q-chev">›</span></a>
                <div class="fq-a"><b>はい、いつでも解約できます。</b>マイページのプラン契約カードからいつでも手続きでき、解約後も期間満了までは全機能を利用できます。引き止め画面で解約しづらくするような設計はしていません。</div>
            </div>
            <div class="faq">
                <a href="#" class="fq-q"><span class="q-mk">Q</span>無料会員と何が違いますか？<span class="q-chev">›</span></a>
                <div class="fq-a">無料会員は一部レースの好走確率のみ閲覧できます。フルプランでは<b>全レース・全頭の好走確率とリアルタイム期待値（LIVE期待値）</b>、好走確率ランキングの全頭表示、的中確率シミュレーション・WIN5的中確率シミュレーションが開放されます。</div>
            </div>
            <div class="faq">
                <a href="#" class="fq-q"><span class="q-mk">Q</span>料金はいつ発生しますか？<span class="q-chev">›</span></a>
                <div class="fq-a">初月無料キャンペーン適用時は<b>無料期間中の請求は0円</b>で、無料期間終了の翌日に初回課金されます。以降は毎月同日の請求です。無料期間中に解約すれば料金はかかりません。</div>
            </div>
            <div class="faq">
                <a href="#" class="fq-q"><span class="q-mk">Q</span>招待コードはあとから使えますか？<span class="q-chev">›</span></a>
                <div class="fq-a">招待コードの特典は<b>この申込画面で入力したときのみ</b>適用されます。X（旧Twitter）やアンバサダーからコードを受け取っている方は、申込前に上の入力欄でご適用ください。</div>
            </div>
        </div>

        <p class="note">※ 価格はすべて<b>仮</b>です。サブスクは月額制・<b>いつでも解約可能</b>（解約後も期間満了まで利用できます）。
        一部の攻略ツール商品はサブスクとは別にポイントが必要です。
        詳細は<a href="/pages/rules">利用規約</a>・<a href="/pages/commercial_law">特定商取引法に基づく表示</a>をご確認ください。</p>

        <div class="sticky-cta" id="stickyCta">
            <button type="button" class="sc-btn" id="scBtn"></button>
            <p class="sc-sub" id="scSub"></p>
        </div>

        {include file="partials/sheet_subscribe.tpl"}
    </div>
{/block}

{block name="scripts"}
    <script src="/js/settle/settle_subsc.js"></script>
{/block}
