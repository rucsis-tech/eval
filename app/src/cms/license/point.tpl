{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <style>
        .payoff_color {
            color: #A44 !important;
        }
    </style>
{/if}

{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>ポイント商品集計</h3>
    <form method="POST">
        <div class="contents d-flex flex-wrap">

            <div class="mx-2">
                <div>
                    集計期間
                </div>
                <div class="d-flex flex-wrap">
                    <div class="input-group date mb-3 rem-14 px-2" id="datetimeStart" data-target-input="nearest">
                        <label class="pt-2 pr-2">開始：</label>
                        <input type="text" name="start" class="form-control datetimepicker-input" value="{$start}"/>
                        <div class="input-group-text" data-toggle="datetimepicker">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>

                    <div class="input-group date mb-3 rem-14 px-2" id="datetimeEnd" data-target-input="nearest">
                        <label class="pt-2 pr-2">終了：</label>
                        <input type="text" name="end" class="form-control datetimepicker-input" value="{$end}"/>
                        <div class="input-group-text" data-target="#datetimeEnd" data-toggle="datetimepicker">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-2">

            </div>

            <div class="mx-2">
                <br>
                <input type="submit" class="btn btn-primary mx-2" name="submit" value="再読込み">
                <input type="submit" class="btn btn-secondary mx-2" name="submit" value="CSVダウンロード">
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th style="width:40rem;">記事タイトル</th>
            <th style="width:12rem;">対象レース</th>
            <th>商品タイプ</th>
            <th>著者</th>
            <th>消費ポイント</th>
            <th>購入者数</th>
        </tr>
        </thead>
        <tbody class="table-group-divider">
        {foreach $list as $item}
            <tr>
                <td class="text-center">{$item.pid}</td>
                <td class="text-left payoff{($item.payoff>0)?'_color':''}">{$item.title}</td>
                <td class="text-left">{$item.race}</td>
                <td class="text-left">{$item.type}</td>
                <td class="text-left">{$item.author}</td>
                <td class="text-end">{$item.point}</td>
                <td class="text-end">{$item.cnt}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script src="{"/cms/jslib/datetimepicker.js"|filetime}"></script>
{literal}
    <script>
        createDatetimepicker({id: 'datetimeStart'});
        createDatetimepicker({id: 'datetimeEnd'});


    </script>
{/literal}

{/if}