{* P1 TOP（確率テーブル） *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/top/top.css">
{/block}

{block name="content"}
        <div class="pc-body">
        <!-- ===== 確率テーブル（TOPの主役／レイヤー1） ===== -->
        <section class="pc-main">
            <div class="sec-title">
                <h2>Probability</h2><span class="en">全出走確率データ</span>
                <a class="guide" href="/pages/guide"><span class="g-mark">🔰</span>初めての方</a>
                <a class="right" href="/schedule/schedule">開催日程 ▶</a>
            </div>
            <div class="prob">
                <div class="prob-nav"><!-- PC：左のレース選択列（追従） -->
                <!-- 日付タブ -->
                <div class="tabs date">
                    <a href="#">7/12(日)</a><a class="on" href="#">7/14(火)</a><a href="#">7/15(水)</a>
                </div>
                <!-- 開催場タブ -->
                <div class="tabs venue">
                    <a class="on" href="#">東京</a><a href="#">京都</a><a href="#">福島</a>
                    <a class="local" href="#">門別</a><a class="local" href="#">高知</a><a class="local" href="#">大井</a>
                    <a class="local" href="#">川崎</a><a class="local" href="#">名古屋</a><a class="local" href="#">佐賀</a>
                </div>
                <!-- レース番号 -->
                <div class="tabs race">
                    <a class="done" href="#">1R</a><a class="done" href="#">2R</a><a class="on" href="#">3R</a><a href="#">4R</a>
                    <a href="#">5R</a><a href="#">6R</a><a href="#">7R</a><a href="#">8R</a>
                    <a href="#">9R</a><a href="#">10R</a><a href="#">11R</a><a href="#">12R</a>
                </div>
                <!-- 発走時間の近いレース（スリムバー） -->
                <div class="nextbar">
                    <span class="lbl">発走間近</span>
                    <div class="nb-list">
                        <a class="soon" href="#"><b>東京3R</b><span class="t">11:10</span></a><a href="#"><b>門別2R</b><span class="t">11:20</span></a><a href="#"><b>京都3R</b><span class="t">11:25</span></a>
                    </div>
                </div>

                </div><!-- /prob-nav -->
                <div class="prob-body">
                <!-- 表示切替（1行：出馬表／結果・払戻し＋モードボタン） -->
                <div class="view-switch">
                    <a class="on" href="#" id="tab-shutsuba">出馬表</a>
                    <a class="disabled" href="#" id="tab-result">結果・払戻し<span class="t-note" id="resultNote">レース後に反映</span></a>
                    <span class="mode-btns" id="subTabs">
                        <a class="btn-mode on" href="#" id="tab-win">勝利確率</a><a class="btn-mode" href="#" id="tab-fuku">複勝確率</a>
                    </span>
                </div>

                <div class="race-info">
                    <b>東京3R</b> <b>3歳未勝利</b> ダ1400m <span class="going">重</span>
                    <span class="time">発走 11:10</span>
                </div>
                <div class="race-info status-row" style="padding-top:0;">
                    <span class="status-badge final" id="statusBadge">確率 最終</span>
                    <span class="status-note" id="statusNote">10:40確定</span>
                    <span class="status-badge live" id="evBadge"><span class="live-dot"></span>期待値 LIVE</span>
                    <span class="status-note" id="evNote">10:55時点・発走まで5分毎更新</span>
                    <button class="rf-btn avail" id="evRefresh" title="最新のオッズ・期待値に更新"><svg viewBox="0 0 24 24"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></button>
                </div>

                <div id="viewShutsuba">
                <table class="prob-t" id="probTable">
                    <thead>
                        <tr>
                            <th style="width:34px" id="th-num">馬番<span class="sort">▼</span></th>
                            <th style="width:34px" id="th-mark">印<span class="sort idle">▼</span></th>
                            <th>馬名（騎手）</th>
                            <th style="width:78px" id="th-prob">勝利確率<span class="sort idle">▼</span></th>
                            <th style="width:78px" id="th-ev">単勝期待値<span class="sort idle">▼</span><span class="th-live"><span class="live-dot"></span>LIVE</span></th>
                        </tr>
                    </thead>
                    <tbody id="probTableBody"></tbody>
                </table>
                <p class="prob-note">※ 好走確率は前日公開の値から、<b style="color:var(--text)">発走約30分前にオッズを取り込んで最終補正され確定</b>します（「最終」横の時刻＝確定時刻／▲▼は公開時の値からの変動）。的中実績・ランキングの集計は最終（確定）の値を使用。期待値は発走まで5分毎に更新。複勝期待値は複勝オッズの変動幅に対応したレンジ表示（赤＝下限でも100%超／橙＝上限で100%超）。印＝あなたの予想印（タップで順送り・長押しで一覧から選択）。</p>

                <a class="btn-sim" href="/simulation/sim_top" id="simBtn">的中確率シミュレーションをする ▶</a>
                </div><!-- /viewShutsuba -->

                <!-- ===== 結果・払戻しビュー（結果取得後のみ機能） ===== -->
                <div id="viewResult" hidden>
                <table class="prob-t result-t">
                    <thead>
                        <tr>
                            <th style="width:34px">着順</th>
                            <th style="width:34px">馬番</th>
                            <th style="width:34px">印</th>
                            <th>馬名</th>
                            <th style="width:34px">人気</th>
                            <th style="width:40px">確率<br>順位</th>
                            <th style="width:52px">単勝<br>期待値</th>
                            <th style="width:52px">複勝<br>期待値</th>
                        </tr>
                    </thead>
                    <tbody id="resultTableBody"></tbody>
                </table>
                <p class="prob-note">※ 人気＝最終単勝人気／確率順位＝Eval好走確率の順位／印＝出馬表で入力したあなたの予想印。期待値は確定オッズに基づく最終値。</p>

                <div class="payout">
                    <div class="p-head">配当</div>
                    <table>
                        <tbody id="payoutTableBody"></tbody>
                    </table>
                </div>

                </div><!-- /viewResult -->
                </div><!-- /prob-body -->

            </div>
        </section>

        <div class="pc-side"><!-- PC：右サイドカラム -->

        <!-- ===== 本日の攻略情報（圧縮版／詳細・一覧は攻略ツールページへ） ===== -->
        <section>
            <div class="sec-title"><h2>本日の攻略情報</h2><span class="en">TODAY'S PICKS</span><a class="right" href="/tools/tools_top">一覧 ▶</a></div>
            <div class="goods">
                <a href="/tools/tools_detail">
                    <span class="g-title">🐎 本日の推奨馬リスト<span class="new">NEW</span></span>
                    <span class="g-price">500pt</span>
                </a>
                <a href="/tools/tools_detail">
                    <span class="g-title">⚠️ 本日の前走不利情報<span class="new">NEW</span></span>
                    <span class="g-price">300pt</span>
                </a>
                <a href="/tools/tools_detail">
                    <span class="g-title">📈 本日のラップ分析情報<span class="new">NEW</span></span>
                    <span class="g-price">300pt</span>
                </a>
            </div>
        </section>

        <!-- ===== アンバサダー（2026-10-01）：アイコン＋名前の横スクロール。タップでアンバサダーページの該当者の位置へ ===== -->
        <section class="amb-top">
            <div class="sec-title"><h2>アンバサダー</h2><span class="en">AMBASSADOR</span><a class="right" href="/pages/ambassadors">一覧 ▶</a></div>
            <ul class="amb-strip" id="ambStrip"></ul>
        </section>

        {include file="partials/topics_module.tpl"}




        <!-- ===== 会員状態別CTA（実装時はログイン状態で出し分け／以下はモック確認用切替付き） ===== -->
        <div class="demo-switch">
            <span class="d-lbl">モック確認用：</span>
            <button class="on" data-state="guest">非ログイン</button>
            <button data-state="free">無料会員</button>
            <button data-state="subsc">サブスク会員</button>
            <button id="a2hsReset">ホーム追加バナー再表示</button>
            <button id="modalReplay">モーダル再生（選択中の状態）</button>
        </div>
        <div class="member-cta">
            <!-- 非ログイン：無料登録に一本化 -->
            <div class="panel cta-guest" id="cta-guest">
                <p class="lead">全出走馬の好走確率と<br><span style="white-space:nowrap">リアルタイム期待値（LIVE期待値）</span><br><b>無料登録</b>ですべて見られます</p>
                <p class="sub">メールアドレスだけ・30秒で完了</p>
                <a class="btn" href="/auth/entry">今すぐ無料登録 ▶</a>
                <a class="login" href="/auth/entry">アカウントをお持ちの方はログイン ▶</a>
            </div>
            <!-- 無料会員：サブスク訴求 -->
            <div class="panel cta-free" id="cta-free" hidden>
                <span class="offer">初月無料</span>
                <p class="lead">今日の<b>最終確率とLIVE期待値</b>、<br>全レース分を見逃していませんか？</p>
                <p class="price">サブスクなら全レース・全機能が使い放題　<b>月額2,980円</b><small>（税込・価格は仮）</small></p>
                <a class="btn" href="/settle/settle_subsc">初月無料でサブスクを始める ▶</a>
                <a class="pt-link" href="/settle/settle_point">ポイント購入はこちら（攻略情報の単品購入に）</a>
            </div>
            <!-- サブスク会員：ポイント導線のみ -->
            <div class="panel cta-subsc" id="cta-subsc" hidden>
                <span class="p-coin">P</span>
                <p class="txt"><b>本日の攻略情報</b>はポイントで単品購入できます（残高 7,777pt）</p>
                <a class="btn" href="/settle/settle_point">ポイント購入 ▶</a>
            </div>
        </div>
        </div><!-- /pc-side -->
        </div><!-- /pc-body -->

        <!-- 予想印ピッカー（印セルの長押しで表示） -->
        <div class="mark-sheet-overlay hidden" id="markSheetOverlay">
            <div class="mark-sheet">
                <p class="ms-title" id="msTitle"></p>
                <div class="ms-grid" id="msGrid">
                    <button data-mark="◉">◉</button>
                    <button data-mark="◎">◎</button>
                    <button data-mark="◯">◯</button>
                    <button data-mark="▲">▲</button>
                    <button data-mark="☆">☆</button>
                    <button data-mark="△">△</button>
                    <button data-mark="×">×</button>
                    <button data-mark="消">消</button>
                    <button data-mark="" class="clear">クリア</button>
                </div>
            </div>
        </div>

{/block}

{block name="scripts"}
    <script src="/js/top/top.js"></script>
{/block}
