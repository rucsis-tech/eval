{* ===================================================================
   templates/layout/base_focus.tpl
   共通のフォーカスレイアウト（LP・登録・静的ページ等で使用）
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
        {block name="header"}
        {include file="layout/header_focus.tpl"}
        {/block}

        {* 静的ページ用の div *}
        {if $static_page_layout|default:false}
        <div class="pc-wrap pc-static w-narrow">
        {/if}

        {* メインコンテンツ *}
        {block name="content"}{/block}

        {* 静的ページ用の div 閉じ *}
        {if $static_page_layout|default:false}
            {if $show_footer|default:false}
            {include file="layout/footer.tpl" footer_variant='static'}
            {/if}
        </div>
        {else}
            {block name="footer"}
            {if $show_footer|default:false}
            {include file="layout/footer.tpl" footer_variant='static'}
            {/if}
            {/block}
        {/if}

    </div>
    <script src="/js/common.js"></script>
    {block name="scripts"}{/block}
</body>
</html>
