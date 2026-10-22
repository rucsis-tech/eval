{* P11 会員登録完了（P11 モック upsellView／フォーカスレイアウト） *}
{extends file="layout/base_focus.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/auth/auth.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-signup w-form">

        <div class="wrap" id="upsellView">
            <div class="done-band">
                <div class="d-check">✓</div>
                <p class="d-title">無料会員登録が完了しました！</p>
                <p class="d-sub">このまま今日のレースの確率をチェックできます</p>
                <span class="d-pt">🎁 登録特典 100pt を付与しました</span>
            </div>

            <div class="unlocked">
                <p class="u-t">いま解放された機能</p>
                <ul>
                    <li>好走確率データ<span class="u-lim">（一部レースのみ）</span></li>
                    <li>リアルタイム期待値（LIVE期待値）<span class="u-lim">（一部レースのみ）</span></li>
                    <li>的中確率シミュレーション<span class="u-lim">（機能制限あり）</span></li>
                    <li>予想印の保存・アンケートでpt獲得</li>
                </ul>
            </div>

            <div class="offer-band" id="offerBand">
                <span class="o-lbl">登録者限定</span>
                <span class="o-txt">フル機能プランが初月無料<small>登録から24時間限定のオファーです</small></span>
                <span class="o-timer" id="offerTimer">23:59:12</span>
            </div>

            <div class="shot has-img">
                <img src="/images/auth/auth_upsell_shot.png" alt="出馬表×妙味チップの機能イメージ" width="414" height="233">
            </div>

            <div class="invite-box">
                <div class="iv-head">
                    <span class="iv-ico">🎟</span>
                    <div>
                        <p class="iv-t">招待コードをお持ちの方</p>
                        <p class="iv-s">X（@master_eval）・公認アンバサダー配布の招待コードで特別価格が適用されます</p>
                    </div>
                </div>
                <div class="iv-form" id="ivForm">
                    <input class="in-text" id="inviteInput" placeholder="招待コードを入力（例：EVAL-XXXX）">
                    <button class="btn-sub" id="inviteBtn" type="button">適用</button>
                </div>
                <p class="iv-err" id="ivErr" hidden>コードが確認できません。入力内容をお確かめください。</p>
                <p class="iv-hint">モック用コード：<b>EVALFREE</b>＝初月無料／<b>EVALW</b>＝初月無料＋特別価格／<b>EVALSP</b>＝特別価格／<b>EVALALL</b>＝全プラン特別価格</p>
                <div class="iv-applied" id="ivApplied" hidden>
                    <span class="ia-check">✓</span>
                    <div class="ia-body">
                        <p class="ia-t" id="ivAppliedT">招待特典を適用しました</p>
                        <p class="ia-s" id="ivAppliedS"></p>
                    </div>
                    <a class="ia-cancel" id="ivCancel" href="#">取消</a>
                </div>
            </div>

            <div class="plans">
                <div class="plan reco" id="planFull">
                    <span class="p-reco">おすすめ</span><span class="p-free" id="freeBadge">初月無料</span>
                    <div class="p-head"><span class="p-name">フル機能プラン</span>
                        <span class="p-price" id="fullPrice">月額9,800円<small>（税込・仮）</small></span></div>
                    <p class="p-desc">中央＋地方の全レースの確率・リアルタイム期待値、<b>全機能使い放題</b>。シミュレーションも無制限。</p>
                    <p class="p-benefit" id="fullBenefit" hidden></p>
                    <a class="p-cta" href="/settle/settle_subsc" id="fullCta">初月無料でフル機能を試す ▶</a>
                </div>
                <div class="plan" id="planChuo">
                    <span class="p-reco" hidden>おすすめ</span>
                    <div class="p-head"><span class="p-name">中央競馬プラン</span>
                        <span class="p-price" id="chuoPrice">月額5,980円<small>（税込・仮）</small></span></div>
                    <p class="p-desc">JRA全レースの確率・期待値・シミュレーションが使い放題。</p>
                    <p class="p-benefit" id="chuoBenefit" hidden></p>
                    <a class="p-cta" href="/settle/settle_subsc" id="chuoCta">中央プランを選ぶ</a>
                </div>
                <div class="plan" id="planChiho">
                    <span class="p-reco" hidden>おすすめ</span>
                    <div class="p-head"><span class="p-name">地方競馬プラン</span>
                        <span class="p-price" id="chihoPrice">月額3,980円<small>（税込・仮）</small></span></div>
                    <p class="p-desc">地方全場（門別・大井・高知ほか）の確率・期待値が使い放題。平日ナイター派に。</p>
                    <p class="p-benefit" id="chihoBenefit" hidden></p>
                    <a class="p-cta" href="/settle/settle_subsc" id="chihoCta">地方プランを選ぶ</a>
                </div>
            </div>

            <div class="cmp">
                <p class="cmp-t">プラン機能比較</p>
                <table>
                    <tr><th></th><th>無料</th><th>中央</th><th>地方</th><th class="full">フル</th></tr>
                    <tr><td>好走確率・期待値</td><td>一部</td><td>中央全R</td><td>地方全R</td><td class="full">全レース</td></tr>
                    <tr><td>妙味チップ</td><td>一部</td><td>中央全R</td><td>地方全R</td><td class="full">全レース</td></tr>
                    <tr><td>シミュレーション</td><td>制限あり</td><td>中央のみ</td><td>地方のみ</td><td class="full">無制限</td></tr>
                    <tr><td>WIN5的中確率算出</td><td>−</td><td>◯</td><td>−</td><td class="full">◯</td></tr>
                    <tr><td>攻略ツール優待（仮）</td><td>−</td><td>−</td><td>−</td><td class="full">◯</td></tr>
                </table>
                <p class="cmp-note">※比較内容・特典はすべて仮。無料枠の範囲は要決定。</p>
            </div>

            <a class="skip-link" href="/" id="skipBtn">まずは無料のまま、今日の確率を見る ▶</a>
            <p class="plan-note">価格・特典はすべて仮。プランはいつでも変更・解約できます。<br>無料のままでも基本機能はずっと使えます。</p>

            <div class="demo-switch">
                <span class="d-lbl">モック確認用：</span>
                <button type="button" id="d-signup">登録画面</button>
                <button type="button" id="d-login">ログイン画面</button>
                <button type="button" id="d-code">コード入力</button>
                <span class="d-lbl">｜初月無料キャンペーン：</span>
                <button type="button" id="d-campOn" class="on">あり</button>
                <button type="button" id="d-campOff">なし</button>
                <span class="d-lbl">｜おすすめ表示：</span>
                <button type="button" class="d-reco on" data-reco="full">フル</button>
                <button type="button" class="d-reco" data-reco="chuo">中央</button>
                <button type="button" class="d-reco" data-reco="chiho">地方</button>
            </div>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/auth/finish.js"></script>
{/block}
