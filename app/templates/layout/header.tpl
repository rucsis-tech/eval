{* 共通ヘッダー（P1 モック準拠） *}
<header>
    <a class="logo" href="/">
        <span class="mark">Eval<span> MODE</span></span>
    </a>
    <div class="h-icons">
        <a class="h-point" href="/settle/settle_point">
            <span class="p-coin">P</span>
            <span class="p-num">{$user_point|default:0|number_format}</span><span class="p-unit">pt</span>
        </a>
        <a class="icon-mypage{if $mypage_active|default:false} is-active{/if}" href="{if $is_login|default:false}/mypage/mypage{else}/auth/login{/if}">
            <span class="icon-guest">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="mp-lbl">{if $is_login|default:false}マイページ{else}ログイン{/if}</span>
        </a>
        <span class="icon-menu" id="menuBtn" role="button" tabindex="0" aria-label="メニュー"><i></i><i></i><i></i></span>
    </div>
</header>
