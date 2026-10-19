{* フォーカスレイアウト用ヘッダー
    $focus_header_type … auth（P11/P12）| exit（P13/P19・デフォルト）| static（S2 ほか法務）
    $close_url … 指定時はリンク遷移。未指定時は common.js で履歴戻り／TOP
*}
{assign var=fht value=$focus_header_type|default:'exit'}
<header>
    {if $fht == 'static'}
        <span class="h-back" id="focusBackBtn" role="button" tabindex="0" aria-label="戻る">‹</span>
    {/if}
    <a class="logo" href="/">
        <span class="mark">Eval<span> MODE</span></span>
    </a>
    {if $fht == 'auth'}
        {if $close_url|default:''}
            <a class="h-close" href="{$close_url|escape}">× 閉じる</a>
        {else}
            <a class="h-close" href="#" id="focusCloseBtn">× 閉じる</a>
        {/if}
    {elseif $fht == 'exit'}
        {if $close_url|default:''}
            <a class="h-close" href="{$close_url|escape}" title="元のページに戻る">✕</a>
        {else}
            <span class="h-close" id="focusCloseBtn" role="button" tabindex="0" title="元のページに戻る">✕</span>
        {/if}
    {/if}
</header>
