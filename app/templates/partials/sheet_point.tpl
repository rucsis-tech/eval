{* =============================================================================
   templates/partials/sheet_point.tpl
   ポイント購入確認・完了ボトムシート部品
   ============================================================================= *}

<div id="sheetPoint" class="sheet-ov{if $show_point_sheet} show{/if}">
  <div class="sheet">
    <div class="grip"></div>

    {if $sheet_step|default:'confirm' eq 'complete'}
      {* -----------------------------------------------------------------------
         1. ポイント購入完了ビュー (Complete View)
         ----------------------------------------------------------------------- *}
      <div class="sh-head">
        <div class="sh-title">ポイント購入完了</div>
        <button type="button" class="sh-close" onclick="closePointSheet()">&times;</button>
      </div>

      <div class="sh-complete-body">
        <div class="complete-badge">
          <svg viewBox="0 0 24 24" width="32" height="32" stroke="currentColor" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
        </div>
        <p class="complete-msg">ポイントのチャージが完了しました</p>

        {if $purchased_pack}
          <div class="sh-pack">
            <div class="p-coin">P</div>
            <div class="p-info">
              <span class="p-pt">{$purchased_pack.points|number_format}<small>pt</small></span>
              {if $purchased_pack.bonus_points|default:0 > 0}
                <span class="p-bonus">+{$purchased_pack.bonus_points|number_format}pt還元</span>
              {/if}
            </div>
            <div class="p-price">
              &yen;{$purchased_pack.price|number_format}
            </div>
          </div>
        {/if}

        <div class="bal-summary">
          <div class="bal-row">
            <span class="bal-lbl">現在の保有ポイント:</span>
            <span class="bal-val"><b>{$user.points|default:0|number_format}</b> pt</span>
          {if $user.points_expire_date}
            <div class="bal-exp">
              有効期限: {$user.points_expire_date|date_format:"%Y/%m/%d"} まで
            </div>
          {/if}
        </div>

        <div class="sh-actions">
          <button type="button" class="btn-sheet-primary" onclick="closePointSheet()">確認して閉じる</button>
        </div>
      </div>

    {else}
      {* -----------------------------------------------------------------------
         2. ポイント購入確認ビュー (Confirmation View)
         ----------------------------------------------------------------------- *}
      <div class="sh-head">
        <div class="sh-title">ポイント購入の確認</div>
        <button type="button" class="sh-close" onclick="closePointSheet()">&times;</button>
      </div>

      <form id="formPointPurchase" method="post" action="/settle/settle_point">
        <input type="hidden" name="action" value="purchase">
        <input type="hidden" name="pack_id" id="sheetPackId" value="{$selected_pack.id|default:''}">
        <input type="hidden" name="csrf_token" value="{$csrf_token|default:''}">

        <div class="sh-pack" id="sheetPackSummary">
          <div class="p-coin">P</div>
          <div class="p-info">
            <span class="p-pt" id="sheetPackPoints">{$selected_pack.points|default:0|number_format}<small>pt</small></span>
            {if $selected_pack.bonus_points|default:0 > 0}
              <span class="p-bonus" id="sheetPackBonus">+{$selected_pack.bonus_points|number_format}pt還元</span>
            {else}
              <span class="p-bonus" id="sheetPackBonus" style="display:none;"></span>
            {/if}
          </div>
          <div class="p-price" id="sheetPackPrice">
            &yen;{$selected_pack.price|default:0|number_format}
          </div>
        </div>

        <div class="payment-method-sec">
          <div class="pm-title">お支払い方法を選択</div>
          <div class="pm-options">
            <label class="pm-option">
              <input type="radio" name="payment_method" value="credit_card" checked>
              <span class="pm-label">クレジットカード決済</span>
            </label>
            <label class="pm-option">
              <input type="radio" name="payment_method" value="paypay">
              <span class="pm-label">PayPay決済</span>
            </label>
          </div>
        </div>

        <div class="bal-change-preview">
          <div class="bc-row">
            <span>購入前所持ポイント</span>
            <span>{$user.points|default:0|number_format} pt</span>
          </div>
          <div class="bc-row highlight">
            <span>購入後所持ポイント</span>
            <span class="bc-after" id="sheetAfterPoints">
              {($user.points|default:0 + $selected_pack.total_points|default:0)|number_format} pt
            </span>
          </div>
        </div>

        <div class="sh-actions">
          <button type="submit" class="btn-sheet-purchase">購入を確定する</button>
          <button type="button" class="btn-sheet-cancel" onclick="closePointSheet()">キャンセル</button>
        </div>
      </form>
    {/if}
  </div>
</div>