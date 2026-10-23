{* PC上部帯：ホーム画面追加・速報・アンケート（TOP モック .pc-top 準拠） *}
{if $show_pc_top|default:false}
<div class="pc-top">
    <div class="a2hs" id="a2hs" hidden role="link" tabindex="0">
        <span class="a-ico"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span>
        <span class="a-txt">
            <span class="a-t">ホーム画面に追加</span><br>
            <span class="a-d">確率テーブルへアプリのようにワンタップで。<b>追加のしかた →</b></span>
        </span>
        <span class="a-btn">追加する</span>
        <span class="a-x" id="a2hsClose" aria-label="閉じる">×</span>
    </div>

    <div class="flash">
        <span class="lbl">速報</span>
        <div class="ticker" id="ticker">
            <a class="show" href="/results/results_top">Eval、日・函館11R 3連複72.4倍<b>【8万6880円】</b>的中！</a>
            <a href="/results/results_top">【WIN5】Eval確率上位で構成、払戻<b>【42万7100円】</b>！</a>
            <a href="/topics/topics_top?tab=flash">好走確率50%超・Sランク馬、東京9Rで1着→単勝460円</a>
            <a href="/topics/topics_top?tab=flash">【New】本日の馬具情報を公開しました（ポイント）</a>
            <a href="/results/results_top">Eval、大井最終 馬単38.2倍<b>【3万8200円】</b>的中！</a>
        </div>
        <a class="more" href="#" id="flashMore">一覧 ▶</a>
    </div>

    <div class="surveybar">
        <span class="lbl">アンケート</span>
        <a class="q" href="/simulation/sim_win5">Q. 今週のWIN5、Eval確率上位で構成するなら何点まで買う？</a>
        <a class="ans-btn" href="/enquete/enquete_top">回答<small>🎁50pt</small></a>
        <a class="more" href="/enquete/enquete_top">一覧 ▶</a>
    </div>
</div>
{/if}
