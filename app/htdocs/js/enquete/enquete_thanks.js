/**
 * P9 アンケート回答後
 */
(function () {
    'use strict';

    var data = window.EvmEnquete;
    var wrap = document.querySelector('.pc-wrap[data-survey-id]');
    if (!wrap) return;

    var survey = data ? data.getSurvey(wrap.getAttribute('data-survey-id')) : null;
    var variant = wrap.getAttribute('data-variant') || 'simple';
    var pt = survey ? survey.pt : 0;

    var tkPt = document.getElementById('tkPt');
    var tkSub = document.getElementById('tkSub');
    var prSlot = document.getElementById('prSlot');
    var leadHigh = document.getElementById('leadHigh');
    var leadLow = document.getElementById('leadLow');
    var note = document.getElementById('tkMockNote');

    if (tkPt) tkPt.textContent = String(pt);
    if (prSlot) prSlot.hidden = true;
    if (leadHigh) leadHigh.hidden = true;
    if (leadLow) leadLow.hidden = true;

    if (variant === 'pr') {
        if (tkSub) tkSub.textContent = 'ポイントを付与しました。あわせてこちらもチェック！';
        if (prSlot) prSlot.hidden = false;
        if (note) {
            note.textContent =
                '※モック注記：パターン②＝サンクス＋PR枠。枠は高さ可変で、バナー・カード・動画など任意のPRクリエイティブを差し替え可能。';
        }
    } else if (variant === 'high') {
        if (tkSub) tkSub.textContent = 'ポイントを付与しました。';
        if (leadHigh) leadHigh.hidden = false;
        if (note) {
            note.textContent =
                '※モック注記：パターン③＝サンクス＋誘導動線。Q1の回答（5・4／3以下）で表示を出し分け。高評価者のみストアレビューへ誘導し、低評価者は改善ヒアリングに接続（レビュー低評価の抑制）。PR案件にも同枠を利用可。';
        }
    } else if (variant === 'low') {
        if (tkSub) tkSub.textContent = 'ポイントを付与しました。';
        if (leadLow) leadLow.hidden = false;
        if (note) {
            note.textContent =
                '※モック注記：パターン③＝サンクス＋誘導動線。Q1の回答（5・4／3以下）で表示を出し分け。高評価者のみストアレビューへ誘導し、低評価者は改善ヒアリングに接続（レビュー低評価の抑制）。PR案件にも同枠を利用可。';
        }
    } else {
        if (tkSub) tkSub.textContent = 'ポイントを付与しました。引き続きEval MODEをお楽しみください。';
        if (note) note.textContent = '※モック注記：パターン①＝サンクスのみ。';
    }
})();
