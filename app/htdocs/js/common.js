/* =====================================================================
   共通UIインタラクション（ドロワー、タブ、モーダル、ボトムシート、更新制御等）
   js/common.js
   ===================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // ==========================================
  // 1. ハンバーガードロワー制御 (Drawer Menu)
  // ==========================================
  const DrawerModule = (() => {
    const init = () => {
      document.addEventListener('click', (e) => {
        // メニュー開くボタン
        const menuBtn = e.target.closest('.icon-menu');
        if (menuBtn) {
          e.preventDefault();
          toggleDrawer(true);
          return;
        }

        // メニュー閉じるボタンまたはオーバーレイ背景
        const closeBtn = e.target.closest('.drawer-close, .drawer-ov');
        if (closeBtn) {
          e.preventDefault();
          toggleDrawer(false);
        }
      });
    };

    const toggleDrawer = (open) => {
      const drawer = document.querySelector('.drawer') || document.querySelector('.drawer-ov');
      if (!drawer) return;

      if (open) {
        drawer.classList.add('show', 'open');
        document.body.style.overflow = 'hidden';
      } else {
        drawer.classList.remove('show', 'open');
        document.body.style.overflow = '';
      }
    };

    return { init, toggleDrawer };
  })();

  // ==========================================
  // 2. 汎用タブ切り替え制御 (Tab UI)
  // ==========================================
  const TabModule = (() => {
    const init = () => {
      document.addEventListener('click', (e) => {
        // タブ要素の判定 (.c-tabs a, .os-tabs a, .cat-chip, .tabs.venue a 等)
        const tabItem = e.target.closest('.c-tabs a, .os-tabs a, .cat-chip, .tabs.date a, .tabs.venue a, .tabs.race a');
        if (!tabItem) return;

        // href がアンカーや javascript: でない外部遷移用リンクの場合は除外
        const href = tabItem.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
          return;
        }

        e.preventDefault();

        // 親コンテナ内での表示切り替え
        const parent = tabItem.parentElement;
        if (parent) {
          const siblings = parent.querySelectorAll('a, button, .cat-chip');
          siblings.forEach(el => el.classList.remove('on', 'active'));
          tabItem.classList.add('on');
        }

        // 対象コンテンツパネルの連動制御 (data-target 属性が存在する場合)
        const targetId = tabItem.getAttribute('data-target') || (href && href.startsWith('#') ? href.substring(1) : null);
        if (targetId) {
          switchTabContent(targetId, tabItem);
        }
      });
    };

    const switchTabContent = (targetId, tabItem) => {
      // グループ単位で切り替える場合は data-tab-group を参照
      const groupName = tabItem.getAttribute('data-tab-group');
      let panels;

      if (groupName) {
        panels = document.querySelectorAll(`[data-tab-group-content="${groupName}"]`);
      } else {
        const targetEl = document.getElementById(targetId);
        if (targetEl && targetEl.parentElement) {
          panels = Array.from(targetEl.parentElement.children).filter(child => 
            child.id || child.hasAttribute('data-tab-content')
          );
        }
      }

      if (panels && panels.length > 0) {
        panels.forEach(panel => {
          if (panel.id === targetId || panel.getAttribute('data-tab-content') === targetId) {
            panel.removeAttribute('hidden');
            panel.classList.add('active');
          } else {
            panel.setAttribute('hidden', 'hidden');
            panel.classList.remove('active');
          }
        });
      }
    };

    return { init };
  })();

  // ==========================================
  // 3. ボトムシート & モーダル制御 (Modal & Sheet)
  // ==========================================
  const ModalSheetModule = (() => {
    const init = () => {
      document.addEventListener('click', (e) => {
        // トリガーボタン (data-sheet-target または .race-row 等)
        const trigger = e.target.closest('[data-sheet-target], .race-row');
        if (trigger) {
          // 的中実績ハーフモーダル等の開閉トリガー
          const targetId = trigger.getAttribute('data-sheet-target') || 'raceSheet';
          const sheetOverlay = document.getElementById(targetId) || document.querySelector('.sheet-ov');
          if (sheetOverlay) {
            e.preventDefault();
            openSheet(sheetOverlay);
            return;
          }
        }

        // 閉じるボタン (.sh-close, .modal-close) または 背景オーバーレイタップ
        const closeBtn = e.target.closest('.sh-close, .modal-close, .sheet-ov, .modal-ov');
        if (closeBtn) {
          // コンテンツ本体のタップ時は閉じないようにガード
          if ((closeBtn.classList.contains('sheet-ov') || closeBtn.classList.contains('modal-ov')) && 
              e.target !== closeBtn) {
            return;
          }
          e.preventDefault();
          const overlay = closeBtn.closest('.sheet-ov, .modal-ov');
          if (overlay) {
            closeSheet(overlay);
          }
        }
      });
    };

    const openSheet = (overlayEl) => {
      overlayEl.classList.add('show');
      overlayEl.removeAttribute('hidden');
      document.body.style.overflow = 'hidden';
    };

    const closeSheet = (overlayEl) => {
      overlayEl.classList.remove('show');
      document.body.style.overflow = '';
    };

    return { init, openSheet, closeSheet };
  })();

  // ==========================================
  // 4. リフレッシュアニメーション制御 (Refresh Button)
  // ==========================================
  const RefreshModule = (() => {
    const init = () => {
      document.addEventListener('click', (e) => {
        const rfBtn = e.target.closest('.rf-btn');
        if (!rfBtn) return;

        e.preventDefault();

        // すでに回転中の場合は連続タップをガード
        if (rfBtn.classList.contains('spin')) return;

        rfBtn.classList.add('spin');

        // アニメーション完了時間（0.6s）経過後に spin クラスを削除
        setTimeout(() => {
          rfBtn.classList.remove('spin');
        }, 600);
      });
    };

    return { init };
  })();

  // ==========================================
  // 5. アクション & インタラクション (Likes, etc.)
  // ==========================================
  const InteractionModule = (() => {
    const init = () => {
      document.addEventListener('click', (e) => {
        // いいねボタンのトグル切替 (.a-like)
        const likeBtn = e.target.closest('.a-like');
        if (likeBtn) {
          e.preventDefault();
          likeBtn.classList.toggle('on');

          // カウント数値の増減（表示用モック処理）
          const countEl = likeBtn.querySelector('.like-count');
          if (countEl) {
            let count = parseInt(countEl.textContent, 10) || 0;
            count = likeBtn.classList.contains('on') ? count + 1 : Math.max(0, count - 1);
            countEl.textContent = count;
          }
        }
      });
    };

    return { init };
  })();

  // モジュールの初期化実行
  DrawerModule.init();
  TabModule.init();
  ModalSheetModule.init();
  RefreshModule.init();
  InteractionModule.init();
});