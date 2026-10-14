{* ===================================================================
   templates/layout/base.tpl
   共通の通常レイアウト
   =================================================================== *}
<!DOCTYPE html>
<html lang="ja">
<head>
{include file="layout/head.tpl"}
{block name="styles"}{/block}
</head>
<body>
  <div class="sp">
    {* 共通ヘッダー *}
    {include file="layout/header.tpl"}

    {* メインコンテンツ挿入領域 *}
    <main class="main-body">
      {block name="content"}{/block}
    </main>

    {* 共通フッター *}
    {include file="layout/footer.tpl"}

    {* SP向け下部固定ナビゲーション *}
    {include file="layout/nav_sp.tpl"}
  </div>

  {* ハンバーガードロワーメニュー *}
  {* {include file="layout/drawer.tpl"} *}

  {* 共通JSは body の閉じタグ直前に配置 *}
  <script src="/js/common.js"></script>

  {* 画面固有スクリプト挿入領域 *}
  {block name="scripts"}{/block}
</body>
</html>