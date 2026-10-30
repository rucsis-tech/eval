/**
 * P1 TOP（モック）
 * probTable / viewResult は JS データから描画（P4 sim_top.js と同形式）
 */
const PROB_HORSES = [
  {num:2,  waku:1, name:'ダノンデサイル',     meta:'牡4／C.ルメール',   win:28.1, fuku:58.4, probHi:true,  diff:'up',   evWin:142, evFuku:'92〜105%',  evHi:true,  hf:'mid', mark:''},
  {num:5,  waku:3, name:'ジャンタルマンタル', meta:'牡4／川田将雅',     win:21.1, fuku:52.8, probHi:true,  diff:'down', evWin:96,  evFuku:'88〜101%',  evHi:false, hf:'mid', mark:''},
  {num:1,  waku:1, name:'エンブロイダリー',   meta:'牝3／横山武史',     win:15.7, fuku:41.1, probHi:false, diff:'up',   evWin:163, evFuku:'104〜128%', evHi:true,  hf:'hi',  mark:''},
  {num:3,  waku:2, name:'カムニャック',       meta:'牝4／M.デムーロ',   win:12.4, fuku:33.2, probHi:false, diff:null,   evWin:88,  evFuku:'85〜97%',   evHi:false, hf:'',    mark:''},
  {num:4,  waku:2, name:'ウインカーネリアン', meta:'牡7／三浦皇成',     win:8.9,  fuku:26.7, probHi:false, diff:null,   evWin:74,  evFuku:'78〜90%',   evHi:false, hf:'',    mark:''},
  {num:8,  waku:4, name:'レガレイラ',         meta:'牝4／戸崎圭太',     win:4.2,  fuku:18.5, probHi:false, diff:'up',   evWin:121, evFuku:'96〜115%',  evHi:true,  hf:'mid', mark:''},
  {num:7,  waku:4, name:'ソールオリエンス',   meta:'牡5／横山和生',     win:2.8,  fuku:14.2, probHi:false, diff:null,   evWin:68,  evFuku:'82〜98%',   evHi:false, hf:'',    mark:''},
  {num:6,  waku:3, name:'ベラジオオペラ',     meta:'牡5／横山典弘',     win:2.1,  fuku:12.6, probHi:false, diff:null,   evWin:92,  evFuku:'88〜102%',  evHi:false, hf:'mid', mark:''},
  {num:9,  waku:5, name:'シックスペンス',     meta:'牡4／松山弘平',     win:1.4,  fuku:9.8,  probHi:false, diff:'down', evWin:110, evFuku:'95〜110%',  evHi:true,  hf:'mid', mark:''},
  {num:10, waku:5, name:'ジューンテイク',     meta:'牡4／岩田望来',     win:0.9,  fuku:7.4,  probHi:false, diff:null,   evWin:55,  evFuku:'70〜84%',   evHi:false, hf:'',    mark:''},
  {num:11, waku:6, name:'サンライズアース',   meta:'牡4／池添謙一',     win:0.7,  fuku:6.2,  probHi:false, diff:null,   evWin:83,  evFuku:'79〜93%',   evHi:false, hf:'',    mark:''},
  {num:12, waku:6, name:'メイショウタバル',   meta:'牡4／浜中俊',       win:0.6,  fuku:5.1,  probHi:false, diff:null,   evWin:47,  evFuku:'60〜75%',   evHi:false, hf:'',    mark:''},
  {num:13, waku:7, name:'コスモキュランダ',   meta:'牡5／丹内祐次',     win:0.4,  fuku:3.9,  probHi:false, diff:null,   evWin:62,  evFuku:'72〜88%',   evHi:false, hf:'',    mark:''},
  {num:14, waku:7, name:'アーバンシック',     meta:'牡4／武豊',         win:0.3,  fuku:3.2,  probHi:false, diff:null,   evWin:71,  evFuku:'65〜80%',   evHi:false, hf:'',    mark:''},
  {num:15, waku:8, name:'ダノンエアズロック', meta:'牡4／坂井瑠星',     win:0.2,  fuku:2.4,  probHi:false, diff:null,   evWin:39,  evFuku:'50〜66%',   evHi:false, hf:'',    mark:''},
  {num:16, waku:8, name:'シュガークン',       meta:'牡4／田辺裕信',     win:0.1,  fuku:1.6,  probHi:false, diff:null,   evWin:28,  evFuku:'42〜58%',   evHi:false, hf:'',    mark:''},
];

