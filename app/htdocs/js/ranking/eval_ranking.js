/**
 * P6 本日の好走確率ランキング（モック）
 */
(function () {
    'use strict';

const HORSES = [
  {name:'ダノンデサイル', jk:'C.ルメール', area:'jra',   venue:'東京',  race:'11R', time:'15:40', st:'final', win:28.1, fuku:58.4, pWin:26.0, pFuku:56.1, ev:142},
  {name:'カピリナ',       jk:'岩田望',    area:'jra',   venue:'福島',  race:'12R', time:'16:05', st:'prev',  win:24.5, fuku:55.2, pWin:24.5, pFuku:55.2, ev:134},
  {name:'ラヴァンダ',     jk:'武豊',      area:'jra',   venue:'東京',  race:'12R', time:'16:20', st:'prev',  win:22.8, fuku:52.1, pWin:22.8, pFuku:52.1, ev:96},
  {name:'ジャンタルマンタル', jk:'川田',  area:'jra',   venue:'東京',  race:'11R', time:'15:40', st:'final', win:21.1, fuku:52.8, pWin:23.4, pFuku:54.0, ev:96},
  {name:'シゲルバクハツ', jk:'坂井',      area:'local', venue:'大井',  race:'10R', time:'16:10', st:'prev',  win:20.6, fuku:49.8, pWin:20.6, pFuku:49.8, ev:118},
  {name:'ロンドンガーズ', jk:'横山武',    area:'jra',   venue:'函館',  race:'12R', time:'15:40', st:'final', win:19.4, fuku:47.2, pWin:16.8, pFuku:44.5, ev:105},
  {name:'テイエムトッキュウ', jk:'田辺',  area:'local', venue:'門別',  race:'8R',  time:'17:25', st:'prev',  win:18.8, fuku:46.5, pWin:18.8, pFuku:46.5, ev:88},
  {name:'ワイドラトゥール', jk:'戸崎',    area:'jra',   venue:'福島',  race:'12R', time:'16:05', st:'prev',  win:18.2, fuku:48.6, pWin:18.2, pFuku:48.6, ev:79},
  {name:'エリカエクスプレス', jk:'松山',  area:'jra',   venue:'京都',  race:'11R', time:'15:45', st:'final', win:17.5, fuku:44.9, pWin:19.0, pFuku:46.2, ev:126},
  {name:'カンチェンジュンガ', jk:'内田',  area:'local', venue:'大井',  race:'11R', time:'16:45', st:'prev',  win:16.9, fuku:43.8, pWin:16.9, pFuku:43.8, ev:101},
  {name:'マピュース',     jk:'三浦',      area:'jra',   venue:'福島',  race:'11R', time:'15:20', st:'final', win:16.2, fuku:42.7, pWin:16.5, pFuku:43.0, ev:92},
  {name:'ジョバンニ',     jk:'菅原明',    area:'local', venue:'高知',  race:'7R',  time:'18:05', st:'prev',  win:15.8, fuku:41.5, pWin:15.8, pFuku:41.5, ev:110},
  {name:'ブローザホーン', jk:'岩田望',    area:'jra',   venue:'函館',  race:'12R', time:'15:40', st:'final', win:15.1, fuku:40.2, pWin:14.0, pFuku:39.1, ev:84},
  {name:'モズモーメント', jk:'杉原',      area:'local', venue:'大井',  race:'9R',  time:'15:35', st:'final', win:14.6, fuku:39.4, pWin:15.2, pFuku:40.0, ev:97},
  {name:'エンブロイダリー', jk:'横山武',  area:'jra',   venue:'京都',  race:'11R', time:'15:45', st:'final', win:14.2, fuku:38.8, pWin:11.6, pFuku:35.9, ev:163, newIn:true},
  {name:'ヨカヨカ',       jk:'津村',      area:'jra',   venue:'京都',  race:'12R', time:'16:20', st:'prev',  win:13.8, fuku:37.9, pWin:13.8, pFuku:37.9, ev:76},
  {name:'ロードフォアエース', jk:'石川',  area:'local', venue:'門別',  race:'10R', time:'18:40', st:'prev',  win:13.1, fuku:36.6, pWin:13.1, pFuku:36.6, ev:121},
  {name:'ケリフレッドアスク', jk:'丹内',  area:'jra',   venue:'福島',  race:'12R', time:'16:05', st:'prev',  win:12.6, fuku:35.8, pWin:12.6, pFuku:35.8, ev:89},
  {name:'ダディーズビビッド', jk:'大野',  area:'local', venue:'高知',  race:'9R',  time:'19:10', st:'prev',  win:12.2, fuku:34.5, pWin:12.2, pFuku:34.5, ev:93},
  {name:'カムニャック',   jk:'M.デムーロ', area:'jra',  venue:'東京',  race:'11R', time:'15:40', st:'final', win:11.9, fuku:33.2, pWin:12.8, pFuku:34.4, ev:71},
  // ---- 発走済み（結果確定）：着順posが流れ込む。デイリー表に残す ----
  {name:'サンライズフレイム', jk:'坂井',   area:'jra',   venue:'東京',  race:'2R',  time:'10:35', st:'final', win:26.3, fuku:56.8, pWin:25.1, pFuku:55.5, ev:121, pos:1},
  {name:'ペリエール',     jk:'ルメール',  area:'jra',   venue:'東京',  race:'3R',  time:'11:10', st:'final', win:23.9, fuku:53.4, pWin:24.2, pFuku:53.8, ev:108, pos:1},
  {name:'グランレイ',     jk:'松山',      area:'jra',   venue:'福島',  race:'3R',  time:'11:20', st:'final', win:19.9, fuku:48.1, pWin:18.6, pFuku:46.9, ev:88,  pos:4},
  {name:'テーオーステルス', jk:'内田',    area:'jra',   venue:'京都',  race:'4R',  time:'11:45', st:'final', win:18.4, fuku:46.2, pWin:19.1, pFuku:47.0, ev:132, pos:2},
  {name:'アシャカトブ',   jk:'木幡巧',    area:'local', venue:'大井',  race:'1R',  time:'13:10', st:'final', win:17.2, fuku:44.0, pWin:16.5, pFuku:43.3, ev:95,  pos:3},
  {name:'リメイク',       jk:'岩田望',    area:'jra',   venue:'京都',  race:'6R',  time:'12:50', st:'final', win:15.5, fuku:41.0, pWin:14.8, pFuku:40.2, ev:102, pos:1},
  {name:'ヒビキ',         jk:'杉原',      area:'jra',   venue:'福島',  race:'5R',  time:'12:20', st:'final', win:12.4, fuku:35.5, pWin:13.0, pFuku:36.1, ev:68,  pos:7},
];

/* 前日アーカイブ（確定版）：全馬結果付きで凍結。過去日はどの会員状態でも全開放 */
const POS_YDAY = [1,3,1,2,6,1,4,2,9,3,1,5,2,8,1,3,7,2,4,11,1,2,5,1,3,2,6];
const HORSES_YDAY = HORSES.map((h, i) => ({...h, st:'final', pos: POS_YDAY[i % POS_YDAY.length]}));

/* 掲載件数ルール（バックエンド管理を想定）：
   {type:'top', n:N}＝確率上位N頭 ／ {type:'minProb', v:X}＝勝利確率X%以上
   デモ設定：すべて=上位40頭／中央=上位20頭／地方=上位20頭 */
const LIST_RULES = {
  all:   {type:'top', n:40},
  jra:   {type:'top', n:20},
  local: {type:'top', n:20},
};
let status = 'guest';
let area = 'all';
let mode = 'win';
let day = 'today';   // 'today'＝生きた表（ロックあり）／'yday'＝確定アーカイブ（全開放）

/* 会員状態→表示制御ルール（実装時はサーバ側で判定） */
const RULES = {
  guest:    {limit:3,  lockArea:null,   msg:'無料登録で4位以下を表示'},
  free:     {limit:10, lockArea:null,   msg:'サブスクで11位以下を表示'},
  sub_chuo: {limit:99, lockArea:'local', msg:'地方はプラン対象外'},
  sub_chiho:{limit:99, lockArea:'jra',   msg:'中央はプラン対象外'},
  sub_full: {limit:99, lockArea:null,   msg:''},
};
const CTA = {
  guest: {cls:'guest',
    lead:'4位以下のランキングは<b>無料登録</b>で<br>すべて見られます',
    sub:'メールアドレスだけ・30秒で完了', btn:'今すぐ無料登録 ▶'},
  free: {cls:'upsell',
    lead:'11位以下も、<b>サブスク</b>なら全頭ノーカットで表示',
    sub:'確率・リアルタイム期待値（LIVE期待値）も全レース開放', btn:'プランを見る ▶'},
  sub_chuo: {cls:'upsell',
    lead:'地方競馬のランキングは<b>フル機能プラン</b>で開放',
    sub:'平日ナイターの妙味馬もチェックできます', btn:'アップグレードを見る ▶'},
  sub_chiho: {cls:'upsell',
    lead:'中央競馬のランキングは<b>フル機能プラン</b>で開放',
    sub:'週末の重賞・WIN5対象レースもチェックできます', btn:'アップグレードを見る ▶'},
  sub_full: null,
};

function render(){
  const isArc = (day === 'yday');
  // 過去日アーカイブは全開放（実績の証明として誰でも閲覧可）／当日は会員状態でロック
  const rule = isArc ? RULES.sub_full : RULES[status];
  const src = isArc ? HORSES_YDAY : HORSES;
  const lr = LIST_RULES[area];
  let list = src
    .filter(h => area === 'all' || h.area === area)
    .sort((a, b) => b[mode] - a[mode]);
  // 掲載ルール適用（上位N頭 or 勝利確率閾値）
  list = lr.type === 'top' ? list.slice(0, lr.n) : list.filter(h => h.win >= lr.v);
  const ruleLabel = lr.type === 'top' ? `確率上位${lr.n}頭` : `勝利確率${lr.v}%以上`;
  // サマリー帯：終了馬の成績（1〜3着数・3着内率）
  const fin = list.filter(h => h.pos);
  const c1 = fin.filter(h => h.pos === 1).length;
  const c2 = fin.filter(h => h.pos === 2).length;
  const c3 = fin.filter(h => h.pos === 3).length;
  const rate = fin.length ? Math.round((c1 + c2 + c3) / fin.length * 100) : 0;
  document.getElementById('daySum').innerHTML = `
    <span class="ds-lbl">${isArc ? '<span class="fixed">確定</span>7/22の成績' : '本日ここまでの成績'}（確率上位${list.length}頭中・終了${fin.length}頭）</span>
    <span class="ds-item"><span class="ds-dot d1">1</span>${c1}</span>
    <span class="ds-item"><span class="ds-dot d2">2</span>${c2}</span>
    <span class="ds-item"><span class="ds-dot d3">3</span>${c3}</span>
    <span class="ds-rate">3着内率 ${rate}%<small>終了分</small></span>`;
  document.getElementById('arcOpen').hidden = !isArc;
  document.getElementById('sumNote').hidden = isArc;
  document.getElementById('updateNote').innerHTML = isArc
    ? '確定版：全レース終了時の最終確率・結果・期待値で凍結（以後更新されません）'
    : `<button class="rf-btn${rfAvail ? ' avail' : ''}" id="rfBtn" title="最新のオッズ・期待値に更新"><svg viewBox="0 0 24 24"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></button>
       <span><span class="live-dot"></span><b id="rfTime">${rfTime}</b>取得　タップで更新　
       <span class="lg-fin">最終</span>＝発走約30分前の最終補正で確定</span>`;
  // 前日公開版での順位（最終行の順位変動矢印に使用）
  const pKey = mode === 'win' ? 'pWin' : 'pFuku';
  const prevOrder = [...list].sort((a, b) => b[pKey] - a[pKey]);
  const prevRank = new Map(prevOrder.map((h, i) => [h.name, i + 1]));

  let visible = 0;
  document.getElementById('rkList').innerHTML = list.map((h, i) => {
    const rank = i + 1;
    // ロック判定：順位上限 or プラン対象外エリア
    const overLimit = rank > rule.limit;
    const areaLocked = rule.lockArea && h.area === rule.lockArea;
    const locked = overLimit || areaLocked;
    if(!locked) visible++;
    const msg = areaLocked ? rule.msg : overLimit ? rule.msg : '';
    const p = h[mode];
    // 順位変動（最終補正が入った行のみ表示）：前日版順位との差
    let diff = '';
    if(h.st === 'final' && !isArc){
      if(h.newIn){
        // 補正前は圏外→最終補正で新規ランクイン（矢印ではなくNEWで表現）
        diff = '<span class="rk-diff new">NEW</span>';
      }else{
        const d = prevRank.get(h.name) - rank;
        diff = d > 0 ? `<span class="rk-diff up">▲${d}</span>`
             : d < 0 ? `<span class="rk-diff down">▼${-d}</span>`
             : '<span class="rk-diff flat">→</span>';
      }
    }
    // 発走済み：着順バッジ（金銀銅はTOP結果ビューと同じ文法）。行タップ先は結果・払戻しビュー
    // 2行目の並び：エリア → 場・R → 着順（確定時のみ）→ 発走時間
    const done = !!h.pos;
    const raceLine =
      `<span class="lc">${h.area === 'jra' ? '中央' : '地方'}</span><b>${h.venue}${h.race}</b>`
      + (done ? `<span class="pos-b${h.pos <= 3 ? ' p' + h.pos : ''}">${h.pos}着</span>` : '')
      + `<span class="rc-t">発走 ${h.time}</span>`;
    return `<li>
      <a class="rk-row${locked ? ' locked' : ''}" ${locked ? `data-lockmsg="${msg}"` : ''} href="#" title="タップで${h.venue}${h.race}の${done ? '結果・払戻し' : '出馬表'}へ（当該馬をハイライト表示・モックでは無遷移）">
        <span class="rk-rankcol">
          <span class="rk-no${rank <= 3 ? ' r' + rank : ''}">${rank}</span>${diff}
        </span>
        <span class="rk-body">
          <span class="rk-name">${h.name}<small>${h.jk}</small></span>
          <span class="rk-race">${raceLine}</span>
        </span>
        <span class="rk-prob">
          <span class="v${p >= 18 ? '' : ' mid'}">${p.toFixed(1)}%</span>
          <span class="u">${h.st === 'final' ? '<span class="st-fin">最終</span>' : (mode === 'win' ? '勝利確率' : '複勝確率')}</span>
        </span>
        <span class="rk-ev">
          <span class="v${h.ev >= 100 ? ' hi' : ''}">${h.ev}%</span>
          ${h.ev >= 100 ? '<span class="myomi">妙味</span>' : '<span class="u">単勝期待値</span>'}
        </span>
        <span class="rk-go">›</span>
      </a>
    </li>`;
  }).join('');
  document.getElementById('rkNote').textContent = isArc
    ? `※ 確定アーカイブ：全レース終了後に最終確率・結果・期待値の最終値で凍結（以後不変）。過去日はどなたでも全て閲覧できます。掲載＝${ruleLabel}（バックエンド設定）。`
    : `※ デイリー掲載（掲載＝${ruleLabel}・バックエンド設定。勝利確率◯%以上の閾値型にも対応）。発走後も馬は残り、着順が流れ込みます。▲▼＝最終補正での順位変動（成績集計は各レースの最終（確定）値を使用）。行タップで出馬表（発走後は結果・払戻し）へ移動し、当該馬をハイライト表示（モックでは無遷移）。表示中 ${visible}頭／対象 ${list.length}頭。`;
  // 状態別CTA（アーカイブ閲覧中の非ログインには「今日を見るには登録」を訴求）
  const c = isArc
    ? (status === 'guest'
        ? {cls:'guest', lead:'<b>今日の</b>確率上位馬は<br>無料登録でチェックできます', sub:'昨日の成績は答え合わせ済み。今日の上位馬は毎朝更新', btn:'今すぐ無料登録 ▶'}
        : null)
    : CTA[status];
  document.getElementById('ctaWrap').innerHTML = c ? `
    <div class="rk-cta ${c.cls}">
      <p class="c-lead">${c.lead}</p>
      <p class="c-sub">${c.sub}</p>
      <a class="c-btn" href="${c.cls === 'guest' ? '/auth/entry' : '/settle/settle_point'}">${c.btn}</a>
    </div>` : '';
}

/* フィルタ・状態切替 */
document.querySelectorAll('[data-area]').forEach(b => b.addEventListener('click', (e) => {
  e.preventDefault();
  area = b.dataset.area;
  document.querySelectorAll('[data-area]').forEach(x => x.classList.toggle('on', x === b));
  render();
}));
document.querySelectorAll('[data-mode]').forEach(b => b.addEventListener('click', (e) => {
  e.preventDefault();
  mode = b.dataset.mode;
  document.querySelectorAll('[data-mode]').forEach(x => x.classList.toggle('on', x === b));
  render();
}));
document.querySelectorAll('[data-st]').forEach(b => b.addEventListener('click', (e) => {
  e.preventDefault();
  status = b.dataset.st;
  document.querySelectorAll('[data-st]').forEach(x => x.classList.toggle('on', x === b));
  render();
}));
// 日付ナビ：前日＝確定アーカイブ／本日＝生きた表（モックは2日分。実装時は任意日へ）
const dayPrev = document.getElementById('dayPrev');
const dayNext = document.getElementById('dayNext');
const dayCur = document.getElementById('dayCur');
function setDay(d){
  day = d;
  const arc = (d === 'yday');
  dayCur.textContent = arc ? '7/22(月)・確定' : '7/23(火)・本日';
  dayPrev.disabled = arc;   // モックは2日分のみ
  dayNext.disabled = !arc;
  dayPrev.textContent = arc ? '◀ 7/21(日)' : '◀ 7/22(月)';
  dayNext.textContent = arc ? '7/23(火)・本日 ▶' : '7/24(水) ▶';
  render();
}
dayPrev.addEventListener('click', (e) => { e.preventDefault(); setDay('yday'); });
dayNext.addEventListener('click', (e) => { e.preventDefault(); setDay('today'); });

/* 手動更新（モック）：
   - 実装時：新データ有無をサーバに問い合わせ、ある場合のみ⟳を点灯（.avail）
   - 更新時のルール：並び順・スクロール位置は維持し、変化した値だけフラッシュ表示 */
let rfTime = '15:15';
let rfAvail = true;   // モック：新データありの点灯状態から開始
document.querySelector('.filter').addEventListener('click', e => {
  const btn = e.target.closest('#rfBtn');
  if(!btn || day !== 'today') return;
  btn.classList.add('spin');
  // 期待値の一部が動く（モック：先頭の未ロック2頭）
  const changed = [];
  const targets = HORSES.filter(h => !h.pos).slice(0, 3);
  targets[0].ev += 4; changed.push(targets[0].name);
  targets[1].ev -= 3; changed.push(targets[1].name);
  rfTime = '15:20';
  rfAvail = false;    // 取得直後は消灯
  setTimeout(() => {
    render();
    // 変化した値だけフラッシュ（並びは確率順のまま不変）
    document.querySelectorAll('.rk-row').forEach(row => {
      const nm = row.querySelector('.rk-name');
      if(nm && changed.some(c => nm.textContent.startsWith(c)))
        row.querySelector('.rk-ev').classList.add('flash');
    });
  }, 350);
});

document.getElementById('rkList').addEventListener('click', (e) => {
  const row = e.target.closest('.rk-row');
  if (row) e.preventDefault();
});

render();

})();
