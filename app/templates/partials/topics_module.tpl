{* ===================================================================
   templates/partials/topics_module.tpl
   TOPページ用 トピックス / 速報 / お知らせ モジュール部品
   =================================================================== *}

{* 1. リアルタイム速報ティッカー *}
{if !empty($flash_ticker_list)}
<div class="flash">
  <span class="lbl">速報</span>
  <div class="ticker">
    {foreach from=$flash_ticker_list item=ticker name=ticker_loop}

      <a href="{$ticker.url|default:'/topics/topics_top'|escape}" class="{if $smarty.foreach.ticker_loop.first}show{/if}">
        <b>{$ticker.category_name|escape}</b>{$ticker.title|escape}
      </a>
    {/foreach}
  </div>
  <a href="/topics/topics_top" class="more">一覧 &gt;</a>
</div>
{/if}

{* 2. トピックス / お知らせ モジュール本体 *}
<section class="topics-module-sec">
  <div class="sec-title">
    <h2>TOPICS &amp; NEWS</h2>
    <span class="en">最新情報</span>
  </div>

  {* タブ切り替えヘッダー *}
  <div class="topics-tabs">
    <button type="button" class="tab-btn active" data-target="tab-topics">トピックス</button>
    <button type="button" class="tab-btn" data-target="tab-flash">速報</button>
    <button type="button" class="tab-btn" data-target="tab-info">お知らせ</button>
  </div>

  {* タブコンテンツ表示領域 *}
  <div class="topics-tab-contents">

    {* --- Tab 1: トピックス --- *}
    <div id="tab-topics" class="tab-content active">
      {if !empty($topics_list)}
        <ul class="topic-list">
          {foreach from=$topics_list item=item}
            <li class="topic-item">
              <a href="/topics/topics_detail?id={$item.id|escape}">
                <span class="t-date">{$item.published_at|date_format:"%Y.%m.%d"}</span>
                {if $item.is_new}
                  <span class="t-badge new">NEW</span>
                {/if}
                <span class="t-cat">{$item.category_name|default:'トピックス'|escape}</span>
                <span class="t-title">{$item.title|escape}</span>
              </a>
            </li>
          {/foreach}
        </ul>
      {else}
        <p class="empty-msg">現在表示できるトピックスはありません。</p>
      {/if}
      <div class="topics-more-wrap">
        <a href="/topics/topics_top" class="btn-more">トピックス一覧を見る</a>
      </div>
    </div>

    {* --- Tab 2: 速報 --- *}
    <div id="tab-flash" class="tab-content">
      {if !empty($flash_news_list)}
        <ul class="topic-list">
          {foreach from=$flash_news_list item=item}
            <li class="topic-item">
              <a href="/topics/topics_detail?id={$item.id|escape}">
                <span class="t-date">{$item.published_at|date_format:"%Y.%m.%d %H:%M"}</span>
                <span class="t-badge flash">速報</span>
                <span class="t-title">{$item.title|escape}</span>
              </a>
            </li>
          {/foreach}
        </ul>
      {else}
        <p class="empty-msg">最新の速報はありません。</p>
      {/if}
      <div class="topics-more-wrap">
        <a href="/topics/topics_top" class="btn-more">速報一覧を見る</a>
      </div>
    </div>

    {* --- Tab 3: お知らせ --- *}
    <div id="tab-info" class="tab-content">
      {if !empty($info_list)}
        <ul class="topic-list">
          {foreach from=$info_list item=item}
            <li class="topic-item">
              <a href="/topics/info_detail?id={$item.id|escape}">
                <span class="t-date">{$item.published_at|date_format:"%Y.%m.%d"}</span>
                <span class="t-cat info">お知らせ</span>
                <span class="t-title">{$item.title|escape}</span>
              </a>
            </li>
          {/foreach}
        </ul>
      {else}
        <p class="empty-msg">現在お知らせはありません。</p>
      {/if}
      <div class="topics-more-wrap">
        <a href="/topics/topics_top" class="btn-more">お知らせ一覧を見る</a>
      </div>
    </div>

  </div>
</section>