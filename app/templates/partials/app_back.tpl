{* ===================================================================
   templates/partials/app_back.tpl
   アプリ起動時（WebView等）の「戻る」ボタン共通パーツ
   =================================================================== *}

{if $is_app|default:false}
  <button type="button" 
          class="app-back-btn{if isset($extra_class)} {$extra_class|escape}{/if}" 
          onclick="{if isset($on_back_click)}{$on_back_click|escape}{else}if (window.history.length > 1) { window.history.back(); } else { location.href='{$back_url|default:'/'|escape:'javascript'}'; }{/if}"
          aria-label="前の画面へ戻る">
    <svg class="app-back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
  </button>
{/if}