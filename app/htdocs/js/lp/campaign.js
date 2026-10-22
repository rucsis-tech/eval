(function () {
  'use strict';

  /** モック: LP経由でプラン画面へ（将来は id に応じたコードをクエリ等で渡す） */
  const cta = document.getElementById('campaignCta');
  if (cta) {
    const params = new URLSearchParams(location.search);
    const id = (params.get('id') || 'evalw').toLowerCase();
    const codeById = { evalw: 'EVALW', 'cp-spat4': 'EVALW' };
    const code = codeById[id] || 'EVALW';
    cta.href = '/settle/settle_subsc?code=' + encodeURIComponent(code);
  }
})();
