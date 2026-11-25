{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <script src="/js/cms/post.js"></script>
    <style>
        .now_event {
            color: #FF0000;
            font-weight: boldl
            background: #00FFFF;
        }

        .end_event {
            color: #FF0000;
            font-weight: boldl
            background: #00FFFF;
        }

    </style>
{/if}

{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>アンケート</h3>
    <form method="POST">
        <input type="hidden" name="insert" value="1">
        <button class="btn btn-warning" type="submit">新規追加</button>
    </form>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>状態</th>
            <th>タイトル</th>
            <th>開催開始時間</th>
            <th>開催終了時間</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="table-group-divider">
        {foreach $EnqueteMasters as $Item}
            <tr>

                <td>{$Item->id}</td>
                <td>{$STATUSES[$Item->status]}</td>
                <td>{$Item->title}</td>
                <td><span class="{if $Item->now_event == 1}now_event{/if}">{$Item->start_at}{if $Item->now_event == 1}<br>開催中！{/if}</span></td>
                <td><span class="{if $Item->end_event == 1}end_event{/if}">{$Item->end_at}{if $Item->end_event == 1}<br>イベント終了{/if}</span></td>
                <td class="rem-14">
                    <a href="/cms/enquete/result?id={$Item->id}">
                        <button class="btn btn-secondary">結果</button>
                    </a>
                    <a href="/cms/enquete/edit?id={$Item->id}">
                        <button class="btn btn-primary">編集</button>
                    </a>
                    <a href="/cms/enquete/index?closed={$Item->id}" onclick="return CheckConfirm()">
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
{/if}