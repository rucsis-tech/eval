/**
 * P14 / P15 / P16 一覧（タブ切替）
 */
(function () {
    'use strict';

    var T = window.EvmTopics;
    var wrap = document.querySelector('.pc-wrap.pc-topics[data-initial-tab]');
    if (!T || !wrap) return;

    var curTab = wrap.getAttribute('data-initial-tab') || 'topics';
    if (!T.TAB_META[curTab]) curTab = 'topics';
    var curCat = 'すべて';

    function setTab(tab) {
        curTab = tab;
        document.querySelectorAll('.c-tabs a').forEach(function (x) {
            x.classList.toggle('on', x.dataset.tab === tab);
        });
        var tabTopics = document.getElementById('tabTopics');
        var tabFlash = document.getElementById('tabFlash');
        var tabInfo = document.getElementById('tabInfo');
        if (tabTopics) tabTopics.hidden = tab !== 'topics';
        if (tabFlash) tabFlash.hidden = tab !== 'flash';
        if (tabInfo) tabInfo.hidden = tab !== 'info';
        var meta = T.TAB_META[tab];
        var pageTitle = document.getElementById('pageTitle');
        var pageEn = document.getElementById('pageEn');
        if (pageTitle && meta) pageTitle.textContent = meta[0];
        if (pageEn && meta) pageEn.textContent = meta[1];
    }

    function renderCats() {
        var catRow = document.getElementById('catRow');
        if (!catRow) return;
        catRow.innerHTML = T.CATS.map(function (c) {
            return (
                '<button type="button" class="cat-chip' +
                (c === curCat ? ' on' : '') +
                '" data-c="' +
                c +
                '">' +
                c +
                '</button>'
            );
        }).join('');
        catRow.querySelectorAll('.cat-chip').forEach(function (b) {
            b.addEventListener('click', function () {
                curCat = b.dataset.c;
                renderCats();
                renderTopics();
            });
        });
    }

    function renderTopics() {
        var list = document.getElementById('topicsList');
        if (!list) return;
        var items = T.TOPICS.filter(function (a) {
            return curCat === 'すべて' || a.cat === curCat;
        });
        list.innerHTML = items
            .map(function (a) {
                var thumb = a.thumb
                    ? '<div class="thumb ' + a.thumb.cls + '">' + a.thumb.label + '</div>'
                    : '';
                var excerpt = !a.thumb && a.excerpt ? '<p class="excerpt">' + a.excerpt + '</p>' : '';
                return (
                    '<li class="card"><a href="/topics/topics_detail?id=' +
                    encodeURIComponent(String(a.id)) +
                    '">' +
                    thumb +
                    '<div class="body"><h3>' +
                    a.title +
                    '</h3>' +
                    excerpt +
                    '<div class="meta"><span class="b-cat">' +
                    a.cat +
                    '</span>' +
                    a.date +
                    (a.isNew ? ' <span class="b-new">NEW</span>' : '') +
                    '<span class="like">♡ ' +
                    a.likes +
                    '</span></div></div></a></li>'
                );
            })
            .join('');
    }

    function renderFlash() {
        var el = document.getElementById('tabFlash');
        if (!el) return;
        el.innerHTML =
            T.FLASH.map(function (g) {
                return (
                    '<div class="f-date">' +
                    g.date +
                    '</div><ul class="f-list">' +
                    g.items
                        .map(function (i) {
                            var lbl = i.type === 'hit' ? '的中' : i.type === 'win5' ? 'WIN5' : '情報';
                            return (
                                '<li><a href="#" class="f-item">' +
                                '<span class="f-type ' +
                                i.type +
                                '">' +
                                lbl +
                                '</span>' +
                                '<span class="f-txt">' +
                                i.txt +
                                '</span>' +
                                '<span class="f-time">' +
                                i.t +
                                '</span></a></li>'
                            );
                        })
                        .join('') +
                    '</ul>'
                );
            }).join('') + '<a href="#" class="read-more">もっと見る ▼</a>';
        el.querySelectorAll('.f-item, .read-more').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
            });
        });
    }

    function renderInfo() {
        var el = document.getElementById('tabInfo');
        if (!el) return;
        var pinned = T.NOTICES.filter(function (n) {
            return n.pinned;
        });
        var rest = T.NOTICES.filter(function (n) {
            return !n.pinned;
        });
        el.innerHTML =
            pinned
                .map(function (n) {
                    return (
                        '<a class="n-pinned" href="/topics/info_detail?id=' +
                        encodeURIComponent(String(n.id)) +
                        '"><div class="np-head"><span class="np-lbl">' +
                        n.lbl +
                        '</span><span class="np-title">' +
                        n.title +
                        '</span></div><p class="np-date">' +
                        n.date +
                        '</p></a>'
                    );
                })
                .join('') +
            '<ul class="info-list">' +
            rest
                .map(function (n) {
                    return (
                        '<li><a href="/topics/info_detail?id=' +
                        encodeURIComponent(String(n.id)) +
                        '"><span class="n-lbl">' +
                        n.lbl +
                        '</span><span class="i-txt">' +
                        n.title +
                        '</span><span class="date">' +
                        n.date.slice(5).replace('-', '/') +
                        '</span></a></li>'
                    );
                })
                .join('') +
            '</ul><a href="#" class="read-more">もっと見る ▼</a>';
        var more = el.querySelector('.read-more');
        if (more) {
            more.addEventListener('click', function (e) {
                e.preventDefault();
            });
        }
    }

    document.querySelectorAll('.c-tabs a').forEach(function (t) {
        t.addEventListener('click', function (e) {
            e.preventDefault();
            setTab(t.dataset.tab);
        });
    });

    setTab(curTab);
    renderCats();
    renderTopics();
    renderFlash();
    renderInfo();
})();
