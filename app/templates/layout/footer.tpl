{* 共通フッター
   $footer_variant … site（TOP 等）| static（静的ページ）
*}
{assign var=fv value=$footer_variant|default:'site'}
{if $fv == 'static'}
<footer class="footer-static" aria-label="フッター">
    <div class="foot-links">
        {include file="partials/footer_nav.tpl"}
    </div>
    <p class="foot-copy">© 2026 Eval MODE</p>
</footer>
{else}
<footer class="footer-site">
    <p class="f-logo">Eval<span> MODE</span></p>
    <nav>
        {include file="partials/footer_nav.tpl"}
    </nav>
    <p class="copy">© 2026 Eval MODE</p>
</footer>
{/if}
