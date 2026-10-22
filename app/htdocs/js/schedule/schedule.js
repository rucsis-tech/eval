/**
 * P18 開催日程（モック）
 */
(function () {
    'use strict';

    const DOW_CLS = { '土': 'sat', '日': 'sun' };
    const WEEK1 = [
        { d: 3, dow: '月', jra: [], loc: [{ n: '船橋', night: true }] },
        { d: 4, dow: '火', jra: [], loc: [{ n: '金沢' }, { n: '門別', night: true }] },
        { d: 5, dow: '水', jra: [], loc: [{ n: '川崎', night: true }] },
        { d: 6, dow: '木', jra: [], loc: [{ n: '門別', night: true }] },
        { d: 7, dow: '金', jra: [], loc: [{ n: '大井', night: true }] },
        { d: 8, dow: '土', jra: [{ n: '新潟' }, { n: '札幌' }], loc: [{ n: '佐賀' }], today: true },
        { d: 9, dow: '日', jra: [{ n: '新潟', g: '関屋記念' }, { n: '札幌' }], loc: [{ n: '高知' }], win5: true },
    ];
    const WEEK2 = [
        { d: 10, dow: '月', jra: [], loc: [{ n: '船橋', night: true }] },
        { d: 11, dow: '火', jra: [], loc: [{ n: '金沢' }, { n: '門別', night: true }] },
        { d: 12, dow: '水', jra: [], loc: [{ n: '大井', night: true }] },
        { d: 13, dow: '木', jra: [], loc: [{ n: '名古屋' }, { n: '門別', night: true }] },
        { d: 14, dow: '金', jra: [], loc: [{ n: '大井', night: true }] },
        { d: 15, dow: '土', jra: [{ n: '新潟' }, { n: '札幌' }], loc: [{ n: '佐賀' }] },
        { d: 16, dow: '日', jra: [{ n: '新潟' }, { n: '札幌', g: '札幌記念' }], loc: [{ n: '高知' }], win5: true },
    ];

    function courseHTML(c) {
        return '<span class="d-course' + (c.night ? ' night' : '') + '">' + c.n
            + (c.g ? '<span class="g">' + c.g + '</span>' : '') + '</span>';
    }

    function dayHTML(x) {
        const noRace = !x.jra.length && !x.loc.length;
        return '<li><a class="day-card' + (x.today ? ' today' : '') + (noRace ? ' off' : '') + '" href="/"'
            + ' title="タップで8/' + x.d + 'の確率テーブルへ（モックではTOPへ）">'
            + (x.today ? '<span class="today-mk">本日</span>' : '')
            + '<span class="d-date"><span class="d-num">' + x.d + '</span><br>'
            + '<span class="d-dow ' + (DOW_CLS[x.dow] || '') + '">' + x.dow + '</span></span>'
            + '<span class="d-body">'
            + (x.jra.length
                ? '<span class="d-row"><span class="d-tag jra">中央</span>' + x.jra.map(courseHTML).join('')
                + (x.win5 ? '<span class="win5-mk">WIN5</span>' : '') + '</span>'
                : '')
            + (x.loc.length
                ? '<span class="d-row"><span class="d-tag loc">地方</span>' + x.loc.map(courseHTML).join('') + '</span>'
                : '')
            + (noRace ? '<span class="d-none">開催なし</span>' : '')
            + '</span><span class="d-chev">›</span></a></li>';
    }

    document.getElementById('dayList').innerHTML = WEEK1.map(dayHTML).join('');
    document.getElementById('dayList2').innerHTML = WEEK2.map(dayHTML).join('');

    document.querySelector('.month-nav').addEventListener('click', (e) => {
        const t = e.target.closest('a');
        if (t) e.preventDefault();
    });
})();
