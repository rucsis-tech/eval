(function () {
  'use strict';

  const ua = navigator.userAgent;
  const isIOS = /iPhone|iPad|iPod/.test(ua)
    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  const isAndroid = /Android/.test(ua);
  const os = isIOS ? 'ios' : isAndroid ? 'android' : 'pc';
  const LABEL = { ios: 'iPhone', android: 'Android', pc: 'パソコン' };

  function show(k) {
    document.querySelectorAll('#osTabs a').forEach((a) => {
      a.classList.toggle('on', a.dataset.os === k);
    });
    document.querySelectorAll('.os-pane').forEach((p) => {
      p.hidden = p.dataset.pane !== k;
    });
  }

  const detect = document.getElementById('detect');
  if (detect) {
    detect.innerHTML = 'お使いの端末：<b>' + LABEL[os] + '</b> と判定しました（タブで切り替えできます）';
  }

  document.querySelectorAll('#osTabs a').forEach((a) => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      show(a.dataset.os);
    });
  });

  show(os);
})();
