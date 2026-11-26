{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>トピックス編集</h3>
    <div style="position:absolute;right:1rem;top:0;"><a href="index">
            <button class="btn btn-secondary">戻る</button>
        </a></div>
    <div class="two_column">
        <div class="one_column">

            <table class="table" style="max-width: 1000px; margin: 0 auto;">
                <tr>
                    <th style="max-width: 100px;">ID</th>
                    <td>{$Topic->id}</td>
                </tr>

                <tr>
                    <th style="max-width: 100px;">カテゴリー</th>
                    <td style="min-width:100px;">
                        <select name="category" form="edit_submit">
                            {foreach Info::TOPIC_CATEGORIES as $value}
                                <option value="{$value}" {if $Topic->category == $value }selected{/if}>{$value}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>

                <tr>
                    <th style="max-width: 200px;">サムネイル画像</th>
                    <td class="d-flex">
                        <div style="max-width:100px;">
                            <img src="{$Topic->image_path|s3}" width="100px">
                        </div>
                        <div class="p-2">
                            <input type="file" class="m-1 w-100" name="image_path">
                            <button class="btn btn-sm btn-secondary m-1" onclick="upload_image(this)">UPLOAD</button>

                            <input type="hidden" class="image_tmp" name="image_tmp" id="image_file" form="edit_submit">
                            <input type="hidden" class="image_origin" name="image_origin" form="edit_submit">
                        </div>
                    </td>

                </tr>


                <tbody>

                <tr>
                    <th style="max-width: 200px;">タイトル</th>
                    <td style="min-width:100px;"><input type="text" name="title" value="{$Topic->title}" class="form-control" form="edit_submit"></td>
                </tr>


                <tr>
                    <th>テキスト</th>
                    <td style="min-width: 400px;"><textarea name="content" class="form-control" form="edit_submit" style="height:4rem;">{$Topic->content}</textarea></td>
                </tr>


                <tr>
                    <th>開始時間</th>
                    <td>
                        <div id="start_at" data-value="{$Topic->start_at}" data-clock="1" data-form="edit_submit" class="input-group date rem-18"></div>
                    </td>
                </tr>
                <tr>
                    <th>終了時間</th>
                    <td>
                        <div id="end_at" data-value="{$Topic->end_at}" data-clock="1" data-form="edit_submit" class="input-group date rem-18"></div>
                    </td>
                </tr>


                <tr>
                    <th style="max-width: 100px;">公開設定</th>
                    <td>
                        <div class="switch_outer rem-4 {if !$Topic->disabled}active{/if}" id="outer" onclick="toggle_change(this)" type="button">
                            <div class="toggle_switch {if !$Topic->disabled}active{/if}" id="switch"></div>
                            <input type="hidden" value="{$Topic->disabled}" name="disabled" id="disabled" form="edit_submit">
                        </div>
                    </td>
                </tr>

                </tbody>

            </table>

            <form action="edit" id="edit_submit" method="POST" class="d-flex justify-content-center">
                <input type="hidden" name="id" value="{$Topic->id}">
                <input type="hidden" name="priority" value="{$Topic->priority}">
                <button class="btn btn-primary m-4 rem-10" type="button" onclick="submit()">更新</button>
            </form>

        </div>

    </div>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script src="/cms/jslib/toggle_change.js"></script>
    <script src="/cms/jslib/upload_image.js"></script>
    <script src="/cms/jslib/datetimepicker.js"></script>
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

        .two_column {
            display: flex;
            justify-content: space-between;
            width: 1200px;
            margin: 0 auto;
        }

        .one_column {
            width: 700px;
            margin: 0 auto;
        }

        .side_column {
            width: 400px;
            margin-left: 20px;
            border: solid 1px #CCC;
            padding: 10px;
        }

        .side_column p {
            width: ! 00%;
            background: #CCC;
            text-align: center;
            font-weight: bold;
        }


        textarea {
            width: 100%;
            text-align: left;
            height: 7em;
            line-height: 2em;
        }

    </style>
{/if}