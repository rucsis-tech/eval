/**
 * P9 アンケート詳細（回答）
 */
(function () {
    'use strict';

    var data = window.EvmEnquete;
    var wrap = document.querySelector('.pc-wrap[data-survey-id]');
    if (!data || !wrap) return;

    var cur = data.getSurvey(wrap.getAttribute('data-survey-id'));
    var dtWrap = document.getElementById('dtWrap');
    var submitBtn = document.getElementById('submitBtn');
    var submitPt = document.getElementById('submitPt');
    if (!cur || !dtWrap || !submitBtn || cur.status === 'closed' || !cur.qs) {
        if (dtWrap) {
            dtWrap.innerHTML =
                '<p class="submit-note">アンケートが見つからないか、終了しています。<a href="/enquete/enquete_top">一覧へ戻る</a></p>';
        }
        if (submitBtn) submitBtn.hidden = true;
        return;
    }

    function renderDetail(s) {
        var img = !s.img
            ? ''
            : s.img.ratio === '1:1'
              ? '<div class="dt-img sq"><div class="inner ' +
                s.img.cls +
                '">' +
                s.img.label +
                '</div></div>'
              : '<div class="dt-img"><div class="inner r169 ' +
                s.img.cls +
                '">' +
                s.img.label +
                '</div></div>';
        var qsHtml = s.qs
            .map(function (q, qi) {
                var head =
                    '<div class="q-head"><span class="q-no">Q' +
                    (qi + 1) +
                    '</span><span class="q-text">' +
                    q.text +
                    '</span><span class="q-req">' +
                    (q.required ? '必須' : '任意') +
                    '</span></div>';
                if (q.type === 'free') {
                    return (
                        '<div class="q-block">' +
                        head +
                        '<textarea class="free-input" data-q="' +
                        qi +
                        '" maxlength="500" placeholder="ご自由にご記入ください（500文字まで）"></textarea>' +
                        '<p class="free-count"><span data-cnt="' +
                        qi +
                        '">0</span>/500</p></div>'
                    );
                }
                var t = q.type === 'single' ? 'radio' : 'checkbox';
                var note = q.type === 'multi' ? '<p class="q-opt-note">あてはまるものをすべて選択</p>' : '';
                var opts = q.opts
                    .map(function (o, oi) {
                        return (
                            '<label class="opt"><input class="pick" type="' +
                            t +
                            '" name="q' +
                            qi +
                            '" data-q="' +
                            qi +
                            '" data-o="' +
                            oi +
                            '"><span class="o-txt">' +
                            o +
                            '</span></label>'
                        );
                    })
                    .join('');
                var other = q.other
                    ? '<label class="opt"><input class="pick" type="' +
                      t +
                      '" name="q' +
                      qi +
                      '" data-q="' +
                      qi +
                      '" data-o="other"><span class="o-txt">その他</span></label>' +
                      '<input class="other-input" type="text" data-other="' +
                      qi +
                      '" placeholder="サービス名を入力" hidden>'
                    : '';
                return '<div class="q-block">' + head + note + '<div class="opts">' + opts + other + '</div></div>';
            })
            .join('');
        dtWrap.innerHTML =
            img +
            '<p class="dt-title">' +
            s.title +
            '</p><div class="dt-meta"><span class="sv-pt">🎁' +
            s.pt +
            'pt</span><span>' +
            s.until +
            '</span><span>' +
            s.joined.toLocaleString() +
            '人回答</span></div>' +
            qsHtml;
        submitPt.textContent = String(s.pt);

        document.querySelectorAll('#dtWrap .pick').forEach(function (el) {
            el.addEventListener('change', function () {
                if (el.dataset.o === 'other') {
                    var inp = document.querySelector('.other-input[data-other="' + el.dataset.q + '"]');
                    if (inp) inp.hidden = !el.checked;
                }
                validate();
            });
        });
        document.querySelectorAll('#dtWrap .free-input').forEach(function (el) {
            el.addEventListener('input', function () {
                var c = document.querySelector('[data-cnt="' + el.dataset.q + '"]');
                if (c) c.textContent = String(el.value.length);
                validate();
            });
        });
        validate();
    }

    function validate() {
        var ok = cur.qs.every(function (q, qi) {
            if (!q.required) return true;
            if (q.type === 'free') {
                var el = document.querySelector('.free-input[data-q="' + qi + '"]');
                return el && el.value.trim().length > 0;
            }
            return document.querySelectorAll('#dtWrap .pick[data-q="' + qi + '"]:checked').length > 0;
        });
        submitBtn.classList.toggle('disabled', !ok);
    }

    function thanksVariant(s) {
        if (s.thanks.type === 'pr') return 'pr';
        if (s.thanks.type === 'conditional') {
            var sel = document.querySelector('#dtWrap .pick[data-q="' + s.thanks.byQ + '"]:checked');
            var isHigh = sel && s.thanks.highIdx.indexOf(+sel.dataset.o) !== -1;
            return isHigh ? 'high' : 'low';
        }
        return 'simple';
    }

    submitBtn.addEventListener('click', function () {
        if (submitBtn.classList.contains('disabled')) return;
        var variant = thanksVariant(cur);
        var bal = document.querySelector('.h-point .p-num');
        if (bal) {
            var n = parseInt(bal.textContent.replace(/,/g, ''), 10) || 0;
            bal.textContent = (n + cur.pt).toLocaleString();
        }
        location.href =
            '/enquete/enquete_thanks?id=' +
            encodeURIComponent(String(cur.id)) +
            '&variant=' +
            encodeURIComponent(variant);
    });

    renderDetail(cur);
})();
