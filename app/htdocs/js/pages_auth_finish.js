/**
 * P11 会員登録完了（アップセル）モック UI
 */
(function () {
    'use strict';

    var PLANS = {
        full: { card: 'planFull', price: 'fullPrice', benefit: 'fullBenefit', cta: 'fullCta', base: '月額9,800円', ctaBase: null },
        chuo: { card: 'planChuo', price: 'chuoPrice', benefit: 'chuoBenefit', cta: 'chuoCta', base: '月額5,980円', ctaBase: '中央プランを選ぶ' },
        chiho: { card: 'planChiho', price: 'chihoPrice', benefit: 'chihoBenefit', cta: 'chihoCta', base: '月額3,980円', ctaBase: '地方プランを選ぶ' }
    };
    var INVITES = {
        EVALFREE: { label: '初月無料', desc: 'フル機能プランの初月が無料になります', plans: { full: { free: true } } },
        EVALW: {
            label: '初月無料＋特別価格',
            desc: 'フル機能プランが初月無料、2ヶ月目以降もずっと月額4,980円（税込・仮）',
            plans: { full: { free: true, sp: '月額4,980円' } }
        },
        EVALSP: { label: '特別価格', desc: 'フル機能プランがずっと月額4,980円（税込・仮）', plans: { full: { sp: '月額4,980円' } } },
        EVALALL: {
            label: '全プラン特別価格',
            desc: 'すべてのプランが特別価格（フル4,980／中央2,980／地方1,980・税込仮）',
            plans: { full: { sp: '月額4,980円' }, chuo: { sp: '月額2,980円' }, chiho: { sp: '月額1,980円' } }
        }
    };

    var campaignOn = true;
    var invite = null;
    var recoPlan = 'full';
    var ivBox = document.querySelector('.invite-box');

    if (!document.getElementById('upsellView')) {
        return;
    }

    function setReco(key) {
        recoPlan = key;
        Object.keys(PLANS).forEach(function (k) {
            var cfg = PLANS[k];
            var card = document.getElementById(cfg.card);
            if (!card) return;
            card.classList.toggle('reco', k === key);
            var reco = card.querySelector('.p-reco');
            if (reco) reco.hidden = k !== key;
        });
        document.querySelectorAll('.d-reco').forEach(function (b) {
            b.classList.toggle('on', b.dataset.reco === key);
        });
    }

    function renderPlans() {
        var fullB = invite && invite.plans.full;
        var freeBadge = document.getElementById('freeBadge');
        var offerBand = document.getElementById('offerBand');
        if (freeBadge) freeBadge.hidden = !(campaignOn || (fullB && fullB.free));
        if (offerBand) offerBand.hidden = !campaignOn;

        Object.keys(PLANS).forEach(function (k) {
            var cfg = PLANS[k];
            var b = invite && invite.plans[k];
            var priceEl = document.getElementById(cfg.price);
            if (priceEl) {
                priceEl.innerHTML = b && b.sp
                    ? '<span class="p-old">' + cfg.base + '</span><span class="p-sp">' + b.sp + '</span><small>（税込・仮）</small>'
                    : cfg.base + '<small>（税込・仮）</small>';
            }
            var ben = document.getElementById(cfg.benefit);
            if (ben) {
                ben.hidden = !b;
                if (b) {
                    ben.textContent = '🎟 招待特典：' + (b.free && b.sp ? '初月無料＋ずっと' + b.sp : b.free ? '初月無料' : 'ずっと' + b.sp);
                }
            }
            var cta = document.getElementById(cfg.cta);
            if (!cta) return;
            if (k === 'full') {
                cta.textContent = fullB ? '招待特典を使って申し込む ▶' : campaignOn ? '初月無料でフル機能を試す ▶' : 'フルプランを申し込む ▶';
            } else {
                cta.textContent = b ? '招待特典で申し込む ▶' : cfg.ctaBase;
            }
        });
    }

    function applyCampaign(on) {
        campaignOn = on;
        var onBtn = document.getElementById('d-campOn');
        var offBtn = document.getElementById('d-campOff');
        if (onBtn) onBtn.classList.toggle('on', on);
        if (offBtn) offBtn.classList.toggle('on', !on);
        renderPlans();
    }

    document.querySelectorAll('.d-reco').forEach(function (b) {
        b.addEventListener('click', function () { setReco(b.dataset.reco); });
    });

    var campOn = document.getElementById('d-campOn');
    var campOff = document.getElementById('d-campOff');
    if (campOn) campOn.addEventListener('click', function () { applyCampaign(true); });
    if (campOff) campOff.addEventListener('click', function () { applyCampaign(false); });

    var inviteBtn = document.getElementById('inviteBtn');
    if (inviteBtn) {
        inviteBtn.addEventListener('click', function () {
            var input = document.getElementById('inviteInput');
            var code = input ? input.value.trim().toUpperCase() : '';
            var hit = INVITES[code];
            var err = document.getElementById('ivErr');
            if (err) err.hidden = !!hit || !code;
            if (!hit) return;
            invite = hit;
            if (ivBox) ivBox.classList.add('applied');
            var form = document.getElementById('ivForm');
            var applied = document.getElementById('ivApplied');
            if (form) form.hidden = true;
            if (applied) applied.hidden = false;
            var t = document.getElementById('ivAppliedT');
            var s = document.getElementById('ivAppliedS');
            if (t) t.textContent = '招待特典「' + hit.label + '」を適用しました';
            if (s) s.textContent = hit.desc;
            renderPlans();
            var first = null;
            Object.keys(PLANS).forEach(function (k) {
                if (!hit.plans[k]) return;
                var card = document.getElementById(PLANS[k].card);
                if (!card) return;
                card.classList.remove('flash');
                void card.offsetWidth;
                card.classList.add('flash');
                if (!first) first = card;
            });
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }

    var ivCancel = document.getElementById('ivCancel');
    if (ivCancel) {
        ivCancel.addEventListener('click', function () {
            invite = null;
            if (ivBox) ivBox.classList.remove('applied');
            var form = document.getElementById('ivForm');
            var applied = document.getElementById('ivApplied');
            if (form) form.hidden = false;
            if (applied) applied.hidden = true;
            renderPlans();
        });
    }

    var demoNav = {
        'd-signup': '/auth/entry',
        'd-login': '/auth/login',
        'd-code': '/auth/register'
    };
    Object.keys(demoNav).forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function () { location.href = demoNav[id]; });
    });

    var skipBtn = document.getElementById('skipBtn');
    if (skipBtn) {
        skipBtn.addEventListener('click', function (e) {
            e.preventDefault();
            location.href = '/';
        });
    }

    document.querySelectorAll('.plan .p-cta').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            location.href = '/settle/settle_subsc';
        });
    });

    setReco('full');
    renderPlans();

    var t = 23 * 3600 + 59 * 60 + 12;
    setInterval(function () {
        t = Math.max(0, t - 1);
        var h = String(Math.floor(t / 3600)).padStart(2, '0');
        var m = String(Math.floor(t % 3600 / 60)).padStart(2, '0');
        var s = String(t % 60).padStart(2, '0');
        var el = document.getElementById('offerTimer');
        if (el) el.textContent = h + ':' + m + ':' + s;
    }, 1000);
})();
