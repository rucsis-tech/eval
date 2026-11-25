{if $template_type=='body'}
    <h3>お知らせ設定</h3>
    <div style="position:absolute;right:1rem;top:0;"><a href="index"><button class="btn btn-secondary">戻る</button></a></div>


    <div class="one_column">
        <form action="/cms/info/edit" id="edit_submit" method="POST">
            <table class="table" id="list_table">

                <tr>
                    <th style="max-width: 50px;">ID</th>
                    <td>{$Info->id}</td>
                </tr>

                <tr>
                    <th>タイトル</th>
                    <td style="min-width:100px;"><input type="text" name="title" value="{$Info->title}" style="width:100%;" class="form-control"/></td>
                </tr>

                <tr>
                    <th style="max-width: 50px;">お知らせ本文</th>
                    <td style="min-width:100px;"><textarea name="content" class="form-control">{$Info->content}</textarea></td>
                </tr>

                <tr>
                    <th>開始時間</th>
                    <td>
                        <div id="start_at" data-value="{$Info->start_at}" data-clock="1" data-form="edit_submit" class="input-group date rem-18"></div>
                    </td>
                </tr>

                <tr>
                    <th>終了時間</th>
                    <td>
                        <div id="end_at" data-value="{$Info->end_at}" data-clock="1" data-form="edit_submit" class="input-group date rem-18"></div>
                    </td>
                </tr>

                <tr>
                    <th>表示優先順位</th>
                    <td style="min-width:100px;">
                        <input type="text" name="priority" data-form="edit_submit" value="{$Info->priority}">
                    </td>
                </tr>

                <tr>
                    <th style="max-width: 100px;">公開設定</th>
                    <td>
                        <div class="switch_outer rem-4 {if !$Info->disabled}active{/if}" id="outer" onclick="toggle_change(this)" type="button">
                            <div class="toggle_switch {if !$Info->disabled}active{/if}" id="switch"></div>
                            <input type="hidden" value="{$Info->disabled}" name="disabled" id="disabled" form="edit_submit">
                        </div>
                    </td>
                </tr>


            </table>

            <div class="d-flex justify-content-center">
                <input type="hidden" name="id" value="{$Info->id}">
                <button class="btn btn-primary m-4 rem-10" type="button" onclick="submit()">更新</button>
            </div>
        </form>


    </div>
{/if}




{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script src="/cms/jslib/datetimepicker.js"></script>
    <script src="/cms/jslib/toggle_change.js"></script>
{literal}
    <script>
        createDatetimepicker({id: 'start_at'});
        createDatetimepicker({id: 'end_at'});
    </script>
{/literal}
{/if}




{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <script src="/js/cms/post.js"></script>
    <style>

        .one_column {
            width: 700px;
            margin: 0 auto;
        }

        textarea {
            width: 100%;
            height: 200px;
        }


    </style>
{/if}