// 結果・払戻しビュー（着順順・モック確定値）
const RESULT_ROWS = [
  {chaku:1,  num:1,  waku:1, name:'エンブロイダリー',   ninki:5,  probRank:3,  probRankCls:'p3', evWin:163, evFuku:121, evWinHi:true,  evFukuHi:true},
  {chaku:2,  num:2,  waku:1, name:'ダノンデサイル',     ninki:1,  probRank:1,  probRankCls:'p1', evWin:142, evFuku:98,  evWinHi:true,  evFukuHi:false},
  {chaku:3,  num:8,  waku:4, name:'レガレイラ',         ninki:8,  probRank:6,  probRankCls:null, evWin:121, evFuku:108, evWinHi:true,  evFukuHi:true},
  {chaku:4,  num:5,  waku:3, name:'ジャンタルマンタル', ninki:2,  probRank:2,  probRankCls:'p2', evWin:96,  evFuku:95,  evWinHi:false, evFukuHi:false},
  {chaku:5,  num:3,  waku:2, name:'カムニャック',       ninki:3,  probRank:4,  probRankCls:'p4', evWin:88,  evFuku:90,  evWinHi:false, evFukuHi:false},
  {chaku:6,  num:6,  waku:3, name:'ベラジオオペラ',     ninki:6,  probRank:8,  probRankCls:null, evWin:92,  evFuku:93,  evWinHi:false, evFukuHi:false},
  {chaku:7,  num:4,  waku:2, name:'ウインカーネリアン', ninki:4,  probRank:5,  probRankCls:'p5', evWin:74,  evFuku:84,  evWinHi:false, evFukuHi:false},
  {chaku:8,  num:9,  waku:5, name:'シックスペンス',     ninki:7,  probRank:9,  probRankCls:null, evWin:110, evFuku:101, evWinHi:true,  evFukuHi:true},
  {chaku:9,  num:7,  waku:4, name:'ソールオリエンス',   ninki:9,  probRank:7,  probRankCls:null, evWin:68,  evFuku:88,  evWinHi:false, evFukuHi:false},
  {chaku:10, num:11, waku:6, name:'サンライズアース',   ninki:11, probRank:11, probRankCls:null, evWin:83,  evFuku:86,  evWinHi:false, evFukuHi:false},
  {chaku:11, num:10, waku:5, name:'ジューンテイク',     ninki:10, probRank:10, probRankCls:null, evWin:55,  evFuku:76,  evWinHi:false, evFukuHi:false},
  {chaku:12, num:13, waku:7, name:'コスモキュランダ',   ninki:13, probRank:13, probRankCls:null, evWin:62,  evFuku:79,  evWinHi:false, evFukuHi:false},
  {chaku:13, num:12, waku:6, name:'メイショウタバル',   ninki:12, probRank:12, probRankCls:null, evWin:47,  evFuku:66,  evWinHi:false, evFukuHi:false},
  {chaku:14, num:14, waku:7, name:'アーバンシック',     ninki:14, probRank:14, probRankCls:null, evWin:71,  evFuku:72,  evWinHi:false, evFukuHi:false},
  {chaku:15, num:16, waku:8, name:'シュガークン',       ninki:16, probRank:16, probRankCls:null, evWin:28,  evFuku:48,  evWinHi:false, evFukuHi:false},
  {chaku:16, num:15, waku:8, name:'ダノンエアズロック', ninki:15, probRank:15, probRankCls:null, evWin:39,  evFuku:55,  evWinHi:false, evFukuHi:false},
];
const RESULT_PAYOUTS = [
  {kind:'単勝', combo:'1', yen:'1,630円'},
  {kind:'複勝', combo:['1','2','8'], yen:['410円','150円','890円']},
  {kind:'馬連', combo:'1-2', yen:'2,340円'},
  {kind:'ワイド', combo:['1-2','1-8','2-8'], yen:['780円','3,120円','1,540円']},
  {kind:'馬単', combo:'1→2', yen:'5,120円'},
  {kind:'3連複', combo:'1-2-8', yen:'12,480円'},
  {kind:'3連単', combo:'1→2→8', yen:'68,930円'},
];

const probTable = document.getElementById('probTable');

function probEsc(s){
  return String(s).replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]));
}

function renderProbTable(){
  const tbody = document.getElementById('probTableBody');
  tbody.innerHTML = PROB_HORSES.map(h => {
    const diffHtml = h.diff === 'up' ? '<i class="diff up">▲</i>'
      : h.diff === 'down' ? '<i class="diff down">▼</i>' : '';
    const probCls = 'rate' + (h.probHi ? ' hi' : '');
    const evCls = 'rate ev' + (h.evHi ? ' hi' : '');
    const mark = h.mark || '';
    const hf = h.hf || '';
    return `<tr>
      <td><span class="waku w${h.waku}">${h.num}</span></td>
      <td class="mark-td"><button type="button" class="mark-btn" data-mark="${mark}">${mark}</button></td>
      <td class="name">${probEsc(h.name)}<small>${probEsc(h.meta)}</small></td>
      <td class="${probCls}" data-win="${h.win}%" data-fuku="${h.fuku}%">${h.win}%${diffHtml}</td>
      <td class="${evCls}" data-win="${h.evWin}%" data-fuku="${h.evFuku}" data-hw="${h.evHi ? '1' : '0'}" data-hf="${hf}">${h.evWin}%</td>
    </tr>`;
  }).join('');
}

