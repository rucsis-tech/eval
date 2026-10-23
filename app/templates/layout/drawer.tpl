{* ハンバーガードロワー（TOP v3.2 モック準拠・全ページ共通） *}
<div class="drawer-overlay hidden" id="drawerOverlay">
    <aside class="drawer">
        <div class="d-head">
            <span class="d-title">メニュー</span>
            <button type="button" class="d-close" id="drawerClose">×</button>
        </div>
        <nav class="d-sec">
            <p class="d-sec-t">メイン</p>
            <a class="d-item" href="/">TOP（確率テーブル）<span class="chev">›</span></a>
            <a class="d-item" href="/schedule/schedule">開催日程<span class="chev">›</span></a>
            <a class="d-item" href="/pages/guide">🔰 初めての方ガイド<span class="chev">›</span></a>
        </nav>
        <nav class="d-sec">
            <p class="d-sec-t">機能</p>
            <a class="d-item" href="/simulation/sim_top">的中確率シミュレーション<span class="chev">›</span></a>
            <a class="d-item" href="/ranking/eval_ranking">本日の好走確率ランキング<span class="chev">›</span></a>
            <a class="d-item" href="/results/results_top">的中実績<span class="chev">›</span></a>
            <a class="d-item" href="/tools/tools_top">おすすめ攻略ツール<span class="chev">›</span></a>
            <a class="d-item" href="/settle/settle_point">ポイント購入<span class="chev">›</span></a>
        </nav>
        <nav class="d-sec">
            <p class="d-sec-t">読みもの・参加</p>
            <a class="d-item" href="/topics/topics_top">トピックス一覧<span class="chev">›</span></a>
            <a class="d-item" href="/topics/topics_top?tab=flash">速報一覧<span class="chev">›</span></a>
            <a class="d-item" href="/topics/topics_top?tab=info">お知らせ<span class="chev">›</span></a>
            <a class="d-item" href="/pages/ambassadors">アンバサダー<span class="chev">›</span></a>
            <a class="d-item" href="/enquete/enquete_top">アンケート<span class="em">🎁ptがもらえる</span><span class="chev">›</span></a>
        </nav>
        <nav class="d-sec" id="d-acc-guest"{if $member_type|default:'guest' != 'guest'} hidden{/if}>
            <p class="d-sec-t">アカウント</p>
            <a class="d-btn primary" href="/auth/entry">無料会員登録（30秒で完了） ▶</a>
            <a class="d-btn ghost" href="/auth/login">ログイン</a>
        </nav>
        <nav class="d-sec" id="d-acc-free"{if $member_type|default:'guest' != 'free'} hidden{/if}>
            <p class="d-sec-t">アカウント</p>
            <a class="d-item" href="/mypage/mypage">マイページ<span class="chev">›</span></a>
            <a class="d-item" href="/mypage/mypage">購入履歴<span class="chev">›</span></a>
            <a class="d-btn primary" href="/settle/settle_subsc">サブスクに申し込む（初月無料） ▶</a>
            <a class="d-item d-logout" href="/">ログアウト</a>
        </nav>
        <nav class="d-sec" id="d-acc-subsc"{if $member_type|default:'guest' != 'subsc'} hidden{/if}>
            <p class="d-sec-t">アカウント</p>
            <a class="d-item" href="/mypage/mypage">マイページ<span class="chev">›</span></a>
            <a class="d-item" href="/mypage/mypage">購入履歴<span class="chev">›</span></a>
            <a class="d-item" href="/mypage/mypage">サブスクのプラン確認・変更<span class="chev">›</span></a>
            <a class="d-item d-logout" href="/">ログアウト</a>
        </nav>
        <details class="d-fold">
            <summary>サポート・規約</summary>
            <a class="d-item sub" href="/pages/faq">よくある質問・料金プラン</a>
            <a class="d-item sub" href="/inquiry/form">お問い合わせ</a>
            <a class="d-item sub" href="/pages/company">運営会社</a>
            <a class="d-item sub" href="/pages/rules">利用規約</a>
            <a class="d-item sub" href="/pages/privacy">プライバシーポリシー</a>
            <a class="d-item sub" href="/pages/commercial_law">特定商取引法に基づく表記</a>
        </details>
        <a class="d-item d-x" href="https://x.com/master_eval" target="_blank" rel="noopener">𝕏 公式X（@master_eval）↗</a>
    </aside>
</div>
