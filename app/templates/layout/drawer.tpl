{* ===================================================================
   templates/layout/drawer.tpl
   共通のハンバーガーメニュー展開用ドロワーナビゲーション
   =================================================================== *}
<!-- ドロワー背景オーバーレイ -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()" aria-hidden="true"></div>

<!-- ドロワーメニュー本体 -->
<aside class="drawer-menu" id="drawerMenu" role="dialog" aria-modal="true" aria-label="メインナビゲーション">
    <!-- ドロワーヘッダー -->
    <div class="drawer-head">
        <a href="/" class="logo">
            <span class="mark">Eval <span>MODE</span></span>
        </a>
        <button type="button" class="drawer-close" id="drawerClose" onclick="closeDrawer()" aria-label="メニューを閉じる">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- ユーザー状態・アカウントサマリー -->
    <div class="drawer-user-box">
        {if $is_logged_in|default:false}
            <div class="user-info">
                <div class="user-avatar">
                    <span>{$user_name|default:'M'|truncate:1:''}</span>
                <div class="user-detail">
                    <span class="user-name">{$user_name|default:'会員'} 様</span>
                    <span class="user-badge {if $is_subscriber|default:false}sub{else}free{/if}">
                        {if $is_subscriber|default:false}プレミアムプラン{else}無料会員{/if}
                    </span>
                </div>
            </div>
            <div class="point-summary">
                <div class="p-label">保有ポイント</div>
                <div class="p-value">
                    <span class="p-coin">P</span>
                    <span class="p-num">{$user_points|default:0|number_format}</span>
                    <span class="p-unit">pt</span>
                </div>
                <a href="/settle/settle_point" class="p-buy-btn">チャージ</a>
            </div>
        {else}
            <div class="auth-prompt">
                <p class="prompt-txt">好走確率ランキングやシミュレーションをご利用いただけます。</p>
                <div class="auth-btns">
                    <a href="/auth/entry" class="btn-entry">無料会員登録</a>
                    <a href="/auth/login" class="btn-login">ログイン</a>
                </div>
            </div>
        {/if}
    </div>

    <!-- メインナビゲーション -->
    <nav class="drawer-nav">
        <!-- 主要コンテンツ -->
        <div class="nav-group">
            <div class="group-title">競馬情報</div>
            <ul class="nav-list">
                <li>
                    <a href="/" class="nav-item">
                        <span class="nav-label">TOP（確率テーブル）</span>
                    </a>
                </li>
                <li>
                    <a href="/ranking/eval_ranking" class="nav-item">
                        <span class="nav-label">本日の好走確率ランキング</span>
                    </a>
                </li>
                <li>
                    <a href="/simulation/sim_top" class="nav-item">
                        <span class="nav-label">的中確率シミュレーション</span>
                    </a>
                </li>
                <li>
                    <a href="/simulation/sim_win5" class="nav-item">
                        <span class="nav-label">WIN5的中確率算出</span>
                    </a>
                </li>
                <li>
                    <a href="/results/results_top" class="nav-item">
                        <span class="nav-label">的中実績</span>
                    </a>
                </li>
                <li>
                    <a href="/tools/tools_top" class="nav-item">
                        <span class="nav-label">おすすめ攻略ツール</span>
                    </a>
                </li>
                <li>
                    <a href="/schedule/schedule" class="nav-item">
                        <span class="nav-label">開催日程</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 会員・課金機能 -->
        <div class="nav-group">
            <div class="group-title">会員サービス</div>
            <ul class="nav-list">
                <li>
                    <a href="/mypage/mypage" class="nav-item">
                        <span class="nav-label">マイページ</span>
                    </a>
                </li>
                <li>
                    <a href="/settle/settle_point" class="nav-item">
                        <span class="nav-label">ポイント購入</span>
                    </a>
                </li>
                <li>
                    <a href="/settle/settle_subsc" class="nav-item">
                        <span class="nav-label">サブスクリプション申込</span>
                    </a>
                </li>
                <li>
                    <a href="/enquete/enquete_top" class="nav-item">
                        <span class="nav-label">アンケート</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 案内・ガイド -->
        <div class="nav-group">
            <div class="group-title">ガイド・コンテンツ</div>
            <ul class="nav-list">
                <li>
                    <a href="/pages/guide" class="nav-item">
                        <span class="nav-label">初めての方ガイド</span>
                    </a>
                </li>
                <li>
                    <a href="/topics/topics_top" class="nav-item">
                        <span class="nav-label">トピックス・速報・お知らせ</span>
                    </a>
                </li>
                <li>
                    <a href="/pages/home_guide" class="nav-item">
                        <span class="nav-label">ホーム画面追加ガイド</span>
                    </a>
                </li>
                <li>
                    <a href="/pages/ambassadors" class="nav-item">
                        <span class="nav-label">アンバサダー紹介</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 法務・運営情報 (S1〜S7) -->
        <div class="nav-group legal-group">
            <div class="group-title">インフォメーション</div>
            <ul class="nav-list legal-list">
                <li><a href="/pages/company">運営会社</a></li>
                <li><a href="/pages/rules">利用規約</a></li>
                <li><a href="/pages/privacy">プライバシーポリシー</a></li>
                <li><a href="/pages/commercial_law">特定商取引法に基づく表記</a></li>
                <li><a href="/pages/faq">よくある質問・料金プラン</a></li>
                <li><a href="/inquiry/form">お問い合わせ</a></li>
                <li><a href="/pages/sitemap">サイトマップ</a></li>
            </ul>
        </div>
    </nav>

    <!-- フッター・ログアウトボタン（ログイン時） -->
    {if $is_logged_in|default:false}
        <div class="drawer-foot">
            <a href="/auth/logout" class="btn-logout">ログアウト</a>
        </div>
    {/if}
</aside>