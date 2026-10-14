{* ===================================================================
   templates/layout/base_focus.tpl
   共通のフォーカスレイアウト（LP・登録・静的ページ等で使用）
   =================================================================== *}
<!DOCTYPE html>
<html lang="ja">
<head>
{include file="layout/head.tpl"}
{block name="styles"}{/block}
<style>
header { justify-content:left; gap: 10px;}
.logo .mark { font-size: 18px;}

</style>
</head>
<body>
    <div class="sp">

        {* 共通ヘッダー *}
        {include file="layout/header.tpl" header_type='focus'}

        <div class="pc-wrap pc-static w-narrow">

            {* メインコンテンツ挿入領域 *}
            <main>
                {block name="content"}{/block}
            </main>

            {* 共通フッター *}
            {include file="layout/footer.tpl" footer_type='focus'}

        </div><!-- /pc-wrap -->
    </div>

    {* 共通JSは body の閉じタグ直前に配置 *}
    <script src="/js/common.js"></script>

    {* 画面固有スクリプト挿入領域 *}
    {block name="scripts"}{/block}
</body>
</html>