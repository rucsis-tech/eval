/**
 * P2 おすすめ攻略ツール（モックデータ駆動）
 */
(function () {
    'use strict';

    var GENRES = [
        { id: 'g1', sort: 1, visible: true, name: '解析が拾えない情報', en: 'HIDDEN FACTORS', desc: '確率に未反映の要素を補完', example: '前走不利／馬具・関係者情報' },
        { id: 'g2', sort: 2, visible: true, name: 'ファクター特化', en: 'DEEP FACTORS', desc: '得意な切り口を深掘り', example: 'ラップ分析／血統分析' },
        { id: 'g3', sort: 3, visible: true, name: 'まとめ・時短', en: 'QUICK PICKS', desc: '結論だけ知りたい人向け', example: '推奨馬リスト／軸推奨／危険な人気馬' }
    ];
    var PRODUCTS = [
        { id: 't101', genre: 'g1', sort: 1, visible: true, icon: '⚠️', color: 'navy', name: '本日の前走不利情報', desc: '前走で不利があり、数字以上に狙える馬を全開催から一覧化', price: { type: 'pt', value: 300 }, isNew: true },
        { id: 't102', genre: 'g1', sort: 2, visible: true, icon: '🔧', color: 'gray', name: '本日の馬具・関係者情報', desc: '初ブリンカー等の馬具変更と、確率上昇シグナルの見方', price: { type: 'free' }, isNew: false },
        { id: 't201', genre: 'g2', sort: 1, visible: true, icon: '📈', color: 'green', name: '本日のラップ分析情報', desc: '想定ラップから導く展開利・該当馬を開催別にまとめ', price: { type: 'pt', value: 300 }, isNew: true },
        { id: 't202', genre: 'g2', sort: 2, visible: true, icon: '🧬', color: 'gold', name: '本日の血統分析情報', desc: 'コース適性×血統傾向で確率を裏付ける該当馬リスト', price: { type: 'pt', value: 300 }, isNew: false },
        { id: 't301', genre: 'g3', sort: 1, visible: true, icon: '🐎', color: 'red', name: '本日の推奨馬リスト', desc: '連対率40%超×人気乖離で選ぶ全開催の軸候補', price: { type: 'pt', value: 500 }, isNew: true },
        { id: 't302', genre: 'g3', sort: 2, visible: true, icon: '🎯', color: 'navy', name: '高好走確率リスト（SABCランク）', desc: '好走確率50%以上／40%以上の該当馬を毎日自動更新', price: { type: 'sub' }, isNew: false },
        { id: 't303', genre: 'g3', sort: 3, visible: true, icon: '🚫', color: 'gray', name: '危険な人気馬リスト', desc: '人気と確率が乖離した“過剰人気”を事前に警告', price: { type: 'pt', value: 300 }, isNew: false }
    ];

    function esc(s) {
        return String(s).replace(/[&<>"]/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[m];
        });
    }

    function priceHTML(p) {
        if (p.type === 'free') return '<span class="g-price free">無料</span>';
        if (p.type === 'sub') return '<span class="g-price sub">サブスク</span>';
        return '<span class="g-price">' + p.value.toLocaleString() + 'pt</span>';
    }

    var stackEl = document.getElementById('stack');
    var sectionsEl = document.getElementById('genreSections');
    if (!stackEl || !sectionsEl) return;

    var genres = GENRES.filter(function (g) { return g.visible; }).sort(function (a, b) { return a.sort - b.sort; });

    stackEl.innerHTML = genres.slice().reverse().map(function (g) {
        var no = genres.indexOf(g) + 1;
        var marks = '①②③④⑤⑥⑦⑧⑨';
        return '<div class="layer"><span class="l-no">上乗せ' + (marks[no - 1] || no) + '</span>' +
            '<span class="l-txt">' + esc(g.name) + '<small>' + esc(g.example) + '</small></span></div>';
    }).join('') +
        '<p class="plus">＋</p>' +
        '<div class="layer base"><span class="l-no">土台</span>' +
        '<span class="l-txt">好走確率（マスターデータ）<small>全出走馬の勝率・複勝率・期待値</small></span></div>';

    sectionsEl.innerHTML = genres.map(function (g) {
        var items = PRODUCTS.filter(function (p) { return p.genre === g.id && p.visible; }).sort(function (a, b) { return a.sort - b.sort; });
        if (!items.length) return '';
        return '<section>' +
            '<div class="sec-title"><h2>' + esc(g.name) + '</h2><span class="en">' + esc(g.en) + '</span><span class="cat-desc">' + esc(g.desc) + '</span></div>' +
            '<div class="goods">' + items.map(function (p) {
                return '<a href="/tools/tools_detail?id=' + encodeURIComponent(p.id) + '" data-id="' + esc(p.id) + '">' +
                    '<span class="g-ico g-' + p.color + '">' + p.icon + '</span>' +
                    '<span class="g-body">' +
                    '<span class="g-title">' + esc(p.name) + (p.isNew ? '<span class="new">NEW</span>' : '') + '</span>' +
                    '<span class="g-desc">' + esc(p.desc) + '</span>' +
                    '</span>' + priceHTML(p.price) + '</a>';
            }).join('') + '</div></section>';
    }).join('');

    function mockIcon(kind, c1, c2, txt) {
        var inner = '';
        if (kind === 'person') {
            inner = '<circle cx="50" cy="41" r="17" fill="' + c2 + '"/><ellipse cx="50" cy="96" rx="33" ry="27" fill="' + c2 + '"/>';
        } else if (kind === 'mono') {
            inner = '<text x="50" y="50" dy=".36em" text-anchor="middle" font-family="Helvetica,Arial,sans-serif" font-weight="900" font-size="' + (txt.length > 1 ? 34 : 46) + '" fill="' + c2 + '">' + txt + '</text>';
        } else if (kind === 'stripes') {
            inner = [0, 1, 2, 3, 4, 5].map(function (i) {
                return '<rect x="' + (-40 + i * 26) + '" y="-20" width="12" height="160" fill="' + c2 + '" transform="rotate(30 50 50)"/>';
            }).join('');
        } else if (kind === 'target') {
            inner = '<circle cx="50" cy="50" r="30" fill="none" stroke="' + c2 + '" stroke-width="7"/><circle cx="50" cy="50" r="15" fill="none" stroke="' + c2 + '" stroke-width="7"/><circle cx="50" cy="50" r="4" fill="' + c2 + '"/>';
        } else if (kind === 'shoe') {
            inner = '<path d="M30 30 v18 a20 20 0 0 0 40 0 v-18" fill="none" stroke="' + c2 + '" stroke-width="10" stroke-linecap="round"/><circle cx="34" cy="42" r="2.6" fill="' + c1 + '"/><circle cx="66" cy="42" r="2.6" fill="' + c1 + '"/>';
        } else if (kind === 'logo') {
            inner = '<path d="M50 18 L80 50 L50 82 L20 50 Z" fill="' + c2 + '"/><path d="M50 34 L66 50 L50 66 L34 50 Z" fill="' + c1 + '"/>';
        }
        var bg = kind === 'person'
            ? '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' + c1 + '"/><stop offset="1" stop-color="' + (txt || c1) + '"/></linearGradient></defs><rect width="100" height="100" fill="url(#g)"/>'
            : '<rect width="100" height="100" fill="' + c1 + '"/>';
        return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">' + bg + inner + '</svg>');
    }

    var AMB_OFFICIAL_ICON = mockIcon('logo', '#0d2b52', '#00c98d');
    var AMBASSADORS = [
        { name: 'うまログ太郎', icon: mockIcon('person', '#ff9a5a', '#fff3e6', '#e0485e') },
        { name: 'Kei｜地方競馬', icon: mockIcon('mono', '#14233d', '#5fb4ff', 'K') },
        { name: '血統メモ', icon: mockIcon('stripes', '#2b1d3f', '#a77bff') },
        { name: 'みお🐴', icon: mockIcon('person', '#ffd1dc', '#ffffff', '#ff8fb1') },
        { name: 'WIN5職人', icon: mockIcon('target', '#0f2a22', '#c8f31d') },
        { name: 'データ馬場', icon: mockIcon('mono', '#f3f5f8', '#0b0c10', 'DB') },
        { name: 'ナイター大井', icon: mockIcon('shoe', '#101a3a', '#f0c44a') },
        { name: 'さくら｜一口馬主', icon: mockIcon('person', '#7fd6c2', '#f4fffb', '#2f8f9d') },
        { name: 'ハル@回収率重視の三連単フォーメーション研究所', icon: mockIcon('mono', '#e6394f', '#ffffff', 'H') },
        { name: 'ゆうき｜POG', icon: mockIcon('person', '#5b8def', '#eaf1ff', '#283a8f') }
    ];

    var off = document.getElementById('ambOfficialIcon');
    if (off) { off.src = AMB_OFFICIAL_ICON; off.alt = 'Eval'; }
    var ambCount = document.getElementById('ambCount');
    if (ambCount) ambCount.textContent = AMBASSADORS.length + '名が活用法や的中報告を発信中';
    var ambStack = document.getElementById('ambStack');
    if (ambStack) {
        ambStack.innerHTML = AMBASSADORS.slice(0, 5).map(function (a) {
            return '<img src="' + a.icon + '" alt="" loading="lazy">';
        }).join('');
    }
})();
