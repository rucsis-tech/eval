{if $template_type=='body'}
    <h3>トピックス設定</h3>
    <div style="position:absolute;right:1rem;top:0;">
        <a href="index">
            <button class="btn btn-secondary">戻る</button>
        </a>
    </div>
    <div class="two_column">
        <div class="one_column">

            <table class="table" id="list_table">

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
                    <th>トピックタイトル</th>
                    <td style="min-width:100px;">
                        <input type="text" name="title" value="{$Topic->title}" form="edit_submit">
                    </td>
                </tr>

                <tr>
                    <th>テキスト</th>
                    <td style="min-width:100px;">
                        <textarea name="text" form="edit_submit" cols="40">{$Topic->text}</textarea>
                    </td>
                </tr>


                <tr>
                    <th>遷移先URL</th>
                    <td style="min-width:100px;">
                        <input type="text" name="link_url" value="{$Topic->link_url}" form="edit_submit">
                    </td>
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

            </table>


            <form action="/cms/topic/edit" id="edit_submit" method="POST" class="d-flex justify-content-center">
                <input type="hidden" name="id" value="{$Topic->id}">
                <input type="hidden" name="priority" value="{$Topic->priority}">
                <button class="btn btn-primary m-4 rem-10" type="button" onclick="submit()">更新</button>
            </form>


        </div>

        <div class="side_column">
            {include file="common/quick_url.tpl"}
        </div>

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
        h3 {
            color: red;
        }

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

        .normal {
            border: solid 3px #FFF;
            background: #64dcc8;
            text-align: center;
            font-weight: bold;
        }

        .normal:hover {
            border: solid 3px #64dcc8;
        }

        .updown {
            height: 1.5rem;
            font-size: 0.8rem;
            line-height: 100%;
        }
    </style>
{/if}
