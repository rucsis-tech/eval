(function () {
  'use strict';

  function mockIcon(kind, c1, c2, txt) {
    let inner = '';
    if (kind === 'person') {
      inner = `<circle cx="50" cy="41" r="17" fill="${c2}"/><ellipse cx="50" cy="96" rx="33" ry="27" fill="${c2}"/>`;
    } else if (kind === 'mono') {
      inner = `<text x="50" y="50" dy=".36em" text-anchor="middle" font-family="Helvetica,Arial,sans-serif" font-weight="900" font-size="${txt.length > 1 ? 34 : 46}" fill="${c2}">${txt}</text>`;
    } else if (kind === 'stripes') {
      inner = [0, 1, 2, 3, 4, 5].map((i) =>
        `<rect x="${-40 + i * 26}" y="-20" width="12" height="160" fill="${c2}" transform="rotate(30 50 50)"/>`
      ).join('');
    } else if (kind === 'target') {
      inner = `<circle cx="50" cy="50" r="30" fill="none" stroke="${c2}" stroke-width="7"/><circle cx="50" cy="50" r="15" fill="none" stroke="${c2}" stroke-width="7"/><circle cx="50" cy="50" r="4" fill="${c2}"/>`;
    } else if (kind === 'shoe') {
      inner = `<path d="M30 30 v18 a20 20 0 0 0 40 0 v-18" fill="none" stroke="${c2}" stroke-width="10" stroke-linecap="round"/><circle cx="34" cy="42" r="2.6" fill="${c1}"/><circle cx="66" cy="42" r="2.6" fill="${c1}"/>`;
    } else if (kind === 'logo') {
      inner = `<path d="M50 18 L80 50 L50 82 L20 50 Z" fill="${c2}"/><path d="M50 34 L66 50 L50 66 L34 50 Z" fill="${c1}"/>`;
    }
    const bg = kind === 'person'
      ? `<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="${c1}"/><stop offset="1" stop-color="${txt || c1}"/></linearGradient></defs><rect width="100" height="100" fill="url(#g)"/>`
      : `<rect width="100" height="100" fill="${c1}"/>`;
    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">${bg}${inner}</svg>`);
  }

  const AMB_OFFICIAL_ICON = mockIcon('logo', '#0d2b52', '#00c98d');
  const AMBASSADORS = [
    { name: 'うまログ太郎', x: 'umalog_taro', tags: ['中央競馬', '穴馬'], desc: '人気薄の激走パターンを毎週分析。Evalの確率と人気の“ズレ”から狙える穴馬をポストしています。', icon: mockIcon('person', '#ff9a5a', '#fff3e6', '#e0485e') },
    { name: 'Kei｜地方競馬', x: 'kei_chiho', tags: ['地方競馬', 'ナイター'], desc: '南関東を中心に地方競馬を毎日チェック。平日の買い目の組み立て方を発信中。', icon: mockIcon('mono', '#14233d', '#5fb4ff', 'K') },
    { name: '血統メモ', x: 'kettou_memo', tags: ['血統', '中央競馬'], desc: '血統×コース適性の視点から、確率上位馬の“裏付け”を解説します。', icon: mockIcon('stripes', '#2b1d3f', '#a77bff') },
    { name: 'みお🐴', x: 'mio_keiba', tags: ['パドック', '初心者向け'], desc: '競馬歴2年。パドック写真と一緒に、初心者目線でEvalの使い方を紹介しています。', icon: mockIcon('person', '#ffd1dc', '#ffffff', '#ff8fb1') },
    { name: 'WIN5職人', x: 'win5_shokunin', tags: ['WIN5'], desc: '毎週日曜はWIN5一本勝負。点数を抑えた買い方と、シミュレーションの活用例をポスト。', icon: mockIcon('target', '#0f2a22', '#c8f31d') },
    { name: 'データ馬場', x: 'data_baba', tags: ['データ分析', '回収率'], desc: '馬場状態と確率の関係を数字で検証。長期の回収率レポートを月1回まとめています。', icon: mockIcon('mono', '#f3f5f8', '#0b0c10', 'DB') },
    { name: 'ナイター大井', x: 'night_ooi', tags: ['地方競馬', 'ナイター'], desc: '仕事帰りの大井ナイター専門。発走前の最終確率チェックをリアルタイムで共有。', icon: mockIcon('shoe', '#101a3a', '#f0c44a') },
    { name: 'さくら｜一口馬主', x: 'sakura_hitokuchi', tags: ['一口馬主', 'POG'], desc: '出資馬の応援が中心。新馬戦・未勝利戦の確率の見方をやさしく解説しています。', icon: mockIcon('person', '#7fd6c2', '#f4fffb', '#2f8f9d') },
    { name: 'ハル@回収率重視の三連単フォーメーション研究所', x: 'haru_roi_sanrentan_lab', tags: ['回収率', '3連単', 'データ分析'], desc: '3連単フォーメーションの点数配分を研究。的中率より回収率を重視した組み方を発信。', icon: mockIcon('mono', '#e6394f', '#ffffff', 'H') },
    { name: 'ゆうき｜POG', x: 'yuki_pog', tags: ['POG', '血統'], desc: '2歳馬の評価とPOG指名馬の追跡。デビュー前後の確率推移をまとめています。', icon: mockIcon('person', '#5b8def', '#eaf1ff', '#283a8f') },
  ];

  const list = document.getElementById('ambGrid');
  const fil = document.getElementById('ambFilter');
  if (!list || !fil) return;

  const off = document.getElementById('ambOfficialIcon');
  if (off) {
    off.src = AMB_OFFICIAL_ICON;
    off.alt = 'Eval';
  }

  const esc = (s) => String(s).replace(/[&<>"]/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[m]));

  list.innerHTML = AMBASSADORS.map((a) => `
    <li id="amb-${esc(a.x)}" data-tags="${esc((a.tags || []).join('|'))}"><a class="amb-row" href="https://x.com/${esc(a.x)}" target="_blank" rel="noopener" title="${esc(a.name)}（@${esc(a.x)}）をXで見る">
      <span class="amb-av">
        <img src="${a.icon}" alt="" loading="lazy" onerror="this.outerHTML='<span class=&quot;ini&quot;>${esc(a.name).charAt(0)}</span>'">
        <span class="x">𝕏</span>
      </span>
      <span class="body">
        <span class="nmline"><span class="nm">${esc(a.name)}</span><span class="id">@${esc(a.x)}</span><span class="go">›</span></span>
        <span class="tags">${(a.tags || []).map((t) => `<span class="tag">${esc(t)}</span>`).join('')}</span>
        <span class="desc">${esc(a.desc || '')}</span>
      </span>
    </a></li>`).join('');

  const cnt = {};
  AMBASSADORS.forEach((a) => (a.tags || []).forEach((t) => { cnt[t] = (cnt[t] || 0) + 1; }));
  const tags = Object.keys(cnt).sort((x, y) => cnt[y] - cnt[x]);
  fil.innerHTML = ['すべて', ...tags].map((t, i) =>
    `<button type="button" class="${i ? '' : 'on'}" data-tag="${i ? esc(t) : ''}">${esc(t)}</button>`
  ).join('');

  const setCount = (n) => {
    const el = document.getElementById('ambCount');
    if (el) el.textContent = n + '名';
  };
  setCount(AMBASSADORS.length);

  fil.addEventListener('click', (e) => {
    const b = e.target.closest('button');
    if (!b) return;
    fil.querySelectorAll('button').forEach((x) => x.classList.toggle('on', x === b));
    const t = b.dataset.tag;
    let n = 0;
    list.querySelectorAll('li').forEach((li) => {
      const show = !t || li.dataset.tags.split('|').includes(t);
      li.hidden = !show;
      if (show) n++;
    });
    setCount(n);
  });

  function jump() {
    const id = decodeURIComponent(location.hash.slice(1));
    if (!id.startsWith('amb-')) return;
    const li = document.getElementById(id);
    if (!li) return;
    li.hidden = false;
    li.scrollIntoView({ block: 'start' });
    li.classList.remove('amb-hl');
    void li.offsetWidth;
    li.classList.add('amb-hl');
  }
  window.addEventListener('hashchange', jump);
  requestAnimationFrame(() => setTimeout(jump, 60));
})();
