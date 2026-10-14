{* =============================================================================
 * templates/partials/sheet_subscribe.tpl
 * サブスクリプション申込 確認・完了ボトムシートモーダル
 * ============================================================================= *}

{* モーダルオーバーレイ *}
<div class="sheet-ov hidden" id="sheet-subsc-overlay" onclick="if(event.target===this) closeSubscSheet();">
  <div class="sheet" id="sheet-subsc-container" role="dialog" aria-modal="true" aria-labelledby="sheet-subsc-title">

    {* -------------------------------------------------------------------------
     * STEP 1: 申込内容確認ビュー
     * ------------------------------------------------------------------------- *}
    <div class="sheet-step" id="subsc-step-confirm">
      <div class="sheet-head">
        <h3 class="sheet-title" id="sheet-subsc-title">サブスクリプション申込確認</h3>
        <button type="button" class="sheet-close" onclick="closeSubscSheet();" aria-label="閉じる">✕</button>
      </div>

      <div class="sheet-body">
        {* 選択プラン概要カード *}
        <div class="sheet-plan-card">
          <div class="sp-badge">選択中のプラン</div>
          <div class="sp-row">
            <span class="sp-name">{$selected_plan.name|default:'プレミアム会員プラン'}</span>
            <span class="sp-price">
              ￥{$selected_plan.price|number_format|default:'980'}
              <small>/ {$selected_plan.period_unit|default:'月'} (税込)</small>
            </span>
          </div>
          {if $selected_plan.description}
            <p class="sp-desc">{$selected_plan.description|escape}</p>
          {/if}
        </div>

        {* 招待コード・割引適用情報 *}
        {if $applied_invite_code}
          <div class="sheet-discount-box">
            <div class="sd-tag">招待コード適用中</div>
            <div class="sd-row">
              <span class="sd-code">{$applied_invite_code|escape}</span>
              <span class="sd-val">-￥{$discount_amount|number_format|default:'0'}</span>
            </div>
          </div>
        {/if}

        {* 決済金額サマリー *}
        <div class="sheet-summary">
          <div class="ss-row">
            <span>初回請求額</span>
            <span class="ss-price">￥{($selected_plan.price - $discount_amount)|number_format}</span>
          </div>
          <div class="ss-row sub">
            <span>次回更新日</span>
            <span>{$next_renewal_date|default:'次回更新時より通常料金'}</span>
          </div>
        </div>

        {* 決済方法の選択 *}
        <div class="sheet-pay-select">
          <label class="sps-label">お支払い方法の選択</label>
          <div class="sps-options">
            {foreach from=$payment_methods item=pay key=code name=pay_loop}
              <label class="sps-item">
                <input type="radio" name="payment_method" value="{$code|escape}" {if $smarty.foreach.pay_loop.first}checked{/if}>
                <span class="sps-text">{$pay.name|escape}</span>
                {if $pay.icon_class}
                  <span class="sps-icon {$pay.icon_class}"></span>
                {/if}
              </label>
            {/foreach}
          </div>
        </div>

        {* 自動更新・規約同意に関する注記 *}
        <div class="sheet-legal-note">
          <p>※サブスクリプションは更新日時に自動更新されます。</p>
          <p>※解約はマイページよりいつでも手続きが可能です。</p>
          <p>※<a href="/pages/rules" target="_blank">利用規約</a>および<a href="/pages/privacy" target="_blank">プライバシーポリシー</a>に同意の上お申し込みください。</p>
        </div>

        {* 確定アクションボタン *}
        <div class="sheet-action">
          <button type="button" class="btn-main" id="btn-subsc-submit" onclick="submitSubscription();">
            規約に同意して継続課金を確定する
          </button>
          <button type="button" class="btn-ghost" onclick="closeSubscSheet();">キャンセル</button>
        </div>
      </div>
    </div>

    {* -------------------------------------------------------------------------
     * STEP 2: 申込完了ビュー（AJAX成功時に切替）
     * ------------------------------------------------------------------------- *}
    <div class="sheet-step hidden" id="subsc-step-complete">
      <div class="sheet-head">
        <h3 class="sheet-title">手続き完了</h3>
        <button type="button" class="sheet-close" onclick="closeSubscSheet();" aria-label="閉じる">✕</button>
      </div>

      <div class="sheet-body text-center">
        <div class="done-band">
          <div class="d-check">✓</div>
          <div class="d-title">サブスクリプション登録が完了しました</div>
          <div class="d-sub">ご利用ありがとうございます。全機能が即時開放されました。</div>
        </div>

        {* 解放された機能一覧 *}
        <div class="unlocked-box">
          <div class="ub-title">解放された機能</div>
          <ul class="ub-list">
            <li>全レースの的中確率・期待値データ閲覧</li>
            <li>WIN5 的中確率シミュレーション無制限利用</li>
            <li>好走確率ランキング全順位の完全開放</li>
            <li>限定トピックス・速報アラートの受信</li>
          </ul>
        </div>

        {* 完了アクションボタン *}
        <div class="sheet-action">
          <a href="/" class="btn-main">さっそく予想データを見る</a>
          <a href="/mypage/mypage" class="btn-ghost">マイページで登録状況を確認</a>
        </div>
      </div>
    </div>

  </div>
</div>