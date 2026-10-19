{* ===================================================================
   templates/layout/base.tpl
   共通の通常レイアウト
   =================================================================== *}
<!DOCTYPE html>
<html lang="ja">
<head>
{include file="layout/head.tpl"}
{block name="extra_css"}{/block}
</head>
<body>
    <div class="sp">

        {* 共通ヘッダー *}
        {include file="layout/header.tpl"}

        {if $show_campaign_modal|default:false}
        {include file="partials/modal.tpl"}
        {/if}

        {include file="layout/nav_pc.tpl"}
        {include file="layout/nav_sp.tpl"}

        {* メインコンテンツ *}
        {block name="content"}{/block}

        {* 共通フッター *}
        {block name="footer"}
        {include file="layout/footer.tpl"}
        {/block}

        {if $show_reg_banner|default:false}
        <div class="reg-banner" id="regBanner">
            <span class="rb-ico"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
            <div class="rb-text">
                <p class="rb-main">無料会員登録でもっと便利に</p>
                <p class="rb-sub">登録だけで<b>各種無料参考情報</b>が利用可能に</p>
            </div>
            <a class="rb-btn" href="/auth/entry">無料登録</a>
            <span class="close" id="regClose" aria-label="閉じる">×</span>
        </div>
        {/if}

        {include file="layout/drawer.tpl"}
        {include file="partials/app_back.tpl"}

    </div>
    <script src="/js/common.js"></script>
    {block name="scripts"}{/block}
</body>
</html>
