{* ===================================================================
   templates/partials/modal.tpl
   汎用キャンペーン・お知らせモーダル
   =================================================================== *}

{if !empty($modal)}
<div class="modal-overlay{if !empty($modal.is_hidden)} hidden{/if}" id="campaign-modal-overlay" data-modal-id="{$modal.id|escape}">
  <div class="modal">
    {* 複数枚表示時のカウンター *}
    {if isset($modal.total_count) && $modal.total_count > 1}
      <span class="m-count"{if !empty($modal.hide_count)} hidden{/if}>
      {$modal.current_index|default:1|escape} / {$modal.total_count|escape}
    {/if}

    {* 閉じるボタン *}
    <button type="button" class="m-close" aria-label="閉じる" onclick="closeModal('{$modal.id|escape}')">&times;</button>

    {* ビジュアル領域（背景グラデーション種別: v-tie, v-sub, v-pt, v-sys, v-reg） *}
    <div class="m-visual v-{$modal.type|default:'tie'|escape}">
      {if !empty($modal.label)}
        <span class="m-lbl">{$modal.label|escape}</span>
      {/if}

      {if !empty($modal.title)}
        <h2>{$modal.title|escape|nl2br}</h2>
      {/if}

      {if !empty($modal.description)}
        <p>{$modal.description|escape|nl2br}</p>
      {/if}
    </div>

    {* フッター領域 *}
    <div class="m-foot">
      {if !empty($modal.cta_url) && !empty($modal.cta_text)}
        <a href="{$modal.cta_url|escape}" class="m-btn">{$modal.cta_text|escape}</a>
      {/if}

      <a href="javascript:void(0);" class="m-skip" onclick="closeModal('{$modal.id|escape}')">
        {$modal.skip_text|default:'閉じる'|escape}
      </a>

      {* 複数枚表示時のドットインジケーター *}
      {if isset($modal.total_count) && $modal.total_count > 1}
        <div class="m-dots">
          {section name=dot loop=$modal.total_count}
            <i class="{if $smarty.section.dot.iteration == $modal.current_index|default:1}on{/if}"></i>
          {/section}
        </div>
      {/if}
    </div>
  </div>
</div>
{/if}
