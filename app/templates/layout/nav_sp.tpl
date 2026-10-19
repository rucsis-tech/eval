{* 下部固定ナビ5項目（モック .gnav 準拠） *}
{assign var=gnav value=$gnav_active|default:''}
<nav class="gnav">
    <a href="/results/results_top" class="hit{if $gnav == 'hit'} active{/if}" aria-label="的中実績（本日{$today_hit_count|default:0}レース的中）">
        <span class="ico"><i class="hit-badge" aria-hidden="true">{$today_hit_count|default:0}</i><svg viewBox="0 0 24 24"><circle cx="11" cy="13" r="8"/><circle cx="11" cy="13" r="3.2"/><path d="M11 13L21 3"/><path d="M21 3h-5"/><path d="M21 3v5"/></svg></span>
        <span class="lbl">的中実績</span>
    </a>
    <a href="/ranking/eval_ranking" class="{if $gnav == 'ranking'}active{/if}">
        <span class="ico"><svg viewBox="0 0 24 24"><rect x="3" y="10" width="5.2" height="10" rx="1" fill="currentColor" stroke="none"/><rect x="9.4" y="4" width="5.2" height="16" rx="1" fill="currentColor" stroke="none"/><rect x="15.8" y="13" width="5.2" height="7" rx="1" fill="currentColor" stroke="none"/></svg></span>
        <span class="lbl">本日の好走<br>確率ランキング</span>
    </a>
    <a class="fab{if $gnav == 'sim'} active{/if}" href="/simulation/sim_top">
        <span class="fab-btn"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg></span>
        <span class="fab-lbl">シミュレーション</span>
    </a>
    <a href="/tools/tools_top" class="{if $gnav == 'tools'}active{/if}">
        <span class="ico"><svg viewBox="0 0 24 24"><path d="M12 3l1.9 5.6L20 9l-4.5 3.9L16.8 19 12 15.7 7.2 19l1.3-6.1L4 9l6.1-.4L12 3z"/></svg></span>
        <span class="lbl">おすすめ<br>攻略ツール</span>
    </a>
    <a href="/settle/settle_point" class="{if $gnav == 'point'}active{/if}">
        <span class="ico"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.5 16V8h3a2.5 2.5 0 0 1 0 5h-3"/></svg></span>
        <span class="lbl">ポイント購入</span>
    </a>
</nav>
