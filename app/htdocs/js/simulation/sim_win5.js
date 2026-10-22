/**
 * P4a WIN5 的中確率算出（モック）
 */
(function () {
    'use strict';

// 対象5レース（馬名のみ定義。勝利確率は順位分布から、期待値・騎手は決定的に生成）
const RACES = [
  {name:'福島10R', title:'安達太良S',    cond:'ダ1150m', time:'14:50',
   horses:['ミルトクレイモー','テーオーステルス','サンライズフレイム','ロードアヴニール','メイショウホルス','グランレイ','アシャカトブ','ペリエール','サトノルフィアン','ヒビキ','ダンツエラン','ナムラフランク','オメガレインボー','ジャスパーウィン','バトルクライ','リメイク'],
   marks:{3:'◎',1:'◯',7:'▲',12:'△'},
   order:[4,3,7,1,12,8,2,16,5,10,6,11,9,14,13,15]},
  {name:'小倉10R', title:'博多S',        cond:'ダ1700m', time:'15:01',
   horses:['シゲルバクハツ','タガノエスコート','メイショウミツヤス','カシノブレンド','テイエムスパーダ','ヨカヨカ','ビッグシーザー','モズメイメイ','アドマイヤラビ','ロードベイリーフ','スマートクラージュ','メディーヴァル','ジャングロ','エイシンスポッター'],
   marks:{1:'◎',6:'◯',2:'▲',10:'△'},
   order:[1,6,2,10,5,7,3,14,8,11,4,12,9,13]},
  {name:'函館11R', title:'五稜郭S',      cond:'芝1800m', time:'15:10',
   horses:['アラタ','マイネルウィルトス','テーオーシリウス','ブローザホーン','ヤマニンサルバム','サトノセシル','ルビーカサブランカ','フェアエールング','ホウオウエミーズ','コスモフリーゲン','ドーブネ','アケルナルスター','ロンドンガーズ','エミュー'],
   marks:{13:'◎',4:'◯',1:'▲',7:'△'},
   order:[13,4,1,7,5,11,3,9,2,10,6,12,8,14]},
  {name:'福島11R', title:'七夕賞（GIII）', cond:'芝2000m', time:'15:20',
   horses:['ボーンディスウェイ','コントラポスト','ショウナンマグマ','カラマティアノス','オーロラエックス','マイネルモーント','メリオーレム','クリスマスパレード','オニャンコポン','センツブラッド','アスクナイスショー','リカンカブール','バトルボーン','オールナット','ヤマニンブークリエ','サヴォーナ'],
   marks:{13:'◎',11:'◯',10:'▲',4:'△',6:'△',2:'×',12:'☆'},
   order:[13,11,10,5,7,4,8,6,12,3,1,9,15,14,16,2]},
  {name:'小倉11R', title:'佐世保S',      cond:'芝1200m', time:'15:35',
   horses:['テイエムトッキュウ','カンチェンジュンガ','モズモーメント','ロードフォアエース','ダディーズビビッド','メイショウソラフネ','アネゴハダ','スティクス','ヨシノイースター','ジュビリーヘッド','ロンドンプラン','ムーンプローブ','エターナルタイム','ショウナンハクラク','スナークスター','クムシラコ','ジョバンニ','ビオグラフィー'],
   marks:{17:'◎',5:'◯',1:'▲',10:'△'},
   order:[17,5,1,10,7,4,2,12,9,3,11,6,14,8,16,13,15,18]},
];
const JOCKEYS = ['ルメール','川田','戸崎','武豊','横山武','松山','坂井','岩田望','三浦','田辺','丹内','菅原明','津村','石川','内田','大野','杉原','木幡巧'];

// 勝利確率：各レースのorder（強さ順・印/本命と整合）に順位分布を割り当て（合計≒97%）
// 期待値：60〜140%を決定的に分散（100%超＝妙味）
RACES.forEach((r, ri) => {
  const n = r.horses.length;
  const w = r.order.map((_, i) => 1 / Math.pow(i + 2.2, 1.6)); // 1番人気≒28-30%の現実的な分布
  const total = w.reduce((s, x) => s + x, 0);
  r.data = r.horses.map((name, i) => {
    const num = i + 1;
    const rankIdx = r.order.indexOf(num);
    const win = Math.max(0.1, Math.round(w[rankIdx] / total * 970) / 10); // %
    const ev = 60 + ((num * 17 + ri * 31) * 13 % 81);                     // 60〜140%
    return {
      num,
      waku: Math.max(1, Math.min(8, Math.ceil(num * 8 / n))),
      name,
      jk: JOCKEYS[(num * 7 + ri) % JOCKEYS.length],
      win, ev,
      mark: r.marks[num] || '',
    };
  });
});

// 選択状態（モック初期値：参考画像と同じ構成＝組数20）
const sel = [ [4,7], [1,6], [13], [5,7,8,11,13], [17] ];

let view = 0; // 0-4=レース選択, 5=サマリー

// ---- ステッパー ----
const stepper = document.getElementById('stepper');
function renderStepper(){
  const allFilled = sel.every(s => s.length > 0);
  stepper.innerHTML = RACES.map((r, i) => `
    <div class="step${view === i ? ' on' : ''}${sel[i].length ? ' filled' : ''}" data-v="${i}">
      <span class="s-no">${i + 1}</span>
      <span class="s-race">${r.name}</span>
      <span class="s-cnt">${sel[i].length ? sel[i].length + '頭' : '未選択'}</span>
    </div>`).join('')
    + `<div class="step result${allFilled ? ' ready' : ''}${view === 5 ? ' on' : ''}" data-v="5">
      <span class="s-no">✓</span>
      <span class="s-race">結果</span>
      <span class="s-cnt">${allFilled ? '算出可' : '入力中'}</span>
    </div>`;
  stepper.querySelectorAll('.step').forEach(el => el.addEventListener('click', () => {
    const v = +el.dataset.v;
    if(v === 5 && !sel.every(s => s.length > 0)) return; // 全レース入力後のみ結果へ
    view = v; renderAll();
  }));
}

// ---- レース選択ビュー ----
const raceView = document.getElementById('raceView');
const sumView = document.getElementById('sumView');
const selBody = document.getElementById('selBody');
function renderRace(){
  const r = RACES[view];
  document.getElementById('raceInfo').innerHTML =
    `<b>${r.name}</b> <b>${r.title}</b> ${r.cond} <span class="time">発走 ${r.time}</span>`;
  selBody.innerHTML = r.data.map(h => `
    <tr>
      <td><span class="waku w${h.waku}">${h.num}</span></td>
      <td><span class="mk${h.mark ? '' : ' none'}">${h.mark || '−'}</span></td>
      <td class="name">${h.name}<small>${h.jk}</small></td>
      <td class="rate${h.win >= 10 ? ' hi' : ''}">${h.win.toFixed(1)}%</td>
      <td class="rate ev${h.ev >= 100 ? ' hi' : ''}">${h.ev}%</td>
      <td><input class="pick" type="checkbox" data-num="${h.num}" ${sel[view].includes(h.num) ? 'checked' : ''}></td>
    </tr>`).join('');
  selBody.querySelectorAll('.pick').forEach(el => el.addEventListener('change', () => {
    const n = +el.dataset.num;
    if(el.checked){ if(!sel[view].includes(n)) sel[view].push(n); }
    else{ sel[view] = sel[view].filter(x => x !== n); }
    renderStepper(); updateNavBtns();
  }));
  updateNavBtns();
}
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
function updateNavBtns(){
  prevBtn.hidden = (view === 0);
  const last = (view === 4);
  nextBtn.textContent = last ? '的中確率を算出 ▶' : '次のレースへ ▶';
  nextBtn.classList.toggle('disabled', sel[view].length === 0);
}
prevBtn.addEventListener('click', (e) => { e.preventDefault(); if(view > 0){ view--; renderAll(); } });
nextBtn.addEventListener('click', (e) => {
  e.preventDefault();
  if(sel[view].length === 0) return;
  view = (view === 4) ? 5 : view + 1;
  renderAll();
});

// ---- サマリービュー ----
function renderSummary(){
  const rows = RACES.map((r, i) => {
    const nums = [...sel[i]].sort((a, b) => a - b);
    const badges = nums.map(n => {
      const h = r.data[n - 1];
      return `<span class="waku w${h.waku}">${n}</span>`;
    }).join('');
    const oneName = nums.length === 1 ? `<span class="one-name">${r.data[nums[0] - 1].name}</span>` : '';
    const empty = nums.length === 0 ? '<span class="none">未選択</span>' : '';
    return `<div class="sum-row" data-v="${i}">
      <span class="r-no">(${i + 1})</span>
      <span class="r-race">${r.name}</span>
      <span class="r-sel">${badges}${oneName}${empty}</span>
      <span class="chev">›</span>
    </div>`;
  }).join('');
  document.getElementById('sumList').innerHTML = rows;
  document.querySelectorAll('.sum-row').forEach(el => el.addEventListener('click', () => {
    view = +el.dataset.v; renderAll();
  }));
  // 組数＝各レース選択頭数の積／的中確率＝Π(選択馬の勝利確率合計)
  const pts = sel.reduce((s, x) => s * x.length, 1);
  const hit = sel.reduce((p, nums, i) =>
    p * nums.reduce((s, n) => s + RACES[i].data[n - 1].win, 0) / 100, 1);
  document.getElementById('sumPts').textContent = pts.toLocaleString() + '点';
  const hp = hit * 100;
  document.getElementById('sumHit').textContent =
    hp >= 1 ? hp.toFixed(2) + '%' : hp.toFixed(3) + '%';
}

// ---- 全体描画 ----
function renderAll(){
  renderStepper();
  const isSum = (view === 5);
  raceView.hidden = isSum;
  sumView.hidden = !isSum;
  if(isSum) renderSummary(); else renderRace();
  window.scrollTo({top: 0, behavior: 'instant'});
}
renderAll();
})();
