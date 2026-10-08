<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Eval MODE｜{$title}</title>
{literal}
<style>
  :root{
    --bg:#0b0c10;          /* サイト背景（黒） */
    --bg-card:#15171d;     /* カード・セクション面 */
    --bg-sunken:#101218;   /* 一段沈んだ面 */
    --line:#2a2e38;        /* 罫線 */
    --text:#f2f4f8;        /* 基本文字（白抜き） */
    --text-sub:#9aa3b2;    /* 補助文字 */
    --navy:#0d2b52;        /* ブランド紺（見出しバー・バッジ） */
    --navy-bright:#1a4d9e;
    --red:#00c98d;         /* 基調アクセント：データグリーン（OS・構造の色） */
    --sig:#e6394f;         /* シグナル：赤（妙味・高確率・最終・発走直近など「今見るべき」印） */
    --sig-deep:#8f1024;
    --sig-soft:#ff6b7d;
    --red-deep:#046b4c;
    --gold:#d4af37;        /* Sランク */
    --silver:#98a4b8;      /* Aランク */
    --bronze:#c07a3e;      /* Bランク */
    --blue-link:#6ea8ff;   /* リンク */
    --free:#00c98d;
    --subsc:#2c5aa8;
    --pt:#f09a1e;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  html{ -webkit-text-size-adjust:100%; }
  /* hidden属性をdisplay指定付きクラスより優先（旧：.cta-subscのdisplay:flexに負け、非ログイン時もpt枠が出ていた不具合の修正） */
  [hidden]{display:none !important;}
  body{
    font-family:"Hiragino Kaku Gothic ProN","Noto Sans JP","Yu Gothic",Meiryo,sans-serif;
    color:var(--text);
    background:#000;
    font-size:14px;
    line-height:1.5;
  }
  .sp{
    max-width:414px;margin:0 auto;background:var(--bg);
    min-height:100vh;box-shadow:0 0 24px rgba(0,0,0,.6);
    overflow:hidden;
    padding-bottom:96px; /* フローティングナビ＋余白分 */
  }

  /* ===== Header ===== */
  header{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 12px;border-bottom:1px solid var(--line);
    position:sticky;top:0;background:rgba(11,12,16,.96);z-index:50;
    backdrop-filter:blur(6px);
  }
  .logo{display:flex;align-items:baseline;gap:6px;text-decoration:none;}
  .logo .mark{
    font-weight:900;font-size:20px;color:#fff;letter-spacing:.02em;
  }
  .logo .mark span{color:var(--red);}
  .h-icons{display:flex;align-items:center;gap:10px;}
  .h-point{
    display:flex;align-items:center;gap:4px;
    background:var(--bg-sunken);border:1px solid var(--line);
    border-radius:14px;padding:3px 10px 3px 4px;text-decoration:none;
  }
  .h-point .p-coin{
    width:18px;height:18px;border-radius:50%;
    background:linear-gradient(135deg,#f5c518,#e8850c);
    color:#1a1305;font-size:10px;font-weight:900;
    display:grid;place-items:center;
  }
  .h-point .p-num{font-size:12px;font-weight:800;color:#fff;font-variant-numeric:tabular-nums;}
  .h-point .p-unit{font-size:9px;color:var(--text-sub);font-weight:700;}
  .icon-mypage{
    display:flex;flex-direction:column;align-items:center;gap:1px;
    text-decoration:none;color:var(--text-sub);
  }
  .icon-mypage .icon-guest{
    width:22px;height:22px;border-radius:50%;background:var(--bg-card);
    border:1px solid var(--line);display:grid;place-items:center;color:var(--text-sub);
  }
  .icon-mypage .icon-guest svg{width:12px;height:12px;}
  .icon-mypage .mp-lbl{font-size:7.5px;font-weight:700;letter-spacing:.02em;}
  .icon-menu{width:22px;display:grid;gap:4px;position:relative;cursor:pointer;}
  .icon-menu i{display:block;height:2px;background:#fff;border-radius:2px;}
  .icon-menu::after{content:"";position:absolute;inset:-10px;} /* 実効タップ領域40px確保 */

  /* ===== ハンバーガードロワーメニュー（右スライドイン） ===== */
  .drawer-overlay{
    position:fixed;inset:0;z-index:260;background:rgba(0,0,0,.55);
    display:flex;justify-content:flex-end;
  }
  .drawer-overlay.hidden{display:none;}
  .drawer{
    width:82%;max-width:320px;height:100%;
    background:var(--bg-card);border-left:1px solid var(--line);
    overflow-y:auto;padding-bottom:28px;
  }
  .d-head{
    position:sticky;top:0;z-index:1;
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 14px;background:var(--bg-card);border-bottom:1px solid var(--line);
  }
  .d-title{font-size:14px;font-weight:900;color:#fff;letter-spacing:.04em;}
  .d-close{
    width:30px;height:30px;border-radius:50%;
    background:var(--bg-sunken);border:1px solid var(--line);color:#fff;font-size:15px;
    display:grid;place-items:center;-webkit-tap-highlight-color:transparent;
  }
  .d-sec{padding:8px 0 6px;border-bottom:1px solid var(--line);}
  .d-sec-t{font-size:9.5px;font-weight:800;color:var(--text-sub);letter-spacing:.1em;padding:4px 14px 2px;}
  .d-item{
    display:flex;align-items:center;gap:7px;
    padding:11px 14px;font-size:12.5px;font-weight:700;color:var(--text);text-decoration:none;
  }
  .d-item .chev{margin-left:auto;color:#4a5468;font-size:12px;}
  .d-item .em{font-size:9px;color:var(--pt);font-weight:800;}
  .d-item.sub{padding:9px 14px 9px 24px;font-size:12px;color:var(--text-sub);font-weight:600;}
  .d-item.d-logout{color:var(--text-sub);font-weight:600;}
  .d-item.d-x{padding:13px 14px;}
  .d-btn{
    display:block;margin:6px 14px 8px;text-align:center;
    font-size:12.5px;font-weight:900;padding:12px 0;border-radius:22px;text-decoration:none;
  }
  .d-btn.primary{background:var(--red);color:#fff;box-shadow:0 2px 0 var(--red-deep);}
  .d-btn.ghost{border:1px solid #4a5468;color:var(--text);}
  .d-fold{border-bottom:1px solid var(--line);}
  .d-fold summary{
    list-style:none;display:flex;align-items:center;
    padding:12px 14px;font-size:12.5px;font-weight:700;color:var(--text);cursor:pointer;
    -webkit-tap-highlight-color:transparent;
  }
  .d-fold summary::-webkit-details-marker{display:none;}
  .d-fold summary::after{content:"＋";margin-left:auto;color:var(--text-sub);font-size:12px;}
  .d-fold[open] summary::after{content:"−";}

  /* ===== Global nav（フローティング・グラス型＋中央FAB＋アクティブピル） ===== */
  .gnav{
    position:fixed;bottom:10px;left:50%;transform:translateX(-50%);
    width:calc(100% - 20px);max-width:394px;z-index:100;
    display:flex;align-items:flex-end;
    background:rgba(17,20,32,.82);
    backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,.08);
    border-radius:22px;
    box-shadow:0 8px 30px rgba(0,0,0,.55);
    padding:0 4px;
    padding-bottom:max(0px, env(safe-area-inset-bottom) - 6px);
  }
  .gnav a{
    flex:1 1 0;min-width:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;gap:3px;
    color:var(--text-sub);font-size:8.5px;font-weight:700;text-decoration:none;
    padding:9px 2px 8px;letter-spacing:0;line-height:1.25;text-align:center;
    -webkit-tap-highlight-color:transparent;
  }
  .gnav a .lbl{
    display:flex;align-items:center;justify-content:center;
    height:22px; /* 2行分の高さで固定し、1行ラベルも上下センターに */
  }
  .gnav a .ico{
    width:44px;height:24px;flex:0 0 auto;
    display:grid;place-items:center;
    border-radius:12px;
    transition:background .25s;
  }
  .gnav a .ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
  /* アクティブピル */
  .gnav a.active{color:#fff;}
  .gnav a.active .ico{background:rgba(0,201,141,.2);}
  .gnav a.active .ico svg{stroke:#5fe8bd;}
  /* 中央FAB：的中確率シミュレーション */
  .gnav .fab{
    flex:1 1 0;min-width:0;position:relative;
    display:flex;flex-direction:column;align-items:center;gap:3px;
    text-decoration:none;padding:0 0 8px;
  }
  .gnav .fab .fab-btn{
    width:54px;height:54px;border-radius:50%;
    margin-top:-20px;
    display:grid;place-items:center;
    background:linear-gradient(150deg,#2fe0a8,#046b4c);
    border:3px solid #0b0c10;
    box-shadow:0 4px 18px rgba(0,201,141,.5), 0 0 0 1px rgba(255,255,255,.06);
  }
  .gnav .fab .fab-btn svg{width:26px;height:26px;stroke:#fff;fill:none;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round;}
  .gnav .fab .fab-lbl{
    font-size:8.5px;font-weight:800;color:#5fe8bd;letter-spacing:.02em;line-height:1.2;
  }
  /* ===== キャンペーンモーダル（タイアップ告知用・汎用） ===== */
  .modal-overlay{
    position:fixed;inset:0;z-index:200;
    background:rgba(0,0,0,.72);
    display:flex;align-items:center;justify-content:center;
    padding:24px;
  }
  .modal-overlay.hidden{display:none;}
  .modal{
    position:relative;width:100%;max-width:340px;
    border-radius:14px;overflow:hidden;
    box-shadow:0 12px 40px rgba(0,0,0,.6);
  }
  .modal .m-visual{
    aspect-ratio:4/3;display:grid;place-items:center;text-align:center;
    background:linear-gradient(135deg,#0d2b52 0%,#163d73 55%,#d7263d 130%);
    color:#fff;padding:20px;
  }
  .modal .m-visual .m-lbl{
    display:inline-block;background:#c8f31d;color:#000;
    font-size:10px;font-weight:900;padding:3px 10px;border-radius:12px;letter-spacing:.08em;
    margin-bottom:10px;
  }
  .modal .m-visual h2{font-size:18px;font-weight:900;line-height:1.5;}
  .modal .m-visual p{font-size:11px;margin-top:8px;opacity:.9;}
  .modal .m-foot{background:var(--bg-card);padding:14px;text-align:center;}
  .modal .m-btn{
    display:block;background:var(--red);color:#fff;text-decoration:none;
    font-weight:900;font-size:14px;padding:13px 0;border-radius:26px;
    box-shadow:0 3px 0 var(--red-deep);
  }
  .modal .m-skip{
    display:inline-block;margin-top:10px;font-size:11px;color:var(--text-sub);text-decoration:none;
  }
  .modal .m-visual.v-tie{background:linear-gradient(135deg,#0d2b52 0%,#163d73 55%,#d7263d 130%);}
  .modal .m-visual.v-sub{background:linear-gradient(135deg,#0a231c,#046b4c);}
  .modal .m-visual.v-pt{background:linear-gradient(135deg,#5a3a06,#b8912a);}
  .modal .m-visual.v-sys{background:linear-gradient(135deg,#3a1218,#8f1024);}
  .modal .m-visual.v-reg{background:linear-gradient(135deg,#1d2606,#15171d 70%);}
  /* 複数枚の連続表示：何枚目かを示すカウンター（2026-09-30追加） */
  .modal .m-count{
    position:absolute;top:10px;left:10px;z-index:1;
    font-size:9.5px;font-weight:900;color:#fff;letter-spacing:.04em;
    background:rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.25);border-radius:10px;padding:2px 9px;
  }
  .modal .m-count[hidden]{display:none;}
  .modal .m-dots{display:flex;justify-content:center;gap:5px;margin-top:10px;}
  .modal .m-dots i{width:6px;height:6px;border-radius:50%;background:#3a4150;}
  .modal .m-dots i.on{background:var(--red);}
  .modal .m-close{
    position:absolute;top:8px;right:8px;
    width:30px;height:30px;border-radius:50%;
    background:rgba(0,0,0,.55);color:#fff;font-size:16px;
    display:grid;place-items:center;border:1px solid rgba(255,255,255,.3);
  }

  /* ===== 速報ティッカー（1行ローテーション型） ===== */
  .flash{
    display:flex;align-items:center;gap:8px;
    height:44px;padding:0 12px;
    border-bottom:1px solid var(--line);background:#0d0f14; /* お試し②：一段沈めたトーン */
  }
  .flash .lbl{
    flex:0 0 auto;background:transparent;border:1px solid var(--red);color:var(--red);
    font-size:10px;font-weight:800;
    padding:1px 8px;border-radius:2px;letter-spacing:.1em;
  }
  .flash .ticker{
    flex:1;min-width:0;position:relative;height:100%;
  }
  .flash .ticker a{
    position:absolute;inset:0;display:flex;align-items:center;
    font-size:12px;color:#7d93b8;text-decoration:none; /* お試し②：リンク色を減光 */
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
    opacity:0;transition:opacity .4s;pointer-events:none;
  }
  .flash .ticker a.show{opacity:1;pointer-events:auto;}
  .flash .ticker a b{color:var(--red);}
  .flash .more{flex:0 0 auto;font-size:10px;color:var(--text-sub);text-decoration:none;}

  /* ===== Section common ===== */
  section{margin-top:10px;}
  .sec-title{
    display:flex;align-items:center;gap:8px;
    padding:10px 12px;background:var(--bg-card);
    border-top:3px solid var(--red);border-bottom:1px solid var(--line);
  }
  .sec-title h2{font-size:15px;font-weight:800;color:#fff;letter-spacing:.03em;}
  .sec-title .en{font-size:9px;color:var(--text-sub);letter-spacing:.16em;font-weight:600;}
  .sec-title .right{font-size:11px;color:var(--blue-link);text-decoration:none;}
  .sec-title .guide{
    margin-left:auto;
    display:flex;align-items:center;gap:3px;
    border:1px solid #7ac943;color:#a4e36a;
    font-size:10px;font-weight:800;
    padding:2px 9px;border-radius:12px;text-decoration:none;
  }
  .sec-title .guide .g-mark{font-size:10px;line-height:1;}

  /* ===== Probability (hero) ===== */
  .prob{background:var(--bg-card);padding-bottom:12px;}
  .tabs{display:flex;overflow-x:auto;-ms-overflow-style:none;scrollbar-width:none;}
  .tabs::-webkit-scrollbar{display:none;}
  .tabs.date{border-bottom:1px solid var(--line);}
  .tabs.date a{
    flex:1 0 33.3%;text-align:center;font-size:11px;font-weight:700;color:var(--text-sub);
    padding:12px 0;text-decoration:none;border-right:1px solid var(--line);
  }
  .tabs.date a.on{color:var(--red);border-bottom:2px solid var(--red);}
  .tabs.venue{padding:6px 10px;gap:8px;}
  .tabs.venue a{
    flex:0 0 auto;font-size:11.5px;font-weight:700;color:var(--text);
    border:1px solid #4a5468;border-radius:14px;padding:5px 14px;text-decoration:none;background:transparent;
    position:relative;
  }
  .tabs.venue a::after{content:"";position:absolute;inset:-6px -3px;} /* 見えないタップ領域拡張 */
  .tabs.venue a.on{background:var(--red);border-color:var(--red);color:#fff;}
  .tabs.venue a.local{border-color:#3a4150;color:var(--text-sub);}
  .tabs.race{
    display:grid;grid-template-columns:repeat(6,1fr);gap:4px;
    padding:0 10px 6px;overflow:visible;
  }
  .tabs.race a{
    text-align:center;font-size:12px;font-weight:700;
    border:1px solid var(--line);border-radius:4px;color:var(--text);
    padding:9px 0;text-decoration:none;background:var(--bg-sunken);
  }
  .tabs.race a.on{background:var(--red);border-color:var(--red);color:#fff;}
  /* 発走済み：グレーアウト（結果閲覧は可能） */
  .tabs.race a.done{
    background:transparent;border-color:#232732;color:#4d5666;
  }
  .view-switch{display:flex;align-items:stretch;border-top:1px solid var(--line);border-bottom:1px solid var(--line);}
  .view-switch a{
    flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
    min-height:46px;padding:0;text-align:center;
    font-size:11px;font-weight:700;
    color:var(--text-sub);text-decoration:none;border-right:1px solid var(--line);background:var(--bg-sunken);
  }
  .view-switch a:last-child{border-right:none;}
  .view-switch a.on{background:var(--bg-card);color:var(--red);border-bottom:2px solid var(--red);}
  /* 結果・払戻し：結果取得前は無効 */
  /* 取得前：押せないことを明示（沈み表示＋理由注記） */
  .view-switch a.disabled{color:#3d4452;background:#0c0e13;} /* 実装時は pointer-events:none を付与（モックでは確認用にタップ可） */
  .view-switch a .t-note{display:block;font-size:8px;font-weight:600;color:#3d4452;line-height:1.2;margin-top:1px;}
  .view-switch a .t-note[hidden]{display:none;} /* 結果取得後は注記を完全に消し、1行を縦中央に */
  .view-switch a.disabled .t-note{color:#333a47;}
  /* 結果取得後：色付きテキストで有効状態を明示 */
  .view-switch a.ready{color:#fff;}
  /* 取得後：有効化フラッシュ（1回のみ）。NEWバッジは不使用（2026-07-23決定：全レース分出ると煩雑） */
  .view-switch a{position:relative;}
  @keyframes tabFlash{
    0%{background:rgba(230,57,79,.45);}
    100%{background:var(--bg-sunken);}
  }
  .view-switch a.flash{animation:tabFlash 1.2s ease-out 1;}
  /* 勝利確率／複勝確率：ボタン型（出馬表内のみ機能） */
  .mode-btns{
    flex:0 0 auto;display:flex;align-items:center;gap:6px;
    padding:0 10px;background:var(--bg-sunken);
  }
  .btn-mode{
    flex:0 0 auto !important;flex-direction:row !important;min-height:0 !important;
    font-size:10.5px;font-weight:800;color:var(--text-sub);
    border:1px solid #4a5468 !important;border-radius:15px;
    padding:7px 13px !important;text-decoration:none;background:transparent !important;
  }
  .btn-mode.on{
    background:var(--red) !important;border-color:var(--red) !important;color:#fff !important;
  }
  /* 結果・払戻し表示中：勝利確率／複勝確率は機能しない（出馬表内のみ）ため減光＋タップ不可。
     非表示にせず残すことでタブ幅のガタつきを防ぎ、「無効はグレーで示す」本ページの流儀
     （結果取得前の結果・払戻しタブと同じ）に統一。選択状態は保持され出馬表に戻ると復帰 */
  .mode-btns.disabled{opacity:.35;pointer-events:none;}
  /* ソート可能な見出し */
  .prob-t th.sortable{cursor:pointer;}
  .prob-t th .sort{font-size:8px;opacity:.85;}

  /* ===== 結果・払戻しビュー ===== */
  .chaku{
    display:inline-grid;place-items:center;width:20px;height:20px;border-radius:50%;
    font-size:10.5px;font-weight:900;background:#2a2e38;color:var(--text-sub);
  }
  .chaku.c1{background:#d4af37;color:#1a1305;}
  .chaku.c2{background:#98a4b8;color:#111;}
  .chaku.c3{background:#c07a3e;color:#fff;}
  .result-t td.name{font-size:12px;}
  /* 確率順位バッジ：1位=赤／2位=水色／3位=橙／4位=黄緑／5位=黄（塗り＋濃色文字で埋もれ防止） */
  .prank{
    display:inline-grid;place-items:center;width:20px;height:20px;border-radius:50%;
    font-size:10.5px;font-weight:900;
  }
  .prank.p1{background:#e6394f;color:#fff;}
  .prank.p2{background:#55c1e8;color:#06222e;}
  .prank.p3{background:#f09a1e;color:#2b1a02;}
  .prank.p4{background:#97c459;color:#1c2b06;}
  .prank.p5{background:#f5d327;color:#2b2405;}
  .payout{margin:10px 12px 12px;border:1px solid var(--line);border-radius:8px;overflow:hidden;}
  .payout .p-head{
    background:#1c2434;color:#fff;font-size:11px;font-weight:800;padding:8px 12px;letter-spacing:.04em;
  }
  .payout table{width:100%;border-collapse:collapse;font-size:11.5px;}
  .payout td{padding:7px 12px;border-top:1px solid var(--line);}
  .payout td.k{width:64px;color:var(--text-sub);font-weight:700;font-size:10.5px;}
  .payout td.c{font-weight:800;color:#fff;font-variant-numeric:tabular-nums;}
  .payout td.y{text-align:right;font-weight:800;color:#ffd25e;font-variant-numeric:tabular-nums;}

  .race-info{
    display:flex;align-items:center;gap:8px;padding:7px 12px;font-size:11px;color:var(--text-sub);
  }
  .race-info b{color:#fff;font-size:13px;}
  .race-info .time{margin-left:auto;background:var(--bg-sunken);border:1px solid var(--line);
    font-size:10px;padding:1px 8px;border-radius:2px;}
  /* 確率（暫定／最終）・期待値（LIVE）ステータス */
  .status-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
  .status-badge{
    display:inline-block;font-size:9.5px;font-weight:900;letter-spacing:.04em;
    padding:2px 8px;border-radius:3px;cursor:pointer;
  }
  .status-badge.prov{background:#3a4150;color:#c3c9d4;}
  .status-badge.final{background:var(--sig);color:#fff;}
  .status-badge.live{
    background:#123047;color:#6ec6ff;border:1px solid #1d5a8a;cursor:default;
  }
  .status-badge.closed{background:#3a4150;color:#c3c9d4;border:none;}
  .status-badge.result{background:#c9a227;color:#1a1305;}
  .status-note{font-size:9px;color:var(--text-sub);}
  /* 手動更新ボタン（5-3原則：鮮度表示＋ユーザー主導更新＋新データ時のみ点灯。自動リロード不採用） */
  .rf-btn{
    width:28px;height:28px;border-radius:50%;margin-left:auto;
    background:var(--bg-sunken);border:1px solid #1d5a8a;color:#6ec6ff;
    cursor:pointer;position:relative;
    display:grid;place-items:center;-webkit-tap-highlight-color:transparent;font-family:inherit;
  }
  .rf-btn svg{
    width:17px;height:17px;display:block;
    stroke:currentColor;fill:none;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;
  }
  .rf-btn::after{content:"";position:absolute;inset:-7px;}
  .rf-btn.spin{animation:rfspin .6s ease-out 1;}
  @keyframes rfspin{from{transform:rotate(0);}to{transform:rotate(360deg);}}
  .rf-btn.avail{border-color:var(--red);color:#5fe8bd;box-shadow:0 0 8px rgba(0,201,141,.35);}
  /* 更新で変化した期待値セルのフラッシュ（並び・スクロールは維持） */
  @keyframes evFlash{0%{background:rgba(0,201,141,.4);}100%{background:transparent;}}
  #probTable td.rate.flash{animation:evFlash 1.1s ease-out 1;}
  /* LIVEドット（点滅） */
  .live-dot{
    display:inline-block;width:5px;height:5px;border-radius:50%;
    background:#6ec6ff;margin-right:3px;vertical-align:1px;
    animation:pulse 1.6s ease-in-out infinite;
  }
  @keyframes pulse{0%,100%{opacity:1;}50%{opacity:.25;}}
  /* 期待値列見出しのLIVE表示 */
  .th-live{display:block;font-size:7px;font-weight:800;color:#6ec6ff;letter-spacing:.08em;line-height:1;margin-top:2px;}
  /* 発売締切後：LIVE表示を消灯 */
  .prob-t.closed .th-live{color:#8a94a6;}
  .prob-t.closed .th-live .live-dot{animation:none;background:#8a94a6;}
  /* シミュレーションボタンの状態 */
  .btn-sim.closed{
    background:#3a4150;color:#9aa3b2;box-shadow:none;pointer-events:none;
  }
  .btn-sim.result{
    background:var(--navy-bright);box-shadow:0 2px 0 #0d2b52;
  }
  /* 補正前：バッジなし・減光なし（「暫定」表記は不使用＝加点表現の原則）。変動矢印のみ最終後に表示 */
  .prob-t.provisional .diff{display:none;}
  /* 最終時：暫定からの変動矢印 */
  .diff{font-size:8px;margin-left:1px;vertical-align:1px;}
  .diff.up{color:var(--sig-soft);}
  .diff.down{color:#6ea8ff;}

  .race-info .going{
    display:inline-grid;place-items:center;
    background:var(--pt);color:#1a1305;font-size:10px;font-weight:900;
    width:18px;height:18px;border-radius:2px;
  }

  table.prob-t{width:100%;border-collapse:collapse;font-size:12px;}
  .prob-t th{
    background:#1c2434;color:#fff;font-size:10px;font-weight:700;
    padding:9px 2px;border-right:1px solid rgba(255,255,255,.08);
  }
  .prob-t th .sort{font-size:8px;opacity:.8;}
  .prob-t th .sort.idle{opacity:.3;} /* 非アクティブ列にも薄い▼を常時表示＝ソート可の示唆 */
  .prob-t td{
    border-bottom:1px solid var(--line);padding:4px 4px;text-align:center;vertical-align:middle;
    height:30px;
  }
  /* 2026-09-30：出馬表の全行を「妙味チップ付き行」と同じ高さに統一（妙味の有無で行高がガタつかないように）。
     妙味チップは数値の下に改行表示（display:block）で固定 */
  #probTable tbody td{height:46px;}
  .prob-t tbody tr:nth-child(odd){background:rgba(255,255,255,.02);}
  .prob-t td.name{
    text-align:left;font-weight:700;font-size:12px;color:#fff;
    overflow:hidden;white-space:nowrap;text-overflow:ellipsis;max-width:0;
  }
  .prob-t td.name small{font-weight:400;font-size:9px;color:var(--text-sub);margin-left:5px;}
  /* 枠色×馬番の統合バッジ */
  .waku{
    display:inline-grid;place-items:center;width:22px;height:22px;font-size:11px;font-weight:900;border-radius:3px;
  }
  .w1{background:#fff;border:1px solid #999;color:#222;}
  .w2{background:#231f20;color:#fff;border:1px solid #555;}
  .w3{background:#d7263d;color:#fff;}
  .w4{background:#1a4d9e;color:#fff;}
  .w5{background:#e8b30c;color:#222;}
  .w6{background:#0a7f45;color:#fff;}
  .w7{background:#e8850c;color:#fff;}
  .w8{background:#e77ba0;color:#fff;}
  .rate{font-weight:800;font-variant-numeric:tabular-nums;color:var(--text);}
  .rate.hi{color:var(--sig-soft);}   /* 下限100%超＝期待値プラス圏 */
  .rate.mid{color:#f0b46a;}  /* 上限のみ100%超＝準注目 */
  /* お試し①：期待値プラスのセルに「妙味」チップを自動付与（数字の意味を画面内で自己説明） */
  #probTable td.rate.ev.hi::after{
    content:"妙味";display:block;width:fit-content;margin-left:auto;margin-right:auto;
    font-size:9px;font-weight:900;color:#fff;
    background:var(--sig);
    line-height:1.3;margin-top:3px;padding:1px 7px;border-radius:8px;letter-spacing:.08em;
  }
  /* 準妙味チップは不採用（mid＝橙の数値色のみ残す） */
  .rate.range{font-size:10.5px;letter-spacing:-.02em;}
  .rank{
    display:inline-grid;place-items:center;width:20px;height:20px;border-radius:50%;
    font-size:11px;font-weight:900;color:#111;
  }
  .rk-s{background:var(--gold);} .rk-a{background:var(--silver);}
  .rk-b{background:var(--bronze);color:#fff;} .rk-c{background:#3a4150;color:#9aa3b2;}
  /* ===== 予想印（ユーザー入力：タップ順送り／長押しピッカー） ===== */
  .prob-t td.mark-td{padding:2px 0;}
  .mark-btn{
    width:28px;height:28px;border-radius:6px;
    display:inline-grid;place-items:center;position:relative;
    background:var(--bg-sunken);border:1px solid #3a4150;
    color:var(--text);font-size:14px;font-weight:800;line-height:1;
    -webkit-tap-highlight-color:transparent;
    -webkit-touch-callout:none;user-select:none;-webkit-user-select:none;
  }
  .mark-btn::after{content:"";position:absolute;inset:-6px -3px;} /* 実効タップ領域40px確保 */
  .mark-btn:empty::before{content:"";width:8px;height:2px;background:#3a4150;border-radius:1px;} /* 未入力プレースホルダ */
  .mark-btn[data-mark="消"]{color:#5a6373;background:transparent;border-color:#2a2e38;}
  /* 消印の行は減光（印セル自体は視認維持） */
  .prob-t tr.h-del td{opacity:.45;}
  .prob-t tr.h-del td.mark-td{opacity:1;}
  /* 結果ビューの印（表示専用・出馬表の入力を同期） */
  .r-mark{font-size:13px;font-weight:800;color:var(--text);}
  .r-mark[data-mark=""]{color:#3a4150;font-weight:400;}
  .r-mark[data-mark="消"]{color:#5a6373;}
  /* 長押しピッカー（ボトムシート） */
  .mark-sheet-overlay{
    position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.55);
    display:flex;align-items:flex-end;justify-content:center;
  }
  .mark-sheet-overlay.hidden{display:none;}
  .mark-sheet{
    width:100%;max-width:414px;background:var(--bg-card);
    border-radius:16px 16px 0 0;border:1px solid var(--line);border-bottom:none;
    padding:14px 14px calc(18px + env(safe-area-inset-bottom));
  }
  .mark-sheet .ms-title{font-size:11px;font-weight:700;color:var(--text-sub);margin-bottom:10px;}
  .mark-sheet .ms-title b{font-size:13px;font-weight:800;color:#fff;margin-right:4px;}
  .mark-sheet .ms-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;}
  .mark-sheet .ms-grid button{
    height:44px;border-radius:8px;
    background:var(--bg-sunken);border:1px solid #3a4150;
    color:var(--text);font-size:17px;font-weight:800;
    -webkit-tap-highlight-color:transparent;
  }
  .mark-sheet .ms-grid button.clear{font-size:11px;font-weight:800;color:var(--text-sub);}
  .mark-sheet .ms-grid button.on{border-color:var(--red);color:var(--red);}
  .prob-note{font-size:10px;color:var(--text-sub);padding:8px 12px 0;}
  .btn-sim{
    display:block;margin:10px 12px 4px;
    background:var(--red);color:#fff;text-decoration:none;
    text-align:center;font-weight:800;font-size:12.5px;
    padding:9px 0;border-radius:6px;
    box-shadow:0 2px 0 var(--red-deep);
  }

  /* ===== 会員登録バナー（下部ナビ上に追従／非ログイン時のみ表示）
     2026-09-30刷新：サイトのトンマナ（ダークカード＋登録訴求色ライムのアクセント）に統一。
     ホーム画面追加バナー（.a2hs）と同じ「お知らせカード」文法：アイコン枠／2行テキスト／ボタン／✕ ===== */
  .reg-banner{
    position:fixed;left:50%;transform:translateX(-50%);
    bottom:calc(92px + env(safe-area-inset-bottom));
    width:calc(100% - 20px);max-width:394px;z-index:90;
    background:rgba(21,23,29,.96);
    backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    border:1px solid rgba(200,243,29,.45);
    border-radius:14px;
    padding:10px 8px 10px 10px;
    display:flex;align-items:center;gap:10px;
    box-shadow:0 6px 22px rgba(0,0,0,.55);
  }
  .reg-banner.hidden{display:none;}
  .reg-banner .rb-ico{
    flex:0 0 auto;width:36px;height:36px;border-radius:10px;
    border:1.5px solid rgba(200,243,29,.7);color:#c8f31d;
    display:grid;place-items:center;
  }
  .reg-banner .rb-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
  .reg-banner .rb-text{flex:1;min-width:0;}
  .reg-banner .rb-main{color:#fff;font-size:12.5px;font-weight:900;line-height:1.35;}
  .reg-banner .rb-sub{color:var(--text-sub);font-size:9.5px;font-weight:700;margin-top:2px;line-height:1.4;}
  .reg-banner .rb-sub b{color:#c8f31d;}
  .reg-banner .rb-btn{
    flex:0 0 auto;
    background:#c8f31d;color:#101406;text-decoration:none;
    font-size:11px;font-weight:900;
    padding:9px 13px;border-radius:9px;white-space:nowrap;
  }
  .reg-banner .close{
    flex:0 0 auto;width:26px;height:26px;border-radius:50%;
    color:var(--text-sub);font-size:17px;line-height:1;
    display:grid;place-items:center;cursor:pointer;position:relative;
    -webkit-tap-highlight-color:transparent;
  }
  .reg-banner .close::after{content:"";position:absolute;inset:-7px;}

  /* ===== ホーム画面追加バナー（PWA／A2HS）2026-09-30追加
     表示条件：①アプリ（standalone）起動中は非表示 ②追加済みを判定できれば非表示
     ③判定不能（iOS Safari等）は✕で閉じてから7日間非表示→7日後に再表示。タップで追加ガイドへ ===== */
  .a2hs{
    margin:10px 12px 0;
    display:flex;align-items:center;gap:10px;
    background:var(--bg-card);border:1px solid rgba(0,201,141,.45);border-radius:14px;
    padding:10px 8px 10px 10px;text-decoration:none;cursor:pointer;
    -webkit-tap-highlight-color:transparent;
  }
  .a2hs[hidden]{display:none;}
  .a2hs .a-ico{
    flex:0 0 auto;width:36px;height:36px;border-radius:10px;
    border:1.5px solid rgba(0,201,141,.75);color:#5fe8bd;
    display:grid;place-items:center;
  }
  .a2hs .a-ico svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;}
  .a2hs .a-txt{flex:1;min-width:0;}
  .a2hs .a-t{font-size:12.5px;font-weight:900;color:#fff;line-height:1.35;}
  .a2hs .a-d{font-size:9.5px;color:var(--text-sub);margin-top:2px;line-height:1.45;}
  .a2hs .a-d b{color:#5fe8bd;font-weight:800;white-space:nowrap;}
  .a2hs .a-btn{
    flex:0 0 auto;background:var(--red);color:#fff;
    font-size:11px;font-weight:900;padding:9px 12px;border-radius:9px;white-space:nowrap;
  }
  .a2hs .a-x{
    flex:0 0 auto;width:26px;height:26px;border-radius:50%;
    color:var(--text-sub);font-size:17px;line-height:1;
    display:grid;place-items:center;position:relative;
  }
  .a2hs .a-x::after{content:"";position:absolute;inset:-7px;}

  /* ===== Topics / Ambassador tabs ===== */
  .c-tabs{display:flex;background:var(--bg-card);border-bottom:2px solid var(--red);}
  .c-tabs a{
    flex:1;text-align:center;font-size:13px;font-weight:800;padding:10px 0;text-decoration:none;
    color:var(--text-sub);background:var(--bg-sunken);
  }
  .c-tabs a.on{background:var(--red);color:#fff;}
  .card-list{list-style:none;background:var(--bg-card);}
  .card{border-bottom:1px solid var(--line);}
  .card a{display:flex;gap:10px;padding:12px;text-decoration:none;color:var(--text);}
  .thumb{
    flex:0 0 96px;height:64px;border-radius:3px;
    display:grid;place-items:center;color:#fff;font-size:10px;font-weight:800;text-align:center;line-height:1.3;
    border:1px solid rgba(255,255,255,.08);
  }
  .th-navy{background:linear-gradient(135deg,#12355f,#0d2b52);}
  .th-red{background:linear-gradient(135deg,#d7263d,#911226);}
  .th-green{background:linear-gradient(135deg,#0a7f45,#0a5c34);}
  .th-gray{background:linear-gradient(135deg,#5b6675,#3a4657);}
  .card .body{flex:1;min-width:0;}
  .card h3{
    font-size:13px;font-weight:800;line-height:1.45;color:#fff;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }
  .meta{display:flex;align-items:center;gap:8px;margin-top:6px;font-size:10px;color:var(--text-sub);flex-wrap:wrap;}
  .badge{font-size:9px;font-weight:800;color:#fff;padding:1px 7px;border-radius:2px;letter-spacing:.05em;}
  .b-free{background:var(--free);}
  .b-sub{background:var(--subsc);}
  .b-pt{background:var(--pt);color:#1a1305;}
  .b-new{color:var(--sig);font-weight:900;font-size:9px;}
  .like{margin-left:auto;color:var(--text-sub);}
  .read-more{
    display:block;text-align:center;font-size:12px;color:var(--blue-link);font-weight:700;
    background:var(--bg-card);padding:12px 0;text-decoration:none;border-bottom:1px solid var(--line);
  }

  /* ===== 発走時間の近いレース（速報ロール同等のスリムバー） ===== */
  .nextbar{
    display:flex;align-items:center;gap:8px;
    height:46px;padding:0 12px;
    background:var(--bg-sunken);
    border-top:1px solid var(--line);
  }
  .nextbar .lbl{
    flex:0 0 auto;background:var(--red);color:#fff;font-size:10px;font-weight:800;
    padding:2px 8px;border-radius:2px;letter-spacing:.08em;
  }
  .nextbar .nb-list{
    flex:1;min-width:0;display:flex;gap:6px;
  }
  .nextbar .nb-list a{
    flex:1;display:flex;align-items:center;justify-content:center;gap:3px;
    border:1px solid var(--line);border-radius:5px;
    background:var(--bg-card);
    font-size:11px;color:var(--text);text-decoration:none;
    padding:7px 2px;white-space:nowrap;
    position:relative;
  }
  .nextbar .nb-list a::after{content:"";position:absolute;inset:-7px -2px;} /* タップ領域拡張 */
  .nextbar .nb-list a b{font-weight:900;color:#fff;}
  .nextbar .nb-list a .t{font-weight:800;font-variant-numeric:tabular-nums;color:var(--text-sub);}
  .nextbar .nb-list a.soon{border-color:var(--red);}
  .nextbar .nb-list a.soon b{color:#ff6b7d;}
  .nextbar .nb-list a.soon .t{color:#ff6b7d;}



  /* ===== 本日の攻略情報（TOPは圧縮版チップ／詳細は攻略ツールページへ） ===== */
  .goods{background:var(--bg-card);padding:8px 12px 12px;display:grid;gap:6px;}
  .goods a{
    display:flex;align-items:center;gap:8px;
    border:1px solid var(--line);border-radius:8px;
    background:var(--bg-sunken);padding:8px 12px;text-decoration:none;
  }
  .goods .g-title{
    flex:1;min-width:0;font-size:12.5px;font-weight:800;color:#fff;
    overflow:hidden;white-space:nowrap;text-overflow:ellipsis;
  }
  .goods .g-title .new{color:var(--sig);font-size:9px;font-weight:900;margin-left:4px;}
  .goods .g-price{
    flex:0 0 auto;
    background:var(--pt);color:#1a1305;font-size:11px;font-weight:900;
    padding:6px 12px;border-radius:15px;white-space:nowrap;
  }


  /* ===== アンケート（速報と同じスリムバー／タップでアンケートTOPへ） ===== */
  .surveybar{
    display:flex;align-items:center;gap:8px;
    height:44px;padding:0 12px;
    background:#0d0f14; /* お試し②：一段沈めたトーン */
    border-bottom:1px solid var(--line);
  }
  .surveybar .lbl{
    flex:0 0 auto;background:transparent;border:1px solid var(--pt);color:var(--pt);
    font-size:10px;font-weight:800;
    padding:1px 8px;border-radius:2px;letter-spacing:.08em;
  }
  .surveybar .q{
    flex:1;min-width:0;font-size:12px;color:var(--text-sub);font-weight:700; /* お試し②：減光 */
    overflow:hidden;white-space:nowrap;text-overflow:ellipsis;
    text-decoration:none;
  }
  .surveybar .ans-btn{
    flex:0 0 auto;display:flex;align-items:center;gap:4px;
    background:transparent;border:1px solid var(--pt);color:var(--pt); /* お試し②：塗り→枠線 */
    font-size:11px;font-weight:900;
    padding:6px 12px;border-radius:16px;text-decoration:none;
  }
  .surveybar .ans-btn small{font-size:8.5px;font-weight:800;}
  .surveybar .more{flex:0 0 auto;font-size:10px;color:var(--text-sub);text-decoration:none;}


  /* ===== お知らせ（トピックス内タブ・コンパクト） ===== */
  .info-list{list-style:none;background:var(--bg-card);}
  .info-list li{border-bottom:1px solid var(--line);}
  .info-list a{
    display:flex;align-items:center;gap:8px;
    padding:8px 12px;font-size:12px;color:var(--blue-link);text-decoration:none;
  }
  .info-list .i-txt{flex:1;min-width:0;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;}
  .info-list .date{flex:0 0 auto;font-size:10px;color:var(--text-sub);font-variant-numeric:tabular-nums;}



  /* ===== 会員状態別CTA（非ログイン／無料会員／サブスク会員で出し分け） ===== */
  .demo-switch{
    display:flex;align-items:center;gap:6px;flex-wrap:wrap;
    padding:10px 12px 0;
  }
  .demo-switch button{white-space:nowrap;}
  .demo-switch .d-lbl{font-size:8.5px;color:#5a6373;font-weight:700;}
  .demo-switch button{
    font-size:9px;font-weight:800;color:var(--text-sub);
    background:var(--bg-sunken);border:1px solid var(--line);border-radius:11px;
    padding:4px 10px;
  }
  .demo-switch button.on{background:var(--red);border-color:var(--red);color:#fff;}

  .member-cta{margin:10px 12px 18px;display:grid;gap:12px;} /* 2026-09-30：上下余白を確保（上のモック切替・下のフッターに密着していた） */
  .member-cta .panel{
    border-radius:12px;overflow:hidden;
    border:1px solid rgba(255,255,255,.08);
  }
  /* 非ログイン：無料登録に一本化 */
  .cta-guest{background:linear-gradient(160deg,#12355f,#081c38);text-align:center;padding:22px 16px 20px;}
  .cta-guest .lead{font-size:14px;font-weight:800;line-height:1.7;color:#fff;}
  .cta-guest .lead b{color:#ffd25e;}
  .cta-guest .sub{font-size:10.5px;color:#bcd0ec;margin-top:5px;}
  .cta-guest .btn{
    display:block;margin:14px auto 0;max-width:280px;
    background:var(--red);color:#fff;font-weight:900;font-size:14px;
    text-decoration:none;padding:14px 0;border-radius:26px;
    box-shadow:0 3px 0 var(--red-deep);
  }
  .cta-guest .login{display:inline-block;margin-top:12px;font-size:11px;color:#bcd0ec;text-decoration:none;}

  /* 無料会員：サブスク訴求を主役に */
  .cta-free{background:linear-gradient(160deg,#0a231c,#15171d);padding:20px 16px 18px;text-align:center;position:relative;}
  .cta-free .offer{
    display:inline-block;background:var(--lime, #c8f31d);background:#c8f31d;color:#000;
    font-size:10px;font-weight:900;padding:3px 12px;border-radius:12px;letter-spacing:.06em;
  }
  .cta-free .lead{font-size:14px;font-weight:800;line-height:1.7;color:#fff;margin-top:9px;}
  .cta-free .lead b{color:#5fe8bd;}
  .cta-free .price{margin-top:6px;font-size:11px;color:var(--text-sub);}
  .cta-free .price b{font-size:16px;color:#fff;font-variant-numeric:tabular-nums;}
  .cta-free .btn{
    display:block;margin:13px auto 0;max-width:290px;
    background:var(--red);color:#fff;font-weight:900;font-size:14px;
    text-decoration:none;padding:14px 0;border-radius:26px;
    box-shadow:0 3px 0 var(--red-deep);
  }
  .cta-free .pt-link{display:inline-block;margin-top:12px;font-size:10.5px;color:var(--text-sub);text-decoration:underline;}

  /* サブスク会員：ポイント導線のみ・コンパクト */
  .cta-subsc{
    display:flex;align-items:center;gap:10px;
    background:var(--bg-card);padding:13px 14px;
  }
  .cta-subsc .p-coin{
    width:26px;height:26px;border-radius:50%;flex:0 0 auto;
    background:linear-gradient(135deg,#f5c518,#e8850c);
    color:#1a1305;font-size:12px;font-weight:900;display:grid;place-items:center;
  }
  .cta-subsc .txt{flex:1;min-width:0;font-size:11px;color:var(--text-sub);line-height:1.5;}
  .cta-subsc .txt b{color:#fff;}
  .cta-subsc .btn{
    flex:0 0 auto;background:var(--pt);color:#1a1305;
    font-size:11px;font-weight:900;padding:8px 14px;border-radius:16px;text-decoration:none;white-space:nowrap;
  }

  /* ===== Footer ===== */
  footer{background:#06070a;color:var(--text-sub);padding:20px 16px 26px;border-top:1px solid var(--line);}
  footer .f-logo{font-weight:900;font-size:18px;color:#fff;}
  footer .f-logo span{color:var(--red);}
  footer nav{display:flex;flex-wrap:wrap;gap:10px 16px;margin-top:14px;font-size:11px;}
  footer nav a{color:var(--text-sub);text-decoration:none;}
  footer .copy{margin-top:18px;font-size:10px;text-align:center;color:#5a6373;}

  /* =====================================================================
     PC版（≥1024px）2026-10-01：同一HTMLのレスポンシブ切替（3カラム案Bを採用）
     - SP（〜1023px）は従来どおり414px幅の縦積み（変更なし）
     - 中央寄せ（最大1240px）。左＝レース選択（日付・開催場・R番号・発走間近／追従）／中央＝確率テーブル／右＝サイド
     - 速報・アンケートはそれぞれ全幅1行
     - 主要ナビ（的中実績〜ポイント購入）はヘッダーに置かず、速報・アンケート直下の緑の帯に配置し、スクロールでヘッダー下に貼りつく。
       理由：情報と色が画面の本文側に集中するため最上部は見られにくい。一方で画面下固定はPCでは確率テーブルを隠すため不採用
     ===================================================================== */
  @media (min-width:1024px){
    body{background:var(--bg);}
    .sp{max-width:none;box-shadow:none;overflow:visible;padding-bottom:48px;}
    /* ヘッダー：ロゴ＋pt・マイページ・メニューのみ（ナビは下部追従） */
    header{height:62px;padding:0 max(24px, calc((100% - 1240px) / 2 + 24px));}
    .logo .mark{font-size:22px;}
    /* 主要ナビ（PC）：速報・アンケート直下の帯。スクロールでヘッダー下に貼りつく（sticky）
       シミュレーションはPC仕様の緑ボタンとして右端に配置（SPの浮き上がりFABは使わない） */
    .gnav{
      position:sticky;top:62px;bottom:auto;left:auto;transform:none;z-index:45;
      width:calc(100% - 48px);max-width:1192px;margin:16px auto 0;
      display:flex;align-items:center;gap:4px;height:56px;padding:0 8px 0 10px;
      background:linear-gradient(90deg,#0c2a20 0%,#121a17 45%,#15171d 100%);
      border:1px solid rgba(0,201,141,.45);border-radius:12px;
      box-shadow:0 8px 22px rgba(0,0,0,.45);
      backdrop-filter:none;-webkit-backdrop-filter:none;
    }
    .gnav a{
      flex:0 0 auto;flex-direction:row;align-items:center;gap:7px;
      height:100%;padding:0 14px;font-size:13px;font-weight:800;color:#c3c9d4;
      border-bottom:2px solid transparent;
    }
    .gnav a:hover{color:#fff;background:rgba(255,255,255,.03);}
    .gnav a .lbl{height:auto;white-space:nowrap;}
    .gnav a .lbl br{display:none;}
    .gnav a .ico{width:auto;height:auto;background:none !important;border-radius:0;}
    .gnav a .ico svg{width:18px;height:18px;stroke:#5fe8bd;}
    .gnav a.active{color:#fff;border-bottom-color:var(--red);}
    .gnav .fab{
      order:9;margin-left:auto;flex:0 0 auto;min-width:0;height:auto;flex-direction:row;gap:8px;
      padding:9px 18px 9px 10px;border-radius:24px;border-bottom:none;
      background:linear-gradient(150deg,#14d69a,#04875f);
      box-shadow:0 3px 14px rgba(0,201,141,.4);
    }
    .gnav .fab:hover{filter:brightness(1.08);background:linear-gradient(150deg,#14d69a,#04875f);}
    .gnav .fab .fab-btn{
      width:28px;height:28px;margin:0;border:none;box-shadow:none;
      background:rgba(255,255,255,.18);
    }
    .gnav .fab .fab-btn svg{width:16px;height:16px;}
    .gnav .fab .fab-lbl{font-size:13.5px;font-weight:900;color:#fff;letter-spacing:.02em;}
    .gnav .fab .fab-lbl::before{content:"的中確率";}
    /* 上部帯：ホーム画面追加・速報・アンケートを各1行（全幅） */
    .pc-top{
      max-width:1240px;margin:0 auto;padding:16px 24px 0;
      display:flex;flex-direction:column;gap:8px;
    }
    .pc-top .a2hs{margin:0;}
    .pc-top .flash, .pc-top .surveybar{border:1px solid var(--line);border-radius:10px;}
    /* 本文グリッド */
    .pc-body{
      max-width:1240px;margin:0 auto;padding:16px 24px 0;
      display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:20px;align-items:start;
    }
    .pc-body > section, .pc-side > section{margin-top:0;border:1px solid var(--line);border-radius:12px;overflow:hidden;}
    .pc-side{display:flex;flex-direction:column;gap:16px;min-width:0;}
    .pc-side .demo-switch{padding:0;}
    .pc-side .member-cta{margin:0;}
    .sec-title{border-top-width:0;box-shadow:inset 0 3px 0 var(--red);}
    /* 確率ブロック：左＝レース選択列（追従）／右＝出馬表 */
    .pc-main{overflow:visible !important;}
    .pc-main .sec-title{border-radius:12px 12px 0 0;}
    .prob{display:grid;grid-template-columns:240px minmax(0,1fr);align-items:start;border-radius:0 0 12px 12px;}
    .prob-nav{padding-bottom:12px;align-self:start;position:sticky;top:130px;} /* ヘッダー62＋ナビ帯56＋余白 */
    .prob-body{min-width:0;border-left:1px solid var(--line);min-height:100%;}
    .tabs.venue{flex-wrap:wrap;overflow:visible;padding:10px;}
    .tabs.race{grid-template-columns:repeat(4,1fr);padding:0 10px 10px;}
    .nextbar{flex-direction:column;align-items:stretch;height:auto;padding:10px;gap:8px;}
    .nextbar .lbl{align-self:flex-start;}
    .nextbar .nb-list{flex-direction:column;}
    .nextbar .nb-list a{justify-content:space-between;padding:8px 10px;}
    /* 出馬表：PCは読みやすいサイズに */
    .prob-t td{font-size:13px;}
    .prob-t td.name{font-size:13.5px;}
    .prob-t td.name small{font-size:10.5px;}
    .prob-t th{font-size:11px;}
    #th-num{width:56px !important;}
    #th-mark{width:46px !important;}
    /* フッター・追従バナー（下部ナビと重ならないよう右下・ナビより上） */
    footer{margin-top:40px;padding-left:max(24px, calc((100% - 1240px) / 2 + 24px));padding-right:max(24px, calc((100% - 1240px) / 2 + 24px));}
    .reg-banner{left:auto;right:24px;transform:none;bottom:24px;width:380px;}
    .modal{max-width:420px;}
  }
  @media (min-width:1024px) and (max-width:1199px){
    .pc-body{grid-template-columns:minmax(0,1fr) 300px;}
    .prob{grid-template-columns:210px minmax(0,1fr);}
    .gnav a{padding:0 9px;font-size:12px;}
  }

  /* =====================================================================
     ナビ「的中実績」の注目演出（2026-10-01）SP・PC共通
     目的：一度でもここに目が行けばナビの存在が際立つよう、静かな画面に小さな“違和感”を入れる
     ① 光が通るシャイン（9秒周期・0.9秒だけ）
     ② 点滅（9秒周期でシャインの約4秒後に2回だけ光る）
     ③ 本日の的中数バッジ（シグナル赤・数字はサーバ値／的中が増えたら1回だけポップ）
     ・頻度は控えめ（常時アニメにしない）。的中実績ページ自身（.active）では演出しない
     ・OSの「視差効果を減らす」設定時は演出を止める（prefers-reduced-motion）
     ===================================================================== */
  .gnav a.hit{position:relative;overflow:hidden;}
  .gnav a.hit .ico{position:relative;overflow:visible;}
  .gnav a.hit:not(.active)::after{
    content:"";position:absolute;top:0;bottom:0;left:0;width:60%;pointer-events:none;
    background:linear-gradient(105deg,transparent 0%,rgba(95,232,189,0) 20%,rgba(190,255,232,.38) 50%,rgba(95,232,189,0) 80%,transparent 100%);
    transform:translateX(-180%) skewX(-12deg);
    animation:hitShine 9s ease-in-out 1.2s infinite;
  }
  @keyframes hitShine{
    0%{transform:translateX(-180%) skewX(-12deg);}
    10%{transform:translateX(260%) skewX(-12deg);}
    100%{transform:translateX(260%) skewX(-12deg);}
  }
  .gnav a.hit:not(.active) .ico svg, .gnav a.hit:not(.active) .lbl{animation:hitBlink 9s linear 1.2s infinite;}
  @keyframes hitBlink{
    0%,44%,47%,50%,100%{filter:none;color:inherit;}
    45.5%,48.5%{filter:drop-shadow(0 0 6px rgba(95,232,189,.95));color:#fff;}
  }
  .hit-badge{
    position:absolute;top:-5px;right:-6px;z-index:1;
    min-width:16px;height:16px;padding:0 4px;border-radius:8px;
    background:var(--sig);color:#fff;font-style:normal;font-size:9.5px;font-weight:900;line-height:16px;text-align:center;
    font-variant-numeric:tabular-nums;box-shadow:0 0 0 2px #111420;
  }
  .hit-badge.pop{animation:hitPop .6s cubic-bezier(.3,1.6,.5,1) 1;}
  @keyframes hitPop{0%{transform:scale(.4);}60%{transform:scale(1.25);}100%{transform:scale(1);}}
  .gnav a.hit.active .hit-badge{display:none;}
  @media (min-width:1024px){
    /* PC：アイコンが小さいため、バッジはラベルの右に縦中央で配置 */
    .gnav a.hit{padding-right:40px;}
    .gnav a.hit .ico{position:static;}
    .hit-badge{top:50%;right:12px;margin-top:-8px;box-shadow:none;}
  }
  @media (prefers-reduced-motion:reduce){
    .gnav a.hit::after, .gnav a.hit .ico svg, .gnav a.hit .lbl{animation:none !important;}
    .gnav a.hit::after{display:none;}
  }

  /* 2026-10-01（再調整）：PCは緑の帯の上でコントラストが足りず、9秒に一瞬の演出では気づかれなかったため
     ① 常時：項目自体を“光るチップ”にして帯の中で浮かせる（動き設定に関係なく常に目立つ）
     ② 5秒周期：白く太い光が通過 → 約1.5秒後に項目ごと2回フラッシュ → 枠がふわっと発光
     ③ OSで「動きを減らす」設定時：光の通過と点滅は止め、枠の発光（ゆっくり明滅）だけ残す */
  @media (min-width:1024px){
    .gnav a.hit:not(.active){
      height:40px;margin:0 4px 0 2px;border-radius:10px;border-bottom:none;color:#fff;
      background:linear-gradient(180deg,rgba(0,201,141,.26),rgba(0,201,141,.12));
      box-shadow:inset 0 0 0 1px rgba(95,232,189,.75),0 0 10px rgba(0,201,141,.25);
      animation:hitFlashPC 5s linear 1s infinite;
    }
    .gnav a.hit:not(.active) .ico svg{stroke:#bfffe8;}
    .gnav a.hit:not(.active)::after{
      width:70%;
      background:linear-gradient(105deg,transparent 0%,rgba(255,255,255,0) 15%,rgba(255,255,255,.55) 42%,rgba(255,255,255,.95) 50%,rgba(255,255,255,.55) 58%,rgba(255,255,255,0) 85%,transparent 100%);
      animation:hitShinePC 5s cubic-bezier(.45,0,.25,1) 1s infinite;
    }
    @keyframes hitShinePC{
      0%{transform:translateX(-160%) skewX(-14deg);}
      22%{transform:translateX(240%) skewX(-14deg);}
      100%{transform:translateX(240%) skewX(-14deg);}
    }
    .gnav a.hit:not(.active) .ico svg, .gnav a.hit:not(.active) .lbl{animation:hitBlinkPC 5s linear 1s infinite;}
    @keyframes hitFlashPC{
      0%,30%,37%,42%,49%,100%{background-color:transparent;box-shadow:inset 0 0 0 1px rgba(95,232,189,.75),0 0 10px rgba(0,201,141,.25);}
      32%,35%,44%,47%{background-color:rgba(95,232,189,.55);box-shadow:inset 0 0 0 1px #d6fff0,0 0 22px rgba(95,232,189,.85);}
      62%{box-shadow:inset 0 0 0 1px rgba(95,232,189,.75),0 0 10px rgba(0,201,141,.25);}
      72%{box-shadow:inset 0 0 0 1px #bfffe8,0 0 26px rgba(95,232,189,.7);}
      88%{box-shadow:inset 0 0 0 1px rgba(95,232,189,.75),0 0 10px rgba(0,201,141,.25);}
    }
    @keyframes hitBlinkPC{
      0%,30%,37%,42%,49%,100%{filter:none;text-shadow:none;}
      32%,35%,44%,47%{filter:drop-shadow(0 0 6px #fff);text-shadow:0 0 12px #fff;}
    }
    @keyframes hitGlowPC{
      0%,100%{box-shadow:inset 0 0 0 1px rgba(95,232,189,.75),0 0 10px rgba(0,201,141,.25);}
      50%{box-shadow:inset 0 0 0 1px #bfffe8,0 0 24px rgba(95,232,189,.75);}
    }
  }
  @media (min-width:1024px) and (prefers-reduced-motion:reduce){
    .gnav a.hit .ico svg, .gnav a.hit .lbl{animation:none !important;}
    .gnav a.hit:not(.active){animation:hitGlowPC 3s ease-in-out infinite !important;}
  }

  /* ===== TOP：アンバサダー枠（2026-10-01）=====
     SP：アイコン＋名前を横スクロールで全員表示（末尾に「一覧」タイル）
     PC：右サイドに縦並びで先頭5名（アイコン＋名前＋属性1つ）＋「他◯名を見る」
     タップ先：アンバサダーページ（P21）の該当者の位置（#amb-{XのID}） */
  .amb-top{background:var(--bg-card);}
  .amb-strip{list-style:none;display:flex;gap:12px;overflow-x:auto;padding:12px 12px 12px;scrollbar-width:none;scroll-snap-type:x proximity;}
  .amb-strip::-webkit-scrollbar{display:none;}
  .amb-strip li{flex:0 0 auto;scroll-snap-align:start;}
  .amb-strip a{display:flex;flex-direction:column;align-items:center;width:62px;text-decoration:none;color:var(--text);-webkit-tap-highlight-color:transparent;}
  .amb-strip .av{
    width:54px;height:54px;border-radius:50%;padding:2px;
    background:conic-gradient(from 200deg,#00c98d,#c8f31d,#00c98d 70%,#0a7a58,#00c98d);
  }
  .amb-strip .av img{display:block;width:100%;height:100%;border-radius:50%;object-fit:cover;border:2px solid var(--bg-card);background:#1d222b;}
  .amb-strip .nm{margin-top:5px;max-width:100%;font-size:9.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  .amb-strip .tg{display:none;}
  .amb-strip .more .av{background:none;border:1px dashed #3a4150;display:grid;place-items:center;color:var(--red);font-size:20px;font-weight:900;padding:0;}
  .amb-strip .more .nm{color:var(--red);}
  .amb-strip a:active .av{transform:scale(.95);}
  @media (min-width:1024px){
    .amb-strip{flex-direction:column;gap:0;overflow:visible;padding:4px 0;}
    .amb-strip li{border-bottom:1px solid var(--line);}
    .amb-strip li:last-child{border-bottom:none;}
    .amb-strip li.pc-off{display:none;}
    .amb-strip a{flex-direction:row;width:auto;gap:10px;padding:8px 14px;}
    .amb-strip a:hover{background:rgba(0,201,141,.05);}
    .amb-strip .av{width:36px;height:36px;flex:0 0 auto;}
    .amb-strip .nm{margin-top:0;flex:1;min-width:0;font-size:12px;font-weight:800;color:#fff;text-align:left;}
    .amb-strip .tg{
      display:block;flex:0 0 auto;font-size:9px;font-weight:800;color:#5fe8bd;
      background:rgba(0,201,141,.08);border:1px solid rgba(0,201,141,.32);border-radius:3px;padding:0 6px;line-height:1.6;
    }
    .amb-strip .more a{justify-content:center;}
    .amb-strip .more .av{display:none;}
    .amb-strip .more .nm{flex:0 0 auto;font-size:11px;color:var(--red);}
  }
</style>
{/literal}
</head>
<body>
<div class="sp">

  <!-- Header -->
  <header>
    <a class="logo" href="EvalMode_TOP_SP_v3.2_グリーン.html">
      <span class="mark">Eval<span> MODE</span></span>
    </a>
    <div class="h-icons">
      <a class="h-point" href="EvalMode_ポイント購入_SP_v1.html">
        <span class="p-coin">P</span>
        <span class="p-num">7,777</span><span class="p-unit">pt</span>
      </a>
      <a class="icon-mypage" href="EvalMode_マイページ_SP_v1.html">
        <span class="icon-guest">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        <span class="mp-lbl">マイページ</span>
      </a>
      <span class="icon-menu" id="menuBtn"><i></i><i></i><i></i></span>
    </div>
  </header>

  <!-- ハンバーガードロワーメニュー（全ページ共通の総合入口／アカウント欄は会員状態で出し分け） -->
  <div class="drawer-overlay hidden" id="drawerOverlay">
    <aside class="drawer">
      <div class="d-head">
        <span class="d-title">メニュー</span>
        <button class="d-close" id="drawerClose">×</button>
      </div>
      <nav class="d-sec">
        <p class="d-sec-t">メイン</p>
        <a class="d-item" href="EvalMode_TOP_SP_v3.2_グリーン.html">TOP（確率テーブル）<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_開催日程_SP_v1.html">開催日程<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_初めての方ガイド_SP_v1.html">🔰 初めての方ガイド<span class="chev">›</span></a>
      </nav>
      <nav class="d-sec">
        <p class="d-sec-t">機能</p>
        <a class="d-item" href="EvalMode_シミュレーション_SP_v1.html">的中確率シミュレーション<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_好走確率ランキング_SP_v1.html">本日の好走確率ランキング<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_的中実績_SP_v3.html">的中実績<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_攻略ツール_SP_v2_グリーン.html">おすすめ攻略ツール<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_ポイント購入_SP_v1.html">ポイント購入<span class="chev">›</span></a>
      </nav>
      <nav class="d-sec">
        <p class="d-sec-t">読みもの・参加</p>
        <a class="d-item" href="EvalMode_トピックス_SP_v1.html">トピックス一覧<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_トピックス_SP_v1.html">速報一覧<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_トピックス_SP_v1.html">お知らせ<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_アンバサダー_SP_v1.html">アンバサダー<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_アンケート_SP_v1.html">アンケート<span class="em">🎁ptがもらえる</span><span class="chev">›</span></a>
      </nav>
      <nav class="d-sec" id="d-acc-guest">
        <p class="d-sec-t">アカウント</p>
        <a class="d-btn primary" href="EvalMode_会員登録ログイン_SP_v1.html">無料会員登録（30秒で完了） ▶</a>
        <a class="d-btn ghost" href="EvalMode_会員登録ログイン_SP_v1.html">ログイン</a>
      </nav>
      <nav class="d-sec" id="d-acc-free" hidden>
        <p class="d-sec-t">アカウント</p>
        <a class="d-item" href="EvalMode_マイページ_SP_v1.html">マイページ<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_マイページ_SP_v1.html">購入履歴<span class="chev">›</span></a>
        <a class="d-btn primary" href="EvalMode_サブスク申込_SP_v2.html">サブスクに申し込む（初月無料） ▶</a>
        <a class="d-item d-logout" href="EvalMode_TOP_SP_v3.2_グリーン.html">ログアウト</a>
      </nav>
      <nav class="d-sec" id="d-acc-subsc" hidden>
        <p class="d-sec-t">アカウント</p>
        <a class="d-item" href="EvalMode_マイページ_SP_v1.html">マイページ<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_マイページ_SP_v1.html">購入履歴<span class="chev">›</span></a>
        <a class="d-item" href="EvalMode_マイページ_SP_v1.html">サブスクのプラン確認・変更<span class="chev">›</span></a>
        <a class="d-item d-logout" href="EvalMode_TOP_SP_v3.2_グリーン.html">ログアウト</a>
      </nav>
      <details class="d-fold">
        <summary>サポート・規約</summary>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">よくある質問・料金プラン</a>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">お問い合わせ</a>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">運営会社</a>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">利用規約</a>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">プライバシーポリシー</a>
        <a class="d-item sub" href="EvalMode_静的ページ共通テンプレ_SP_v1.html">特定商取引法に基づく表記</a>
      </details>
      <a class="d-item d-x" href="https://x.com/master_eval" target="_blank" rel="noopener">𝕏 公式X（@master_eval）↗</a>
    </aside>
  </div>

  <!-- キャンペーンモーダル（全ページ共通の告知基盤）
       2026-09-30：ユーザーステータス別に複数枚登録でき、×／閉じるで次の1枚が順番に表示される連続表示方式に変更。
       内容・対象ステータス・優先度・期間・頻度は管理画面（モーダル管理）で設定 -->
  <div class="modal-overlay hidden" id="campaignModal">
    <div class="modal">
      <span class="m-count" id="mCount" hidden></span>
      <span class="m-close" id="modalClose">×</span>
      <div class="m-visual" id="mVisual">
        <div>
          <span class="m-lbl" id="mLbl"></span>
          <h2 id="mTitle"></h2>
          <p id="mDesc"></p>
        </div>
      </div>
      <div class="m-foot">
        <a class="m-btn" href="#" id="mCta"></a>
        <a class="m-skip" href="#" id="modalSkip">閉じる</a>
        <div class="m-dots" id="mDots"></div>
      </div>
    </div>
  </div>

  <!-- PC：上部帯（ホーム画面追加・速報・アンケート）。SPでは通常の縦積み -->
  <div class="pc-top">
  <!-- ホーム画面追加バナー（PWA）：アプリ起動中・追加済み判定時は非表示／判定不能時は✕で7日非表示 -->
  <div class="a2hs" id="a2hs" hidden role="link" tabindex="0">
    <span class="a-ico"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span>
    <span class="a-txt">
      <span class="a-t">ホーム画面に追加</span><br>
      <span class="a-d">確率テーブルへアプリのようにワンタップで。<b>追加のしかた →</b></span>
    </span>
    <span class="a-btn">追加する</span>
    <span class="a-x" id="a2hsClose" aria-label="閉じる">×</span>
  </div>

  <!-- 速報（1行ローテーション） -->
  <div class="flash">
    <span class="lbl">速報</span>
    <div class="ticker" id="ticker">
      <a class="show" href="EvalMode_的中実績_SP_v3.html">Eval、日・函館11R 3連複72.4倍<b>【8万6880円】</b>的中！</a>
      <a href="EvalMode_的中実績_SP_v3.html">【WIN5】Eval確率上位で構成、払戻<b>【42万7100円】</b>！</a>
      <a href="EvalMode_トピックス_SP_v1.html">好走確率50%超・Sランク馬、東京9Rで1着→単勝460円</a>
      <a href="EvalMode_トピックス_SP_v1.html">【New】本日の馬具情報を公開しました（ポイント）</a>
      <a href="EvalMode_的中実績_SP_v3.html">Eval、大井最終 馬単38.2倍<b>【3万8200円】</b>的中！</a>
    </div>
    <a class="more" href="#" id="flashMore">一覧 ▶</a>
  </div>

  <!-- アンケート（速報下配置／タップでアンケートTOPへ） -->
  <div class="surveybar">
    <span class="lbl">アンケート</span>
    <a class="q" href="EvalMode_WIN5シミュレーション_SP_v1.html">Q. 今週のWIN5、Eval確率上位で構成するなら何点まで買う？</a>
    <a class="ans-btn" href="EvalMode_アンケート_SP_v1.html">回答<small>🎁50pt</small></a>
    <a class="more" href="EvalMode_アンケート_SP_v1.html">一覧 ▶</a>
  </div>
  </div><!-- /pc-top -->

  <!-- Global nav：SP＝画面下に固定（フローティング・グラス型／中央FAB＝シミュレーション）
       PC＝速報・アンケートの直下に帯として配置し、スクロールで画面上部（ヘッダー下）に貼りつく -->
  <nav class="gnav">
    <a class="hit" href="EvalMode_的中実績_SP_v3.html" aria-label="的中実績（本日14レース的中）">
      <span class="ico"><i class="hit-badge" aria-hidden="true">14</i><svg viewBox="0 0 24 24"><circle cx="11" cy="13" r="8"/><circle cx="11" cy="13" r="3.2"/><path d="M11 13L21 3"/><path d="M21 3h-5"/><path d="M21 3v5"/></svg></span>
      <span class="lbl">的中実績</span>
    </a>
    <a href="EvalMode_好走確率ランキング_SP_v1.html">
      <span class="ico"><svg viewBox="0 0 24 24"><rect x="3" y="10" width="5.2" height="10" rx="1" fill="currentColor" stroke="none"/><rect x="9.4" y="4" width="5.2" height="16" rx="1" fill="currentColor" stroke="none"/><rect x="15.8" y="13" width="5.2" height="7" rx="1" fill="currentColor" stroke="none"/></svg></span>
      <span class="lbl">本日の好走<br>確率ランキング</span>
    </a>
    <a class="fab" href="EvalMode_シミュレーション_SP_v1.html">
      <span class="fab-btn"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg></span>
      <span class="fab-lbl">シミュレーション</span>
    </a>
    <a href="EvalMode_攻略ツール_SP_v2_グリーン.html">
      <span class="ico"><svg viewBox="0 0 24 24"><path d="M12 3l1.9 5.6L20 9l-4.5 3.9L16.8 19 12 15.7 7.2 19l1.3-6.1L4 9l6.1-.4L12 3z"/></svg></span>
      <span class="lbl">おすすめ<br>攻略ツール</span>
    </a>
    <a href="EvalMode_ポイント購入_SP_v1.html">
      <span class="ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.5 16V8h3a2.5 2.5 0 0 1 0 5h-3"/></svg></span>
      <span class="lbl">ポイント購入</span>
    </a>
  </nav>

  <!-- PC：本文グリッド（確率ブロック［レース選択列＋出馬表］／右サイド）。SPでは通常の縦積み -->
  <div class="pc-body">
  <!-- ===== 確率テーブル（TOPの主役／レイヤー1） ===== -->
  <section class="pc-main">
    <div class="sec-title">
      <h2>Probability</h2><span class="en">全出走確率データ</span>
      <a class="guide" href="EvalMode_初めての方ガイド_SP_v1.html"><span class="g-mark">🔰</span>初めての方</a>
      <a class="right" href="EvalMode_開催日程_SP_v1.html">開催日程 ▶</a>
    </div>
    <div class="prob">
      <div class="prob-nav"><!-- PC：左のレース選択列（追従） -->
      <!-- 日付タブ -->
      <div class="tabs date">
        <a href="#">7/12(日)</a><a class="on" href="#">7/14(火)</a><a href="#">7/15(水)</a>
      </div>
      <!-- 開催場タブ -->
      <div class="tabs venue">
        <a class="on" href="#">東京</a><a href="#">京都</a><a href="#">福島</a>
        <a class="local" href="#">門別</a><a class="local" href="#">高知</a><a class="local" href="#">大井</a>
        <a class="local" href="#">川崎</a><a class="local" href="#">名古屋</a><a class="local" href="#">佐賀</a>
      </div>
      <!-- レース番号 -->
      <div class="tabs race">
        <a class="done" href="#">1R</a><a class="done" href="#">2R</a><a class="on" href="#">3R</a><a href="#">4R</a>
        <a href="#">5R</a><a href="#">6R</a><a href="#">7R</a><a href="#">8R</a>
        <a href="#">9R</a><a href="#">10R</a><a href="#">11R</a><a href="#">12R</a>
      </div>
      <!-- 発走時間の近いレース（スリムバー） -->
      <div class="nextbar">
        <span class="lbl">発走間近</span>
        <div class="nb-list">
          <a class="soon" href="#"><b>東京3R</b><span class="t">11:10</span></a><a href="#"><b>門別2R</b><span class="t">11:20</span></a><a href="#"><b>京都3R</b><span class="t">11:25</span></a>
        </div>
      </div>

      </div><!-- /prob-nav -->
      <div class="prob-body">
      <!-- 表示切替（1行：出馬表／結果・払戻し＋モードボタン） -->
      <div class="view-switch">
        <a class="on" href="#" id="tab-shutsuba">出馬表</a>
        <a class="disabled" href="#" id="tab-result">結果・払戻し<span class="t-note" id="resultNote">レース後に反映</span></a>
        <span class="mode-btns" id="subTabs">
          <a class="btn-mode on" href="#" id="tab-win">勝利確率</a><a class="btn-mode" href="#" id="tab-fuku">複勝確率</a>
        </span>
      </div>

      <div class="race-info">
        <b>東京3R</b> <b>3歳未勝利</b> ダ1400m <span class="going">重</span>
        <span class="time">発走 11:10</span>
      </div>
      <div class="race-info status-row" style="padding-top:0;">
        <span class="status-badge final" id="statusBadge">確率 最終</span>
        <span class="status-note" id="statusNote">10:40確定</span>
        <span class="status-badge live" id="evBadge"><span class="live-dot"></span>期待値 LIVE</span>
        <span class="status-note" id="evNote">10:55時点・発走まで5分毎更新</span>
        <button class="rf-btn avail" id="evRefresh" title="最新のオッズ・期待値に更新"><svg viewBox="0 0 24 24"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></button>
      </div>

      <div id="viewShutsuba">
      <table class="prob-t" id="probTable">
        <thead>
          <tr>
            <th style="width:34px" id="th-num">馬番<span class="sort">▼</span></th>
            <th style="width:34px" id="th-mark">印<span class="sort idle">▼</span></th>
            <th>馬名（騎手）</th>
            <th style="width:78px" id="th-prob">勝利確率<span class="sort idle">▼</span></th>
            <th style="width:78px" id="th-ev">単勝期待値<span class="sort idle">▼</span><span class="th-live"><span class="live-dot"></span>LIVE</span></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="waku w1">2</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ダノンデサイル<small>牡4／C.ルメール</small></td>
            <td class="rate hi" data-win="28.1%" data-fuku="58.4%">28.1%<i class="diff up">▲</i></td>
            <td class="rate ev hi" data-win="142%" data-fuku="92〜105%" data-hw="1" data-hf="mid">142%</td>
          </tr>
          <tr>
            <td><span class="waku w3">5</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ジャンタルマンタル<small>牡4／川田将雅</small></td>
            <td class="rate hi" data-win="21.1%" data-fuku="52.8%">21.1%<i class="diff down">▼</i></td>
            <td class="rate ev" data-win="96%" data-fuku="88〜101%" data-hw="0" data-hf="mid">96%</td>
          </tr>
          <tr>
            <td><span class="waku w1">1</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">エンブロイダリー<small>牝3／横山武史</small></td>
            <td class="rate" data-win="15.7%" data-fuku="41.1%">15.7%<i class="diff up">▲</i></td>
            <td class="rate ev hi" data-win="163%" data-fuku="104〜128%" data-hw="1" data-hf="hi">163%</td>
          </tr>
          <tr>
            <td><span class="waku w2">3</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">カムニャック<small>牝4／M.デムーロ</small></td>
            <td class="rate" data-win="12.4%" data-fuku="33.2%">12.4%</td>
            <td class="rate ev" data-win="88%" data-fuku="85〜97%" data-hw="0" data-hf="">88%</td>
          </tr>
          <tr>
            <td><span class="waku w2">4</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ウインカーネリアン<small>牡7／三浦皇成</small></td>
            <td class="rate" data-win="8.9%" data-fuku="26.7%">8.9%</td>
            <td class="rate ev" data-win="74%" data-fuku="78〜90%" data-hw="0" data-hf="">74%</td>
          </tr>
          <tr>
            <td><span class="waku w4">8</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">レガレイラ<small>牝4／戸崎圭太</small></td>
            <td class="rate" data-win="4.2%" data-fuku="18.5%">4.2%<i class="diff up">▲</i></td>
            <td class="rate ev hi" data-win="121%" data-fuku="96〜115%" data-hw="1" data-hf="mid">121%</td>
          </tr>
          <tr>
            <td><span class="waku w4">7</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ソールオリエンス<small>牡5／横山和生</small></td>
            <td class="rate" data-win="2.8%" data-fuku="14.2%">2.8%</td>
            <td class="rate ev" data-win="68%" data-fuku="82〜98%" data-hw="0" data-hf="">68%</td>
          </tr>
          <tr>
            <td><span class="waku w3">6</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ベラジオオペラ<small>牡5／横山典弘</small></td>
            <td class="rate" data-win="2.1%" data-fuku="12.6%">2.1%</td>
            <td class="rate ev" data-win="92%" data-fuku="88〜102%" data-hw="0" data-hf="mid">92%</td>
          </tr>
          <tr>
            <td><span class="waku w5">9</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">シックスペンス<small>牡4／松山弘平</small></td>
            <td class="rate" data-win="1.4%" data-fuku="9.8%">1.4%<i class="diff down">▼</i></td>
            <td class="rate ev hi" data-win="110%" data-fuku="95〜110%" data-hw="1" data-hf="mid">110%</td>
          </tr>
          <tr>
            <td><span class="waku w5">10</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ジューンテイク<small>牡4／岩田望来</small></td>
            <td class="rate" data-win="0.9%" data-fuku="7.4%">0.9%</td>
            <td class="rate ev" data-win="55%" data-fuku="70〜84%" data-hw="0" data-hf="">55%</td>
          </tr>
          <tr>
            <td><span class="waku w6">11</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">サンライズアース<small>牡4／池添謙一</small></td>
            <td class="rate" data-win="0.7%" data-fuku="6.2%">0.7%</td>
            <td class="rate ev" data-win="83%" data-fuku="79〜93%" data-hw="0" data-hf="">83%</td>
          </tr>
          <tr>
            <td><span class="waku w6">12</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">メイショウタバル<small>牡4／浜中俊</small></td>
            <td class="rate" data-win="0.6%" data-fuku="5.1%">0.6%</td>
            <td class="rate ev" data-win="47%" data-fuku="60〜75%" data-hw="0" data-hf="">47%</td>
          </tr>
          <tr>
            <td><span class="waku w7">13</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">コスモキュランダ<small>牡5／丹内祐次</small></td>
            <td class="rate" data-win="0.4%" data-fuku="3.9%">0.4%</td>
            <td class="rate ev" data-win="62%" data-fuku="72〜88%" data-hw="0" data-hf="">62%</td>
          </tr>
          <tr>
            <td><span class="waku w7">14</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">アーバンシック<small>牡4／武豊</small></td>
            <td class="rate" data-win="0.3%" data-fuku="3.2%">0.3%</td>
            <td class="rate ev" data-win="71%" data-fuku="65〜80%" data-hw="0" data-hf="">71%</td>
          </tr>
          <tr>
            <td><span class="waku w8">15</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">ダノンエアズロック<small>牡4／坂井瑠星</small></td>
            <td class="rate" data-win="0.2%" data-fuku="2.4%">0.2%</td>
            <td class="rate ev" data-win="39%" data-fuku="50〜66%" data-hw="0" data-hf="">39%</td>
          </tr>
          <tr>
            <td><span class="waku w8">16</span></td>
            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
            <td class="name">シュガークン<small>牡4／田辺裕信</small></td>
            <td class="rate" data-win="0.1%" data-fuku="1.6%">0.1%</td>
            <td class="rate ev" data-win="28%" data-fuku="42〜58%" data-hw="0" data-hf="">28%</td>
          </tr>
        </tbody>
      </table>
      <p class="prob-note">※ 好走確率は前日公開の値から、<b style="color:var(--text)">発走約30分前にオッズを取り込んで最終補正され確定</b>します（「最終」横の時刻＝確定時刻／▲▼は公開時の値からの変動）。的中実績・ランキングの集計は最終（確定）の値を使用。期待値は発走まで5分毎に更新。複勝期待値は複勝オッズの変動幅に対応したレンジ表示（赤＝下限でも100%超／橙＝上限で100%超）。印＝あなたの予想印（タップで順送り・長押しで一覧から選択）。</p>

      <a class="btn-sim" href="EvalMode_シミュレーション_SP_v1.html" id="simBtn">的中確率シミュレーションをする ▶</a>
      </div><!-- /viewShutsuba -->

      <!-- ===== 結果・払戻しビュー（結果取得後のみ機能） ===== -->
      <div id="viewResult" hidden>
      <table class="prob-t result-t">
        <thead>
          <tr>
            <th style="width:34px">着順</th>
            <th style="width:34px">馬番</th>
            <th style="width:34px">印</th>
            <th>馬名</th>
            <th style="width:34px">人気</th>
            <th style="width:40px">確率<br>順位</th>
            <th style="width:52px">単勝<br>期待値</th>
            <th style="width:52px">複勝<br>期待値</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="chaku c1">1</span></td>
            <td><span class="waku w1">1</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">エンブロイダリー</td>
            <td>5</td>
            <td><span class="prank p3">3</span></td>
            <td class="rate hi">163%</td>
            <td class="rate hi">121%</td>
          </tr>
          <tr>
            <td><span class="chaku c2">2</span></td>
            <td><span class="waku w1">2</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ダノンデサイル</td>
            <td>1</td>
            <td><span class="prank p1">1</span></td>
            <td class="rate hi">142%</td>
            <td class="rate">98%</td>
          </tr>
          <tr>
            <td><span class="chaku c3">3</span></td>
            <td><span class="waku w4">8</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">レガレイラ</td>
            <td>8</td>
            <td>6</td>
            <td class="rate hi">121%</td>
            <td class="rate hi">108%</td>
          </tr>
          <tr>
            <td><span class="chaku">4</span></td>
            <td><span class="waku w3">5</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ジャンタルマンタル</td>
            <td>2</td>
            <td><span class="prank p2">2</span></td>
            <td class="rate">96%</td>
            <td class="rate">95%</td>
          </tr>
          <tr>
            <td><span class="chaku">5</span></td>
            <td><span class="waku w2">3</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">カムニャック</td>
            <td>3</td>
            <td><span class="prank p4">4</span></td>
            <td class="rate">88%</td>
            <td class="rate">90%</td>
          </tr>
          <tr>
            <td><span class="chaku">6</span></td>
            <td><span class="waku w3">6</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ベラジオオペラ</td>
            <td>6</td>
            <td>8</td>
            <td class="rate">92%</td>
            <td class="rate">93%</td>
          </tr>
          <tr>
            <td><span class="chaku">7</span></td>
            <td><span class="waku w2">4</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ウインカーネリアン</td>
            <td>4</td>
            <td><span class="prank p5">5</span></td>
            <td class="rate">74%</td>
            <td class="rate">84%</td>
          </tr>
          <tr>
            <td><span class="chaku">8</span></td>
            <td><span class="waku w5">9</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">シックスペンス</td>
            <td>7</td>
            <td>9</td>
            <td class="rate hi">110%</td>
            <td class="rate hi">101%</td>
          </tr>
          <tr>
            <td><span class="chaku">9</span></td>
            <td><span class="waku w4">7</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ソールオリエンス</td>
            <td>9</td>
            <td>7</td>
            <td class="rate">68%</td>
            <td class="rate">88%</td>
          </tr>
          <tr>
            <td><span class="chaku">10</span></td>
            <td><span class="waku w6">11</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">サンライズアース</td>
            <td>11</td>
            <td>11</td>
            <td class="rate">83%</td>
            <td class="rate">86%</td>
          </tr>
          <tr>
            <td><span class="chaku">11</span></td>
            <td><span class="waku w5">10</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ジューンテイク</td>
            <td>10</td>
            <td>10</td>
            <td class="rate">55%</td>
            <td class="rate">76%</td>
          </tr>
          <tr>
            <td><span class="chaku">12</span></td>
            <td><span class="waku w7">13</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">コスモキュランダ</td>
            <td>13</td>
            <td>13</td>
            <td class="rate">62%</td>
            <td class="rate">79%</td>
          </tr>
          <tr>
            <td><span class="chaku">13</span></td>
            <td><span class="waku w6">12</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">メイショウタバル</td>
            <td>12</td>
            <td>12</td>
            <td class="rate">47%</td>
            <td class="rate">66%</td>
          </tr>
          <tr>
            <td><span class="chaku">14</span></td>
            <td><span class="waku w7">14</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">アーバンシック</td>
            <td>14</td>
            <td>14</td>
            <td class="rate">71%</td>
            <td class="rate">72%</td>
          </tr>
          <tr>
            <td><span class="chaku">15</span></td>
            <td><span class="waku w8">16</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">シュガークン</td>
            <td>16</td>
            <td>16</td>
            <td class="rate">28%</td>
            <td class="rate">48%</td>
          </tr>
          <tr>
            <td><span class="chaku">16</span></td>
            <td><span class="waku w8">15</span></td>
            <td><span class="r-mark" data-mark="">−</span></td>
            <td class="name">ダノンエアズロック</td>
            <td>15</td>
            <td>15</td>
            <td class="rate">39%</td>
            <td class="rate">55%</td>
          </tr>
        </tbody>
      </table>
      <p class="prob-note">※ 人気＝最終単勝人気／確率順位＝Eval好走確率の順位／印＝出馬表で入力したあなたの予想印。期待値は確定オッズに基づく最終値。</p>

      <div class="payout">
        <div class="p-head">配当</div>
        <table>
          <tr><td class="k">単勝</td><td class="c">1</td><td class="y">1,630円</td></tr>
          <tr><td class="k">複勝</td><td class="c">1<br>2<br>8</td><td class="y">410円<br>150円<br>890円</td></tr>
          <tr><td class="k">馬連</td><td class="c">1-2</td><td class="y">2,340円</td></tr>
          <tr><td class="k">ワイド</td><td class="c">1-2<br>1-8<br>2-8</td><td class="y">780円<br>3,120円<br>1,540円</td></tr>
          <tr><td class="k">馬単</td><td class="c">1→2</td><td class="y">5,120円</td></tr>
          <tr><td class="k">3連複</td><td class="c">1-2-8</td><td class="y">12,480円</td></tr>
          <tr><td class="k">3連単</td><td class="c">1→2→8</td><td class="y">68,930円</td></tr>
        </table>
      </div>

      </div><!-- /viewResult -->
      </div><!-- /prob-body -->

    </div>
  </section>

  <div class="pc-side"><!-- PC：右サイドカラム -->

  <!-- ===== 本日の攻略情報（圧縮版／詳細・一覧は攻略ツールページへ） ===== -->
  <section>
    <div class="sec-title"><h2>本日の攻略情報</h2><span class="en">TODAY'S PICKS</span><a class="right" href="EvalMode_攻略ツール_SP_v2_グリーン.html">一覧 ▶</a></div>
    <div class="goods">
      <a href="EvalMode_商品詳細_SP_v1.html">
        <span class="g-title">🐎 本日の推奨馬リスト<span class="new">NEW</span></span>
        <span class="g-price">500pt</span>
      </a>
      <a href="EvalMode_商品詳細_SP_v1.html">
        <span class="g-title">⚠️ 本日の前走不利情報<span class="new">NEW</span></span>
        <span class="g-price">300pt</span>
      </a>
      <a href="EvalMode_商品詳細_SP_v1.html">
        <span class="g-title">📈 本日のラップ分析情報<span class="new">NEW</span></span>
        <span class="g-price">300pt</span>
      </a>
    </div>
  </section>

  <!-- ===== アンバサダー（2026-10-01）：アイコン＋名前の横スクロール。タップでアンバサダーページの該当者の位置へ ===== -->
  <section class="amb-top">
    <div class="sec-title"><h2>アンバサダー</h2><span class="en">AMBASSADOR</span><a class="right" href="EvalMode_アンバサダー_SP_v1.html">一覧 ▶</a></div>
    <ul class="amb-strip" id="ambStrip"></ul>
  </section>

  <!-- ===== トピックス／速報／お知らせ ===== -->
  <section id="topicsModule">
    <div class="sec-title"><h2>トピックス</h2><span class="en">TOPICS</span></div>
    <div class="c-tabs">
      <a class="on" href="#" data-tab="topics">トピックス</a>
      <a href="#" data-tab="flash">速報</a>
      <a href="#" data-tab="info">お知らせ</a>
    </div>
    <ul class="card-list" id="list-topics">
      <li class="card"><a href="EvalMode_トピックス_SP_v1.html">
        <div class="thumb th-navy">WIN5<br>確率構成</div>
        <div class="body">
          <h3>【WIN5攻略】Eval確率上位×点数最適化で組む今週の構成案 対象5レースの好走確率を全公開</h3>
          <div class="meta"><span class="badge b-sub">サブスク限定</span>2026-07-13 18:00 <span class="b-new">NEW</span><span class="like">♡ 34</span></div>
        </div>
      </a></li>
      <li class="card"><a href="EvalMode_トピックス_SP_v1.html">
        <div class="thumb th-red">危険な<br>人気馬</div>
        <div class="body">
          <h3>【東京11R／危険な人気馬】想定1人気も複勝率26.7%止まり 確率が示す“過剰人気”の根拠とは</h3>
          <div class="meta"><span class="badge b-pt">300pt</span>2026-07-13 17:30 <span class="b-new">NEW</span><span class="like">♡ 21</span></div>
        </div>
      </a></li>
      <li class="card"><a href="EvalMode_トピックス_SP_v1.html">
        <div class="thumb th-green">ラップ<br>推奨</div>
        <div class="body">
          <h3>【ラップ推奨／ユリス】前半3F想定から導く展開利 本日の該当馬を開催別に一覧公開</h3>
          <div class="meta"><span class="badge b-pt">300pt</span>2026-07-13 12:00<span class="like">♡ 18</span></div>
        </div>
      </a></li>
      <li class="card"><a href="EvalMode_トピックス_SP_v1.html">
        <div class="thumb th-gray">馬具<br>情報</div>
        <div class="body">
          <h3>【本日の馬具情報】初ブリンカー・チークピーシズ変更馬まとめ 確率上昇シグナルの見方も解説</h3>
          <div class="meta"><span class="badge b-free">無料</span>2026-07-12 17:00<span class="like">♡ 45</span></div>
        </div>
      </a></li>
    </ul>

    <ul class="info-list" id="list-flash" hidden>
      <li><a href="EvalMode_的中実績_SP_v3.html"><span class="i-txt">Eval、日・函館11R 3連複72.4倍<b style="color:var(--sig)">【8万6880円】</b>的中！</span><span class="date">7/12 15:42</span></a></li>
      <li><a href="EvalMode_的中実績_SP_v3.html"><span class="i-txt">【WIN5】Eval確率上位で構成、払戻<b style="color:var(--sig)">【42万7100円】</b>！</span><span class="date">7/12 17:05</span></a></li>
      <li><a href="EvalMode_トピックス_SP_v1.html"><span class="i-txt">好走確率50%超・Sランク馬、東京9Rで1着→単勝460円</span><span class="date">7/12 14:20</span></a></li>
      <li><a href="EvalMode_トピックス_SP_v1.html"><span class="i-txt">【New】本日の馬具情報を公開しました（ポイント）</span><span class="date">7/14 09:00</span></a></li>
      <li><a href="EvalMode_的中実績_SP_v3.html"><span class="i-txt">Eval、大井最終 馬単38.2倍<b style="color:var(--sig)">【3万8200円】</b>的中！</span><span class="date">7/11 20:48</span></a></li>
    </ul>

    <ul class="info-list" id="list-info" hidden>
      <li><a href="EvalMode_トピックス_SP_v1.html"><span class="i-txt">【News】地方競馬（門別・高知・大井）の確率提供を開始しました</span><span class="date">07/10</span></a></li>
      <li><a href="EvalMode_トピックス_SP_v1.html"><span class="i-txt">【Info】ポイントの有効期限にご注意ください</span><span class="date">07/01</span></a></li>
      <li><a href="EvalMode_トピックス_SP_v1.html"><span class="i-txt">【お知らせ】アプリのプッシュ通知設定について</span><span class="date">06/24</span></a></li>
    </ul>

    <a class="read-more" href="EvalMode_トピックス_SP_v1.html" id="readMore">Read More...</a>
  </section>



  <!-- ===== 会員状態別CTA（実装時はログイン状態で出し分け／以下はモック確認用切替付き） ===== -->
  <div class="demo-switch">
    <span class="d-lbl">モック確認用：</span>
    <button class="on" data-state="guest">非ログイン</button>
    <button data-state="free">無料会員</button>
    <button data-state="subsc">サブスク会員</button>
    <button id="a2hsReset">ホーム追加バナー再表示</button>
    <button id="modalReplay">モーダル再生（選択中の状態）</button>
  </div>
  <div class="member-cta">
    <!-- 非ログイン：無料登録に一本化 -->
    <div class="panel cta-guest" id="cta-guest">
      <p class="lead">全出走馬の好走確率と<br><span style="white-space:nowrap">リアルタイム期待値（LIVE期待値）</span><br><b>無料登録</b>ですべて見られます</p>
      <p class="sub">メールアドレスだけ・30秒で完了</p>
      <a class="btn" href="EvalMode_会員登録ログイン_SP_v1.html">今すぐ無料登録 ▶</a>
      <a class="login" href="EvalMode_会員登録ログイン_SP_v1.html">アカウントをお持ちの方はログイン ▶</a>
    </div>
    <!-- 無料会員：サブスク訴求 -->
    <div class="panel cta-free" id="cta-free" hidden>
      <span class="offer">初月無料</span>
      <p class="lead">今日の<b>最終確率とLIVE期待値</b>、<br>全レース分を見逃していませんか？</p>
      <p class="price">サブスクなら全レース・全機能が使い放題　<b>月額2,980円</b><small>（税込・価格は仮）</small></p>
      <a class="btn" href="EvalMode_サブスク申込_SP_v2.html">初月無料でサブスクを始める ▶</a>
      <a class="pt-link" href="EvalMode_ポイント購入_SP_v1.html">ポイント購入はこちら（攻略情報の単品購入に）</a>
    </div>
    <!-- サブスク会員：ポイント導線のみ -->
    <div class="panel cta-subsc" id="cta-subsc" hidden>
      <span class="p-coin">P</span>
      <p class="txt"><b>本日の攻略情報</b>はポイントで単品購入できます（残高 7,777pt）</p>
      <a class="btn" href="EvalMode_ポイント購入_SP_v1.html">ポイント購入 ▶</a>
    </div>
  </div>
  </div><!-- /pc-side -->
  </div><!-- /pc-body -->

  <!-- ===== Footer ===== -->
  <footer>
    <p class="f-logo">Eval<span> MODE</span></p>
    <nav>
      <a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">運営会社</a><a href="EvalMode_トピックス_SP_v1.html">お知らせ</a><a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">よくある質問・料金プラン</a>
      <a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">プライバシーポリシー</a><a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">利用規約</a><a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">特定商取引法に基づく表記</a>
      <a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">お問い合わせ</a><a href="EvalMode_静的ページ共通テンプレ_SP_v1.html">サイトマップ</a>
    </nav>
    <p class="copy">© 2026 Eval MODE</p>
  </footer>

  <!-- 会員登録バナー（下部ナビ上に追従／非ログイン状態のみ表示）※2026-09-30 トンマナ統一版 -->
  <div class="reg-banner" id="regBanner">
    <span class="rb-ico"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
    <div class="rb-text">
      <p class="rb-main">無料会員登録でもっと便利に</p>
      <p class="rb-sub">登録だけで<b>各種無料参考情報</b>が利用可能に</p>
    </div>
    <a class="rb-btn" href="EvalMode_会員登録ログイン_SP_v1.html">無料登録</a>
    <span class="close" id="regClose" aria-label="閉じる">×</span>
  </div>

  <!-- 予想印ピッカー（印セルの長押しで表示） -->
  <div class="mark-sheet-overlay hidden" id="markSheetOverlay">
    <div class="mark-sheet">
      <p class="ms-title" id="msTitle"></p>
      <div class="ms-grid" id="msGrid">
        <button data-mark="◉">◉</button>
        <button data-mark="◎">◎</button>
        <button data-mark="◯">◯</button>
        <button data-mark="▲">▲</button>
        <button data-mark="☆">☆</button>
        <button data-mark="△">△</button>
        <button data-mark="×">×</button>
        <button data-mark="消">消</button>
        <button data-mark="" class="clear">クリア</button>
      </div>
    </div>
  </div>


</div>
{literal}
<script>
  // 勝利確率／複勝確率タブの表示切替（モック動作）
  const tabWin  = document.getElementById('tab-win');
  const tabFuku = document.getElementById('tab-fuku');
  const thProb  = document.getElementById('th-prob');
  const thEv    = document.getElementById('th-ev');
  const cells   = document.querySelectorAll('#probTable tbody td.rate');

  // 現在の表示モードとソート状態
  let curMode = 'win';
  let sortCol = 'num';    // 'num' | 'mark' | 'prob' | 'ev'（デフォルトは馬番順）
  let sortDir = 'desc';   // 'desc'＝各列の自然順（馬番1→・印◉→・確率高→）／'asc'＝その反転

  const thMark = document.getElementById('th-mark');
  const thNum  = document.getElementById('th-num');

  function headerHTML(){
    // アクティブ列＝濃い▼/▲、非アクティブ列＝薄い▼を常時表示（ソート可能の視覚的示唆）
    const ar = col => sortCol === col
      ? '<span class="sort">' + (sortDir === 'desc' ? '▼' : '▲') + '</span>'
      : '<span class="sort idle">▼</span>';
    thNum.innerHTML  = '馬番' + ar('num');
    thMark.innerHTML = '印' + ar('mark');
    thProb.innerHTML = (curMode === 'win' ? '勝利確率' : '複勝確率') + ar('prob');
    thEv.innerHTML = (curMode === 'win' ? '単勝期待値' : '複勝期待値')
      + ar('ev')
      + '<span class="th-live"><span class="live-dot"></span>LIVE</span>';
  }

  // 印の優先順位：◉8＞◎7＞◯6＞▲5＞☆4＞△3＞×2＞無印1＞消0
  // （無印は「判断なし」なので、能動的に切った消より上に置く）
  const MARK_CYCLE = ['','◉','◎','◯','▲','☆','△','×','消'];
  const markPrio = m => m === '消' ? 0 : m === '' ? 1 : 9 - MARK_CYCLE.indexOf(m);

  function sortRows(){
    const tbody = probTable.querySelector('tbody');
    const trs = Array.from(tbody.querySelectorAll('tr'));
    const probKey = tr => parseFloat(tr.querySelectorAll('td.rate')[0].dataset[curMode]);
    const key = tr => {
      if(sortCol === 'num')  return -parseInt(tr.querySelector('.waku').textContent, 10); // ▼（自然順）＝馬番1→16
      if(sortCol === 'mark') return markPrio(tr.querySelector('.mark-btn').dataset.mark || '');
      const tds = tr.querySelectorAll('td.rate');
      const td = (sortCol === 'prob') ? tds[0] : tds[1];
      return parseFloat(td.dataset[curMode]); // レンジ値は下限で比較
    };
    trs.sort((a,b) => {
      const d = sortDir === 'desc' ? key(b)-key(a) : key(a)-key(b);
      return d || probKey(b)-probKey(a); // 同値（同印）内は確率降順で安定
    });
    trs.forEach(tr => tbody.appendChild(tr));
  }

  function switchMode(mode){
    curMode = mode;
    if(mode === 'win'){ tabWin.classList.add('on'); tabFuku.classList.remove('on'); }
    else{ tabFuku.classList.add('on'); tabWin.classList.remove('on'); }
    cells.forEach(td => {
      const isEv = td.classList.contains('ev');
      td.textContent = td.dataset[mode === 'win' ? 'win' : 'fuku'];
      if(!isEv) return;
      td.classList.remove('hi','mid','range');
      if(mode === 'win'){
        if(td.dataset.hw === '1') td.classList.add('hi');
      }else{
        td.classList.add('range');
        if(td.dataset.hf) td.classList.add(td.dataset.hf);
      }
    });
    headerHTML(); sortRows();
  }
  tabWin.addEventListener('click',  e => { e.preventDefault(); switchMode('win');  });
  tabFuku.addEventListener('click', e => { e.preventDefault(); switchMode('fuku'); });

  // 見出しタップでソート（同列タップで昇順⇄降順を切替）
  thProb.classList.add('sortable'); thEv.classList.add('sortable');
  function toggleSort(col){
    if(sortCol === col){ sortDir = (sortDir === 'desc') ? 'asc' : 'desc'; }
    else{ sortCol = col; sortDir = 'desc'; }
    headerHTML(); sortRows();
  }
  thProb.addEventListener('click', () => toggleSort('prob'));
  thEv.addEventListener('click',  () => toggleSort('ev'));
  thMark.classList.add('sortable');
  thMark.addEventListener('click', () => toggleSort('mark'));
  thNum.classList.add('sortable');
  thNum.addEventListener('click', () => toggleSort('num'));

  // ===== 予想印：タップ順送り／長押しピッカー（モック：入力の保存なし） =====
  // 印ソート中でも入力直後に行を並べ替えない（指の下で行が動くのを防ぐ。再ソートは見出しタップで）
  function setMark(btn, m){
    btn.dataset.mark = m;
    btn.textContent = m;
    btn.closest('tr').classList.toggle('h-del', m === '消');
  }
  const msOverlay = document.getElementById('markSheetOverlay');
  const msTitle   = document.getElementById('msTitle');
  let pickerTarget = null, lpTimer = null, lpFired = false;
  function openMarkSheet(btn){
    pickerTarget = btn;
    const name = btn.closest('tr').querySelector('td.name').firstChild.textContent.trim();
    msTitle.innerHTML = '<b>' + name + '</b>の印を選択';
    document.querySelectorAll('#msGrid button').forEach(b =>
      b.classList.toggle('on', (b.dataset.mark || '') === (btn.dataset.mark || '')));
    msOverlay.classList.remove('hidden');
  }
  document.querySelectorAll('#probTable .mark-btn').forEach(btn => {
    btn.addEventListener('contextmenu', e => e.preventDefault());
    btn.addEventListener('pointerdown', () => {
      lpFired = false;
      lpTimer = setTimeout(() => { lpFired = true; openMarkSheet(btn); }, 450);
    });
    ['pointerup','pointerleave','pointercancel'].forEach(ev =>
      btn.addEventListener(ev, () => clearTimeout(lpTimer)));
    btn.addEventListener('click', e => {
      e.preventDefault();
      if(lpFired){ lpFired = false; return; } // 長押し後のclickは無視
      const i = MARK_CYCLE.indexOf(btn.dataset.mark || '');
      setMark(btn, MARK_CYCLE[(i + 1) % MARK_CYCLE.length]);
    });
  });
  document.querySelectorAll('#msGrid button').forEach(b =>
    b.addEventListener('click', () => {
      if(pickerTarget) setMark(pickerTarget, b.dataset.mark || '');
      msOverlay.classList.add('hidden');
    }));
  msOverlay.addEventListener('click', e => { if(e.target === msOverlay) msOverlay.classList.add('hidden'); });

  // 結果ビューへ印を同期（馬名キー・表示専用）
  function syncResultMarks(){
    const map = {};
    document.querySelectorAll('#probTable tbody tr').forEach(tr => {
      map[tr.querySelector('td.name').firstChild.textContent.trim()] =
        tr.querySelector('.mark-btn').dataset.mark || '';
    });
    document.querySelectorAll('#viewResult .result-t tbody tr').forEach(tr => {
      const m = map[tr.querySelector('td.name').textContent.trim()] ?? '';
      const sp = tr.querySelector('.r-mark');
      sp.dataset.mark = m;
      sp.textContent = m || '−';
      tr.classList.toggle('h-del', m === '消');
    });
  }

  // 出馬表／結果・払戻し ビュー切替（結果は取得後のみ有効）
  const tabShutsuba = document.getElementById('tab-shutsuba');
  const tabResult   = document.getElementById('tab-result');
  const subTabs     = document.getElementById('subTabs');
  const viewShutsuba= document.getElementById('viewShutsuba');
  const viewResult  = document.getElementById('viewResult');
  function showView(v){
    const isRes = (v === 'result');
    tabShutsuba.classList.toggle('on', !isRes);
    tabResult.classList.toggle('on', isRes);
    // 勝利確率／複勝確率は出馬表内のみ機能：結果表示中は減光＋タップ不可
    // ※旧実装の hidden 属性は .mode-btns の display:flex（author CSS）に負けて効かないため class 制御に変更
    subTabs.classList.toggle('disabled', isRes);
    viewShutsuba.hidden = isRes;
    viewResult.hidden = !isRes;
    if(isRes){
      syncResultMarks(); // 出馬表で入力した予想印を結果ビューに反映
    }
  }
  tabShutsuba.addEventListener('click', e => { e.preventDefault(); showView('shutsuba'); });
  // ※モック確認用：結果・払戻しタップでレース状態も「結果確定」に同期
  //   実装時は結果取得前タップ不可（disabled）のまま
  tabResult.addEventListener('click', e => {
    e.preventDefault();
    stateIdx = 3; applyState(stateIdx);
    showView('result');
  });

  // 暫定／最終ステータス（モック：バッジタップで切替。実装時はサーバ側状態で固定）
  // 暫定＝初期データ／最終＝発走約30分前にオッズ取得→好走確率を補正し期待値を再計算
  const statusBadge = document.getElementById('statusBadge');
  const statusNote  = document.getElementById('statusNote');
  const probTable   = document.getElementById('probTable');
  // レースは「暫定 → 最終 → 発売締切 → 結果確定」の4状態を持つステートマシン
  // 期待値のLIVE更新（5分毎オッズ取得）は 暫定〜締切前 の間のみ稼働
  // ※モック：確率バッジのタップで4状態をローテーション。実装時はサーバ側状態で固定
  const evBadge = document.getElementById('evBadge');
  const evNote  = document.getElementById('evNote');
  const simBtn  = document.getElementById('simBtn');
  const states = [
    { key:'provisional', // 〜発走約30分前（確率バッジは出さない。予告文言のみ）
      prob:{cls:'prov', txt:'', note:'発走約30分前に最終補正されます'},
      ev:{cls:'live', txt:'<span class="live-dot"></span>期待値 LIVE', note:'10:25時点・5分毎更新'},
      tableCls:'provisional',
      btn:{cls:'', txt:'的中確率シミュレーションをする ▶'} },
    { key:'final', // 30分前〜発売締切（2026-10-01確定：好走確率は発走約30分前の最終補正で確定し、以降は変動しない。注記は確定時刻）
      prob:{cls:'final', txt:'確率 最終', note:'10:40確定'},
      ev:{cls:'live', txt:'<span class="live-dot"></span>期待値 LIVE', note:'10:55時点・発走まで5分毎更新'},
      tableCls:'',
      btn:{cls:'', txt:'的中確率シミュレーションをする ▶'} },
    { key:'closed', // 発売締切〜結果確定：数値は最終取得値で凍結
      prob:{cls:'final', txt:'確率 最終', note:'10:40確定'},
      ev:{cls:'closed', txt:'発売締切', note:'最終取得値・11:08時点'},
      tableCls:'closed',
      btn:{cls:'closed', txt:'発売締切（結果待ち）'} },
    { key:'result', // 結果確定後：答え合わせモードへ
      prob:{cls:'final', txt:'確率 最終', note:'10:40確定'},
      ev:{cls:'result', txt:'結果確定', note:''},
      tableCls:'closed',
      btn:{cls:'result', txt:'結果・払戻しを見る ▶'} },
  ];
  let stateIdx = 1; // 初期表示は「最終」
  function applyState(i){
    const st = states[i];
    statusBadge.className = 'status-badge ' + st.prob.cls;
    statusBadge.textContent = st.prob.txt;
    statusBadge.style.display = st.prob.txt ? '' : 'none'; // 補正前はバッジ自体を出さない
    statusNote.textContent = st.prob.note;
    evBadge.className = 'status-badge ' + st.ev.cls;
    evBadge.innerHTML = st.ev.txt;
    evNote.textContent = st.ev.note;
    probTable.className = 'prob-t' + (st.tableCls ? ' ' + st.tableCls : '');
    simBtn.className = 'btn-sim' + (st.btn.cls ? ' ' + st.btn.cls : '');
    simBtn.textContent = st.btn.txt;
    // 結果・払戻しタブは「結果確定」状態でのみ有効
    const isResult = (st.key === 'result');
    const wasDisabled = tabResult.classList.contains('disabled');
    tabResult.classList.toggle('disabled', !isResult);
    const note = document.getElementById('resultNote');
    if(isResult){
      // 取得後：注記を非表示にし「結果・払戻し」1行を縦中央に。テキストは色付きで有効を明示
      // NEWバッジは付けない（到着の合図は1回きりのフラッシュのみ）
      tabResult.classList.add('ready');
      note.hidden = true;
      if(wasDisabled){
        tabResult.classList.remove('flash');
        void tabResult.offsetWidth; // reflow でアニメ再発火
        tabResult.classList.add('flash');
      }
    }else{
      // 取得前：グレー無効＋理由注記
      tabResult.classList.remove('ready');
      note.hidden = false;
      showView('shutsuba');
    }
    simBtn.onclick = isResult ? (e => { e.preventDefault(); showView('result'); }) : null;
    // 更新ボタンは期待値LIVE中（補正前〜最終）のみ表示。締切・結果確定後は凍結のため非表示
    document.getElementById('evRefresh').style.display = (st.ev.cls === 'live') ? '' : 'none';
  }
  applyState(stateIdx);

  // 初期表示：馬番順（1→16・▼）で並べる ※probTable定義後に実行すること
  headerHTML(); sortRows();

  statusBadge.addEventListener('click', () => {
    stateIdx = (stateIdx + 1) % states.length;
    applyState(stateIdx);
  });
  // モック確認用：補正前はバッジが無いため、確率の注記タップでも状態ローテできるようにする
  statusNote.style.cursor = 'pointer';
  statusNote.addEventListener('click', () => {
    stateIdx = (stateIdx + 1) % states.length;
    applyState(stateIdx);
  });

  // 手動更新（モック）：⟳タップで最新オッズ取得を再現。
  // 並び順・スクロール位置は維持し、変化した期待値セルだけフラッシュ（5-3原則）
  const evRefresh = document.getElementById('evRefresh');
  evRefresh.addEventListener('click', () => {
    evRefresh.classList.add('spin');
    setTimeout(() => evRefresh.classList.remove('spin'), 650);
    const targets = ['ダノンデサイル', 'ジャンタルマンタル'];
    const deltas = [4, -3];
    document.querySelectorAll('#probTable tbody tr').forEach(tr => {
      const name = tr.querySelector('td.name').firstChild.textContent.trim();
      const idx = targets.indexOf(name);
      if(idx < 0) return;
      const td = tr.querySelectorAll('td.rate')[1];
      const next = parseFloat(td.dataset.win) + deltas[idx];
      td.dataset.win = next + '%';
      if(curMode === 'win') td.textContent = next + '%';
      td.classList.remove('flash'); void td.offsetWidth; td.classList.add('flash');
    });
    evNote.textContent = '11:00時点・発走まで5分毎更新';
    evRefresh.classList.remove('avail'); // 取得直後は消灯（実装時は新データ検知で再点灯）
  });

  // 速報ティッカー：1行ローテーション（4秒間隔）
  const items = document.querySelectorAll('#ticker a');
  let ti = 0;
  setInterval(() => {
    items[ti].classList.remove('show');
    ti = (ti + 1) % items.length;
    items[ti].classList.add('show');
  }, 4000);

  // トピックス／アンバサダー／お知らせ タブ切替＋Read More遷移先連動
  const cTabs = document.querySelectorAll('.c-tabs a');
  const lists = {
    topics: document.getElementById('list-topics'),
    flash:  document.getElementById('list-flash'),
    info:   document.getElementById('list-info'),
  };
  const readMore = document.getElementById('readMore');
  const readMoreHref = { topics:'EvalMode_トピックス_SP_v1.html', flash:'EvalMode_トピックス_SP_v1.html', info:'EvalMode_トピックス_SP_v1.html' }; // 実装時は /topics /flash /information に差し替え
  cTabs.forEach(tab => tab.addEventListener('click', e => {
    e.preventDefault();
    cTabs.forEach(t => t.classList.remove('on'));
    tab.classList.add('on');
    const key = tab.dataset.tab;
    Object.entries(lists).forEach(([k, el]) => { el.hidden = (k !== key); });
    readMore.setAttribute('href', readMoreHref[key]); // タップで各一覧ページへ
  }));

  // 速報ティッカーの「一覧 ▶」→ トピックスモジュールの速報タブへ（受け皿）
  document.getElementById('flashMore').addEventListener('click', e => {
    e.preventDefault();
    const flashTab = document.querySelector('.c-tabs a[data-tab="flash"]');
    flashTab.click();
    document.getElementById('topicsModule').scrollIntoView({behavior:'smooth'});
  });

  // 会員状態別CTA（モック確認用スイッチ。実装時はログイン状態で出し分け）
  const ctaPanels = { guest:document.getElementById('cta-guest'), free:document.getElementById('cta-free'), subsc:document.getElementById('cta-subsc') };
  document.querySelectorAll('.demo-switch button[data-state]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.demo-switch button[data-state]').forEach(b => b.classList.remove('on'));
      btn.classList.add('on');
      Object.entries(ctaPanels).forEach(([k, el]) => { el.hidden = (k !== btn.dataset.state); });
      // 非ログイン時のみライム追従バナーを表示（連動デモ）
      const rb = document.getElementById('regBanner');
      if(rb) rb.classList.toggle('hidden', btn.dataset.state !== 'guest');
    });
  });

  // ハンバーガードロワー：アカウント欄はモック確認用スイッチの会員状態と連動
  const drawerOverlay = document.getElementById('drawerOverlay');
  const accPanels = { guest:'d-acc-guest', free:'d-acc-free', subsc:'d-acc-subsc' };
  document.getElementById('menuBtn').addEventListener('click', () => {
    const st = document.querySelector('.demo-switch button[data-state].on')?.dataset.state || 'guest';
    Object.entries(accPanels).forEach(([k, id]) =>
      document.getElementById(id).hidden = (k !== st));
    drawerOverlay.classList.remove('hidden');
  });
  document.getElementById('drawerClose').addEventListener('click', () => drawerOverlay.classList.add('hidden'));
  drawerOverlay.addEventListener('click', e => { if(e.target === drawerOverlay) drawerOverlay.classList.add('hidden'); });

  // 会員登録追従バナー：×で非表示（実装時は非ログイン判定で出し分け）
  document.getElementById('regClose').addEventListener('click', () =>
    document.getElementById('regBanner').classList.add('hidden'));

  // ===== ホーム画面追加バナー（PWA／A2HS） =====
  // 判定順：①standalone起動（アプリとして開いている）→非表示
  //         ②追加済みを判定できる→非表示（appinstalledイベントの記録／Android Chromeの getInstalledRelatedApps／
  //           実装時はログインユーザーの「standalone起動履歴あり」フラグをサーバ保存し、ブラウザ側でも参照）
  //         ③判定不能（iOS Safari等）→✕で閉じた日時を保存し、7日経過で再表示
  const A2HS_SNOOZE_DAYS = 7;               // 再表示までの日数（バックエンド設定値に置換可）
  const A2HS_KEY = 'evm_a2hs_dismissed_at';
  const A2HS_INSTALLED = 'evm_a2hs_installed';
  const a2hs = document.getElementById('a2hs');
  const lsGet = k => { try{ return localStorage.getItem(k); }catch(e){ return null; } };
  const lsSet = (k, v) => { try{ localStorage.setItem(k, v); }catch(e){} };
  const lsDel = k => { try{ localStorage.removeItem(k); }catch(e){} };
  const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  async function isInstalled(){
    if(lsGet(A2HS_INSTALLED)) return true;
    try{
      if(navigator.getInstalledRelatedApps){
        const apps = await navigator.getInstalledRelatedApps();
        if(apps && apps.length) return true;
      }
    }catch(e){}
    return false;
  }
  function snoozed(){
    const t = parseInt(lsGet(A2HS_KEY) || '0', 10);
    return t && (Date.now() - t) < A2HS_SNOOZE_DAYS * 864e5;
  }
  async function initA2hs(){
    if(isStandalone()) return;            // ① アプリで開いている
    if(await isInstalled()) return;       // ② 追加済みと判定
    if(snoozed()) return;                 // ③ 7日以内に✕済み
    a2hs.hidden = false;
  }
  window.addEventListener('appinstalled', () => { lsSet(A2HS_INSTALLED, '1'); a2hs.hidden = true; });
  document.getElementById('a2hsClose').addEventListener('click', e => {
    e.stopPropagation();
    lsSet(A2HS_KEY, String(Date.now()));
    a2hs.hidden = true;
  });
  a2hs.addEventListener('click', () => { location.href = 'EvalMode_ホーム画面追加ガイド_SP_v1.html'; });
  document.getElementById('a2hsReset').addEventListener('click', () => {
    lsDel(A2HS_KEY); lsDel(A2HS_INSTALLED); a2hs.hidden = false; window.scrollTo({top:0, behavior:'smooth'});
  });
  initA2hs();

  // ===== キャンペーンモーダル（複数枚・連続表示） =====
  // 対象ステータス：guest=非ログイン／free=ログイン無料／paid=ログイン有料（premium・light両方）／
  //                premium=プレミアム（全機能）／light=ライト（中央限定・地方限定＝プレミアム以外）
  // 表示順：システム告知 → priority降順 → 配信開始が新しい順。1セッションの最大表示枚数は管理画面設定（既定3枚）
  // ×・「閉じる」・背景タップで閉じると、次の1枚が続けて表示される（CTAタップ時は遷移するため以降は表示しない）
  // 頻度制御（同一モーダルは既定1回・クールダウン日数）は従来どおりモーダル単位でサーバ管理
  const MODALS = [
    {id:'cp-spat4',  type:'campaign', priority:80, audience:['guest','free','paid'], visual:'v-tie', lbl:'タイアップ企画',
     title:'SPAT4×Eval MODE<br>連携キャンペーン開催中', desc:'期間中の投票で最大10,000ptプレゼント（〜7/31）',
     cta:'キャンペーン詳細を見る ▶', href:'EvalMode_キャンペーンLP_SP_v1.html'},
    {id:'cp-reg',    type:'campaign', priority:70, audience:['guest'], visual:'v-reg', lbl:'新規登録特典',
     title:'今なら無料登録で<br>100ptプレゼント', desc:'登録は30秒・メールアドレスだけでOK（〜8/31）',
     cta:'無料登録する ▶', href:'EvalMode_会員登録ログイン_SP_v1.html'},
    {id:'cp-subfree',type:'campaign', priority:60, audience:['free'], visual:'v-sub', lbl:'期間限定',
     title:'プレミアムプラン<br>初月無料キャンペーン', desc:'全レースの確率・期待値が今なら初月0円（〜8/10）',
     cta:'初月無料で試す ▶', href:'EvalMode_サブスク申込_SP_v2.html'},
    {id:'cp-up',     type:'campaign', priority:55, audience:['light'], visual:'v-sub', lbl:'アップグレード',
     title:'中央も地方も全部見る<br>プレミアムへ', desc:'差額のみで今月から切替可能',
     cta:'プランを見る ▶', href:'EvalMode_サブスク申込_SP_v2.html'},
    {id:'cp-ptsale', type:'campaign', priority:40, audience:['free','paid'], visual:'v-pt', lbl:'ptセール',
     title:'ポイント20%増量セール', desc:'期間中のpt購入で20%ボーナス（〜7/27）',
     cta:'ポイントを購入する ▶', href:'EvalMode_ポイント購入_SP_v1.html'},
  ];
  const MAX_PER_SESSION = 3;
  const modal = document.getElementById('campaignModal');
  let mQueue = [], mIdx = 0;
  const matchAud = (m, st) => m.audience.includes(st) || (m.audience.includes('paid') && (st === 'premium' || st === 'light'));
  function buildQueue(st){
    return MODALS.filter(m => matchAud(m, st))
      .sort((a, b) => (b.type === 'system') - (a.type === 'system') || b.priority - a.priority)
      .slice(0, MAX_PER_SESSION);
  }
  function renderModal(){
    const m = mQueue[mIdx];
    document.getElementById('mVisual').className = 'm-visual ' + m.visual;
    document.getElementById('mLbl').textContent = m.lbl;
    document.getElementById('mTitle').innerHTML = m.title;
    document.getElementById('mDesc').textContent = m.desc;
    const cta = document.getElementById('mCta');
    cta.textContent = m.cta; cta.href = m.href;
    const multi = mQueue.length > 1, last = mIdx === mQueue.length - 1;
    const cnt = document.getElementById('mCount');
    cnt.hidden = !multi; cnt.textContent = 'お知らせ ' + (mIdx + 1) + ' / ' + mQueue.length;
    document.getElementById('mDots').innerHTML = multi ? mQueue.map((_, i) => `<i class="${i === mIdx ? 'on' : ''}"></i>`).join('') : '';
    document.getElementById('modalSkip').textContent = (multi && !last) ? '閉じて次のお知らせへ' : (mIdx === 0 ? '閉じて確率を見る' : '閉じる');
    modal.classList.remove('hidden');
  }
  function openModals(st){
    mQueue = buildQueue(st); mIdx = 0;
    if(mQueue.length) renderModal(); else modal.classList.add('hidden');
  }
  function closeModal(){
    mIdx++;
    if(mIdx < mQueue.length){ renderModal(); } // 次の1枚を続けて表示
    else modal.classList.add('hidden');
  }
  document.getElementById('modalClose').addEventListener('click', closeModal);
  document.getElementById('modalSkip').addEventListener('click', e => { e.preventDefault(); closeModal(); });
  modal.addEventListener('click', e => { if(e.target === modal) closeModal(); });
  const demoStateToModal = {guest:'guest', free:'free', subsc:'premium'};
  document.getElementById('modalReplay').addEventListener('click', () => {
    const st = document.querySelector('.demo-switch button[data-state].on')?.dataset.state || 'guest';
    openModals(demoStateToModal[st]);
  });
  openModals('guest'); // 初期表示：非ログイン想定


  // ナビ「的中実績」バッジ：本日の的中数（実装時はサーバ値。的中確定でリアルタイム加算し、増えた時だけ1回ポップ）
  // モック：25秒後に1件増えた想定でポップを再現
  setTimeout(() => {
    const hb = document.querySelector('.gnav a.hit .hit-badge');
    if(!hb) return;
    hb.textContent = String(+hb.textContent + 1);
    hb.classList.remove('pop'); void hb.offsetWidth; hb.classList.add('pop');
    hb.closest('a').setAttribute('aria-label', '的中実績（本日' + hb.textContent + 'レース的中）');
  }, 25000);
</script>
<script>
/* =========================================================
   TOP：アンバサダー枠（2026-10-01）
   実装時：AMBASSADORS はアンバサダーページ（P21）と同じデータ（管理画面で登録・表示順どおり）
   ========================================================= */
function mockIcon(kind, c1, c2, txt){
  let inner = '';
  if(kind === 'person'){
    inner = `<circle cx="50" cy="41" r="17" fill="${c2}"/><ellipse cx="50" cy="96" rx="33" ry="27" fill="${c2}"/>`;
  }else if(kind === 'mono'){
    inner = `<text x="50" y="50" dy=".36em" text-anchor="middle" font-family="Helvetica,Arial,sans-serif" font-weight="900" font-size="${txt.length > 1 ? 34 : 46}" fill="${c2}">${txt}</text>`;
  }else if(kind === 'stripes'){
    inner = [0,1,2,3,4,5].map(i => `<rect x="${-40 + i * 26}" y="-20" width="12" height="160" fill="${c2}" transform="rotate(30 50 50)"/>`).join('');
  }else if(kind === 'target'){
    inner = `<circle cx="50" cy="50" r="30" fill="none" stroke="${c2}" stroke-width="7"/><circle cx="50" cy="50" r="15" fill="none" stroke="${c2}" stroke-width="7"/><circle cx="50" cy="50" r="4" fill="${c2}"/>`;
  }else if(kind === 'shoe'){
    inner = `<path d="M30 30 v18 a20 20 0 0 0 40 0 v-18" fill="none" stroke="${c2}" stroke-width="10" stroke-linecap="round"/><circle cx="34" cy="42" r="2.6" fill="${c1}"/><circle cx="66" cy="42" r="2.6" fill="${c1}"/>`;
  }else if(kind === 'logo'){
    inner = `<path d="M50 18 L80 50 L50 82 L20 50 Z" fill="${c2}"/><path d="M50 34 L66 50 L50 66 L34 50 Z" fill="${c1}"/>`;
  }
  const bg = kind === 'person'
    ? `<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="${c1}"/><stop offset="1" stop-color="${txt || c1}"/></linearGradient></defs><rect width="100" height="100" fill="url(#g)"/>`
    : `<rect width="100" height="100" fill="${c1}"/>`;
  return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">${bg}${inner}</svg>`);
}

const AMB_OFFICIAL_ICON = mockIcon('logo', '#0d2b52', '#00c98d');
const AMBASSADORS = [
  {name:'うまログ太郎',     x:'umalog_taro',      tags:['中央競馬','穴馬'],         desc:'人気薄の激走パターンを毎週分析。Evalの確率と人気の“ズレ”から狙える穴馬をポストしています。',  icon:mockIcon('person', '#ff9a5a', '#fff3e6', '#e0485e')},
  {name:'Kei｜地方競馬',    x:'kei_chiho',        tags:['地方競馬','ナイター'],     desc:'南関東を中心に地方競馬を毎日チェック。平日の買い目の組み立て方を発信中。',                      icon:mockIcon('mono',   '#14233d', '#5fb4ff', 'K')},
  {name:'血統メモ',         x:'kettou_memo',      tags:['血統','中央競馬'],         desc:'血統×コース適性の視点から、確率上位馬の“裏付け”を解説します。',                                  icon:mockIcon('stripes','#2b1d3f', '#a77bff')},
  {name:'みお🐴',           x:'mio_keiba',        tags:['パドック','初心者向け'],   desc:'競馬歴2年。パドック写真と一緒に、初心者目線でEvalの使い方を紹介しています。',                  icon:mockIcon('person', '#ffd1dc', '#ffffff', '#ff8fb1')},
  {name:'WIN5職人',         x:'win5_shokunin',    tags:['WIN5'],                    desc:'毎週日曜はWIN5一本勝負。点数を抑えた買い方と、シミュレーションの活用例をポスト。',                icon:mockIcon('target', '#0f2a22', '#c8f31d')},
  {name:'データ馬場',       x:'data_baba',        tags:['データ分析','回収率'],     desc:'馬場状態と確率の関係を数字で検証。長期の回収率レポートを月1回まとめています。',                  icon:mockIcon('mono',   '#f3f5f8', '#0b0c10', 'DB')},
  {name:'ナイター大井',     x:'night_ooi',        tags:['地方競馬','ナイター'],     desc:'仕事帰りの大井ナイター専門。発走前の最終確率チェックをリアルタイムで共有。',                      icon:mockIcon('shoe',   '#101a3a', '#f0c44a')},
  {name:'さくら｜一口馬主', x:'sakura_hitokuchi', tags:['一口馬主','POG'],          desc:'出資馬の応援が中心。新馬戦・未勝利戦の確率の見方をやさしく解説しています。',                    icon:mockIcon('person', '#7fd6c2', '#f4fffb', '#2f8f9d')},
  {name:'ハル@回収率重視の三連単フォーメーション研究所', x:'haru_roi_sanrentan_lab', tags:['回収率','3連単','データ分析'], desc:'3連単フォーメーションの点数配分を研究。的中率より回収率を重視した組み方を発信。', icon:mockIcon('mono',   '#e6394f', '#ffffff', 'H')},
  {name:'ゆうき｜POG',      x:'yuki_pog',         tags:['POG','血統'],              desc:'2歳馬の評価とPOG指名馬の追跡。デビュー前後の確率推移をまとめています。',                        icon:mockIcon('person', '#5b8def', '#eaf1ff', '#283a8f')}
];
(function renderAmbStrip(){
  const PC_MAX = 5; // PC右サイドに出す人数
  const esc = s => String(s).replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));
  const page = 'EvalMode_アンバサダー_SP_v1.html';
  const items = AMBASSADORS.map((a, i) => `
    <li${i >= PC_MAX ? ' class="pc-off"' : ''}><a href="${page}#amb-${encodeURIComponent(a.x)}" title="${esc(a.name)}">
      <span class="av"><img src="${a.icon}" alt="" loading="lazy"></span>
      <span class="nm">${esc(a.name)}</span>
      <span class="tg">${esc((a.tags || [])[0] || '')}</span>
    </a></li>`).join('');
  const rest = Math.max(0, AMBASSADORS.length - PC_MAX);
  const more = `<li class="more"><a href="${page}"><span class="av">›</span><span class="nm" data-sp="一覧" data-pc="${rest ? `他${rest}名を見る ›` : '一覧を見る ›'}"></span></a></li>`;
  const ul = document.getElementById('ambStrip');
  ul.innerHTML = items + more;
  const lbl = ul.querySelector('.more .nm');
  const mq = window.matchMedia('(min-width:1024px)');
  const setLbl = () => lbl.textContent = mq.matches ? lbl.dataset.pc : lbl.dataset.sp;
  setLbl(); mq.addEventListener ? mq.addEventListener('change', setLbl) : mq.addListener(setLbl);
})();
</script>
{/literal}
</body>
</html>
