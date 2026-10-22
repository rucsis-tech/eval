/**
 * P3 商品詳細（モック）
 */
(function () {
    'use strict';

    var wrap = document.querySelector('.pc-wrap[data-tool-id]');
    var toolId = wrap ? wrap.getAttribute('data-tool-id') || 't303' : 't303';

    var PRODUCT_BY_ID = {
        t101: { name: '本日の前走不利情報（8/8）', cat: '解析が拾えない情報', price: 300, type: 'pt', isNew: true, contentTitle: '前走不利該当馬' },
        t102: { name: '本日の馬具・関係者情報（8/8）', cat: '解析が拾えない情報', price: 0, type: 'freebie', isNew: false, contentTitle: '馬具・関係者シグナル' },
        t201: { name: '本日のラップ分析情報（8/8）', cat: 'ファクター特化', price: 300, type: 'pt', isNew: true, contentTitle: 'ラップ分析該当馬' },
        t202: { name: '本日の血統分析情報（8/8）', cat: 'ファクター特化', price: 300, type: 'pt', isNew: false, contentTitle: '血統分析該当馬' },
        t301: { name: '推奨馬リスト（8/8 中央）', cat: 'まとめ・時短', price: 500, type: 'pt', isNew: true, contentTitle: '推奨馬リスト' },
        t302: { name: '高好走確率リスト（SABCランク）', cat: 'まとめ・時短', price: 0, type: 'subsc', isNew: false, contentTitle: 'SABCランク一覧' },
        t303: { name: '危険な人気馬リスト（8/8）', cat: 'まとめ・時短', price: 300, type: 'pt', isNew: false, contentTitle: '危険な人気馬' }
    };

    var PRODUCT = PRODUCT_BY_ID[toolId] || PRODUCT_BY_ID.t301;

    var REC_HORSES = [
        { race: '新潟11R', time: '15:30', umaban: 7, f: 'f5', name: 'エヴァルライト', prob: 31.2, ev: 128, hi: true, memo: '最終追い切り抜群。内枠有利の馬場で先行力が活きる' },
        { race: '札幌9R', time: '14:22', umaban: 3, f: 'f3', name: 'グリーンシグナル', prob: 27.8, ev: 112, hi: false, memo: '前走は出遅れ不利。巻き返し十分の一戦' },
        { race: '新潟8R', time: '13:48', umaban: 12, f: 'f7', name: 'データストリーム', prob: 24.5, ev: 135, hi: true, memo: '距離短縮で一変期待。オッズに歪みあり妙味大' },
        { race: '札幌6R', time: '12:40', umaban: 1, f: 'f1', name: 'プロブフォンテン', prob: 22.1, ev: 104, hi: false, memo: '血統的にこの条件がベスト。人気の盲点' },
        { race: '新潟5R', time: '12:10', umaban: 5, f: 'f4', name: 'マスターオッズ', prob: 19.6, ev: 121, hi: false, memo: '新馬戦の内容が秀逸。上積み見込める2戦目' }
    ];

    var status = 'free';
    var type = PRODUCT.type;
    var owned = false;
    var balance = 7777;

    function yen(n) {
        return n.toLocaleString();
    }

    function esc(s) {
        return String(s).replace(/[&<>"]/g, function (m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[m];
        });
    }

    function syncProductHeader() {
        var cat = document.getElementById('catLabel');
        var name = document.getElementById('prodName');
        var prodNew = document.getElementById('prodNew');
        var contentTitle = document.getElementById('contentTitle');
        if (cat) cat.textContent = PRODUCT.cat;
        if (name) name.textContent = PRODUCT.name;
        if (prodNew) prodNew.hidden = !PRODUCT.isNew;
        if (contentTitle) contentTitle.textContent = PRODUCT.contentTitle;
    }

    function syncMockTypeButtons() {
        document.querySelectorAll('.mock-ctrl a[data-ty]').forEach(function (x) {
            x.classList.toggle('on', x.getAttribute('data-ty') === type);
        });
    }

    function canView() {
        if (status === 'guest') return false;
        if (type === 'freebie') return true;
        if (type === 'subsc') return status === 'sub_full';
        return owned;
    }

    function render() {
        var hBal = document.querySelector('.h-point .p-num');
        if (hBal) hBal.textContent = status === 'guest' ? '−' : yen(balance);

        var pb = document.getElementById('priceBand');
        if (pb) {
            if (type === 'freebie') {
                pb.innerHTML = '<div><p class="pb-label">価格</p><p class="pb-price free">無料</p></div>' +
                    (status !== 'guest' ? '<span class="pb-owned">閲覧できます</span>' : '');
            } else if (type === 'subsc') {
                pb.innerHTML = '<div><p class="pb-label">価格</p><p class="pb-price free" style="font-size:14px;">サブスク見放題対象</p></div>' +
                    '<span class="pb-sub">全機能使い放題プラン</span>' +
                    (status === 'sub_full' ? '<span class="pb-owned">見放題で閲覧中</span>' : '');
            } else {
                var short = status !== 'guest' && balance < PRODUCT.price;
                pb.innerHTML = '<div><p class="pb-label">価格</p><p class="pb-price">' + yen(PRODUCT.price) + '<small>pt</small></p></div>' +
                    (owned && status !== 'guest' ? '<span class="pb-owned">購入済み</span>' :
                        status !== 'guest' ? '<span class="pb-bal">保有ポイント <b>' + yen(balance) + 'pt</b>' + (short ? '<br><span class="short">残高不足</span>' : '') + '</span>' : '');
            }
        }

        var listHTML = '<ul class="rec-list">' + REC_HORSES.map(function (h) {
            return '<li class="rec-row">' +
                '<span class="r-race"><b>' + esc(h.race) + '</b>' + esc(h.time) + '発走</span>' +
                '<span class="umaban ' + h.f + '">' + h.umaban + '</span>' +
                '<span><span class="r-name">' + esc(h.name) + '</span><br>' +
                '<span style="font-size:9.5px;color:var(--text-sub);line-height:1.5;">' + esc(h.memo) + '</span></span>' +
                '<span class="r-prob"><span class="v' + (h.hi ? ' hi' : '') + '">' + h.ev + '%</span><small>単勝期待値</small>' +
                '<span class="v" style="font-size:11px;">' + h.prob + '%</span><small>勝利確率</small></span></li>';
        }).join('') + '</ul>' +
            '<p class="rec-note">※ 確率・期待値は<b>8/7 20:00公開時点</b>の値です。最新の値はTOPページ（出馬表）でご確認ください。</p>';

        var ca = document.getElementById('contentArea');
        if (ca) {
            if (canView()) {
                ca.innerHTML = listHTML;
            } else {
                var msg = status === 'guest'
                    ? '閲覧には無料登録が必要です'
                    : type === 'subsc' ? '全機能使い放題プランで見放題'
                        : yen(PRODUCT.price) + 'ptで購入すると全' + REC_HORSES.length + '頭を閲覧できます';
                ca.innerHTML = '<div class="lock-wrap">' + listHTML +
                    '<div class="lock-ov">' +
                    '<span class="l-ico"><svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></span>' +
                    '<p class="l-msg">' + esc(msg) + '</p></div></div>';
            }
        }

        var cw = document.getElementById('ctaWrap');
        if (!cw) return;

        if (canView()) {
            cw.innerHTML = type === 'pt'
                ? '<p class="cta-note">購入済みの商品です。当日レース終了まで閲覧できます（マイページの購入済み一覧からも開けます）。</p>'
                : '';
        } else if (status === 'guest') {
            cw.innerHTML = '<a class="cta-main lime" href="/auth/entry">無料登録して' + (type === 'freebie' ? '閲覧する' : 'すすむ') + ' ›</a>' +
                '<a class="cta-sub" href="/auth/login">アカウントをお持ちの方は<b>ログイン</b></a>';
        } else if (type === 'subsc') {
            cw.innerHTML = '<a class="cta-main subsc" href="/settle/settle_subsc">全機能使い放題プランを見る ›</a>' +
                '<p class="cta-note">プラン加入で対象商品がすべて見放題になります。</p>';
        } else if (balance < PRODUCT.price) {
            cw.innerHTML = '<a class="cta-main green" id="goPt" href="/settle/settle_point">ポイントが足りません — ポイント購入へ ›</a>' +
                '<p class="cta-note">保有 ' + yen(balance) + 'pt ／ 必要 ' + yen(PRODUCT.price) + 'pt</p>';
        } else {
            cw.innerHTML = '<button type="button" class="cta-main pt" id="buyOpen">' + yen(PRODUCT.price) + 'ptで購入して閲覧する</button>' +
                '<p class="cta-note">保有ポイント ' + yen(balance) + 'pt → 購入後 ' + yen(balance - PRODUCT.price) + 'pt</p>';
            var buyOpen = document.getElementById('buyOpen');
            if (buyOpen) buyOpen.addEventListener('click', openSheet);
        }
    }

    function openSheet() {
        var sh = document.getElementById('sheet');
        var sheetOv = document.getElementById('sheetOv');
        if (!sh || !sheetOv) return;
        sh.innerHTML =
            '<div class="grip"></div>' +
            '<div class="sh-head"><span class="sh-title">購入内容の確認</span><span class="sh-close" data-close>✕</span></div>' +
            '<div class="sh-item">' +
            '<span><span class="si-name">' + esc(PRODUCT.name) + '</span><br><span class="si-cat">' + esc(PRODUCT.cat) + '／Eval公式</span></span>' +
            '<span class="si-price">' + yen(PRODUCT.price) + 'pt</span></div>' +
            '<div class="sh-bal"><span>保有ポイント</span><b>' + yen(balance) + 'pt → ' + yen(balance - PRODUCT.price) + 'pt</b></div>' +
            '<p class="sh-terms">情報商品の性質上、<b style="color:#f2f4f8;">購入後の返金はできません</b>。当日レース終了まで閲覧できます。</p>' +
            '<button type="button" class="sh-cta" id="buyBtn">' + yen(PRODUCT.price) + 'ptで購入する</button>' +
            '<a class="sh-cancel" data-close>キャンセル</a>';
        sh.querySelectorAll('[data-close]').forEach(function (el) {
            el.addEventListener('click', closeSheet);
        });
        var buyBtn = document.getElementById('buyBtn');
        if (buyBtn) buyBtn.addEventListener('click', complete);
        sheetOv.classList.add('show');
    }

    function complete() {
        balance -= PRODUCT.price;
        owned = true;
        var mcOwned = document.getElementById('mcOwned');
        if (mcOwned) mcOwned.classList.add('on');
        var sh = document.getElementById('sheet');
        if (sh) {
            sh.innerHTML =
                '<div class="grip"></div>' +
                '<div class="done-view">' +
                '<div class="d-mark"><svg viewBox="0 0 24 24"><path d="M4 12.5l5 5L20 6.5"/></svg></div>' +
                '<p class="d-title">購入が完了しました</p>' +
                '<p class="d-sub">そのまま下にスクロールして' + esc(PRODUCT.contentTitle) + 'をご覧ください</p></div>' +
                '<a class="sh-cta" data-close style="background:linear-gradient(150deg,#0e9a70,#046b4c);color:#fff;">' + esc(PRODUCT.contentTitle) + 'を見る</a>';
            sh.querySelectorAll('[data-close]').forEach(function (el) {
                el.addEventListener('click', closeSheet);
            });
        }
        render();
    }

    function closeSheet() {
        var sheetOv = document.getElementById('sheetOv');
        if (sheetOv) sheetOv.classList.remove('show');
    }

    var sheetOv = document.getElementById('sheetOv');
    if (sheetOv) {
        sheetOv.addEventListener('click', function (e) {
            if (e.target === e.currentTarget) closeSheet();
        });
    }

    document.querySelectorAll('.mock-ctrl a[data-st]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            status = a.getAttribute('data-st');
            document.querySelectorAll('.mock-ctrl a[data-st]').forEach(function (x) {
                x.classList.toggle('on', x === a);
            });
            render();
        });
    });

    document.querySelectorAll('.mock-ctrl a[data-ty]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            type = a.getAttribute('data-ty');
            document.querySelectorAll('.mock-ctrl a[data-ty]').forEach(function (x) {
                x.classList.toggle('on', x === a);
            });
            render();
        });
    });

    var mcOwned = document.getElementById('mcOwned');
    if (mcOwned) {
        mcOwned.addEventListener('click', function (e) {
            e.preventDefault();
            owned = !owned;
            mcOwned.classList.toggle('on', owned);
            render();
        });
    }

    syncProductHeader();
    syncMockTypeButtons();
    render();
})();
