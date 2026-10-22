{* P8 マイページ *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/mypage/mypage.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-mypage w-narrow">

    <!-- ページ見出し -->
    <div class="page-head">
    <h1>マイページ</h1>
    <span class="en">MY PAGE</span>
    </div>

    <!-- モック確認用（実装対象外） -->
    <div class="mock-ctrl">
    <span class="m-lbl">モック確認用：</span>
    <a href="#" data-st="guest">非会員</a>
    <a href="#" data-st="free" class="on">無料会員</a>
    <a href="#" data-st="sub_full">フル会員</a>
    <a href="#" data-st="sub_chuo">中央限定会員</a>
    <a href="#" data-st="amb">アンバサダー</a>
    </div>

    <!-- 会員ビュー -->
    <div id="memberView"></div>

    <!-- 非会員ビュー -->
    <div class="guest-view" id="guestView" hidden>
    <div class="g-ico">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <p class="g-t">マイページの利用には<br>ログインが必要です</p>
    <p class="g-d">無料登録すると、全出走馬の好走確率とリアルタイム期待値（LIVE期待値）が見られます。登録は30秒で完了します。</p>
    <a class="g-reg" href="/auth/entry">無料ではじめる ›</a>
    <a class="g-login" href="/auth/login">ログイン</a>
    </div>

    <!-- アンバサダー：月別報酬の詳細シート -->
    <div class="sheet-bg" id="paySheet" hidden>
    <div class="sheet">
    <div class="sh-h"><span class="sh-t" id="psTitle"></span><span class="sh-x" id="psClose">×</span></div>
    <table class="kv" id="psBody"></table>
    <p class="sh-n">※ 報酬はあなたの紹介コードを利用して課金したユーザーの、当月のサブスク決済（新規＝初回・継続＝2回目以降）とポイント購入が対象です。返金があった決済は対象外。締日・支払日・最低振込額は契約内容によります（表示は仮）。</p>
    </div>
    </div>

{/block}

{block name="scripts"}
    <script src="/js/mypage/mypage.js"></script>
{/block}
