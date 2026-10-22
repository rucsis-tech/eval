/**
 * P17a お知らせ詳細
 */
(function () {
    'use strict';

    var T = window.EvmTopics;
    var wrap = document.querySelector('.pc-wrap[data-notice-id]');
    if (!T || !wrap) return;

    var notice = T.getNotice(wrap.getAttribute('data-notice-id'));
    var artWrap = document.getElementById('artWrap');
    if (!artWrap) return;

    if (!notice) {
        artWrap.innerHTML =
            '<p class="a-note">お知らせが見つかりません。<a href="/topics/topics_top?tab=info">一覧へ戻る</a></p>';
        return;
    }

    artWrap.innerHTML =
        '<div class="a-meta"><span class="n-lbl">' +
        notice.lbl +
        '</span><span>' +
        notice.date +
        '</span></div>' +
        '<h1 class="a-title">' +
        notice.title +
        '</h1>' +
        '<div class="a-body">' +
        T.bodyHTML(notice.body) +
        '</div>';
})();
