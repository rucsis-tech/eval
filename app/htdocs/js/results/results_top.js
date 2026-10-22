/**
 * P5 的中実績（モック）
 */
(function () {
    'use strict';

const TYPE_LABEL = {uma:'馬連', utan:'馬単', sanpuku:'3連複', santan:'3連単'};
const AREA_LABEL = {jra:'中央', local:'地方'};

// モック固定の「現在時刻」（実装時は現在時刻・サーバー時刻）
const NOW = {label:'15:40', min: 15 * 60 + 40};
const NEW_WINDOW_MIN = 180; // 発走時刻から約3時間はNEW

/* 日別データ（仮）：today=本日（開催中・リアルタイム反映）
   jraN/localN=対象R数（nullは非開催）／jraDone/localDone=結果確定済みR数
   races=的中レースのみ（a=エリア, t=場, r=R, name=レース名, time=発走時刻, pay=的中券種→払戻円/各100円） */
const DAYS = [{"date":"8/8(土)","jraN":24,"jraDone":22,"localN":12,"localDone":6,"races":[{"a":"jra","t":"新潟","r":1,"name":"2歳新馬","time":"9:50","pay":{"uma":2680,"utan":5220,"sanpuku":7300,"santan":33770}},{"a":"jra","t":"新潟","r":6,"name":"3歳以上1勝クラス","time":"12:40","pay":{"uma":3420,"utan":7280,"sanpuku":10710,"santan":50720}},{"a":"jra","t":"新潟","r":7,"name":"3歳未勝利","time":"13:14","pay":{"uma":2140,"utan":4420}},{"a":"jra","t":"新潟","r":8,"name":"2歳新馬","time":"13:48","pay":{"uma":2680,"utan":4720}},{"a":"jra","t":"新潟","r":9,"name":"羊ヶ丘特別","time":"14:22","pay":{"uma":1660,"utan":2900,"sanpuku":2850,"santan":16910}},{"a":"jra","t":"新潟","r":11,"name":"関屋記念（G3）","time":"15:30","pay":{"uma":2340,"utan":4180,"sanpuku":8120,"santan":68930}},{"a":"jra","t":"札幌","r":2,"name":"2歳新馬","time":"10:24","pay":{"uma":4380,"utan":8030}},{"a":"jra","t":"札幌","r":6,"name":"3歳以上3勝クラス","time":"12:40","pay":{"uma":380,"utan":800,"sanpuku":1010,"santan":4000}},{"a":"jra","t":"札幌","r":8,"name":"3歳以上3勝クラス","time":"13:48","pay":{"uma":780,"utan":1630}},{"a":"jra","t":"札幌","r":9,"name":"日本海S","time":"14:22","pay":{"uma":2680,"utan":5700,"sanpuku":7180,"santan":42120}},{"a":"jra","t":"札幌","r":11,"name":"札幌日刊スポーツ杯","time":"15:30","pay":{"uma":1660,"utan":3400,"sanpuku":5580,"santan":21440}},{"a":"local","t":"佐賀","r":1,"name":"B3","time":"10:20","pay":{"uma":380,"utan":730,"sanpuku":1090,"santan":4030}},{"a":"local","t":"佐賀","r":5,"name":"スタールビー賞","time":"12:28","pay":{"uma":1120,"utan":2130}},{"a":"local","t":"佐賀","r":6,"name":"マリーゴールド特別","time":"13:00","pay":{"uma":1120,"utan":2450}}],"today":true},
{"date":"8/7(金)","jraN":null,"jraDone":null,"localN":10,"localDone":10,"races":[{"a":"local","t":"大井","r":1,"name":"B3","time":"14:40","pay":{"uma":2140,"utan":4230,"sanpuku":6200,"santan":36190}},{"a":"local","t":"大井","r":2,"name":"B3","time":"15:12","pay":{"uma":1380,"utan":2960}},{"a":"local","t":"大井","r":4,"name":"C2選抜","time":"16:16","pay":{"uma":620,"utan":1340,"sanpuku":1060,"santan":4360}},{"a":"local","t":"大井","r":6,"name":"C1","time":"17:20","pay":{"uma":320,"utan":630}},{"a":"local","t":"大井","r":9,"name":"薫風賞","time":"18:56","pay":{"uma":4380,"utan":9480,"sanpuku":9230,"santan":39670}},{"a":"local","t":"大井","r":10,"name":"マリーゴールド特別","time":"19:28","pay":{"uma":780,"utan":1550,"sanpuku":2500,"santan":10720}}]},
{"date":"8/6(木)","jraN":null,"jraDone":null,"localN":9,"localDone":9,"races":[{"a":"local","t":"門別","r":4,"name":"C1","time":"16:16","pay":{"uma":5260,"utan":9120}},{"a":"local","t":"門別","r":6,"name":"C1","time":"17:20","pay":{"uma":930,"utan":1700}},{"a":"local","t":"門別","r":7,"name":"A2","time":"17:52","pay":{"uma":780,"utan":1460,"sanpuku":1550,"santan":5980}},{"a":"local","t":"門別","r":8,"name":"サマーナイト賞","time":"18:24","pay":{"uma":5260,"utan":10210,"sanpuku":12300,"santan":47970}},{"a":"local","t":"門別","r":9,"name":"薫風賞","time":"18:56","pay":{"uma":1660,"utan":3200}}]},
{"date":"8/5(水)","jraN":null,"jraDone":null,"localN":10,"localDone":10,"races":[{"a":"local","t":"川崎","r":1,"name":"B2","time":"14:40","pay":{"uma":380,"utan":660,"sanpuku":960,"santan":5520}},{"a":"local","t":"川崎","r":2,"name":"B2","time":"15:12","pay":{"uma":540,"utan":1150}},{"a":"local","t":"川崎","r":4,"name":"C1","time":"16:16","pay":{"uma":780,"utan":1560}},{"a":"local","t":"川崎","r":5,"name":"B1","time":"16:48","pay":{"uma":1120,"utan":2430,"sanpuku":2260,"santan":9360}},{"a":"local","t":"川崎","r":6,"name":"C1","time":"17:20","pay":{"uma":4380,"utan":7980}},{"a":"local","t":"川崎","r":9,"name":"マリーゴールド特別","time":"18:56","pay":{"uma":930,"utan":1980}},{"a":"local","t":"川崎","r":10,"name":"マリーゴールド特別","time":"19:28","pay":{"uma":380,"utan":680,"sanpuku":730,"santan":3650}}]},
{"date":"8/4(火)","jraN":null,"jraDone":null,"localN":9,"localDone":9,"races":[{"a":"local","t":"金沢","r":2,"name":"B3","time":"10:52","pay":{"uma":1120,"utan":2270,"sanpuku":2330,"santan":7060}},{"a":"local","t":"金沢","r":5,"name":"B1","time":"12:28","pay":{"uma":320,"utan":700}},{"a":"local","t":"金沢","r":6,"name":"B1","time":"13:00","pay":{"uma":5260,"utan":10570}},{"a":"local","t":"金沢","r":7,"name":"B2","time":"13:32","pay":{"uma":930,"utan":1610,"sanpuku":2990,"santan":10470}}]},
{"date":"8/3(月)","jraN":null,"jraDone":null,"localN":10,"localDone":10,"races":[{"a":"local","t":"船橋","r":2,"name":"B1","time":"15:12","pay":{"uma":3420,"utan":5980}},{"a":"local","t":"船橋","r":5,"name":"C2","time":"16:48","pay":{"uma":1660,"utan":2890,"sanpuku":5780,"santan":18840}},{"a":"local","t":"船橋","r":6,"name":"B2選抜","time":"17:20","pay":{"uma":1120,"utan":1960}},{"a":"local","t":"船橋","r":7,"name":"B2","time":"17:52","pay":{"uma":780,"utan":1520}},{"a":"local","t":"船橋","r":9,"name":"サマーナイト賞","time":"18:56","pay":{"uma":1120,"utan":2330,"sanpuku":2690,"santan":8090}},{"a":"local","t":"船橋","r":10,"name":"薫風賞","time":"19:28","pay":{"uma":930,"utan":2020,"sanpuku":2770,"santan":10440}}]},
{"date":"8/2(日)","jraN":24,"jraDone":24,"localN":8,"localDone":8,"races":[{"a":"jra","t":"新潟","r":1,"name":"2歳未勝利","time":"9:50","pay":{"uma":2680,"utan":5470,"sanpuku":9120,"santan":30520}},{"a":"jra","t":"新潟","r":3,"name":"2歳未勝利","time":"10:58","pay":{"uma":450,"utan":980,"sanpuku":1380,"santan":4630}},{"a":"jra","t":"新潟","r":4,"name":"3歳未勝利","time":"11:32","pay":{"uma":380,"utan":660}},{"a":"jra","t":"新潟","r":9,"name":"おおぞら特別","time":"14:22","pay":{"uma":320,"utan":670,"sanpuku":510,"santan":2870}},{"a":"jra","t":"新潟","r":10,"name":"羊ヶ丘特別","time":"14:56","pay":{"uma":2140,"utan":4650,"sanpuku":5890,"santan":20000}},{"a":"jra","t":"新潟","r":11,"name":"越後S","time":"15:30","pay":{"uma":1860,"utan":3720,"sanpuku":6540,"santan":52310}},{"a":"jra","t":"新潟","r":12,"name":"3歳以上1勝クラス","time":"16:04","pay":{"uma":1380,"utan":3020}},{"a":"jra","t":"札幌","r":1,"name":"2歳未勝利","time":"9:50","pay":{"uma":5260,"utan":10500}},{"a":"jra","t":"札幌","r":4,"name":"2歳新馬","time":"11:32","pay":{"uma":540,"utan":920}},{"a":"jra","t":"札幌","r":5,"name":"2歳新馬","time":"12:06","pay":{"uma":2140,"utan":4540,"sanpuku":5430,"santan":26890}},{"a":"jra","t":"札幌","r":9,"name":"八海山特別","time":"14:22","pay":{"uma":1120,"utan":2340,"sanpuku":2850,"santan":10560}},{"a":"jra","t":"札幌","r":10,"name":"八海山特別","time":"14:56","pay":{"uma":540,"utan":1100,"sanpuku":1540,"santan":9600}},{"a":"jra","t":"札幌","r":11,"name":"札幌日刊スポーツ杯","time":"15:30","pay":{"uma":1380,"utan":2600}},{"a":"local","t":"高知","r":1,"name":"B1","time":"14:40","pay":{"uma":450,"utan":780}},{"a":"local","t":"高知","r":4,"name":"C2","time":"16:16","pay":{"uma":620,"utan":1280}},{"a":"local","t":"高知","r":6,"name":"C1","time":"17:20","pay":{"uma":1380,"utan":2940,"sanpuku":2780,"santan":16050}},{"a":"local","t":"高知","r":7,"name":"スタールビー賞","time":"17:52","pay":{"uma":2140,"utan":4220,"sanpuku":6720,"santan":36420}},{"a":"local","t":"高知","r":8,"name":"サマーナイト賞","time":"18:24","pay":{"uma":780,"utan":1640,"sanpuku":1250,"santan":4570}}]},
{"date":"8/1(土)","jraN":24,"jraDone":24,"localN":8,"localDone":8,"races":[{"a":"jra","t":"新潟","r":1,"name":"2歳未勝利","time":"9:50","pay":{"uma":1120,"utan":2310,"sanpuku":3740,"santan":13130}},{"a":"jra","t":"新潟","r":4,"name":"2歳新馬","time":"11:32","pay":{"uma":5260,"utan":8980}},{"a":"jra","t":"新潟","r":6,"name":"2歳未勝利","time":"12:40","pay":{"uma":5260,"utan":9350}},{"a":"jra","t":"新潟","r":10,"name":"羊ヶ丘特別","time":"14:56","pay":{"uma":930,"utan":1780}},{"a":"jra","t":"新潟","r":11,"name":"新潟日報賞","time":"15:30","pay":{"uma":2680,"utan":5480,"sanpuku":9020,"santan":58330}},{"a":"jra","t":"新潟","r":12,"name":"3歳以上1勝クラス","time":"16:04","pay":{"uma":3420,"utan":7010,"sanpuku":8470,"santan":45550}},{"a":"jra","t":"札幌","r":5,"name":"2歳未勝利","time":"12:06","pay":{"uma":3420,"utan":7490,"sanpuku":7650,"santan":38640}},{"a":"jra","t":"札幌","r":8,"name":"3歳以上3勝クラス","time":"13:48","pay":{"uma":1660,"utan":3070,"sanpuku":5120,"santan":31310}},{"a":"jra","t":"札幌","r":9,"name":"羊ヶ丘特別","time":"14:22","pay":{"uma":320,"utan":620}},{"a":"jra","t":"札幌","r":10,"name":"羊ヶ丘特別","time":"14:56","pay":{"uma":2140,"utan":3930,"sanpuku":3290,"santan":13470}},{"a":"jra","t":"札幌","r":11,"name":"札幌日刊スポーツ杯","time":"15:30","pay":{"uma":3240,"utan":6980,"sanpuku":12450,"santan":86880}},{"a":"local","t":"佐賀","r":2,"name":"C2選抜","time":"10:52","pay":{"uma":1380,"utan":2820}},{"a":"local","t":"佐賀","r":5,"name":"B2","time":"12:28","pay":{"uma":5260,"utan":10580,"sanpuku":10240,"santan":34480}},{"a":"local","t":"佐賀","r":7,"name":"マリーゴールド特別","time":"13:32","pay":{"uma":380,"utan":800}},{"a":"local","t":"佐賀","r":8,"name":"サマーナイト賞","time":"14:04","pay":{"uma":2140,"utan":3720,"sanpuku":6190,"santan":36400}}]}];

let scope = 'total'; // 'total' | 'jra' | 'local'
let dayIdx = 0;      // 選択中の日（デフォルト＝本日）

const inScope = r => scope === 'total' || r.a === scope;
const toMin = t => { const [h, m] = t.split(':').map(Number); return h * 60 + m; };
const isNew = (d, r) => !!d.today && (NOW.min - toMin(r.time)) >= 0 && (NOW.min - toMin(r.time)) <= NEW_WINDOW_MIN;
const doneN = d => scope === 'jra' ? d.jraDone : scope === 'local' ? d.localDone : (d.jraDone || 0) + (d.localDone || 0);
const schedN = d => scope === 'jra' ? d.jraN : scope === 'local' ? d.localN : (d.jraN || 0) + (d.localN || 0);

/* 的中バッジ：的中（全行・赤）／万馬券（いずれかの払戻が1万円以上・ゴールド）。NEWは緑
   ri＝行番号：一覧描画時のポップ演出を上から順に少しずつ遅らせる */
const isMan = r => Object.values(r.pay).some(v => v >= 10000);
function badgesHTML(r, ri = 0, mod = ''){
  const d0 = Math.min(ri, 12) * 0.05;
  const b = ['<span class="rb rb-hit" style="--d:' + d0.toFixed(2) + 's">的中</span>'];
  if(isMan(r)) b.push('<span class="rb rb-man" style="--d:' + (d0 + .08).toFixed(2) + 's">万馬券</span>');
  return '<span class="rr-badges' + (mod ? ' ' + mod : '') + '">' + b.join('') + '</span>';
}

/* ===== レース結果ハーフモーダル =====
   一覧の往復を減らすため、レース行タップはページ遷移せずその場で結果の要点を表示。
   詳細（全着順・全配当）を見たい場合のみシート内CTAからTOPの結果・払戻しへ遷移する二段構え */
const HORSE_POOL = ['エヴァルライト','グリーンシグナル','データストリーム','プロブフォンテン','マスターオッズ','シンボリック','ライブアロー','ネオファクター','クロスレンジ','ブライトモード','サザンクロッカー','ハイパーボリック','スカイテーブル','リアルタイマー','ゴールドウェイト','フェアバリュー'];
// track+r から決定的に疑似乱数を生成（再描画で結果が変わらないように）
function seededRand(key){
  let h = 0;
  for(const c of key) h = (h * 31 + c.charCodeAt(0)) >>> 0;
  return () => { h = (h * 1103515245 + 12345) >>> 0; return (h >>> 8) / 16777216; };
}
function mockResult(d, r){
  const rnd = seededRand(d.date + r.t + r.r);
  const pick = (arr, n) => {
    const pool = [...arr], out = [];
    while(out.length < n) out.push(pool.splice(Math.floor(rnd() * pool.length), 1)[0]);
    return out;
  };
  const trio = !!r.pay.santan;
  const names = pick(HORSE_POOL, 3);
  const umaban = pick(Array.from({length:16}, (_, i) => i + 1), 3);
  // Eval確率順位：的中条件と整合（1・2着は上位6頭内。3着はトリオ的中時のみ上位6頭内）
  const evTop = pick([1,2,3,4,5,6], 3);
  const ev3out = 7 + Math.floor(rnd() * 5);
  const evs = [evTop[0], evTop[1], trio ? evTop[2] : ev3out];
  const pops = [1 + Math.floor(rnd() * 5), 1 + Math.floor(rnd() * 7), trio ? 2 + Math.floor(rnd() * 7) : 5 + Math.floor(rnd() * 7)];
  return [0, 1, 2].map(i => ({pos:i + 1, umaban:umaban[i], name:names[i], ev:evs[i], pop:pops[i]}));
}
function openSheet(d, r){
  const fin = mockResult(d, r);
  const chips = Object.keys(TYPE_LABEL).filter(t => r.pay[t]).map(t =>
    `<span class="rr-chip${r.pay[t] >= 10000 ? ' man' : ''}"><span class="k">${TYPE_LABEL[t]}</span>${r.pay[t].toLocaleString()}円</span>`
  ).join('');
  const inCnt = fin.filter(f => f.ev <= 6).length;
  document.getElementById('sheet').innerHTML = `
    <div class="grip"></div>
    <div class="sh-head">
      <span class="sh-race">${r.t}${r.r}R</span>
      <span class="sh-name">${r.name}</span>
      <span class="sh-time">${d.date} ${r.time}発走</span>
      <span class="sh-close" id="shClose">✕</span>
    </div>
    <div class="sh-badges">${badgesHTML(r)}</div>
    <p class="sh-sec">レース結果（1〜3着）</p>
    <ul class="sh-fin">${fin.map(f => `
      <li>
        <span class="pos-b p${f.pos}">${f.pos}</span>
        <span class="sh-umaban">${f.umaban}</span>
        <span class="sh-horse">${f.name}</span>
        <span class="sh-ev${f.ev <= 6 ? '' : ' out'}">Eval ${f.ev}位</span>
        <span class="sh-pop">${f.pop}番人気</span>
      </li>`).join('')}
    </ul>
    <p class="sh-note">${inCnt === 3 ? '<b>1〜3着すべてEval好走確率上位6頭内</b>。4券種すべて的中しました。' : '<b>1・2着がEval好走確率上位6頭内</b>。馬連・馬単が的中しました。'}</p>
    <p class="sh-sec">的中と払戻（各100円）</p>
    <span class="rr-types">${chips}</span>
    <a class="sh-cta" href="/" title="タップで${r.t}${r.r}Rの結果・払戻し（出馬表ページ）へ（モックでは無遷移）">このレースの結果・払戻しをすべて見る <span class="ar">›</span></a>
  `;
  document.getElementById('sheetOv').classList.add('show');
  document.getElementById('shClose').addEventListener('click', closeSheet);
}
function closeSheet(){ document.getElementById('sheetOv').classList.remove('show'); }
document.getElementById('sheetOv').addEventListener('click', e => { if(e.target === e.currentTarget) closeSheet(); });

function render(){
  const d = DAYS[dayIdx];
  // 新しいレースが上（発走時刻降順）
  const races = d.races.filter(inScope).sort((a, b) => toMin(b.time) - toMin(a.time));
  const n = doneN(d) || 0;
  const sched = schedN(d) || 0;

  // ---- 日付チップ ----
  document.getElementById('dateNav').innerHTML = DAYS.map((x, i) =>
    `<a class="date-chip${i === dayIdx ? ' on' : ''}" data-i="${i}">${x.date}${x.today ? '<span class="today-mk">本日</span>' : ''}</a>`
  ).join('');
  document.querySelectorAll('.date-chip').forEach(c => c.addEventListener('click', (e) => {
    e.preventDefault();
    dayIdx = Number(c.dataset.i);
    render();
  }));

  // ---- サマリー見出し（選択日の状態） ----
  document.getElementById('sumTitle').textContent =
    `${d.date}の${scope === 'total' ? '総合' : scope === 'jra' ? '中央' : '地方'}サマリー`;
  document.getElementById('sumState').innerHTML = d.today
    ? `<span class="live-mk">開催中</span><span>${NOW.label} 更新</span>
       <span class="rf-btn glow" id="rfBtn" title="手動更新（新データがある時のみ点灯）">
         <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v5h-5"/></svg>
       </span>`
    : `<span>確定</span>`;
  const rf = document.getElementById('rfBtn');
  if(rf) rf.addEventListener('click', () => {
    rf.classList.add('spin'); rf.classList.remove('glow');
    setTimeout(() => rf.classList.remove('spin'), 650);
  });

  // ---- 選択日のサマリー（4カード） ----
  if(!sched){
    document.getElementById('statGrid').innerHTML =
      `<div class="stat" style="grid-column:1/-1;"><p class="s-name">${scope === 'jra' ? '中央' : ''}開催なし</p>
        <p class="s-sub">この日の${scope === 'jra' ? '中央競馬の' : ''}開催はありません</p></div>`;
  }else{
    let pair = 0, trio = 0, man = 0, best = null;
    races.forEach(r => {
      pair++;
      if(r.pay.santan) trio++;
      Object.keys(r.pay).forEach(t => {
        if(r.pay[t] >= 10000) man++;
        if(!best || r.pay[t] > best.yen) best = {yen:r.pay[t], type:TYPE_LABEL[t], race:`${r.t}${r.r}R`};
      });
    });
    const pct = x => n ? Math.round(x / n * 100) : 0;
    const denom = d.today ? `<small>/消化${n}R</small>` : `<small>/${n}R</small>`;
    document.getElementById('statGrid').innerHTML = `
      <div class="stat"><p class="s-name">馬連・馬単 的中</p>
        <p class="s-rate">${pair}${denom}</p>
        <p class="s-sub">的中率 ${pct(pair)}%${d.today ? `（全${sched}R予定）` : ''}</p></div>
      <div class="stat"><p class="s-name">3連複・3連単 的中</p>
        <p class="s-rate">${trio}${denom}</p>
        <p class="s-sub">的中率 ${pct(trio)}%</p></div>
      <div class="stat"><p class="s-name">${d.today ? 'ここまでの' : ''}最高払戻</p>
        <p class="s-rate${best && best.yen >= 50000 ? ' hot' : ''}">${best ? best.yen.toLocaleString() : '−'}<small>${best ? '円' : ''}</small></p>
        <p class="s-sub">${best ? `${best.type}・${best.race}` : '的中待ち'}</p></div>
      <div class="stat"><p class="s-name">万馬券的中（1万円以上）</p>
        <p class="s-rate${man ? ' hot' : ''}">${man}<small>本</small></p>
        <p class="s-sub">100円あたり払戻1万円以上</p></div>`;
  }

  // ---- 的中レース（1R単位） ----
  document.getElementById('listTitle').textContent = `${d.date}の的中レース`;
  let rows;
  if(!sched){
    rows = `<li class="empty-row">${scope === 'jra' ? '中央開催なし' : '開催なし'}</li>`;
  }else if(!races.length){
    rows = `<li class="empty-row">${d.today ? 'まだ的中はありません（レース確定ごとに反映されます）' : 'この日の的中はありません'}</li>`;
  }else{
    rows = races.map((r, ri) => {
      const chips = Object.keys(TYPE_LABEL).filter(t => r.pay[t]).map(t =>
        `<span class="rr-chip${r.pay[t] >= 10000 ? ' man' : ''}"><span class="k">${TYPE_LABEL[t]}</span>${r.pay[t].toLocaleString()}円</span>`
      ).join('');
      const area = scope === 'total' ? `<span class="rr-area">${AREA_LABEL[r.a]}</span>` : '';
      const nw = isNew(d, r) ? '<span class="rr-new">NEW</span>' : '';
      const man = isMan(r);
      return `<li><a class="race-row${man ? ' is-man' : ''}" data-key="${r.t}_${r.r}" title="タップで${r.t}${r.r}Rの結果を表示">
        ${badgesHTML(r, ri, 'col')}
        <span class="rr-body">
        <span class="rr-head">${area}
          <span class="rr-race">${r.t}${r.r}R</span>${nw}
          <span class="rr-name">${r.name}</span>
          <span class="rr-time">${r.time}発走</span>
          <span class="rr-chev">›</span>
        </span>
        <span class="rr-types">${chips}</span>
        </span>
      </a></li>`;
    }).join('');
  }
  document.getElementById('raceList').innerHTML = rows +
    (sched ? `<li><a class="day-foot" href="/ranking/eval_ranking" title="タップで${d.date}のランキングへ（モックでは無遷移）">${d.today ? '本日の好走確率ランキングへ' : `${d.date}の確定ランキングで答え合わせ`} <span class="ar">›</span></a></li>` : '');

  // ---- レース行タップ→結果ハーフモーダル ----
  document.querySelectorAll('.race-row').forEach(a => a.addEventListener('click', (e) => {
    e.preventDefault();
    const [t, rn] = a.dataset.key.split('_');
    const race = races.find(x => x.t === t && x.r === Number(rn));
    if(race) openSheet(d, race);
  }));

  // ---- 注記 ----
  document.getElementById('listNote').innerHTML = d.today
    ? '※ 本日の的中はレース確定ごとに反映されます（発走から約3時間は<b style="color:#00c98d;">NEW</b>表示）。<b style="color:#ff6b7d;">的中</b>＝全的中レース／<b style="color:#e8b52a;">万馬券</b>＝払戻1万円以上（100円あたり。該当の配当は赤地で表示）。⟳は新データがある時のみ点灯します。レース行タップでその場に結果（1〜3着・Eval確率順位・払戻）を表示します。'
    : '※ 過去日の実績は全レース終了後に確定済みで、以後変動しません。<b style="color:#ff6b7d;">的中</b>＝全的中レース／<b style="color:#e8b52a;">万馬券</b>＝払戻1万円以上（100円あたり。該当の配当は赤地で表示）。レース行タップでその場に結果（1〜3着・Eval確率順位・払戻）を表示、フッターから該当日の確定ランキングへ移動します（モックでは無遷移）。';
}

document.querySelectorAll('.c-tabs a').forEach(t => t.addEventListener('click', (e) => {
  e.preventDefault();
  scope = t.dataset.scope;
  document.querySelectorAll('.c-tabs a').forEach(x => x.classList.toggle('on', x === t));
  render();
}));

render();
})();
