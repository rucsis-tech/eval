/**
 * P9 アンケート一覧
 */
(function () {
    'use strict';

    var data = window.EvmEnquete;
    if (!data) return;

    var curTab = 'open';
    var svList = document.getElementById('svList');
    if (!svList) return;

    function renderList() {
        var items = data.SURVEYS.filter(function (s) {
            return s.status === curTab;
        });
        svList.innerHTML = items
            .map(function (s) {
                var img = s.img
                    ? '<div class="sv-img ' +
                      s.img.cls +
                      '">' +
                      s.img.label +
                      '<span class="ratio-tag">元素材 ' +
                      s.img.ratio +
                      (s.img.ratio === '1:1' ? '→16:9クロップ' : '') +
                      '</span></div>'
                    : '';
                var st = s.result
                    ? '<span class="st result">結果を見る</span>'
                    : s.status === 'closed'
                      ? '<span class="st end">終了</span>'
                      : s.answered
                        ? '<span class="st done">✓ 回答済み</span>'
                        : '<span class="st new">未回答</span>';
                var tag = s.status === 'closed' ? 'div' : 'a';
                var href =
                    s.status === 'closed'
                        ? ''
                        : ' href="/enquete/enquete_detail?id=' + encodeURIComponent(s.id) + '"';
                return (
                    '<' +
                    tag +
                    ' class="sv-card"' +
                    href +
                    ' data-id="' +
                    s.id +
                    '">' +
                    img +
                    '<div class="sv-head"><span class="sv-title">' +
                    s.title +
                    '</span><span class="sv-pt">🎁' +
                    s.pt +
                    'pt</span></div>' +
                    '<div class="sv-meta">' +
                    st +
                    '<span>' +
                    s.until +
                    '</span><span class="joined">' +
                    s.joined.toLocaleString() +
                    '人回答</span></div></' +
                    tag +
                    '>'
                );
            })
            .join('');
    }

    document.querySelectorAll('.c-tabs a').forEach(function (t) {
        t.addEventListener('click', function (e) {
            e.preventDefault();
            curTab = t.dataset.tab;
            document.querySelectorAll('.c-tabs a').forEach(function (x) {
                x.classList.toggle('on', x === t);
            });
            renderList();
        });
    });

    renderList();
})();
