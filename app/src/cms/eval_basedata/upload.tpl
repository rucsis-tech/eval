{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>Windex地方版 upload確認</h3>
    <div style="">

        <form action="upload.php" method="POST" enctype="multipart/form-data" style="margin:8px 0;">
            <div class="block" style="text-align:right">
                <input type="submit" name="confirmed" value="すべて更新する" class="btn btn-primary" onclick="disableSubmit(this)">
            </div>
            <input type="hidden" name="target_day" value="{$target_day}">
            {foreach $resources as $key=>$resource}
                <input type="hidden" name="{$key}-name" value="{$resource.name}">
                <input type="hidden" name="{$key}-tmp_name" value="{$resource.tmp_name}">
                <div class="block">
                    <h5>{$key}: {$resource.name}</h5>
                    {if (isset($resource.error))}
                        {$resource.error.message}
                    {else}
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                            <tr>
                                {foreach $resource.data.list[0] as $field => $col}
                                    <th class="th">{$field}</th>
                                {/foreach}
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $resource.data.list as $row}
                                <tr>
                                    {foreach $row as $col}
                                        <td>{$col}</td>
                                    {/foreach}
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    {/if}


                </div>
            {/foreach}
        </form>
    </div>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->

{/if}



{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <script src="/js/cms/post.js"></script>
    <style>
        .block {
            width: 80%;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .th {
            padding: 4px 10px;
            text-align: center;
            background-color: silver !important;
        }

        td {
            padding: 4px 10px;
            text-align: center;
        }
    </style>
{/if}