function renderResultTable(){
  const tbody = document.getElementById('resultTableBody');
  tbody.innerHTML = RESULT_ROWS.map(r => {
    const chakuCls = r.chaku <= 3 ? `chaku c${r.chaku}` : 'chaku';
    const prank = r.probRankCls
      ? `<span class="prank ${r.probRankCls}">${r.probRank}</span>`
      : String(r.probRank);
    return `<tr>
      <td><span class="${chakuCls}">${r.chaku}</span></td>
      <td><span class="waku w${r.waku}">${r.num}</span></td>
      <td><span class="r-mark" data-mark="">−</span></td>
      <td class="name">${probEsc(r.name)}</td>
      <td>${r.ninki}</td>
      <td>${prank}</td>
      <td class="rate${r.evWinHi ? ' hi' : ''}">${r.evWin}%</td>
      <td class="rate${r.evFukuHi ? ' hi' : ''}">${r.evFuku}%</td>
    </tr>`;
  }).join('');
}

function renderPayoutTable(){
  const tbody = document.getElementById('payoutTableBody');
  const cell = v => Array.isArray(v) ? v.join('<br>') : v;
  tbody.innerHTML = RESULT_PAYOUTS.map(p =>
    `<tr><td class="k">${probEsc(p.kind)}</td><td class="c">${cell(p.combo)}</td><td class="y">${cell(p.yen)}</td></tr>`
  ).join('');
}

renderProbTable();
renderResultTable();
renderPayoutTable();

// 勝利確率／複勝確率タブの表示切替（モック動作）
  const tabWin  = document.getElementById('tab-win');
  const tabFuku = document.getElementById('tab-fuku');
  const thProb  = document.getElementById('th-prob');
  const thEv    = document.getElementById('th-ev');
  let cells     = document.querySelectorAll('#probTable tbody td.rate');

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

  // モック確認用：A2HSバナー再表示（common.js の A2HS 制御をリセット）
  const a2hsReset = document.getElementById('a2hsReset');
  if (a2hsReset) {
    a2hsReset.addEventListener('click', () => {
      try {
        localStorage.removeItem('evm_a2hs_dismissed_at');
        localStorage.removeItem('evm_a2hs_installed');
      } catch (e) {}
      const a2hs = document.getElementById('a2hs');
      if (a2hs) a2hs.hidden = false;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ===== キャンペーンモーダル（複数枚・連続表示） =====
  // 対象ステータス：guest=非ログイン／free=ログイン無料／paid=ログイン有料（premium・light両方）／
  //                premium=プレミアム（全機能）／light=ライト（中央限定・地方限定＝プレミアム以外）
  // 表示順：システム告知 → priority降順 → 配信開始が新しい順。1セッションの最大表示枚数は管理画面設定（既定3枚）
  // ×・「閉じる」・背景タップで閉じると、次の1枚が続けて表示される（CTAタップ時は遷移するため以降は表示しない）
  // 頻度制御（同一モーダルは既定1回・クールダウン日数）は従来どおりモーダル単位でサーバ管理
  const MODALS = [
    {id:'cp-spat4',  type:'campaign', priority:80, audience:['guest','free','paid'], visual:'v-tie', lbl:'タイアップ企画',
     title:'SPAT4×Eval MODE<br>連携キャンペーン開催中', desc:'期間中の投票で最大10,000ptプレゼント（〜7/31）',
     cta:'キャンペーン詳細を見る ▶', href:'/lp/campaign?id=cp-spat4'},
    {id:'cp-reg',    type:'campaign', priority:70, audience:['guest'], visual:'v-reg', lbl:'新規登録特典',
     title:'今なら無料登録で<br>100ptプレゼント', desc:'登録は30秒・メールアドレスだけでOK（〜8/31）',
     cta:'無料登録する ▶', href:'/auth/entry'},
    {id:'cp-subfree',type:'campaign', priority:60, audience:['free'], visual:'v-sub', lbl:'期間限定',
     title:'プレミアムプラン<br>初月無料キャンペーン', desc:'全レースの確率・期待値が今なら初月0円（〜8/10）',
     cta:'初月無料で試す ▶', href:'/settle/settle_subsc'},
    {id:'cp-up',     type:'campaign', priority:55, audience:['light'], visual:'v-sub', lbl:'アップグレード',
     title:'中央も地方も全部見る<br>プレミアムへ', desc:'差額のみで今月から切替可能',
     cta:'プランを見る ▶', href:'/settle/settle_subsc'},
    {id:'cp-ptsale', type:'campaign', priority:40, audience:['free','paid'], visual:'v-pt', lbl:'ptセール',
     title:'ポイント20%増量セール', desc:'期間中のpt購入で20%ボーナス（〜7/27）',
     cta:'ポイントを購入する ▶', href:'/settle/settle_point'},
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
  const page = '/pages/ambassadors';
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
