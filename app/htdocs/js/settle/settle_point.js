/**
 * P7 ポイント購入（モック）
 */
(function () {
    'use strict';

    const CAMPAIGNS = [
        { id: 'first1000', type: 'once', pt: 1000, bonus: 1000, price: 1000, base: 2000,
            note: '初めてのポイント購入限定。<b>2,000pt</b>が1,000円（実質+100%）。有効期限：購入から90日' },
        { id: 'summer3000', type: 'limited', pt: 3000, bonus: 900, price: 3000, until: '8/17(月) 23:59',
            note: 'サマーキャンペーン。通常+3%のところ<b>+30%</b>。有効期限：購入から90日' },
    ];
    const PACKS = [
        { pt: 500, bonus: 0, price: 500 },
        { pt: 1000, bonus: 0, price: 1000 },
        { pt: 3000, bonus: 90, price: 3000 },
        { pt: 5000, bonus: 250, price: 5000, pop: true },
        { pt: 10000, bonus: 800, price: 10000 },
    ];
    const PAY_METHODS = [
        { id: 'card', name: 'クレジットカード', sub: 'Visa / Master / JCB / AMEX' },
        { id: 'apple', name: 'Apple Pay', sub: '' },
        { id: 'google', name: 'Google Pay', sub: '' },
        { id: 'carrier', name: 'キャリア決済', sub: 'docomo / au / SoftBank' },
    ];

    let isGuest = false;
    let oncePurchased = false;
    let campaignOn = true;
    let balance = 7777;
    let payMethod = 'card';

    const yen = (n) => n.toLocaleString();
    const hBalEl = () => document.querySelector('.h-point .p-num');

    function render() {
        const bal = document.getElementById('balCard');
        const hBal = hBalEl();
        if (isGuest) {
            bal.className = 'bal-card guest';
            bal.innerHTML = '<span class="p-coin">P</span>'
                + '<div><p class="b-name">保有ポイント</p><p class="b-num">ログインすると残高が表示されます</p></div>';
            if (hBal) hBal.textContent = '−';
        } else {
            bal.className = 'bal-card';
            bal.innerHTML = '<span class="p-coin">P</span>'
                + '<div><p class="b-name">保有ポイント</p><p class="b-num">' + yen(balance) + '<small>pt</small></p></div>'
                + '<div class="b-exp">有効期限が近いポイント<br><b>500pt</b>（8/31まで）</div>';
            if (hBal) hBal.textContent = yen(balance);
        }
        document.getElementById('regCta').hidden = !isGuest;

        const camps = campaignOn ? CAMPAIGNS : [];
        document.getElementById('campTitle').hidden = !camps.length;
        document.getElementById('campList').innerHTML = camps.map((c) => {
            const done = c.type === 'once' && oncePurchased && !isGuest;
            const badges =
                (c.type === 'once' ? '<span class="c-tag once">おひとり様1回限り</span>' : '<span class="c-tag limited">期間限定</span>')
                + (c.until ? '<span class="c-tag until">〜' + c.until + '</span>' : '');
            return '<a href="#" class="camp-card' + (done ? ' done' : '') + '" data-camp="' + c.id + '">'
                + (done ? '<span class="c-done">購入済み</span>' : '')
                + '<span class="c-badges">' + badges + '</span>'
                + '<span class="c-main">'
                + '<span class="c-pt">' + yen(c.pt + c.bonus) + '<small>pt</small></span>'
                + '<span class="c-bonus">+' + yen(c.bonus) + 'pt ボーナス</span>'
                + '<span class="c-price"><span class="yen">¥' + yen(c.price) + '</span>'
                + (c.base ? '<span class="base">通常 ¥' + yen(c.base) + '相当</span>' : '') + '</span>'
                + '</span><span class="c-note">' + c.note + '</span></a>';
        }).join('');

        document.getElementById('packList').innerHTML = PACKS.map((p, i) => (
            '<li><a href="#" class="pack-row" data-pack="' + i + '">'
            + '<span class="p-coin">P</span>'
            + '<span class="p-pt">' + yen(p.pt + p.bonus) + '<small>pt</small></span>'
            + (p.bonus ? '<span class="p-bonus">+' + yen(p.bonus) + 'pt</span>' : '')
            + (p.pop ? '<span class="p-pop">人気No.1</span>' : '')
            + '<span class="p-price">¥' + yen(p.price) + '</span>'
            + '<span class="p-chev">›</span></a></li>'
        )).join('');
    }

    function openSheet(item) {
        const sh = document.getElementById('sheet');
        if (isGuest) {
            sh.innerHTML = ''
                + '<div class="grip"></div>'
                + '<div class="sh-head"><span class="sh-title">無料登録してポイントを購入</span><span class="sh-close" data-close>✕</span></div>'
                + '<div class="sh-pack">'
                + '<span class="p-coin">P</span>'
                + '<span class="sp-pt">' + yen(item.pt + item.bonus) + '<small>pt</small></span>'
                + (item.bonus ? '<span class="sp-bonus">+' + yen(item.bonus) + 'pt</span>' : '')
                + '<span class="sp-price">¥' + yen(item.price) + '</span></div>'
                + '<p class="sh-terms">ポイントの購入には無料会員登録（またはログイン）が必要です。登録は30秒で完了し、そのまま購入に戻れます。</p>'
                + '<a class="sh-cta lime" href="/auth/entry">無料登録にすすむ ›</a>'
                + '<a href="#" class="sh-cancel" data-close>あとで</a>';
        } else {
            sh.innerHTML = ''
                + '<div class="grip"></div>'
                + '<div class="sh-head"><span class="sh-title">購入内容の確認</span><span class="sh-close" data-close>✕</span></div>'
                + '<div class="sh-pack">'
                + '<span class="p-coin">P</span>'
                + '<span class="sp-pt">' + yen(item.pt + item.bonus) + '<small>pt</small></span>'
                + (item.bonus ? '<span class="sp-bonus">+' + yen(item.bonus) + 'pt ボーナス込み</span>' : '')
                + '<span class="sp-price">¥' + yen(item.price) + '</span></div>'
                + '<p class="sh-sec">支払い方法</p>'
                + '<div class="pay-list">' + PAY_METHODS.map((m) => (
                    '<a href="#" class="pay-row' + (m.id === payMethod ? ' on' : '') + '" data-pay="' + m.id + '">'
                    + '<span class="radio"></span>' + m.name
                    + (m.sub ? '<span class="p-sub">' + m.sub + '</span>' : '')
                    + '</a>'
                )).join('') + '</div>'
                + '<p class="sh-terms">有効期限：' + (item.camp ? '購入から<b>90日</b>（キャンペーンポイント）' : '購入から<b>180日</b>') + '。'
                + (item.camp && item.camp.type === 'once' ? 'このパックは<b>おひとり様1回限り</b>です。' : '')
                + '購入後の払い戻しはできません。</p>'
                + '<button type="button" class="sh-cta" id="buyBtn">¥' + yen(item.price) + ' で購入する</button>'
                + '<a href="#" class="sh-cancel" data-close>キャンセル</a>';
            sh.querySelector('#buyBtn').addEventListener('click', () => completePurchase(item));
            sh.querySelectorAll('.pay-row').forEach((r) => r.addEventListener('click', (e) => {
                e.preventDefault();
                payMethod = r.dataset.pay;
                sh.querySelectorAll('.pay-row').forEach((x) => x.classList.toggle('on', x === r));
            }));
        }
        sh.querySelectorAll('[data-close]').forEach((el) => el.addEventListener('click', (e) => {
            e.preventDefault();
            closeSheet();
        }));
        document.getElementById('sheetOv').classList.add('show');
    }

    function completePurchase(item) {
        const got = item.pt + item.bonus;
        balance += got;
        if (item.camp && item.camp.type === 'once') oncePurchased = true;
        const sh = document.getElementById('sheet');
        sh.innerHTML = ''
            + '<div class="grip"></div>'
            + '<div class="done-view">'
            + '<div class="d-mark"><svg viewBox="0 0 24 24"><path d="M4 12.5l5 5L20 6.5"/></svg></div>'
            + '<p class="d-title">購入が完了しました</p>'
            + '<p class="d-pt"><b>+' + yen(got) + 'pt</b> を獲得しました</p>'
            + '<div class="d-bal"><span>現在の保有ポイント</span><b>' + yen(balance) + 'pt</b></div></div>'
            + '<a class="sh-cta" href="/tools/tools_top" style="margin-top:16px;">ポイントを使う（攻略ツールへ） ›</a>'
            + '<a href="#" class="sh-cancel" data-close>閉じる</a>';
        sh.querySelectorAll('[data-close]').forEach((el) => el.addEventListener('click', (e) => {
            e.preventDefault();
            closeSheet();
            render();
        }));
        render();
        document.getElementById('sheetOv').classList.add('show');
    }

    function closeSheet() {
        document.getElementById('sheetOv').classList.remove('show');
    }

    document.getElementById('sheetOv').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) closeSheet();
    });

    document.getElementById('campList').addEventListener('click', (e) => {
        const a = e.target.closest('.camp-card:not(.done)');
        if (!a) return;
        e.preventDefault();
        const c = CAMPAIGNS.find((x) => x.id === a.dataset.camp);
        if (c) openSheet({ pt: c.pt, bonus: c.bonus, price: c.price, camp: c });
    });

    document.getElementById('packList').addEventListener('click', (e) => {
        const a = e.target.closest('.pack-row');
        if (!a) return;
        e.preventDefault();
        const p = PACKS[Number(a.dataset.pack)];
        if (p) openSheet({ pt: p.pt, bonus: p.bonus, price: p.price });
    });

    const mc = {
        member: document.getElementById('mcMember'),
        guest: document.getElementById('mcGuest'),
        once: document.getElementById('mcOnce'),
        campOff: document.getElementById('mcCampOff'),
    };
    mc.member.addEventListener('click', (e) => {
        e.preventDefault();
        isGuest = false;
        mc.member.classList.add('on');
        mc.guest.classList.remove('on');
        render();
    });
    mc.guest.addEventListener('click', (e) => {
        e.preventDefault();
        isGuest = true;
        mc.guest.classList.add('on');
        mc.member.classList.remove('on');
        render();
    });
    mc.once.addEventListener('click', (e) => {
        e.preventDefault();
        oncePurchased = !oncePurchased;
        mc.once.classList.toggle('on', oncePurchased);
        render();
    });
    mc.campOff.addEventListener('click', (e) => {
        e.preventDefault();
        campaignOn = !campaignOn;
        mc.campOff.classList.toggle('on', !campaignOn);
        render();
    });

    render();
})();
