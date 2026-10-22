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
                    <tbody>
                        <tr>
                            <td><span class="waku w1">2</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ダノンデサイル<small>牡4／C.ルメール</small></td>
                            <td class="rate hi" data-win="28.1%" data-fuku="58.4%">28.1%<i class="diff up">▲</i></td>
                            <td class="rate ev hi" data-win="142%" data-fuku="92〜105%" data-hw="1" data-hf="mid">142%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w3">5</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ジャンタルマンタル<small>牡4／川田将雅</small></td>
                            <td class="rate hi" data-win="21.1%" data-fuku="52.8%">21.1%<i class="diff down">▼</i></td>
                            <td class="rate ev" data-win="96%" data-fuku="88〜101%" data-hw="0" data-hf="mid">96%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w1">1</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">エンブロイダリー<small>牝3／横山武史</small></td>
                            <td class="rate" data-win="15.7%" data-fuku="41.1%">15.7%<i class="diff up">▲</i></td>
                            <td class="rate ev hi" data-win="163%" data-fuku="104〜128%" data-hw="1" data-hf="hi">163%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w2">3</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">カムニャック<small>牝4／M.デムーロ</small></td>
                            <td class="rate" data-win="12.4%" data-fuku="33.2%">12.4%</td>
                            <td class="rate ev" data-win="88%" data-fuku="85〜97%" data-hw="0" data-hf="">88%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w2">4</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ウインカーネリアン<small>牡7／三浦皇成</small></td>
                            <td class="rate" data-win="8.9%" data-fuku="26.7%">8.9%</td>
                            <td class="rate ev" data-win="74%" data-fuku="78〜90%" data-hw="0" data-hf="">74%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w4">8</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">レガレイラ<small>牝4／戸崎圭太</small></td>
                            <td class="rate" data-win="4.2%" data-fuku="18.5%">4.2%<i class="diff up">▲</i></td>
                            <td class="rate ev hi" data-win="121%" data-fuku="96〜115%" data-hw="1" data-hf="mid">121%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w4">7</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ソールオリエンス<small>牡5／横山和生</small></td>
                            <td class="rate" data-win="2.8%" data-fuku="14.2%">2.8%</td>
                            <td class="rate ev" data-win="68%" data-fuku="82〜98%" data-hw="0" data-hf="">68%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w3">6</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ベラジオオペラ<small>牡5／横山典弘</small></td>
                            <td class="rate" data-win="2.1%" data-fuku="12.6%">2.1%</td>
                            <td class="rate ev" data-win="92%" data-fuku="88〜102%" data-hw="0" data-hf="mid">92%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w5">9</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">シックスペンス<small>牡4／松山弘平</small></td>
                            <td class="rate" data-win="1.4%" data-fuku="9.8%">1.4%<i class="diff down">▼</i></td>
                            <td class="rate ev hi" data-win="110%" data-fuku="95〜110%" data-hw="1" data-hf="mid">110%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w5">10</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ジューンテイク<small>牡4／岩田望来</small></td>
                            <td class="rate" data-win="0.9%" data-fuku="7.4%">0.9%</td>
                            <td class="rate ev" data-win="55%" data-fuku="70〜84%" data-hw="0" data-hf="">55%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w6">11</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">サンライズアース<small>牡4／池添謙一</small></td>
                            <td class="rate" data-win="0.7%" data-fuku="6.2%">0.7%</td>
                            <td class="rate ev" data-win="83%" data-fuku="79〜93%" data-hw="0" data-hf="">83%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w6">12</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">メイショウタバル<small>牡4／浜中俊</small></td>
                            <td class="rate" data-win="0.6%" data-fuku="5.1%">0.6%</td>
                            <td class="rate ev" data-win="47%" data-fuku="60〜75%" data-hw="0" data-hf="">47%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w7">13</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">コスモキュランダ<small>牡5／丹内祐次</small></td>
                            <td class="rate" data-win="0.4%" data-fuku="3.9%">0.4%</td>
                            <td class="rate ev" data-win="62%" data-fuku="72〜88%" data-hw="0" data-hf="">62%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w7">14</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">アーバンシック<small>牡4／武豊</small></td>
                            <td class="rate" data-win="0.3%" data-fuku="3.2%">0.3%</td>
                            <td class="rate ev" data-win="71%" data-fuku="65〜80%" data-hw="0" data-hf="">71%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w8">15</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">ダノンエアズロック<small>牡4／坂井瑠星</small></td>
                            <td class="rate" data-win="0.2%" data-fuku="2.4%">0.2%</td>
                            <td class="rate ev" data-win="39%" data-fuku="50〜66%" data-hw="0" data-hf="">39%</td>
                        </tr>
                        <tr>
                            <td><span class="waku w8">16</span></td>
                            <td class="mark-td"><button class="mark-btn" data-mark=""></button></td>
                            <td class="name">シュガークン<small>牡4／田辺裕信</small></td>
                            <td class="rate" data-win="0.1%" data-fuku="1.6%">0.1%</td>
                            <td class="rate ev" data-win="28%" data-fuku="42〜58%" data-hw="0" data-hf="">28%</td>
                        </tr>
                    </tbody>
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
                    <tbody>
                        <tr>
                            <td><span class="chaku c1">1</span></td>
                            <td><span class="waku w1">1</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">エンブロイダリー</td>
                            <td>5</td>
                            <td><span class="prank p3">3</span></td>
                            <td class="rate hi">163%</td>
                            <td class="rate hi">121%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku c2">2</span></td>
                            <td><span class="waku w1">2</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ダノンデサイル</td>
                            <td>1</td>
                            <td><span class="prank p1">1</span></td>
                            <td class="rate hi">142%</td>
                            <td class="rate">98%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku c3">3</span></td>
                            <td><span class="waku w4">8</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">レガレイラ</td>
                            <td>8</td>
                            <td>6</td>
                            <td class="rate hi">121%</td>
                            <td class="rate hi">108%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">4</span></td>
                            <td><span class="waku w3">5</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ジャンタルマンタル</td>
                            <td>2</td>
                            <td><span class="prank p2">2</span></td>
                            <td class="rate">96%</td>
                            <td class="rate">95%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">5</span></td>
                            <td><span class="waku w2">3</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">カムニャック</td>
                            <td>3</td>
                            <td><span class="prank p4">4</span></td>
                            <td class="rate">88%</td>
                            <td class="rate">90%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">6</span></td>
                            <td><span class="waku w3">6</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ベラジオオペラ</td>
                            <td>6</td>
                            <td>8</td>
                            <td class="rate">92%</td>
                            <td class="rate">93%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">7</span></td>
                            <td><span class="waku w2">4</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ウインカーネリアン</td>
                            <td>4</td>
                            <td><span class="prank p5">5</span></td>
                            <td class="rate">74%</td>
                            <td class="rate">84%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">8</span></td>
                            <td><span class="waku w5">9</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">シックスペンス</td>
                            <td>7</td>
                            <td>9</td>
                            <td class="rate hi">110%</td>
                            <td class="rate hi">101%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">9</span></td>
                            <td><span class="waku w4">7</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ソールオリエンス</td>
                            <td>9</td>
                            <td>7</td>
                            <td class="rate">68%</td>
                            <td class="rate">88%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">10</span></td>
                            <td><span class="waku w6">11</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">サンライズアース</td>
                            <td>11</td>
                            <td>11</td>
                            <td class="rate">83%</td>
                            <td class="rate">86%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">11</span></td>
                            <td><span class="waku w5">10</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ジューンテイク</td>
                            <td>10</td>
                            <td>10</td>
                            <td class="rate">55%</td>
                            <td class="rate">76%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">12</span></td>
                            <td><span class="waku w7">13</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">コスモキュランダ</td>
                            <td>13</td>
                            <td>13</td>
                            <td class="rate">62%</td>
                            <td class="rate">79%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">13</span></td>
                            <td><span class="waku w6">12</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">メイショウタバル</td>
                            <td>12</td>
                            <td>12</td>
                            <td class="rate">47%</td>
                            <td class="rate">66%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">14</span></td>
                            <td><span class="waku w7">14</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">アーバンシック</td>
                            <td>14</td>
                            <td>14</td>
                            <td class="rate">71%</td>
                            <td class="rate">72%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">15</span></td>
                            <td><span class="waku w8">16</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">シュガークン</td>
                            <td>16</td>
                            <td>16</td>
                            <td class="rate">28%</td>
                            <td class="rate">48%</td>
                        </tr>
                        <tr>
                            <td><span class="chaku">16</span></td>
                            <td><span class="waku w8">15</span></td>
                            <td><span class="r-mark" data-mark="">−</span></td>
                            <td class="name">ダノンエアズロック</td>
                            <td>15</td>
                            <td>15</td>
                            <td class="rate">39%</td>
                            <td class="rate">55%</td>
                        </tr>
                    </tbody>
                </table>
                <p class="prob-note">※ 人気＝最終単勝人気／確率順位＝Eval好走確率の順位／印＝出馬表で入力したあなたの予想印。期待値は確定オッズに基づく最終値。</p>

                <div class="payout">
                    <div class="p-head">配当</div>
                    <table>
                        <tr><td class="k">単勝</td><td class="c">1</td><td class="y">1,630円</td></tr>
                        <tr><td class="k">複勝</td><td class="c">1<br>2<br>8</td><td class="y">410円<br>150円<br>890円</td></tr>
                        <tr><td class="k">馬連</td><td class="c">1-2</td><td class="y">2,340円</td></tr>
                        <tr><td class="k">ワイド</td><td class="c">1-2<br>1-8<br>2-8</td><td class="y">780円<br>3,120円<br>1,540円</td></tr>
                        <tr><td class="k">馬単</td><td class="c">1→2</td><td class="y">5,120円</td></tr>
                        <tr><td class="k">3連複</td><td class="c">1-2-8</td><td class="y">12,480円</td></tr>
                        <tr><td class="k">3連単</td><td class="c">1→2→8</td><td class="y">68,930円</td></tr>
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
