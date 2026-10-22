{* P10 初めての方ガイド *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages/guide.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-guide pc-grid">

    <!-- ページ見出し -->
    <div class="g-full">
    <div class="page-head">
    <h1>🔰 初めての方ガイド</h1>
    <span class="en">BEGINNER'S GUIDE</span>
    <p class="lead">Eval MODEは<b>説明書がいらない設計</b>を目指していますが、このページでは3分で全体像をつかめるように、数字の読み方と1日の使い方をまとめました。</p>
    </div>

    <!-- 目次 -->
    </div><!-- /g-full -->
    <div class="g-left">
    <div class="toc">
    <a href="#g1">Eval MODEとは</a>
    <a href="#g2">確率テーブルの見方</a>
    <a href="#g3">用語の読み方</a>
    <a href="#g4">1日の使い方</a>
    <a href="#g5">会員ステータス</a>
    <a href="#g6">ポイント</a>
    </div>

    <!-- 1. Eval MODEとは -->
    </div><!-- /g-left -->
    <div class="g-main">
    <div class="sec-title" id="g1"><span class="no">1</span><h2>Eval MODEとは</h2></div>
    <div class="idea">
    Eval MODEの土台は、<b>全出走馬の「好走確率」＝予想のマスターデータ</b>です。<br>
    そこにあなたのスタンス（血統派・ラップ派・パドック派…）を上乗せして、<b>自分だけの予想</b>を組み立ててください。ほかの予想法を否定しません。あらゆる予想の土台になることが、このサービスの価値です。
    </div>
    <p class="body-txt">中央・地方の<b>全レース・全出走馬</b>が対象です。確率は独自解析で算出し、<span class="g">毎日</span>その結果を的中実績ページで答え合わせできます。</p>

    <!-- 2. 確率テーブルの見方 -->
    <div class="sec-title" id="g2"><span class="no">2</span><h2>確率テーブル（TOP）の見方</h2></div>
    <p class="body-txt">TOPページの出馬表には、馬ごとに4つの情報が並びます。</p>
    <div class="demo-wrap">
    <div class="demo-tbl">
    <div class="dt-head">
    <span class="hc" style="flex:0 0 22px;"><i>1</i>馬番</span>
    <span class="hc" style="flex:0 0 20px;"><i>2</i>印</span>
    <span class="hc grow">馬名</span>
    <span class="hc num"><i>3</i>勝利確率</span>
    <span class="hc num"><i>4</i>単勝期待値</span>
    </div>
    <div class="dt-row">
    <span class="umaban" style="background:#e8c31e;color:#111;">7</span>
    <span class="mk">◎</span>
    <span class="nm">エヴァルライト</span>
    <span class="pv">31.2%</span>
    <span class="ev myomi">128%<i class="mchip">妙味</i></span>
    </div>
    <div class="dt-row">
    <span class="umaban" style="background:#d33;color:#fff;">3</span>
    <span class="mk"></span>
    <span class="nm">グリーンシグナル</span>
    <span class="pv">27.8%</span>
    <span class="ev">96%</span>
    </div>
    <div class="dt-row">
    <span class="umaban" style="background:#1a5dd6;color:#fff;">5</span>
    <span class="mk">▲</span>
    <span class="nm">マスターオッズ</span>
    <span class="pv">19.6%</span>
    <span class="ev myomi">121%<i class="mchip">妙味</i></span>
    </div>
    </div>
    </div>
    <ul class="an-list">
    <li><span class="an2">1</span><span><b>馬番</b>：枠順の色は競馬の規定色です</span></li>
    <li><span class="an2">2</span><span><b>予想印</b>：タップで◎◯▲…を自由につけられます（長押しで一覧から選択）。結果画面にも反映されるので答え合わせに便利</span></li>
    <li><span class="an2">3</span><span><b>勝利確率／複勝確率</b>：Evalの独自解析値。ボタンで切替できます</span></li>
    <li><span class="an2">4</span><span><b>単勝期待値</b>：確率×現在オッズ。<span class="r">100%を超えると「妙味」＝赤いチップ</span>が付きます</span></li>
    </ul>
    <p class="body-txt">列の見出しをタップすると<b>並び替え</b>できます（確率順・期待値順・印順・馬番順）。</p>

    <!-- 3. 用語の読み方 -->
    <div class="sec-title" id="g3"><span class="no">3</span><h2>数字と用語の読み方</h2></div>
    <div class="term-list">
    <div class="term">
    <p class="t-name">好走確率</p>
    <p class="t-desc">その馬が<b>勝つ確率（勝利確率）・3着以内に入る確率（複勝確率）</b>のEval独自解析値。前日夜に公開され、当日の<b>発走約30分前にオッズを取り込んで最終補正</b>され確定します（公開時から値が変わることがあります）。</p>
    </div>
    <div class="term">
    <p class="t-name">リアルタイム期待値（LIVE期待値）<span class="badge live">LIVE</span></p>
    <p class="t-desc">好走確率×<b>現在のオッズ</b>で計算する「買い得度」。オッズは発売締切まで動くため、期待値も<b>リアルタイムに変わります</b>。100%超なら理論上プラス圏。</p>
    </div>
    <div class="term">
    <p class="t-name">妙味 <span class="badge myomi">妙味</span></p>
    <p class="t-desc">期待値が<b>100%を超えた馬</b>に付く赤いチップ。「実力よりオッズが甘い＝買い得」のサインです。赤い表示は本サービスでは<b>「今見るべき印」</b>にだけ使っています。</p>
    </div>
    <div class="term">
    <p class="t-name">最終 <span class="badge final">最終</span></p>
    <p class="t-desc">発走約30分前の最終補正で<b>確定した確率</b>に付くバッジ（横に確定時刻を表示）。バッジのない時間帯は前日公開値のままでも十分に使えます（最終補正でさらに精度が上がります）。</p>
    </div>
    </div>

    <!-- 4. 1日の使い方 -->
    <div class="sec-title" id="g4"><span class="no">4</span><h2>1日の使い方（モデルケース）</h2></div>
    <ul class="tl">
    <li>
    <p class="tl-time">前日 20:00〜</p>
    <p class="tl-t">前日確率をチェック</p>
    <p class="tl-d">翌日全レースの好走確率が公開。<b>気になるレースに予想印</b>をつけておくと当日がラクです。</p>
    </li>
    <li>
    <p class="tl-time">当日 朝</p>
    <p class="tl-t">朝の新聞がわりに</p>
    <p class="tl-d">好走確率ランキングで<b>本日の上位馬</b>を横断チェック。オッズが付き始めたら期待値・妙味も見えてきます。</p>
    </li>
    <li>
    <p class="tl-time">レース直前</p>
    <p class="tl-t">最終補正＋妙味を確認</p>
    <p class="tl-d">「最終」バッジ付きの確率と、直前オッズでの<b>妙味チップ</b>を最終確認。的中確率シミュレーションで買い目の的中確率も計算できます。</p>
    </li>
    <li>
    <p class="tl-time">レース後</p>
    <p class="tl-t">答え合わせ</p>
    <p class="tl-d"><b>的中実績ページ</b>と<b>確定ランキング</b>で、Evalの確率が実際どうだったかを毎日確認できます。この繰り返しが確率への信頼と、自分の予想の精度を育てます。</p>
    </li>
    </ul>

    <!-- 5. 会員ステータス -->
    <div class="sec-title" id="g5"><span class="no">5</span><h2>会員ステータスと見られる範囲</h2></div>
    <div class="st-wrap">
    <table class="st">
    <tr><th style="text-align:left;">機能</th><th>非会員</th><th>無料会員</th><th class="hl">サブスク</th></tr>
    <tr><td class="f">好走確率・期待値</td><td class="no">−</td><td class="part">一部レース</td><td class="ok">プラン対象の全レース</td></tr>
    <tr><td class="f">好走確率ランキング</td><td class="part">上位3頭</td><td class="part">上位10頭</td><td class="ok">全頭</td></tr>
    <tr><td class="f">的中実績・トピックス</td><td class="ok">✓</td><td class="ok">✓</td><td class="ok">✓</td></tr>
    <tr><td class="f">シミュレーション</td><td class="no">−</td><td class="part">一部</td><td class="ok">✓</td></tr>
    </table>
    </div>
    <p class="body-txt">サブスクは<b>中央競馬限定／地方競馬限定／全機能使い放題</b>の3プラン。いつでも解約できます。まずは無料登録で使用感を試すのがおすすめです。</p>

    <!-- 6. ポイント -->
    <div class="sec-title" id="g6"><span class="no">6</span><h2>ポイント（pt）とは</h2></div>
    <p class="body-txt"><b>1pt＝1円</b>で使えるサービス内通貨です。おすすめ攻略ツールの<b>単品商品の購入</b>（推奨馬リスト・前走不利情報など）に使います。<br>
    入手方法は<b>①ポイント購入ページで購入</b>（まとめ買いボーナスあり）、<b>②アンケート回答などの特典</b>の2つ。有効期限は購入から180日です。<br>
    ※一部の商品は、サブスク会員でもポイントが必要です（サブスク＝全商品見放題ではありません）。</p>

    <!-- CTA -->
    <div class="cta-wrap">
    <a class="cta-main" href="/auth/entry">無料ではじめる（30秒） ›</a>
    <a class="cta-sub" href="/settle/settle_subsc">プランをくわしく見る ›</a>
    </div>
    <p class="note">※ 本ページの数値・画面はサンプルです。確率・期待値は的中や利益を保証するものではありません。馬券の購入は20歳以上・ご自身の判断でお願いします。</p>

    </div><!-- /g-main -->

    </div><!-- /pc-wrap -->
{/block}
