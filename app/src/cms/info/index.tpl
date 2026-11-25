{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <style>
    </style>
{/if}

{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>お知らせ一覧</h3>
    <form method="POST">
        <input type="hidden" name="insert" value="1">
        <button class="btn btn-warning" type="submit">新規追加</button>
    </form>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>タイトル</th>
            <th>表示開始時間</th>
            <th>表示終了時間</th>
            <th class="text-center">表示優先順位</th>
            <th class="text-center">公開設定</th>
        </tr>
        </thead>
        <tbody class="table-group-divider">
        {foreach $Infos as $Item}
            <tr>
                <td>{$Item->id}</td>
                <td>{$Item->title}</td>
                <td>{$Item->start_at}</td>
                <td>{$Item->end_at}</td>
                <td class="text-center">{$Item->priority}</td>
                <td class="text-center">{if $Item->disabled == 0}表示{else}非表示{/if}</td>
                <td>
                    <a href="/cms/info/edit?id={$Item->id}">
                        <button class="btn btn-primary">編集</button>
                    </a>
                    <a href="/cms/info/index?closed={$Item->id}" onclick="return CheckConfirm()">
                        <button class="btn btn-danger">削除</button>
                    </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script src="/cms/jslib/datetimepicker.js"></script>
    <script src="/js/cms/post.js"></script>
{literal}
{/literal}

{/if}