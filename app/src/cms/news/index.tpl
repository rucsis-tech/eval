{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <style>
    </style>
{/if}

{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>トピックス一覧</h3>
    <form method="POST">
        <input type="hidden" name="insert" value="1">
        <button class="btn btn-warning" type="submit">新規追加</button>
    </form>
    <table class="table table-striped">
        <thead class="">
        <tr>
            <th>ID</th>
            <th>カテゴリー</th>
            <th>トピック文章</th>
            <th>表示開始時間</th>
            <th>表示終了時間</th>
            <th>公開設定</th>
        </tr>
        </thead>

        <tbody class="table-group-divider">
        {foreach $Topics as $Item}
            <tr>
                <td>{$Item->id}</td>
                <td>{$Item->category}</td>
                <td>{$Item->title}</td>
                <td>{$Item->start_at}</td>
                <td>{$Item->end_at}</td>
                <td>{if $Item->disabled == 0}表示{else}非表示{/if}</td>
                <td>
                    <a href="/cms/topic/edit?id={$Item->id}">
                        <button class="btn btn-primary">編集</button>
                    </a>
                    <a href="/cms/topic/index?closed={$Item->id}" onclick="return CheckConfirm()">
                        <button class="btn btn-danger" style="">削除</button>
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