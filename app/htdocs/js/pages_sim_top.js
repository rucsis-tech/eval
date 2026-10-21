/**
 * P4 的中確率シミュレーション（モック）
 */
(function () {
    'use strict';

// 出走馬データ（ヴィクトリアマイル18頭サンプル）
// win=勝利確率% fuku=複勝確率% mark=予想印（TOP連動想定）
const HORSES = [
  {num:1, waku:1, name:'カピリナ',        win:24.5, fuku:55.2, mark:'▲'},
  {num:2, waku:1, name:'ワイドラトゥール', win:18.2, fuku:48.6, mark:''},
  {num:3, waku:2, name:'マピュース',      win:12.8, fuku:39.4, mark:''},
  {num:4, waku:2, name:'エリカエクスプレス', win:9.6, fuku:33.8, mark:'◯'},
  {num:5, waku:3, name:'ケリフレッドアスク', win:6.2, fuku:25.1, mark:''},
  {num:6, waku:3, name:'ラヴァンダ',      win:8.4, fuku:30.2, mark:'◎'},
  {num:7, waku:4, name:'クイーンズウォーク', win:4.8, fuku:21.5, mark:''},
  {num:8, waku:4, name:'カムニャック',    win:3.9, fuku:18.9, mark:''},
  {num:9, waku:5, name:'ココナッツブラウン', win:2.6, fuku:14.2, mark:'△'},
  {num:10, waku:5, name:'ドロップオブライト', win:2.1, fuku:12.0, mark:''},
  {num:11, waku:6, name:'ボンドガール',   win:1.8, fuku:10.6, mark:''},
  {num:12, waku:6, name:'エンブロイダリー', win:1.6, fuku:9.8, mark:'☆'},
  {num:13, waku:7, name:'カナテープ',     win:1.2, fuku:7.9, mark:''},
  {num:14, waku:7, name:'ジョスラン',     win:0.9, fuku:6.4, mark:''},
  {num:15, waku:7, name:'アイサンサン',   win:0.6, fuku:4.8, mark:''},
  {num:16, waku:8, name:'ニシノティアモ', win:0.4, fuku:3.5, mark:''},
  {num:17, waku:8, name:'パラディレーヌ', win:0.3, fuku:2.9, mark:''},
  {num:18, waku:8, name:'チェルヴィニア', win:0.1, fuku:1.7, mark:''},
];
const byNum = {}; HORSES.forEach(h => byNum[h.num] = h);

// 券種ごとの買い方（※人気順は全券種共通の時短オプションとして付与）
const KINDS = {
  umaren:  {label:'馬連',  n:2, ordered:false, methods:['人気順','流し','ボックス','フォーメーション']},
  umatan:  {label:'馬単',  n:2, ordered:true,  methods:['人気順','軸1着流し','軸2着流し','ボックス','フォーメーション']},
  wide:    {label:'ワイド', n:2, ordered:false, methods:['人気順','軸1頭流し','軸2頭流し','ボックス','フォーメーション']},
  sanfuku: {label:'3連複', n:3, ordered:false, methods:['人気順','軸1頭流し','軸2頭流し','ボックス','フォーメーション']},
  santan:  {label:'3連単', n:3, ordered:true,  methods:['人気順','ボックス','フォーメーション']},
};

let kind = 'wide';
let method = '軸1頭流し';
let probMode = 'win';   // 'win' | 'fuku'（テーブル表示のみ切替）
let ninkiN = 10;

// ---- 選択列の構成を決める ----
function colsFor(kind, method){
  const k = KINDS[kind];
  if(method === '人気順') return [];                     // 人気順は自動選択（上位N頭）
  if(method === 'ボックス') return [{key:'sel', label:'選択', type:'check'}];
  if(method === 'フォーメーション'){
    if(kind === 'umatan') return [{key:'f1',label:'1着',type:'check'},{key:'f2',label:'2着',type:'check'}];
    if(kind === 'santan') return [{key:'f1',label:'1着',type:'check'},{key:'f2',label:'2着',type:'check'},{key:'f3',label:'3着',type:'check'}];
    if(k.n === 2) return [{key:'f1',label:'1頭目',type:'check'},{key:'f2',label:'2頭目',type:'check'}];
    return [{key:'f1',label:'1頭目',type:'check'},{key:'f2',label:'2頭目',type:'check'},{key:'f3',label:'3頭目',type:'check'}];
  }
  if(method === '軸2頭流し') return [{key:'axis',label:'軸馬',type:'check',max:2},{key:'aite',label:'相手',type:'check'}];
  // 流し／軸1着流し／軸2着流し／軸1頭流し
  return [{key:'axis',label:'軸馬',type:'radio'},{key:'aite',label:'相手',type:'check'}];
}

// ---- テーブル描画 ----
const selHead = document.getElementById('selHead');
const selBody = document.getElementById('selBody');
function renderTable(){
  const cols = colsFor(kind, method);
  selHead.innerHTML =
    '<th style="width:26px">枠</th><th style="width:30px">番</th><th style="width:30px">印</th><th>馬名</th>'
    + cols.map(c => `<th style="width:${cols.length>=3?'44':'52'}px">${c.label}</th>`).join('');
  selBody.innerHTML = HORSES.map(h => {
    const p = probMode === 'win' ? h.win : h.fuku;
    const hi = (probMode === 'win' ? h.win >= 8 : h.fuku >= 30);
    const inputs = cols.map(c => {
      const t = c.type === 'radio' ? 'radio' : 'checkbox';
      const name = c.type === 'radio' ? `name="${c.key}"` : '';
      return `<td><input class="pick" type="${t}" ${name} data-key="${c.key}" data-num="${h.num}" ${c.max?`data-max="${c.max}"`:''}></td>`;
    }).join('');
    return `<tr>
      <td><span class="waku w${h.waku}">${h.waku}</span></td>
      <td style="font-weight:800">${h.num}</td>
      <td><span class="mk${h.mark?'':' none'}">${h.mark || '−'}</span></td>
      <td class="name">${h.name}<span class="prob${hi?' hi':''}">${p.toFixed(1)}%</span></td>
      ${inputs}</tr>`;
  }).join('');
  // 入力イベント
  selBody.querySelectorAll('.pick').forEach(el => el.addEventListener('change', onPick));
  updateNote(); updateCount();
}

function onPick(e){
  const el = e.target;
  // 軸2頭流し：軸チェックは最大2つ
  if(el.dataset.max){
    const same = selBody.querySelectorAll(`.pick[data-key="${el.dataset.key}"]:checked`);
    if(same.length > +el.dataset.max){ el.checked = false; }
  }
  updateCount();
}

function picked(key){
  return Array.from(selBody.querySelectorAll(`.pick[data-key="${key}"]:checked`)).map(el => +el.dataset.num);
}

// ---- 買い目生成 ----
function combos(){
  const k = KINDS[kind];
  if(method === '人気順') return []; // 人気順は選択不要（現在オッズの人気順リストを直接表示）
  if(method === 'ボックス'){
    const s = picked('sel');
    return k.ordered ? perms(s, k.n) : sets(s, k.n);
  }
  if(method === 'フォーメーション'){
    const g = [picked('f1'), picked('f2'), picked('f3')].slice(0, k.n);
    if(g.some(x => !x.length)) return [];
    let out = [];
    const walk = (i, acc) => {
      if(i === g.length){ out.push([...acc]); return; }
      for(const n of g[i]){ if(!acc.includes(n)){ acc.push(n); walk(i+1, acc); acc.pop(); } }
    };
    walk(0, []);
    if(!k.ordered){ // 順不同券種は組み合わせを集合で重複排除
      const seen = new Set();
      out = out.filter(c => { const key = [...c].sort((a,b)=>a-b).join('-'); if(seen.has(key)) return false; seen.add(key); return true; });
    }
    return out;
  }
  // 流し系
  const axis = picked('axis'), aite = picked('aite').filter(n => !axis.includes(n));
  if(!axis.length || !aite.length) return [];
  if(method === '軸2頭流し'){
    if(axis.length < 2) return [];
    if(k.n === 3) return aite.map(p => [axis[0], axis[1], p]);          // 3連複：軸2頭＋相手1頭
    return aite.flatMap(p => [[axis[0], p], [axis[1], p]]);             // ワイド：各軸×相手
  }
  const a = axis[0];
  if(k.n === 3) return sets(aite, 2).map(pr => [a, ...pr]);             // 3連複軸1頭：軸＋相手2頭
  if(kind === 'umatan' && method === '軸2着流し') return aite.map(p => [p, a]);
  return aite.map(p => [a, p]);                                          // 馬連/ワイド流し・馬単軸1着
}
function sets(arr, n){
  const out = [];
  const walk = (start, acc) => {
    if(acc.length === n){ out.push([...acc]); return; }
    for(let i = start; i < arr.length; i++){ acc.push(arr[i]); walk(i+1, acc); acc.pop(); }
  };
  walk(0, []); return out;
}
function perms(arr, n){
  const out = [];
  const walk = acc => {
    if(acc.length === n){ out.push([...acc]); return; }
    for(const x of arr){ if(!acc.includes(x)){ acc.push(x); walk(acc); acc.pop(); } }
  };
  walk([]); return out;
}

// ---- 的中確率（Harville式・勝利確率ベースの参考値） ----
const P = {}; HORSES.forEach(h => P[h.num] = h.win / 100);
const pTan = (a,b,c) => c === undefined
  ? P[a] * P[b] / (1 - P[a])
  : P[a] * P[b] / (1 - P[a]) * P[c] / (1 - P[a] - P[b]);
function hitProb(c){
  if(kind === 'umatan') return pTan(c[0], c[1]);
  if(kind === 'umaren') return pTan(c[0], c[1]) + pTan(c[1], c[0]);
  if(kind === 'santan') return pTan(c[0], c[1], c[2]);
  if(kind === 'sanfuku') return perms(c, 3).reduce((s,o) => s + pTan(o[0], o[1], o[2]), 0);
  // ワイド＝2頭とも3着以内：全3連複集合の和
  let s = 0;
  for(const h of HORSES){ if(h.num !== c[0] && h.num !== c[1]) s += perms([c[0], c[1], h.num], 3).reduce((t,o) => t + pTan(o[0], o[1], o[2]), 0); }
  return s;
}
// ---- 擬似オッズ・期待値（モック：実装時は現在オッズを取得） ----
// オッズ＝控除率22.5%を的中確率に反映＋組み合わせ固有の揺らぎ（決定的）
function oddsOf(c, hit){
  const seed = c.reduce((s,n,i) => s + n * [7,13,17][i], 0) * 31 + kind.length;
  const j = 0.8 + (seed % 61) / 100;      // 0.80〜1.40
  return 0.775 / hit * j;
}
function evPct(c, hit){ return Math.round(hit * oddsOf(c, hit) * 100); }
// 券種の全組み合わせ（人気順リスト用）
function allCombos(){
  const nums = HORSES.map(h => h.num);
  const k = KINDS[kind];
  return k.ordered ? perms(nums, k.n) : sets(nums, k.n);
}

// ---- 表示更新 ----
const ptCount = document.getElementById('ptCount');
const calcBtn = document.getElementById('calcBtn');
const resultSec = document.getElementById('resultSec');
function updateCount(){
  const n = combos().length;
  ptCount.textContent = n + '点';
  ptCount.classList.toggle('zero', n === 0);
  calcBtn.classList.toggle('disabled', n === 0);
  resultSec.hidden = true; // 条件が変わったら結果は再計算まで非表示
}
function updateNote(){
  const notes = {
    '人気順':'現在オッズを取得し、この券種の人気順に組み合わせ・的中確率・期待値を表示します（オッズは5分毎更新）。表示点数は上で選択。',
    '流し':'軸馬1頭（ラジオ）を選び、相手（チェック）との全組み合わせを購入します。',
    '軸1着流し':'軸馬が1着固定。相手が2着の全組み合わせです。',
    '軸2着流し':'軸馬が2着固定。相手が1着の全組み合わせです。',
    '軸1頭流し':'軸馬1頭＋相手から2頭の全組み合わせです。',
    '軸2頭流し': kind === 'wide' ? '軸馬2頭（チェック・最大2）それぞれと相手の組み合わせです。' : '軸馬2頭（チェック・最大2）＋相手1頭の組み合わせです。',
    'ボックス':'選択した馬の全組み合わせを購入します。',
    'フォーメーション':'着順（または頭数枠）ごとに馬を選び、その全組み合わせを購入します。',
  };
  document.getElementById('selNote').textContent =
    '※ ' + (notes[method] || '') + ' 印はTOPの出馬表で入力した予想印です。馬名右の数値はEvalの' + (probMode === 'win' ? '勝利確率' : '複勝確率') + '。';
  document.getElementById('methodNote').textContent = KINDS[kind].label + 'を選択中';
}

// ---- 結果描画 ----
const resHead = document.getElementById('resHead');
const resBody = document.getElementById('resBody');
const resCount = document.getElementById('resCount');
function renderResHead(withRank){
  resHead.innerHTML =
    (withRank ? '<th style="width:38px">人気</th>' : '')
    + '<th style="width:70px">予想印</th><th>組み合わせ</th><th style="width:74px">的中確率</th><th style="width:60px">期待値</th>';
}
function rowHTML(r, rank){
  const sep = KINDS[kind].ordered ? ' → ' : ' − ';
  const mk = r.c.map(n => byNum[n].mark || '無').join(KINDS[kind].ordered ? '→' : '−');
  const hp = r.hit * 100;
  const hpTxt = hp >= 10 ? hp.toFixed(1) : hp.toFixed(2);
  const ev = evPct(r.c, r.hit);
  return `<tr>
    ${rank ? `<td class="rank">${rank}</td>` : ''}
    <td class="marks">${mk}</td>
    <td class="combo">${r.c.join(sep)}</td>
    <td class="hit${hp >= 5 ? ' hi' : ''}">${hpTxt}%</td>
    <td class="ev${ev >= 100 ? ' hi' : ''}">${ev}%</td></tr>`;
}
calcBtn.addEventListener('click', () => {
  const cs = combos();
  if(!cs.length) return;
  const rows = cs.map(c => ({c, hit: hitProb(c)})).sort((a,b) => b.hit - a.hit);
  const CAP = 60;
  renderResHead(false);
  resBody.innerHTML = rows.slice(0, CAP).map(r => rowHTML(r)).join('');
  resCount.textContent = rows.length + '点' + (rows.length > CAP ? `（上位${CAP}点を表示）` : '');
  resultSec.hidden = false;
  resultSec.scrollIntoView({behavior:'smooth', block:'start'});
});

// ---- 人気順ビュー：現在オッズの人気順に組み合わせ＋的中確率＋期待値を表示 ----
function renderNinki(){
  const rows = allCombos()
    .map(c => { const hit = hitProb(c); return {c, hit, odds: oddsOf(c, hit)}; })
    .sort((a,b) => a.odds - b.odds)   // オッズ昇順＝人気順
    .slice(0, ninkiN);
  renderResHead(true);
  resBody.innerHTML = rows.map((r, i) => rowHTML(r, i + 1)).join('');
  resCount.textContent = `${KINDS[kind].label} 人気上位${ninkiN}点・15:32時点オッズ`;
  resultSec.hidden = false;
}

// ---- 券種・買い方・モード切替 ----
const methodChips = document.getElementById('methodChips');
const ninkiRow = document.getElementById('ninkiRow');
function renderMethods(){
  methodChips.innerHTML = KINDS[kind].methods.map(m =>
    `<button class="chip${m === method ? ' on' : ''}" data-m="${m}">${m}</button>`).join('');
  methodChips.querySelectorAll('.chip').forEach(b => b.addEventListener('click', () => {
    method = b.dataset.m; renderMethods(); renderAll();
  }));
  ninkiRow.hidden = (method !== '人気順');
}
document.querySelectorAll('#kindChips .chip').forEach(b => b.addEventListener('click', () => {
  kind = b.dataset.kind;
  document.querySelectorAll('#kindChips .chip').forEach(x => x.classList.toggle('on', x === b));
  if(!KINDS[kind].methods.includes(method)) method = KINDS[kind].methods[1] || KINDS[kind].methods[0];
  renderMethods(); renderAll();
}));
ninkiRow.querySelectorAll('.chip').forEach(b => b.addEventListener('click', () => {
  ninkiN = +b.dataset.n;
  ninkiRow.querySelectorAll('.chip').forEach(x => x.classList.toggle('on', x === b));
  if(method === '人気順') renderNinki();
}));
const modeWin = document.getElementById('modeWin');
const modeFuku = document.getElementById('modeFuku');
[modeWin, modeFuku].forEach(b => b.addEventListener('click', () => {
  probMode = (b === modeWin) ? 'win' : 'fuku';
  modeWin.classList.toggle('on', probMode === 'win');
  modeFuku.classList.toggle('on', probMode === 'fuku');
  // 選択状態を保持したまま表示のみ更新
  const saved = Array.from(selBody.querySelectorAll('.pick:checked')).map(el => el.dataset.key + ':' + el.dataset.num);
  renderTable();
  saved.forEach(s => {
    const [k2, n] = s.split(':');
    const el = selBody.querySelector(`.pick[data-key="${k2}"][data-num="${n}"]`);
    if(el) el.checked = true;
  });
  updateCount();
}));

// ---- 全体描画：人気順は選択UIを畳んで人気順リストを直接表示 ----
const selWrap = document.getElementById('selWrap');
const countBar = document.getElementById('countBar');
const modeRow = document.getElementById('modeRow');
function renderAll(){
  const ninki = (method === '人気順');
  selWrap.hidden = ninki;
  countBar.hidden = ninki;
  modeRow.hidden = ninki;
  if(ninki){ updateNote(); renderNinki(); }
  else { renderTable(); resultSec.hidden = true; }
}

// ---- WIN5ボタンの位置制御 ----
// 実装時：中央競馬開催の前日20:00〜当日15:00（販売時間）は上部（日程と券種の間）、
//         それ以外は下部ボタン群に表示。サーバ時刻で判定。
// モック：下のモック確認用トグルで切替（デフォルト＝販売中）
const win5Top = document.getElementById('win5Top');
const win5Btm = document.getElementById('win5Btm');
function applyWin5(onSale){
  win5Top.hidden = !onSale;
  win5Btm.hidden = onSale;
}
document.querySelectorAll('.demo-switch button').forEach(b => b.addEventListener('click', () => {
  document.querySelectorAll('.demo-switch button').forEach(x => x.classList.toggle('on', x === b));
  applyWin5(b.dataset.w === '1');
}));
applyWin5(true);

renderMethods();
renderAll();
})();
