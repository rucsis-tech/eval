/**
 * P13 サブスク申込（モック）
 */
(function () {
    'use strict';

const PLANS = [
  {id:'full', name:'全機能使い放題プラン', area:'中央＋地方 全レース対応', price:9800, reco:true,
   benefits:['全レースの好走確率・リアルタイム期待値（LIVE期待値）','好走確率ランキング全頭表示','的中確率シミュレーション 無制限','WIN5的中確率シミュレーション','サブスク対象攻略ツール見放題']},
  {id:'chuo', name:'中央競馬限定プラン', area:'JRA全レース対応', price:5980, reco:false,
   benefits:['中央全レースの好走確率・リアルタイム期待値（LIVE期待値）','好走確率ランキング中央全頭表示','的中確率シミュレーション（中央）','WIN5的中確率シミュレーション']},
  {id:'chiho', name:'地方競馬限定プラン', area:'地方全レース対応', price:3980, reco:false,
   benefits:['地方全レースの好走確率・リアルタイム期待値（LIVE期待値）','好走確率ランキング地方全頭表示','的中確率シミュレーション（地方）']},
];
/* 直近7日間の払戻TOP5（期間・件数ともバックエンド管理。デモ=7日間・5件）
   単発の最高額ではなくランキングで見せることで、朝イチや小配当日でも
   「継続的に高配当を的中させている」証拠として機能させる */
const PAY_TOP5 = [
  {yen:86880, type:'3連単', race:'札幌11R', date:'8/1'},
  {yen:68930, type:'3連単', race:'新潟11R', date:'8/8'},
  {yen:67800, type:'3連単', race:'金沢7R',  date:'8/4'},
  {yen:53110, type:'3連単', race:'札幌5R',  date:'8/2'},
  {yen:52310, type:'3連単', race:'大井10R', date:'8/7'},
];
const RK_CLS = i => i === 0 ? 'r1' : i === 1 ? 'r2' : i === 2 ? 'r3' : 'r45';

/* バンド内ティッカー（TOPの速報ティッカーと同じ文法・3.2秒ローテ） */
let payIdx = 0;
setInterval(() => {
  const el = document.getElementById('payRot');
  if(!el) return;
  el.classList.add('fade');
  setTimeout(() => {
    payIdx = (payIdx + 1) % PAY_TOP5.length;
    const t = PAY_TOP5[payIdx];
    el.innerHTML = `<span class="rk ${RK_CLS(payIdx)}">${payIdx + 1}位</span><span class="hot">${t.yen.toLocaleString()}円</span>（${t.race} ${t.date}）`;
    el.classList.remove('fade');
  }, 250);
}, 3200);

/* プラン別の直近実績（的中実績P5のデータを転用。期間・数値はバックエンド管理のデモ値） */
const PROOF = {
  full:  {label:'直近の的中実績（総合）', hits:'的中18R／32R', best:'68,930円',
          band:'昨日 8/8(土)：<b>的中18R／32R</b>・最高払戻 <span class="hot">68,930円</span>（3連単・新潟11R）<br>直近7日間の最高払戻 <span class="hot">86,880円</span>（3連単・札幌11R 8/1）'},
  chuo:  {label:'直近の的中実績（中央）', hits:'的中13R／24R', best:'68,930円',
          band:'昨日 8/8(土)中央：<b>的中13R／24R</b>・最高払戻 <span class="hot">68,930円</span>（3連単・新潟11R）<br>直近7日間の中央最高払戻 <span class="hot">86,880円</span>（3連単・札幌11R 8/1）'},
  chiho: {label:'直近の的中実績（地方）', hits:'的中5R／8R', best:'34,480円',
          band:'昨日 8/8(土)地方：<b>的中5R／8R</b>・最高払戻 <span class="hot">34,480円</span>（3連単・佐賀5R）<br>直近7日間の地方最高払戻 <span class="hot">52,310円</span>（3連単・大井10R 8/7）'},
};

/* 招待コード（バックエンド管理・プラン単位で特典を設定） */
const INVITES = {
  EVALFREE:{label:'フルプラン初月無料', plans:{full:{type:'firstfree'}}},
  EVALW:   {label:'フルプラン初月無料＋2ヶ月目以降も特別価格', plans:{full:{type:'firstfree_sp', sp:7800}}},
  EVALSP:  {label:'フルプラン特別価格', plans:{full:{type:'sp', sp:7800}}},
  EVALALL: {label:'全プラン特別価格', plans:{full:{type:'sp', sp:7800}, chuo:{type:'sp', sp:4980}, chiho:{type:'sp', sp:2980}}},
};

let status = 'free';      // 'guest' | 'free' | 'sub_full'
let campaign = true;      // 初月無料キャンペーン（バックエンド出し分け）
let moment = 'live';      // 'eve'（開催前夜）| 'live'（開催中）※実装時はバックエンドが開催カレンダー＋時刻で判定
let invite = null;        // 適用中コード
let payMethod = 'card';
const yen = n => n.toLocaleString();

function planOffer(p){
  // 適用中の招待コード特典（なければキャンペーン初月無料のみ）
  const iv = invite ? INVITES[invite].plans[p.id] : null;
  return {
    firstFree: campaign || (iv && (iv.type === 'firstfree' || iv.type === 'firstfree_sp')),
    sp: iv && iv.sp ? iv.sp : null,
  };
}

/* ===== モーメントバンド（獲得動機に合わせた2モード）＝バックエンド判定
   ・eve（開催前夜）：翌日開催の準備訴求（前日確率が今夜から見られる）
   ・live（開催中）：本日ここまでの的中＋次のレースまでのカウントダウン（刹那的な決断の受け皿）
   ※「負けを取り戻せる」等の断定表現は使わない（射幸心を煽る表現を避け、データで立て直す文脈に統一） */
function momentHTML(){
  if(moment === 'eve'){
    return `
      <div class="mo-row1">
        <span class="mo-tag">明日の開催に間に合います</span>
      </div>
      <div class="mo-row1" style="margin-top:6px;">
        <span class="mo-main">明日 8/9(日) <b>中央24R・地方16R</b></span>
      </div>
      <div class="mo-row2">
        <span>前日確率は<b>今夜20:00</b>からすべて公開</span><span class="sep">｜</span>
        <span>登録は約30秒・朝の新聞がわりに</span>
      </div>`;
  }
  return `
    <div class="mo-row1">
      <span class="mo-tag"><span class="dot"></span>LIVE</span>
      <span class="mo-main">本日 <b>的中14R</b>／36R中28R消化</span>
      <span class="cd"><span id="cdTimer">23:41</span><small>次のレース 新潟12R 16:04</small></span>
    </div>
    <div class="mo-row2">
      <span>直近7日間の払戻TOP5：</span>
      <span class="pay-rot" id="payRot"><span class="rk r1">1位</span><span class="hot">86,880円</span>（札幌11R 8/1）</span>
    </div>
    <div class="mo-row2">
      <span>妙味点灯中 <b>7R</b></span>
      <span class="mo-chip">新潟12R</span><span class="mo-chip">札幌12R</span><span class="mo-chip">佐賀9R</span><span class="mo-chip">高知7R</span>
    </div>`;
}
/* ※最高払戻は「直近◯日間」からバックエンドが選んで表示（デモ=7日間）。
   朝イチや配当が小さい日でも常にベストな実績数字を見せるための期間バッファ */

/* カウントダウン（モック：16:04発走までの残り。実装時はサーバー時刻基準） */
let cdSec = 23 * 60 + 41;
setInterval(() => {
  const el = document.getElementById('cdTimer');
  if(!el) return;
  cdSec = Math.max(0, cdSec - 1);
  el.textContent = String(Math.floor(cdSec / 60)).padStart(2,'0') + ':' + String(cdSec % 60).padStart(2,'0');
}, 1000);

function render(){
  document.getElementById('guestBar').hidden = status !== 'guest';
  document.getElementById('curBar').hidden = status !== 'sub_full';

  // モーメントバンド
  const mo = document.getElementById('momentBand');
  mo.className = 'moment ' + moment;
  mo.innerHTML = momentHTML();

  // 追従CTA（契約中は非表示）
  const sc = document.getElementById('stickyCta');
  sc.hidden = status === 'sub_full';
  if(!sc.hidden){
    const o = planOffer(PLANS[0]);
    document.getElementById('scBtn').textContent =
      (moment === 'eve' ? '明日の全レースに間に合う — ' : '次のレースに間に合う — ') +
      (o.firstFree ? '初月無料ではじめる ›' : 'フルプランをはじめる ›');
    document.getElementById('scSub').textContent = '登録は約30秒・いつでも解約OK' + (o.sp ? '・特別価格適用中' : '');
  }

  document.getElementById('planList').innerHTML = PLANS.map(p => {
    const o = planOffer(p);
    const isCur = status === 'sub_full' && p.id === 'full';
    const cta = isCur ? '契約中のプラン'
      : status === 'sub_full' ? 'このプランに変更する'
      : 'このプランで申し込む';
    const ctaCls = isCur ? ' cur' : status === 'sub_full' ? ' sub' : '';
    return `<div class="plan-card${p.reco ? ' reco' : ''}${o.sp ? ' pop' : ''}" data-plan="${p.id}">
      ${p.reco ? '<span class="reco-badge">おすすめ</span>' : ''}
      ${isCur ? '<span class="cur-badge">現在のプラン</span>' : ''}
      <p class="p-name">${p.name}${o.firstFree && !isCur ? '<span class="free-badge">初月無料</span>' : ''}</p>
      <p class="p-area">${p.area}</p>
      <div class="p-price-row">
        ${o.sp ? `<span class="p-old">¥${yen(p.price)}</span><span class="p-price sale">¥${yen(o.sp)}<small>/月（仮）</small></span>`
               : `<span class="p-price">¥${yen(p.price)}<small>/月（仮）</small></span>`}
      </div>
      <ul class="p-benefits">${p.benefits.map(b => `<li>${b}</li>`).join('')}</ul>
      <a href="#" class="p-proof" data-area="${p.id}">
        <span class="ico">📈</span>
        <span class="txt">
          <span class="t1">${PROOF[p.id].label}</span>
          <span class="t2">昨日 <b>${PROOF[p.id].hits}</b>・最高 <span class="hot">${PROOF[p.id].best}</span></span>
        </span>
        <span class="go">実績を見る ›</span>
      </a>
      <button class="p-cta${ctaCls}">${cta}</button>
    </div>`;
  }).join('');
}

/* ===== 招待コード適用 ===== */
document.getElementById('ivApply').addEventListener('click', () => {
  const box = document.getElementById('inviteBox');
  const msg = document.getElementById('ivMsg');
  const code = document.getElementById('ivInput').value.trim().toUpperCase();
  msg.hidden = false;
  if(INVITES[code]){
    invite = code;
    box.classList.add('applied');
    document.getElementById('ivTitle').textContent = '✓ 招待コード適用中：' + code;
    msg.className = 'iv-msg ok';
    msg.textContent = '特典：' + INVITES[code].label + ' が適用されました。';
  }else{
    invite = null;
    box.classList.remove('applied');
    document.getElementById('ivTitle').textContent = '🎟 招待コードをお持ちの方';
    msg.className = 'iv-msg ng';
    msg.textContent = code ? 'コードが見つかりません。入力内容をご確認ください。' : 'コードを入力してください。';
  }
  render();
});

/* ===== 申込確認シート ===== */
const PAY_METHODS = [
  {id:'card',    name:'クレジットカード', sub:'Visa / Master / JCB / AMEX'},
  {id:'apple',   name:'Apple Pay', sub:''},
  {id:'google',  name:'Google Pay', sub:''},
  {id:'carrier', name:'キャリア決済', sub:'docomo / au / SoftBank'},
];
/* 実績ハーフモーダル：プランカード内のトリガー行から表示（ページを縦に伸ばさず証拠を見せる） */
function openProofSheet(area){
  const pf = PROOF[area];
  const sh = document.getElementById('sheet');
  sh.innerHTML = `
    <div class="grip"></div>
    <div class="sh-head"><span class="sh-title">${pf.label}</span><span class="sh-close" data-close>✕</span></div>
    <p style="font-size:9.5px;color:var(--text-sub);margin-top:4px;line-height:1.7;">Eval好走確率上位6頭ボックス（馬連・馬単・3連複・3連単）を毎レース購入した場合の実績。毎日、的中実績ページで答え合わせできます。</p>
    <div class="pf-band"><span>${pf.band}</span></div>
    <p style="font-size:10px;font-weight:800;color:var(--text-sub);margin-top:12px;letter-spacing:.06em;">直近7日間の払戻TOP5（総合）</p>
    <ul class="pay-rank">${PAY_TOP5.map((t, i) => `
      <li><span class="pr-no n${i < 3 ? i + 1 : 45}">${i + 1}</span>
        <span class="pr-yen">${t.yen.toLocaleString()}円</span>
        <span style="font-size:10px;color:var(--text-sub);">${t.type}</span>
        <span class="pr-meta">${t.race}<br>${t.date}</span></li>`).join('')}
    </ul>
    <div class="voice">
      <div class="v-card">
        <div class="v-head">
          <span class="v-av" style="background:linear-gradient(135deg,#2fe0a8,#046b4c);">タ</span>
          <span><span class="v-name">タカシ@回収率改善中</span><br><span class="v-id">@taka_keiba ・ 8/8</span></span>
          <span class="v-x">𝕏</span>
        </div>
        <p class="v-body">関屋記念、Evalの上位6頭で3連単ハマった…。感覚で買ってた頃には戻れない</p>
      </div>
      <div class="v-card">
        <div class="v-head">
          <span class="v-av" style="background:linear-gradient(135deg,#f5a623,#e8850c);">ユ</span>
          <span><span class="v-name">ゆう｜データ派</span><br><span class="v-id">@yu_data_umauma ・ 8/7</span></span>
          <span class="v-x">𝕏</span>
        </div>
        <p class="v-body">前日夜に確率チェック→朝は印つけるだけ。週末の予想時間が半分になった</p>
        <p class="v-note">※掲載ポストはバックエンド管理（アンバサダー・ユーザー投稿から選定）</p>
      </div>
    </div>
    <a class="sh-cta" data-close style="margin-top:14px;">このプランで申し込む</a>
    <a class="sh-cancel" href="/results/results_top" style="color:var(--red);font-weight:900;">的中実績ページで答え合わせ ›</a>
    <a class="sh-cancel" data-close>閉じる</a>`;
  sh.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeSheet));
  // 「このプランで申し込む」→閉じて申込シートへ
  sh.querySelector('.sh-cta').addEventListener('click', () => {
    setTimeout(() => openSheet(PLANS.find(x => x.id === area)), 120);
  });
  document.getElementById('sheetOv').classList.add('show');
}

