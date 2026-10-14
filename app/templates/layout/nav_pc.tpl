{* ===================================================================
   templates/layout/nav_pc.tpl
   PC向けの主要ナビゲーション（1024px以上）
   =================================================================== *}
<nav class="gnav pc-nav">
  {* TOP *}
  <a href="/" class="{if $page_id == 'P1'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
    </span>
    <span class="lbl">TOP</span>
  </a>

  {* 本日の好走確率ランキング *}
  <a href="/ranking/eval_ranking" class="{if $page_id == 'P6'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"></path></svg>
    </span>
    <span class="lbl">好走確率</span>
  </a>

  {* 的中実績（注目演出・的中数バッジ付き） *}
  <a href="/results/results_top" class="hit {if $page_id == 'P5'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
      {if isset($today_hit_count) && $today_hit_count > 0}
        <i class="hit-badge{if $hit_badge_pop} pop{/if}">{$today_hit_count}</i>
      {/if}
    </span>
    <span class="lbl">的中実績</span>
  </a>

  {* 開催日程 *}
  <a href="/schedule/schedule" class="{if $page_id == 'P18'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
    </span>
    <span class="lbl">開催日程</span>
  </a>

  {* おすすめ攻略ツール *}

  <a href="/tools/tools_top" class="{if $page_id == 'P2' || $page_id == 'P3'}active{/if}">
|---|<span class="ico">
      <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
    </span>
    <span class="lbl">攻略ツール</span>
  </a>

  {* トピックス / 速報 / お知らせ *}
  <a href="/topics/topics_top" class="{if $page_id == 'P14' || $page_id == 'P15' || $page_id == 'P16' || $page_id == 'P17' || $page_id == 'P17a'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l5 5v9a2 2 0 0 1-2 2z"></path><polyline points="14 4 14 9 19 9"></polyline></svg>
    </span>
    <span class="lbl">トピックス</span>
  </a>

  {* アンケート *}
  <a href="/enquete/enquete_top" class="{if $page_id == 'P9'}active{/if}">
    <span class="ico">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 19 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
    </span>
    <span class="lbl">アンケート</span>
  </a>

  {* 右端固定FABボタン：的中確率シミュレーション *}
  <a href="/simulation/sim_top" class="fab {if $page_id == 'P4' || $page_id == 'P4a'}active{/if}">
    <span class="fab-btn">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
    </span>
    <span class="fab-lbl">シミュレーション</span>
  </a>
</nav>