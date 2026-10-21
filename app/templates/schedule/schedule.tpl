{* P18 開催日程 *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/pages_schedule.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-schedule">

        <div class="page-head">
            <h1>開催日程</h1>
            <span class="en">RACE CALENDAR</span>
            <p class="lead">中央・地方の開催予定です。日付をタップすると<b>その日の確率テーブル（TOP）</b>へ移動します。</p>
        </div>

        <div class="month-nav">
            <a href="#">‹</a>
            <span class="m">2026年8月</span>
            <a href="#">›</a>
            <a href="#" class="today-btn">本日へ</a>
        </div>

        <div class="wk-grid">
            <div class="week-label">今週（8/3〜8/9）</div>
            <ul class="day-list" id="dayList"></ul>
            <div class="week-label">来週（8/10〜8/16）</div>
            <ul class="day-list" id="dayList2"></ul>
        </div>

        <p class="note">※ 開催日程は主催者発表に基づき更新されます（代替開催・中止は速報でお知らせ）。
        🌙＝ナイター開催。<b>WIN5</b>マークの日は中央でWIN5が発売され、<b>WIN5的中確率シミュレーション</b>が利用できます。
        過去日をタップすると結果・払戻し（確定）を表示します。</p>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/pages_schedule.js"></script>
{/block}
