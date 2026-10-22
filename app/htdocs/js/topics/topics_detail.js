/**
 * P17 トピックス記事詳細
 */
(function () {
    'use strict';

    var T = window.EvmTopics;
    var wrap = document.querySelector('.pc-wrap[data-article-id]');
    if (!T || !wrap) return;

    var article = T.getTopic(wrap.getAttribute('data-article-id'));
    var artWrap = document.getElementById('artWrap');
    var relWrap = document.getElementById('relWrap');
    if (!artWrap) return;

    if (!article) {
        artWrap.innerHTML =
            '<p class="a-note">記事が見つかりません。<a href="/topics/topics_top">一覧へ戻る</a></p>';
        if (relWrap) relWrap.innerHTML = '';
        return;
    }

    var hero = article.thumb
        ? '<div class="a-hero ' +
          article.thumb.cls +
          '">' +
          article.thumb.label.replace('<br>', ' ') +
          '</div>'
        : '';
    var tool = article.tool
        ? '<div class="a-tool"><div class="t-head">この記事に関連する攻略ツール</div>' +
          '<a href="/tools/tools_detail?id=t303"><span class="g-ico">📈</span>' +
          '<span class="g-title">' +
          article.tool.title +
          '</span><span class="g-price">' +
          article.tool.price +
          '</span></a></div>'
        : '';

    artWrap.innerHTML =
        '<div class="a-meta"><span class="b-cat">' +
        article.cat +
        '</span><span>' +
        article.date +
        '</span></div>' +
        '<h1 class="a-title">' +
        article.title +
        '</h1>' +
        hero +
        '<div class="a-body">' +
        T.bodyHTML(article.body) +
        '</div>' +
        '<div class="a-actions">' +
        '<button type="button" class="a-like" id="likeBtn">♡ いいね <span id="likeN">' +
        article.likes +
        '</span></button>' +
        '<a class="a-share" href="#">𝕏 シェア</a></div>' +
        tool;

    var rel = T.TOPICS.filter(function (x) {
        return x.id !== article.id;
    })
        .sort(function (x, y) {
            return (y.cat === article.cat) - (x.cat === article.cat);
        })
        .slice(0, 2);

    if (relWrap) {
        relWrap.innerHTML =
            '<div class="rel-head">関連記事</div><ul class="card-list">' +
            rel
                .map(function (r) {
                    var thumb = r.thumb
                        ? '<div class="thumb ' + r.thumb.cls + '">' + r.thumb.label + '</div>'
                        : '';
                    return (
                        '<li class="card"><a href="/topics/topics_detail?id=' +
                        encodeURIComponent(String(r.id)) +
                        '">' +
                        thumb +
                        '<div class="body"><h3>' +
                        r.title +
                        '</h3><div class="meta"><span class="b-cat">' +
                        r.cat +
                        '</span>' +
                        r.date +
                        '<span class="like">♡ ' +
                        r.likes +
                        '</span></div></div></a></li>'
                    );
                })
                .join('') +
            '</ul>';
    }

    var likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        var liked = false;
        likeBtn.addEventListener('click', function (e) {
            liked = !liked;
            e.currentTarget.classList.toggle('on', liked);
            var likeN = document.getElementById('likeN');
            if (likeN) likeN.textContent = String(article.likes + (liked ? 1 : 0));
        });
    }

    var share = document.querySelector('.a-share');
    if (share) {
        share.addEventListener('click', function (e) {
            e.preventDefault();
        });
    }
})();
