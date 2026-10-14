{* ===================================================================
   templates/layout/footer.tpl
   共通フッターコンポーネント (Eval MODE)
   =================================================================== *}
{literal}
<style>
/* ===== フッター（静的ページ相互リンク） ===== */
.foot-links{
margin-top:20px;border-top:1px solid var(--line);background:var(--bg-card);
padding:12px 14px;display:flex;flex-wrap:wrap;gap:8px 16px;
}
.foot-links a{font-size:10px;color:var(--text-sub);text-decoration:none;font-weight:700;}
.foot-copy{font-size:9px;color:#4d5666;text-align:center;padding:12px 0 4px;background:var(--bg-card);}

/* ===== フッター（通常ページ相互リンク） ===== */
footer{background:#06070a;color:var(--text-sub);padding:20px 16px 26px;border-top:1px solid var(--line);}
footer .f-logo{font-weight:900;font-size:18px;color:#fff;}
footer .f-logo span{color:var(--red);}
footer nav{display:flex;flex-wrap:wrap;gap:10px 16px;margin-top:14px;font-size:11px;}
footer nav a{color:var(--text-sub);text-decoration:none;}
footer .copy{margin-top:18px;font-size:10px;text-align:center;color:#5a6373;}

/* フッター・追従バナー（下部ナビと重ならないよう右下・ナビより上） */
footer{margin-top:40px;padding-left:max(24px, calc((100% - 1240px) / 2 + 24px));padding-right:max(24px, calc((100% - 1240px) / 2 + 24px));}
</style>
{/literal}


{if $footer_type == 'focus'}
    <div class="foot-links">
        <a href="/pages/rules">利用規約</a>
        <a href="/pages/privacy">プライバシーポリシー</a>
        <a href="/pages/payment_rules">資金決済法に基づく表示</a>
        <a href="/pages/commercial_law">特定商取引法に基づく表記</a>
        <a href="/pages/company">運営会社</a>
        <a href="/inquiry/form">お問い合わせ</a>
        <a href="/pages/requirements">推奨環境</a>
    </div>
    <p class="foot-copy">© 2026 Eval MODE</p>
{else}
    <footer>
        <p class="f-logo">Eval<span> MODE</span></p>
        <nav>
            <a href="/pages/company">運営会社</a>
            <a href="/topics/topics_top">お知らせ</a>
            <a href="/pages/faq">よくある質問・料金プラン</a>
            <a href="/pages/privacy">プライバシーポリシー</a>
            <a href="/pages/rules">利用規約</a>
            <a href="/pages/commercial_law">特定商取引法に基づく表記</a>
            <a href="/inquiry/form">お問い合わせ</a>
            <a href="/pages/sitemap">サイトマップ</a>
        </nav>
        <p class="copy">© 2026 Eval MODE</p>
    </footer>
{/if}

