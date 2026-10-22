/**
 * Eval MODE - common.js
 * モックHTML各ページに散在していた共通UI制御を統合（Smarty 共通パーツ向け）
 */
(function () {
    'use strict';

    function $(id) {
        return document.getElementById(id);
    }

    /* ----- ハンバーガードロワー ----- */
    function initDrawer() {
        const overlay = $('drawerOverlay');
        const menuBtn = $('menuBtn');
        const closeBtn = $('drawerClose');
        if (!overlay || !menuBtn) return;

        const syncDrawerAccount = () => {
            const st = document.querySelector('.demo-switch button[data-state].on')?.dataset.state;
            if (!st) return;
            const accPanels = { guest: 'd-acc-guest', free: 'd-acc-free', subsc: 'd-acc-subsc' };
            Object.entries(accPanels).forEach(([k, id]) => {
                const el = document.getElementById(id);
                if (el) el.hidden = k !== st;
            });
        };
        const open = () => {
            syncDrawerAccount();
            overlay.classList.remove('hidden');
        };
        const close = () => overlay.classList.add('hidden');

        menuBtn.addEventListener('click', open);
        menuBtn.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                open();
            }
        });
        if (closeBtn) closeBtn.addEventListener('click', close);
        overlay.addEventListener('click', e => {
            if (e.target === overlay) close();
        });
    }

    /* ----- フォーカスレイアウト：戻る／閉じる（exit・auth） ----- */
    function goBackOrTop(fallback) {
        const top = fallback || '/';
        let same = false;
        try {
            same = !!document.referrer && new URL(document.referrer).origin === location.origin;
        } catch (e) {}
        if (location.protocol === 'file:') same = history.length > 1;
        if (same && history.length > 1) history.back();
        else location.href = top;
    }

    function bindFocusDismiss(el) {
        if (!el) return;
        const act = e => {
            if (e) e.preventDefault();
            goBackOrTop(el.dataset.fallbackTop || '/');
        };
        el.addEventListener('click', act);
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                act(e);
            }
        });
    }

    function initFocusHeader() {
        bindFocusDismiss($('focusCloseBtn'));
        bindFocusDismiss($('focusBackBtn'));
    }

    /* ----- 会員登録追従バナー ----- */
    function initRegBanner() {
        const close = $('regClose');
        const banner = $('regBanner');
        if (!close || !banner) return;
        close.addEventListener('click', () => banner.classList.add('hidden'));
    }

    /* ----- 汎用タブ（.c-tabs / .os-tabs 等） ----- */
    function initTabs() {
        document.querySelectorAll('.c-tabs, .os-tabs').forEach(root => {
            const tabs = root.querySelectorAll('a[data-tab], a[data-scope]');
            if (!tabs.length) return;
            tabs.forEach(tab => {
                tab.addEventListener('click', e => {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('on'));
                    tab.classList.add('on');
                    const key = tab.dataset.tab;
                    if (!key) return;
                    const lists = {
                        topics: $('list-topics'),
                        flash: $('list-flash'),
                        info: $('list-info'),
                    };
                    Object.entries(lists).forEach(([k, el]) => {
                        if (el) el.hidden = k !== key;
                    });
                    const readMore = $('readMore');
                    if (readMore) {
                        const href = {
                            topics: '/topics/topics_top',
                            flash: '/topics/topics_top?tab=flash',
                            info: '/topics/topics_top?tab=info',
                        };
                        readMore.setAttribute('href', href[key] || '/topics/topics_top');
                    }
                });
            });
        });
    }

    /* ----- TOP：速報ティッカー ----- */
    function initTicker() {
        const items = document.querySelectorAll('#ticker a');
        if (items.length < 2) return;
        let ti = 0;
        setInterval(() => {
            items[ti].classList.remove('show');
            ti = (ti + 1) % items.length;
            items[ti].classList.add('show');
        }, 4000);
    }

    function initFlashMore() {
        const flashMore = $('flashMore');
        const flashTab = document.querySelector('.c-tabs a[data-tab="flash"]');
        const module = $('topicsModule');
        if (!flashMore || !flashTab || !module) return;
        flashMore.addEventListener('click', e => {
            e.preventDefault();
            flashTab.click();
            module.scrollIntoView({ behavior: 'smooth' });
        });
    }

    /* ----- ボトムシート／ハーフモーダル（オーバーレイタップで閉じる） ----- */
    function initSheets() {
        document.querySelectorAll('.sheet-ov').forEach(ov => {
            ov.addEventListener('click', e => {
                if (e.target === ov) ov.classList.remove('show');
            });
        });
    }

    /* ----- ホーム画面追加バナー（PWA / A2HS） ----- */
    function initA2hs() {
        const a2hs = $('a2hs');
        if (!a2hs) return;
        const SNOOZE_DAYS = 7;
        const KEY = 'evm_a2hs_dismissed_at';
        const INSTALLED = 'evm_a2hs_installed';
        const lsGet = k => {
            try { return localStorage.getItem(k); } catch (e) { return null; }
        };
        const lsSet = (k, v) => {
            try { localStorage.setItem(k, v); } catch (e) {}
        };
        const isStandalone = () =>
            window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        async function isInstalled() {
            if (lsGet(INSTALLED)) return true;
            try {
                if (navigator.getInstalledRelatedApps) {
                    const apps = await navigator.getInstalledRelatedApps();
                    if (apps && apps.length) return true;
                }
            } catch (e) {}
            return false;
        }
        function snoozed() {
            const t = parseInt(lsGet(KEY) || '0', 10);
            return t && Date.now() - t < SNOOZE_DAYS * 864e5;
        }
        async function run() {
            if (isStandalone()) return;
            if (await isInstalled()) return;
            if (snoozed()) return;
            a2hs.hidden = false;
        }
        run();
        window.addEventListener('appinstalled', () => {
            lsSet(INSTALLED, '1');
            a2hs.hidden = true;
        });
        const close = $('a2hsClose');
        if (close) {
            close.addEventListener('click', e => {
                e.stopPropagation();
                lsSet(KEY, String(Date.now()));
                a2hs.hidden = true;
            });
        }
        a2hs.addEventListener('click', () => {
            location.href = '/pages/home_guide';
        });
    }

    /* ----- アプリ用「戻る」ボタン（?app=1 は実装対象外） ----- */
    function initAppBack() {
        const btn = $('appBack');
        if (!btn) return;
        const isApp =
            (window.matchMedia && matchMedia('(display-mode: standalone)').matches) ||
            window.navigator.standalone === true ||
            /EvalModeApp/i.test(navigator.userAgent);
        if (!isApp) return;
        btn.hidden = false;

        function place() {
            let top = window.innerHeight;
            document.querySelectorAll('.gnav, .sticky-cta').forEach(el => {
                const s = getComputedStyle(el);
                if (s.display === 'none' || s.visibility === 'hidden' || (s.position !== 'fixed' && s.position !== 'sticky')) return;
                const r = el.getBoundingClientRect();
                if (r.height > 0 && r.bottom >= window.innerHeight - 30) top = Math.min(top, r.top);
            });
            const b = top < window.innerHeight ? window.innerHeight - top + 10 : 16;
            btn.style.setProperty('--app-back-b', b + 'px');
        }
        place();
        window.addEventListener('resize', place);
        setTimeout(place, 400);

        let lastY = window.scrollY;
        let ticking = false;
        function onScroll() {
            const y = window.scrollY;
            const dy = y - lastY;
            const atTop = y < 40;
            const atBottom = window.innerHeight + y >= document.documentElement.scrollHeight - 4;
            if (atTop || atBottom || dy < -6) btn.classList.remove('is-hide');
            else if (dy > 6) btn.classList.add('is-hide');
            if (Math.abs(dy) > 6) lastY = y;
            ticking = false;
        }
        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(onScroll);
            }
        }, { passive: true });

        btn.addEventListener('click', () => {
            let same = false;
            try {
                same = !!document.referrer && new URL(document.referrer).origin === location.origin;
            } catch (e) {}
            if (location.protocol === 'file:') same = history.length > 1;
            if (same && history.length > 1) history.back();
            else location.href = '/';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initDrawer();
        initFocusHeader();
        initRegBanner();
        initTabs();
        initTicker();
        initFlashMore();
        initSheets();
        initA2hs();
        initAppBack();
    });
})();
