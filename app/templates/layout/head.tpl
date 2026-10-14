{* ===================================================================
   templates/layout/head.tpl
   共通ヘッドテンプレート
   =================================================================== *}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eval｜{$title}</title>

    <!-- 共通スタイルの読み込み -->
    <link rel="stylesheet" href="/css/common.css">

    <!-- ページ固有CSSの読み込み領域（変数指定またはブロック上書き対応） -->
    {block name="extra_css"}
        {if !empty($extra_css)}
            {if is_array($extra_css)}
                {foreach $extra_css as $css_file}
                    <link rel="stylesheet" href="{$css_file}">
                {/foreach}
            {else}
                <link rel="stylesheet" href="{$extra_css}">
            {/if}
        {/if}
    {/block}
</head>