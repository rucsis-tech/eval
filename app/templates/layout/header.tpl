{* ===================================================================
   templates/layout/header.tpl
   共通ヘッダーコンポーネント (Eval MODE)
   =================================================================== *}
{if $header_type == 'focus'}
    <header>
        <span class="h-back">‹</span>
        <a class="logo" href="/"><span class="mark">Eval<span> MODE</span></span></a>
    </header>
{else}
    <header class="site-header">
    {* サイトロゴ *}
    <a href="/" class="logo">
        <span class="mark">Eval <span>MODE</span></span>
    </a>

    {* 右側アクションアイコン群 *}
    <div class="h-icons">
        {* ポイント残高表示 *}
        <a href="/settle/settle_point" class="h-point" aria-label="ポイント購入・残高確認">
        <span class="p-coin">P</span>
        <span class="p-num">{$user.point|default:0|number_format}</span>
        </a>

        {* マイページ / ログインアイコン *}
        <a href="/mypage/mypage" class="icon-mypage{if $is_login|default:false} is-active{/if}">
        <div class="icon-guest">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <span class="mp-lbl">{if $is_login|default:false}マイページ{else}ログイン{/if}</span>
        </a>

        {* ハンバーガーメニュー開閉ボタン *}
        <button type="button" class="icon-menu" id="js-drawer-trigger" aria-label="メニューを開く" aria-controls="js-drawer-menu" aria-expanded="false">
        <i></i>
        <i></i>
        <i></i>
        </button>
    </div>
    </header>
{/if}
