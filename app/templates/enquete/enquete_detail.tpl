{* P9 アンケート詳細（回答） *}
{extends file="layout/base.tpl"}

{block name="extra_css"}
    <link rel="stylesheet" href="/css/enquete/enquete.css">
{/block}

{block name="content"}
    <div class="pc-wrap pc-survey" data-survey-id="{$survey_id|escape}">

        <div id="detailView">
            <div class="back-row"><a href="/enquete/enquete_top">◀ アンケート一覧へ戻る</a></div>
            <div class="dt-wrap" id="dtWrap"></div>
            <div class="submit-row">
                <button type="button" class="btn-submit disabled" id="submitBtn">回答する（🎁<span id="submitPt"></span>pt）</button>
            </div>
            <p class="submit-note">※ 回答は1人1回まで。回答内容は個人が特定されない形で集計されます。<br>選択肢は1問あたり最大50件・自由記述は500文字まで。</p>
        </div>

    </div>
{/block}

{block name="scripts"}
    <script src="/js/enquete/enquete_common.js"></script>
    <script src="/js/enquete/enquete_detail.js"></script>
{/block}
