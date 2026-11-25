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
        <thead>
        <tr>
            <th>ID</th>
            <th>サムネイル画像</th>
            <th>トピック文章</th>
            <th>表示開始</th>
            <th>表示終了</th>
            <th>公開設定</th>
            <th></th>
        </tr>
        </thead>

        <tbody class="table-group-divider">
        {foreach $List as $Item}
            <tr>
                <td>{$Item->id}</td>
                <td style="max-width:100px;">
                    {if !empty($Item->image_path)}
                        <img src="{$Item->image_path|s3}" width="100px">
                    {/if}
                </td>
                <td style="max-width:400px;">{$Item->content}</td>
                <td style="min-width:100px;">{$Item->start_at}</td>
                <td style="min-width:100px;">{$Item->end_at}</td>
                <td>
                    <div class="mx-2">
                        {($Item->disabled) ? '非表示':'表示'}
                    </div>
                </td>
                <td>
                    <div class="d-flex">
                        <div style="margin-left: 20px;">
                            <a href="/cms/topic/edit?id={$Item->id}">
                                <button class="btn btn-primary">編集</button>
                            </a>

                            <a href="/cms/topic/index?closed={$Item->id}" onclick="return CheckConfirm()">
                                <button class="btn btn-danger" style="">削除</button>
                            </a>

                        </div>
                    </div>
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
    <script>
        createDatetimepicker({id: 'datetimeStart'});
        createDatetimepicker({id: 'datetimeEnd'});

        function clearValue() {
            document.getElementsByName('category')[0].value = '';
            document.getElementsByName('start')[0].value = '';
            document.getElementsByName('end')[0].value = '';
            document.getElementsByName('author')[0].value = '';
            document.getElementsByName('keyword')[0].value = '';
        }

    </script>
{/literal}

{/if}