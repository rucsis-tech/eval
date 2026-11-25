{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>アンケート・クイズ結果</h3>
    <div style="position:absolute;right:1rem;top:0;">
        <a href="index">
            <button class="btn btn-secondary">戻る</button>
        </a>
    </div>
    <div class="one_column">
        {literal}
            <div id="enquete_master">
                <div class="one_column">

                    <table class="table" style="max-width: 1000px; margin: 0 auto;">

                        <tr>
                            <th style="max-width: 100px;">ID</th>
                            <td style="min-width:100px;">
                                {{Enquete.id}}
                                <input type="hidden" name="id" value="{$Everyone.id}" class="form-control">
                            </td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;">タイトル</th>
                            <td style="min-width:100px;">
                                {{Enquete.title}}
                            </td>
                        </tr>

                        <tr>
                            <th style="max-width: 300px;">説明文</th>
                            <td style="min-width:100px;">
                                {{Enquete.description}}
                            </td>
                        </tr>

                        <tr>
                            <th style="max-width: 100px;">
                                回答
                            </th>
                            <td style="min-width:100px;">
                                <div v-for="(question,qindex) in Enquete.questions" class="rounded p-2 mb-4 border position-relative d-flex">

                                    <div class="my-1 mx-2" style="width:60%;">

                                        <div data-type="Q" :data-number="qindex+1" class="mb-2 rem-4 dragger" style="font-size:1.2rem;font-weight: bold;" @dragstart="dragstart" @dragover="dragover" @drop="drop" draggable="true">
                                            Q.{{qindex+1}}
                                        </div>

                                        {{question.caption}}

                                        <div class="mt-1">選択肢({{question.selects.length}}) 選択可能数({{question.selectable}})</div>


                                        <div class="mt-2 mx-0">
                                            <div v-for="(select,index) in question.selects" class="d-flex my-1 pl-20">
                                                <div :data-type="'Q-'+(qindex+1)" :style="'background-color:'+colors[question.selected.indexOf(index+1)]" class="mx-2 px-2 dragger">{{index+1}}</div>
                                                <div style="width:24rem;">{{select.option}}</div>
                                                <div style="width:5rem;text-align: right;">{{select.selected}}</div>
                                            </div>
                                        </div>

                                        <div class="mt-2">回答者({{question.answer_count}}人)</div>
                                        <div class="mt-1">
                                           <span v-for="(num,count) in question.counts" class="me-2">
                                                {{count}}項目選択者:{{num}}人
                                           </span>
                                        </div>

                                    </div>

                                    <div style="width:40%;">
                                        <canvas :id="'circle_'+qindex" width="350" height="600"></canvas>
                                    </div>

                                </div>

                            </td>
                       </tr>

                    </table>

            </div>
        {/literal}
    </div>
{/if}


{if $template_type=='foot'}
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <!-- FOOT ------------------------------------------------------------------------------------------------------->
    <script>
        const STATUSES = {json_encode(Enquete::STATUSES)}
        const EnqueteMaster = {json_encode($EnqueteMaster)}
    </script>
{literal}
    <script type="module">
        import {createApp} from "/js/admin/vue.esm-browser.prod.js"
        import Datetime from '/cms/jslib/vue/datetime.src.vue'
        import Toggle from '/cms/jslib/vue/toggle.src.vue'

        const content = createApp({
            components: {
                'toggle-switch': Toggle,
                'datetime-picker': Datetime,
            },

            mounted() {
                for(let i=0;i<this.Enquete.questions.length;i++) {
                    const question = this.Enquete.questions[i]
                    const labels = []
                    const data = []
                    for(let i in question.selected) {
                        const qn = question.selected[i]
                        labels.push(qn+'.'+question.selects[qn-1].option)
                        data.push(question.selects[qn-1].selected)
                    }



                    let context = document.querySelector("#circle_"+i).getContext('2d')
                    new Chart(context, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                backgroundColor: this.colors,
                                data: data
                            }]
                        },
                        options: {
                            responsive: false,
                            plugins: {
                              }
                        }
                    });
                }
            },
            computed: {},
            methods: {},
            data() {
                return {
                    STATUSES: STATUSES,
                    Enquete: EnqueteMaster,
                    message: '',
                    colors:[ '#E48661', '#41B883','#40D8FF', '#DD6B66',"#60A0C0","#EDE080","#E5C7EB","#B1D5BB","#E5E7EB","#E5E7CB","#C5E7EB",]
                }
            },
        }).mount('#enquete_master');
    </script>
{/literal}
{/if}

{if $template_type=='head'}
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <!-- HEAD ------------------------------------------------------------------------------------------------------->
    <script src="/js/cms/post.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
    <style>
        .one_column {
            margin: 2rem auto;
        }

        .dragger {
            border: 1px solid #ccc;
            text-align: center;
        }

        .btn-white {
            --bs-btn-color: #444;
            --bs-btn-bg: #fff;
            --bs-btn-border-color: #aaa;
            --bs-btn-hover-color: #000;
            --bs-btn-hover-bg: #bbb;
            --bs-btn-hover-border-color: #bbb;
            --bs-btn-focus-shadow-rgb: 130, 138, 145;
            --bs-btn-active-color: #000;
            --bs-btn-active-bg: #bbb;
            --bs-btn-active-border-color: #bbb;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(1, 1, 1, 0.125);
            --bs-btn-disabled-color: #000;
            --bs-btn-disabled-bg: #aaa;
            --bs-btn-disabled-border-color: #aaa;
        }
    </style>
{/if}