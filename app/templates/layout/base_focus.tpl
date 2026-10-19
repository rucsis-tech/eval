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

        {* メインコンテンツ *}
        {block name="content"}{/block}

        {* 共通フッター *}
        {block name="footer"}{/block}

    </div>
    <script src="/js/common.js"></script>
    {block name="scripts"}{/block}
</body>
</html>
