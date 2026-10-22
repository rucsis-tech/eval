/**
 * P9 アンケート共通モックデータ
 */
(function (global) {
    'use strict';

    var SURVEYS = [
        {
            id: 1,
            status: 'open',
            img: { ratio: '16:9', cls: 'im-a', label: 'WIN5' },
            title: '今週のWIN5、Eval確率上位で構成するなら何点まで買う？',
            pt: 50,
            until: '7/26(日) 14:50まで',
            joined: 1834,
            answered: false,
            thanks: { type: 'pr' },
            qs: [
                {
                    type: 'single',
                    text: '購入予定の点数を教えてください',
                    required: true,
                    opts: ['1点（完全セレクト）', '2〜10点', '11〜20点', '21〜50点', '51点以上', 'WIN5は買わない'],
                },
            ],
        },
        {
            id: 2,
            status: 'open',
            img: null,
            title: 'Eval MODE 満足度調査（ご意見をお聞かせください）',
            pt: 100,
            until: '7/31(金) 23:59まで',
            joined: 412,
            answered: false,
            thanks: { type: 'conditional', byQ: 0, highIdx: [0, 1] },
            qs: [
                {
                    type: 'single',
                    text: 'Eval MODEの総合満足度を教えてください',
                    required: true,
                    opts: ['5：とても満足', '4：満足', '3：ふつう', '2：やや不満', '1：不満'],
                },
                {
                    type: 'free',
                    text: '改善してほしい点・ご要望があれば教えてください（任意）',
                    required: false,
                },
            ],
        },
        {
            id: 3,
            status: 'open',
            img: { ratio: '1:1', cls: 'im-b', label: '投票' },
            title: '普段使っている投票サービスは？（複数選択可）',
            pt: 30,
            until: '7/27(月) 18:00まで',
            joined: 986,
            answered: true,
            thanks: { type: 'simple' },
            qs: [
                {
                    type: 'multi',
                    text: '利用中のサービスをすべて選択してください',
                    required: true,
                    other: true,
                    opts: ['即PAT', 'SPAT4', 'オッズパーク', '楽天競馬', 'netkeiba（投票連携）', 'WINTICKET', '現地・ウインズで購入'],
                },
            ],
        },
        {
            id: 4,
            status: 'closed',
            img: { ratio: '16:9', cls: 'im-c', label: '宝塚記念' },
            title: '【結果発表】宝塚記念、勝つのはどの馬だと思う？',
            pt: 50,
            until: '6/27(土) 14:50 終了',
            joined: 5210,
            result: true,
        },
        {
            id: 5,
            status: 'closed',
            img: null,
            title: '夏競馬でいちばん楽しみな重賞は？',
            pt: 30,
            until: '7/5(日) 23:59 終了',
            joined: 1420,
            result: true,
        },
    ];

    function getSurvey(id) {
        var n = parseInt(String(id), 10);
        return SURVEYS.find(function (s) {
            return s.id === n;
        });
    }

    global.EvmEnquete = {
        SURVEYS: SURVEYS,
        getSurvey: getSurvey,
    };
})(typeof window !== 'undefined' ? window : this);