function openSheet(p){
  const sh = document.getElementById('sheet');
  if(status === 'guest'){
    sh.innerHTML = `
      <div class="grip"></div>
      <div class="sh-head"><span class="sh-title">無料登録してプランに申し込む</span><span class="sh-close" data-close>✕</span></div>
      <div class="sh-plan"><p class="sp-name">${p.name}</p>
        <p class="sp-lines">申込には無料会員登録（またはログイン）が必要です。登録は30秒で完了し、そのままこの画面に戻ります。${invite ? `<br>適用中の招待コード <b>${invite}</b> は登録後も維持されます。` : ''}</p></div>
      <a class="sh-cta lime" href="/auth/entry">無料登録にすすむ ›</a>
      <a class="sh-cancel" data-close>あとで</a>`;
  }else{
    const o = planOffer(p);
    const monthly = o.sp || p.price;
    const change = status === 'sub_full';
    sh.innerHTML = `
      <div class="grip"></div>
      <div class="sh-head"><span class="sh-title">${change ? 'プラン変更の確認' : '申込内容の確認'}</span><span class="sh-close" data-close>✕</span></div>
      <div class="sh-plan">
        <p class="sp-name">${p.name}</p>
        <p class="sp-lines">
          ${o.firstFree && !change ? '初月：<span class="free-v">無料（キャンペーン適用）</span><br>' : ''}
          月額：${o.sp ? `<s style="color:#9aa3b2;">¥${yen(p.price)}</s> <span class="sp-v">¥${yen(monthly)}</span>` : `<b>¥${yen(monthly)}</b>`}<small>（仮）</small><br>
          ${invite && INVITES[invite].plans[p.id] ? `適用コード：<b>${invite}</b>（${INVITES[invite].label}）<br>` : ''}
          ${change ? '適用開始：<b>次回更新日（9/8）から</b>' : `初回課金日：<b>${o.firstFree ? '9/9（無料期間終了の翌日）' : '本日'}</b>`}
        </p>
      </div>
      <p class="sh-sec">支払い方法</p>
      <div class="pay-list">${PAY_METHODS.map(m => `
        <a href="#" class="pay-row${m.id === payMethod ? ' on' : ''}" data-pay="${m.id}">
          <span class="radio"></span>${m.name}
          ${m.sub ? `<span class="p-sub">${m.sub}</span>` : ''}
        </a>`).join('')}
      </div>
      <p class="sh-terms"><b>いつでも解約できます</b>（解約後も期間満了まで利用可能・日割り返金なし）。
      解約はマイページ＞プラン契約カードから行えます。一部の攻略ツール商品は別途ポイントが必要です。</p>
      <button class="sh-cta" id="subBtn">${change ? 'このプランに変更する' : o.firstFree ? '初月無料ではじめる' : `月額¥${yen(monthly)}で申し込む`}</button>
      <a class="sh-cancel" data-close>キャンセル</a>`;
    document.getElementById('subBtn').addEventListener('click', () => complete(p, change));
    sh.querySelectorAll('.pay-row').forEach(r => r.addEventListener('click', (e) => {
      e.preventDefault();
      payMethod = r.dataset.pay;
      sh.querySelectorAll('.pay-row').forEach(x => x.classList.toggle('on', x === r));
    }));
  }
  sh.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeSheet));
  document.getElementById('sheetOv').classList.add('show');
}
function complete(p, change){
  const sh = document.getElementById('sheet');
  sh.innerHTML = `
    <div class="grip"></div>
    <div class="done-view">
      <div class="d-mark"><svg viewBox="0 0 24 24"><path d="M4 12.5l5 5L20 6.5"/></svg></div>
      <p class="d-title">${change ? 'プラン変更を受け付けました' : '申込が完了しました'}</p>
      <p class="d-sub">${change ? '次回更新日（9/8）から新しいプランが適用されます。' : `<b>${p.name}</b>の全機能が今すぐ使えます。<br>契約内容はマイページからいつでも確認できます。`}</p>
    </div>
    <a class="sh-cta" href="/">さっそく確率テーブルを見る ›</a>
    <a class="sh-cancel" data-close>閉じる</a>`;
  sh.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', closeSheet));
}
function closeSheet(){ document.getElementById('sheetOv').classList.remove('show'); }
document.getElementById('sheetOv').addEventListener('click', e => { if(e.target === e.currentTarget) closeSheet(); });

