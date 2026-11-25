{if $template_type=='body'}
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <!-- BODY ------------------------------------------------------------------------------------------------------->
    <h3>アンケート・クイズ設定</h3>
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
                        <th style="max-width: 100px;">状態</th>
                        <td style="min-width:100px;">
                            <select v-model="Enquete.status" name="status">
                                <option v-for="(status,key) in STATUSES" :value="key">
                                    {{status}}
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 300px;">タイトル</th>
                        <td style="min-width:100px;">
                            <input v-model="Enquete.title" type="text" name="vote_score" class="form-control w-100">
                        </td>
                    </tr>

                                        <tr>
                        <th style="max-width: 300px;">説明文</th>
                        <td style="min-width:100px;">
                            <textarea v-model="Enquete.description" class="form-control w-100" rows="4"></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 300px;">開始日時</th>
                        <td style="min-width:100px;">
                           <datetime-picker v-model="Enquete.start_at" clock="1" sec="1" caption="" class="rem-18"></datetime-picker>
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 300px;">終了日時</th>
                        <td style="min-width:100px;">
                           <datetime-picker v-model="Enquete.end_at" clock="1" sec="1" caption="" class="rem-18 p-0"></datetime-picker>
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 300px;">全回答時獲得スコア</th>
                        <td style="min-width:100px;">
                            <input v-model="Enquete.score" type="number" name="vote_score" class="form-control rem-8">
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 100px;">回答集計を公開</th>
                        <td style="min-width:100px;">
                            <toggle-switch v-model="Enquete.publish"></toggle-switch>
                        </td>
                    </tr>

                    <tr>
                        <th style="max-width: 100px;">
                        設問
                        </th>
                        <td style="min-width:100px;">
                        <div v-for="(question,qindex) in Enquete.questions" class="rounded p-2 mb-4 border position-relative">
                            <button class="btn btn-sm btn-secondary mx-2 end-0 position-absolute" @click="delQuestion(qindex)">設問削除</button>
                            <div data-type="Q" :data-number="qindex+1" class="mb-2 rem-4 dragger" style="font-size:1.2rem;font-weight: bold;" @dragstart="dragstart" @dragover="dragover" @drop="drop" draggable="true">
                                Q.{{qindex+1}}
                            </div>

                            <input v-model="question.caption" type="text" class="w-100 mb-2">

                            <div class="d-flex my-1 mx-2">
                                <div class="mt-1">選択肢({{question.selects.length}})</div>
                                <button class="btn btn-sm btn-secondary py-0 mx-2" @click="addSelect(question)">追加</button>
                                <div class="mt-1 mx-2">選択可能数</div>
                                <input v-model="question.selectable" type="number" class="form-control rem-4" style="padding:0.2rem 0.2rem 0.2rem 1rem;">
                                <button v-show="question.selects.length>0" class="btn btn-sm btn-white mx-4" @click="question.hide=!question.hide">{{question.hide?'選択肢を表示':'選択肢を隠す'}}</button>
                            </div>

                            <div class="mt-2 mx-0" v-show="question.hide!==true">
                                <div v-for="(select,index) in question.selects" class="d-flex my-1">
                                    <div :data-type="'Q-'+(qindex+1)" :data-number="index+1"  class="mx-2 px-2 dragger" @dragstart="dragstart" @dragover="dragover" @drop="drop" draggable="true">{{index+1}}</div>
                                    <input v-model="select.option" type="text" class="w-50" placeholder="選択肢">
                                    <toggle-switch v-model="select.correct" class="m-1"></toggle-switch>
                                    <button class="btn btn-sm btn-secondary mx-2" @click="delSelect(qindex,index)">削除</button>
                                </div>
                            </div>
                            <div v-show="question.hide===true" style="margin-left:2rem;">
                                ...
                            </div>

                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" @click="addQuestion">設問追加</button>
                        </div>
                        </td>
                    </tr>



                </table>

                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary m-4 rem-10" type="button" @click="post">更新</button>
                </div>
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
            },
            computed: {},
            methods: {
                // -------------------------------------------------------------
                // 設問追加
                addQuestion: function(){
                    if (!this.Enquete.questions) this.Enquete.questions = []
                    this.Enquete.questions.push({
                        caption : '',
                        selectable: 1,
                        selects:[],
                    })
                },
                delQuestion: function(q_index){
                    if (window.confirm('Q.'+(q_index+1)+' を削除しますか？')){
                       this.Enquete.questions.splice(q_index, 1)
                    }
                },
                // -------------------------------------------------------------
                // 選択肢追加
                addSelect: function(question){
                    question.hide=false
                    question.selects.push({
                        option: '',
                        correct: true,
                    })
                },
                delSelect: function(q_index,index){
                    if (window.confirm('設問.'+(index+1)+' を削除しますか？')){
                       this.Enquete.questions[q_index].selects.splice(index, 1)
                    }
                },
                // -------------------------------------------------------------
                // ドラッグコントロール
                dragstart: function(event){
                   const type = event.currentTarget.getAttribute('data-type')
                   const number = event.currentTarget.getAttribute('data-number')
                   const dataList = event.dataTransfer.items;
                   dataList.add(type, "type")
                   dataList.add(number, "number")
                },
                dragover: function(event){
                    event.preventDefault()
                    event.dataTransfer.dropEffect = 'copy'
                },
                drop: function(event){
                    console.log("Drop");
                    const srcType = event.dataTransfer.getData("type");
                    if (srcType === event.currentTarget.getAttribute('data-type')) {
                        const srcIndex = event.dataTransfer.getData("number") - 1
                        const dstIndex = event.currentTarget.getAttribute('data-number') - 1
                        if (srcType === 'Q') {
                            // 設問入れ替え
                            this.Enquete.questions = this.moveArrayItem(this.Enquete.questions , srcIndex, dstIndex)
                        } else {
                            // 選択肢入れ替え
                            const questionIndex = srcType.split('-')[1] - 1
                            this.Enquete.questions[questionIndex].selects = this.moveArrayItem(this.Enquete.questions[questionIndex].selects , srcIndex, dstIndex)
                        }
                        console.log(srcIndex,'->>',dstIndex)

                    }
                },
                moveArrayItem:　function (array, srcIndex, dstIndex) {
                    // 取り出す
                    const item = array.splice(srcIndex, 1)[0]
                    if (dstIndex !== srcIndex+1) {
                        if (srcIndex < dstIndex) dstIndex--;
                    }
                    // 入れる
                    array.splice(dstIndex, 0,item)
                    return array
                },
                // -------------------------------------------------------------
                // データ送信
                post:function(){

                    postJsonRequest('',this.Enquete).then(()=>{
                        document.location.href = 'index'
                    })

                }

            },
            data() {
                return {
                    STATUSES: STATUSES,
                    Enquete: EnqueteMaster,
                    message: '',
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
    <style>
        .one_column {
            margin: 2rem auto;
        }
        .dragger{
            cursor:grab;
            border:1px solid #ccc;
            text-align:center;
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