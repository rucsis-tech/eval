{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>Evalベースデータ</h3>
    <div style="">
        <!-- 開催日タブを押して表示を切り替えたい -->
        <div class="tab_block flex">
            <div style="margin-right: 20px; font-weight:bold;">開催日程</div>
            {foreach $days as $day => $info}
                <a href="?target_day={$day}">
                    <div class="tab_btn" style="cursor: pointer;{($target_day===$day)?'color:white;background-color:#66E;':''}">{$info.day}</div>
                </a>
            {/foreach}
            <div style="display: flex; align-items: center; margin-left: auto; margin-right: 20px;">
                <div>日付指定</div>
                <div id="jump_at" data-value="{$WindexDay->start_at}" data-clock="0" class="input-group date rem-12 mx-2" onchange="onChangeJumpAt"></div>
            </div>
        </div>
        <div class="tab_block">

            <div style="position:relative;border-radius: 4px;border:solid 2px #DDD;padding:8px;margin:16px 8px;">

                <form method="POST" style="display:flex;justify-content: flex-end">
                    <div style="margin-right:auto;"><b>{$day}用</b></div>
                    <div>表示開始日時</div>
                    <div id="start_at" data-value="{$WindexDay->start_at}" data-clock="1" class="input-group date rem-12 mx-2"></div>
                    <input type="submit" value="更新">
                </form>

                <hr>

                <form action="upload.php" method="POST" enctype="multipart/form-data" style="">
                    <input type="hidden" name="target_day" value="{$target_day}">

                    <div style="margin-right: 10px;">
                        <b>ベースデータ</b><br>
                        <input type="file" name="basedata_uma" multiple style="width:80%;margin-bottom: 6px;"><br>
                    </div>

                    <input type="submit" class="btn btn-primary submit" value="アップロード">
                </form>
            </div>
        </div>


        <!--div class="tab_block">
            <div>
                開催場所:
                {foreach $Target AS $Kaisai}
                    {$Kaisai->info.keibajo}
                {/foreach}
            </div>
            <div>
                Win5：
            </div>
        </div-->


        <div class="tab_block">
            <h4>{$day}のレース</h4>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>場所</th>
                    <th>レースコード</th>
                    <th>レース名</th>
                    <!--th>中止</th-->
                    <!--th>ポイント</th-->
                    <th style="text-align: center;">会員利用</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {foreach $WindexRaces as $WindexRace}
                    <tr>
                        <td>{$PLACES[$WindexRace->place]}</td>
                        <td><a href="race?race_code={$WindexRace->race_code}">{$WindexRace->race_code}</a></td>
                        <td>{$WindexRace->race_name}</td>

                        <!--td><input id="s{$WindexRace->race_code}" type="checkbox" value="1" {$WindexRace->status?'checked':''} onchange="onChange(this)" style="scale: 180%;margin-top: 6px;"></td-->
                        <!--td><input id="p{$WindexRace->race_code}" type="number" value="{$WindexRace->need_points}" onchange="onChange(this)" onkeydown="onChange(this)" class="text-end"></td-->
                        <td style="text-align: center;">
                            {if $WindexRace->point_count}
                                <a href="race?race_code={$WindexRace->race_code}#point">有</a>
                            {/if}
                        </td>
                        <td style="text-align: center;">
                            <button id="{$WindexRace->race_code}" class="btn btn-sm btn-primary" onclick="onClickUpdate(this)" disabled>修正</button>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

        </div>


    </div>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
{literal}
    <script src="/cms/jslib/datetimepicker.js"></script>
    <script type="text/javascript">
        createDatetimepicker({id: 'start_at'});
        const jdp = createDatetimepicker({id: 'jump_at'});
        jdp.subscribe('change.td', (event) => {
            let day = jdp.dates.formatInput(event.date);
            day = day.split('/').join('-')
            window.location.href = '?target_day=' + day;
        })


        function onClickUpdate(btn) {
            const id = btn.getAttribute("id");
            const status = document.getElementById('s' + id).checked;
            const point = document.getElementById('p' + id).value;

            postJsonRequest('_update', {
                target: 'race',
                race_code: id,
                status: status ? 1 : 0,
                point: point,
            }).then((response) => {
                btn.setAttribute("disabled", true);

            }).catch((error) => {

            });

        }

        function onChange(input) {
            const id = input.getAttribute("id").substring(1);
            const btn = document.getElementById(id);
            btn.removeAttribute("disabled");
        }


        function onChangeJumpAt(input) {
            console.log(input);
        }
    </script>
{/literal}

{/if}



{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <script src="/js/cms/post.js"></script>
    <style>
        .tab_block {
            width: 80%;
            justify-content: center;
            margin: 0 auto 30px;
        }

        .flex {
            display: flex;
        }

        .submit {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }

        .tab_btn {
            font-size: 14px;
            width: 100px;
            border: 2px solid #444;
            border-radius: 4px;
            margin-right: 10px;
            line-height: 2em;
            padding: 2px 0;
            text-align: center;
        }

        td {
            padding: 4px 1rem 0 1rem !important;
        }
    </style>
{/if}