/* ===== モック確認用トグル ===== */
document.querySelectorAll('.mock-ctrl a[data-st]').forEach(a => a.addEventListener('click', (e) => {
  e.preventDefault();
  status = a.dataset.st;
  document.querySelectorAll('.mock-ctrl a[data-st]').forEach(x => x.classList.toggle('on', x === a));
  render();
}));
document.getElementById('mcCamp').addEventListener('click', (e) => {
  e.preventDefault();
  campaign = !campaign;
  document.getElementById('mcCamp').classList.toggle('on', campaign);
  render();
});
document.querySelectorAll('.mock-ctrl a[data-mo]').forEach(a => a.addEventListener('click', (e) => {
  e.preventDefault();
  moment = a.dataset.mo;
  document.querySelectorAll('.mock-ctrl a[data-mo]').forEach(x => x.classList.toggle('on', x === a));
  render();
}));

/* 追従CTA→おすすめプラン（フル）の申込シートを直接開く（タップ数最小化） */
document.getElementById('scBtn').addEventListener('click', () => openSheet(PLANS[0]));

/* FAQ開閉 */
document.querySelectorAll('.faq .fq-q').forEach(q => q.addEventListener('click', (e) => {
  e.preventDefault();
  q.closest('.faq').classList.toggle('open');
}));

document.getElementById('planList').addEventListener('click', (e) => {
  const proof = e.target.closest('.p-proof');
  if (proof) {
    e.preventDefault();
    openProofSheet(proof.dataset.area);
    return;
  }
  const btn = e.target.closest('.p-cta:not(.cur)');
  if (!btn) return;
  const card = btn.closest('.plan-card');
  if (!card) return;
  const p = PLANS.find(x => x.id === card.dataset.plan);
  if (p) openSheet(p);
});

/* Before/Afterスライダー */
const baSlider = document.getElementById('baSlider');
baSlider.addEventListener('input', () => {
  document.getElementById('baBox').style.setProperty('--pos', baSlider.value + '%');
});

render();

})();