{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3></h3>Evalレースデータ</h3>
    <div style="">
        <div class="tab_block">
            <h4>
                {$WindexRace->race_code}<br>
                {$PLACES[$place]} {$WindexRace->race_name}
            </h4>

            <div style="margin-right: 20px; font-weight:bold;">{$day} </div>
            <table class="table table-sm text-center">
                <thead>
                <tr>
                    <th>馬番</th>
                    <th>枠番</th>
                    <th>馬名</th>

                    <th>単勝確率</th>
                    <th>連対確率</th>
                    <th>複勝確率</th>
                    <th>単勝期待値</th>
                    <th>複勝期待値</th>

                    <th>着順</th>
                    <th>異常</th>
                </tr>
                </thead>
                <tbody>
                {foreach $WindexUmas as $WindexUma}
                    <tr>
                        <td>{$WindexUma->umaban}</td>
                        <td>{$WindexUma->wakuban}</td>
                        <td class="text-start">{$WindexUma->name}</td>

                        <td>{$WindexUma->tansho}</td>
                        <td>{$WindexUma->rentai}</td>
                        <td>{$WindexUma->fukusho}</td>
                        <td>{$WindexUma->tan_kitai}</td>
                        <td>{$WindexUma->fuku_kitai}</td>

                        <td>{$WindexUma->chakujun}</td>
                        <td>{$WindexUma->status}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

        </div>

        {if !empty($PointLogs)}
            <hr>
            <a id="point"></a>
            <div style="max-width: 1000px;margin: 16px auto;display:flex;">
                <div>
                    <h3>ポイント消費済会員</h3>
                    [oid:{$origin_id}] {count($PointLogs)}件
                    {foreach $PointLogs as $PointLog}
                        <div>
                            {($PointLog->premium_point+$PointLog->gift_point)|abs}pt {$PointLog->uid}
                        </div>
                    {/foreach}
                </div>

                <div class="m-4">
                    {if !empty($reason)}
                        <button class="btn btn-secondary m-1" disabled>特別付与済</button>
                        <div>特別付与理由：{$reason}</div>
                    {else}
                        特別付与理由：<input id="reason" type="text" value="{$reason}" class="form-control rem-20 m-1">
                        <button class="btn btn-danger mx-1" onclick="compensate()">特別付与</button>
                    {/if}
                </div>
            </div>
        {/if}

    </div>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script>
        const origin_id = {$origin_id}
    </script>
{literal}
    <script>
        /**
         * 特別付与実施
         */
        function compensate() {
            const reason = document.getElementById('reason').value;
            if (reason.length === 0) return
            if (window.confirm('[ ' + reason + ' ] 特別付与を実施しますか？')) {
                postJsonRequest('/cms/wp/_compensate.php', {origin_id: origin_id, reason: reason}).then((response) => {
                    console.log(response)
                    document.location.reload();
                })
            }
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

    </style>
{/if}