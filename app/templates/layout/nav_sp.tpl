{* ===================================================================
   templates/layout/nav_sp.tpl
   スマホ向けの固定下部ナビゲーション
   =================================================================== *}
<nav class="gnav">
  {* 1. TOP *}
  <a href="/" class="{if $active_nav == 'top'}active{/if}">
    <div class="ico">
      <svg viewBox="0 0 24 24">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
        <polyline points="9 22 9 12 15 12 15 22"></polyline>
      </svg>
    </div>
    <div class="lbl">TOP</div>
  </a>

  {* 2. 攻略ツール *}
  <a href="/tools/tools_top" class="{if $active_nav == 'tools'}active{/if}">
    <div class="ico">
      <svg viewBox="0 0 24 24">
        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
        <line x1="8" y1="21" x2="16" y2="21"></line>
        <line x1="12" y1="17" x2="12" y2="21"></line>
      </svg>
    </div>
    <div class="lbl">攻略ツール</div>
  </a>

  {* 3. シミュレーション（中央FABボタン） *}
  <a href="/simulation/sim_top" class="fab {if $active_nav == 'simulation'}active{/if}">
    <div class="fab-btn">
      <svg viewBox="0 0 24 24">
        <path d="M18 20V10M12 20V4M6 20v-6"></path>
      </svg>
    </div>
    <div class="fab-lbl">シミュレーション</div>
  </a>

  {* 4. 的中実績 *}
  <a href="/results/results_top" class="{if $active_nav == 'results'}active{/if}">
    <div class="ico">
      <svg viewBox="0 0 24 24">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>
    <div class="lbl">的中実績</div>
  </a>

  {* 5. マイページ *}
  <a href="/mypage/mypage" class="{if $active_nav == 'mypage'}active{/if}">
    <div class="ico">
      <svg viewBox="0 0 24 24">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
    </div>
    <div class="lbl">マイページ</div>
  </a>
</nav>