<template>
  <h3>トピック管理</h3>
  <div class="contents">

    <el-row>
      <el-col :span="12">
        <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false">
        </el-date-picker>
        ～
        <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false">
        </el-date-picker>
        <el-button @click="loadTopic" style="margin-left:20px;">再読み込み</el-button>
      </el-col>
      <el-col :span="8" style="margin-top:5px;">
      </el-col>
      <el-col :span="4" style="text-align:right">

      </el-col>
    </el-row>

    <!-- トピック一覧   --------------------------------------------------------------------------------------->

    <topic-table ref="topicTbl" :gridData="topicList" :changeFunction="selectTopic"></topic-table>

    <hr>
    <!-- トピック編集   --------------------------------------------------------------------------------------->

    <el-checkbox v-model="topicNewMode" @change="onChangeTopicNewMode" :disabled="!auth.TopicEdit">
      <span style="font-size:16px;padding-right: 1rem;">
        新規入力
      </span>
    </el-checkbox>
    <el-button type="danger" :disabled="!auth.TopicEdit || selectedTopic === null" @click="onClickDelTopicConfirm">
      トピック削除
    </el-button>

    <div ref="topicFormArea" style="padding:5px;">
      <el-row style="margin-bottom:5px;">

        <el-col :span="6" style="font-size:18px;">
          掲載開始
          <el-date-picker v-model="beginAt" type="datetime" placeholder="前" :clearable="false" :disabled="selectedTopic === null && !topicNewMode">
          </el-date-picker>
          <transition name="el-zoom-in-top">
            <div v-show="beginAtErr !== ''" style="color:#b22222">{{ beginAtErr }}</div>
          </transition>
        </el-col>
        <el-col :span="1" style="font-size:18px;">
          ～
        </el-col>
        <el-col :span="6" style="font-size:18px;">
          掲載終了
          <el-date-picker v-model="closeAt" type="datetime" placeholder="後" :clearable="false" :disabled="selectedTopic === null && !topicNewMode">
          </el-date-picker>
          <transition name="el-zoom-in-top">
            <div v-show="closeAtErr !== ''" style="color:#b22222"> {{ closeAtErr }}</div>
          </transition>
        </el-col>

      </el-row>


      <el-row style="margin-bottom:5px;">
        <el-col :span="12" style="font-size:18px;">
          件名<br/>
          <el-input v-model="topicTitle" style="width:95%" :disabled="selectedTopic === null && !topicNewMode"></el-input>
          <transition name="el-zoom-in-top">
            <div v-show="topicTitleErr !== ''" style="color:#b22222">
              {{ topicTitleErr }}
            </div>
          </transition>
        </el-col>
        <el-col :span="12" style="font-size:18px;">
          <span style="color:#AAA;">En</span><br/>
          <el-input v-model="topicTitleEn" style="width:95%" :disabled="selectedTopic === null && !topicNewMode" placeholder="English"></el-input>
        </el-col>
      </el-row>


      <el-row style="margin-bottom:5px;">
        <el-col :span="12" style="font-size:18px;">
          本文<br/>
          <el-input :disabled="selectedTopic === null && !topicNewMode" style="width:95%" type="textarea" :rows="10" v-model="topicContent">
          </el-input>
          <transition name="el-zoom-in-top">
            <div v-show="topicContentErr !== ''" style="color:#b22222">
              {{ topicContentErr }}
            </div>
          </transition>
        </el-col>
        <el-col :span="12" style="font-size:18px;">
          <span style="color:#AAA;">En</span><br/>
          <el-input :disabled="selectedTopic === null && !topicNewMode" style="width:95%" type="textarea" :rows="10" v-model="topicContentEn" placeholder="English">
          </el-input>
        </el-col>
      </el-row>


      <el-button @click="checkTopicInput" :disabled="!topicNewMode && selectedTopic === null">
        入力確認
      </el-button>
      <transition name="el-zoom-in-center">
        <div style="margin: 0.5rem 0;">
          <el-button type="primary" :disabled="!auth.TopicEdit" style="width:250px" v-show="addBtnShow && topicNewMode" @click="onClickAddTopic">
            新規登録
          </el-button>
          <el-button type="primary" :disabled="!auth.TopicEdit || selectedTopic === null" style="width:250px" v-show="addBtnShow && !topicNewMode" @click="onClickModifyTopic">
            修正
          </el-button>
        </div>
      </transition>

    </div>


  </div>
</template>

<script>
import TopicTable from './TopicTable.vue'
import * as Define from '../../js/Define.js'
import Common from '../../js/Common.js'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'topic',
  mixins: [AppVue],
  components: {
    'topic-table': TopicTable
  },
  data() {
    return {
      since: new Date().setTime(new Date().getTime() - 86400 * 7 * 1000),
      until: new Date().setTime(new Date().getTime() + 86400 * 7 * 1000),
      topicList: [],
      selectedTopic: null,

      topicNewMode: false,
      addBtnShow: false,

      topicTitle: '',
      topicContent: '',
      topicTitleEn: '',
      topicContentEn: '',

      beginAt: null,
      closeAt: null,

      topicTitleErr: '',
      topicContentErr: '',
      beginAtErr: '',
      closeAtErr: '',

    }
  },
  methods: {
    logined() {
      this.setAuth('distribute')
      this.loadTopic()
      //this.loadFrom()
    },
    loadTopic() {
      this.topicList = []
      this.selectedTopic = null
      var data = new FormData()
      data.append('type', 'LIST')
      data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      this.post(Define.API_TOPIC, data).then((response) => {
        if (response.topicList.length === 0) {
          this.message('該当期間のトピックはありません')
        } else {
          this.topicList = response.topicList
        }
      })
    },

    //----------------------------------
    // 新規トピックモード変更
    onChangeTopicNewMode(checked) {
      this.clearErrorMessage()
      this.addBtnShow = false
      if (this.topicNewMode) {
        this.$refs.topicFormArea.style.backgroundColor = '#ffffe0'
        this.clearTopicForm()
      } else {
        this.$refs.topicFormArea.style.backgroundColor = '#f0f8ff'
      }
    },
    //----------------------------------
    // 新規トピック
    onClickAddTopic() {
      if (this.checkTopicInput()) {
        const data = this.getTopicContent('ADD')
        data.type =
            this.post(Define.API_TOPIC, data).then((response) => {
              this.message('トピックを登録しました', 'success')
              this.clearTopicForm()
              this.loadTopic()
            })
      }
    },
    getTopicContent(type) {
      return {
        type: type,
        title: this.topicTitle,
        content: this.topicContent,
        title_en: this.topicTitleEn,
        content_en: this.topicContentEn,
        begin_at: (new Date(this.beginAt)).toLocaleString(),
        close_at: (new Date(this.closeAt)).toLocaleString(),
      }
    },
    //----------------------------------
    // トピック編集
    onClickModifyTopic() {
      if (this.checkTopicInput()) {
        const data = this.getTopicContent('MOD')
        data.id = this.selectedTopic.id
        this.post(Define.API_TOPIC, data).then((response) => {
          this.message('トピックを修正しました', 'success')
          this.clearTopicForm()
          this.loadTopic()
        })
      }
    },
    //----------------------------------
    // トピック削除
    onClickDelTopicConfirm() {
      if (this.selectedTopic === null) {
        this.message('トピックが選択されていません')
      } else {
        this.confirm('トピック削除', '「' + this.selectedTopic.title + '」を削除してよろしいですか？', 'info', this.delTopic)
      }
    },
    delTopic() {
      this.post(Define.API_TOPIC, {type: 'DEL', id: this.selectedTopic.id,}).then((response) => {
        this.message('トピックを削除しました:', 'success')
        this.selectedTopic = []
        this.loadTopic()
      })
    },
    //----------------------------------
    // トピック選択
    selectTopic(row, event, column) {
      if (this.topicNewMode) {
        this.clearTopicSelect()
        return
      }
      this.selectedTopic = row
      this.topicTitle = row.title
      this.topicContent = row.content
      this.topicTitleEn = row.title_en
      this.topicContentEn = row.content_en
      this.beginAt = row.begin_at
      this.closeAt = row.close_at
      this.addBtnShow = false
    },

    //----------------------------------
    // 入力内容確認
    checkTopicInput() {
      this.clearErrorMessage()
      this.closeAtErr = (!this.closeAt) ? 'トピック掲載開始が入力されていません。' : ''
      this.beginAtErr = (!this.beginAt) ? 'トピック掲載終了が入力されていません。' : ''
      this.topicTitleErr = (this.topicTitle === '') ? '件名が入力されていません。' : ''
      this.topicContentErr = (this.topicContent === '') ? '本文が入力されていません。' : ''


      if (this.closeAtErr !== '' || this.beginAtErr !== '' || this.topicTitleErr !== '' || this.topicContentErr !== '') {
        this.addBtnShow = false
        return false
      }
      this.addBtnShow = true
      return true
    },

    //----------------------------------
    clearTopicForm() {
      this.beginAt = new Date().setTime(new Date().getTime());
      this.closeAt = new Date().setTime(new Date().getTime() + (86400 * 365) * 1000);
      this.topicTitle = ''
      this.topicContent = ''
      this.topicTitleEn = ''
      this.topicContentEn = ''
      this.addBtnShow = false
      this.clearTopicSelect()


    },
    //----------------------------------
    clearErrorMessage() {
      this.beginAtErr = ''
      this.closeAtErr = ''
      this.topicTitleErr = ''
      this.topicContentErr = ''
    },

    clearTopicSelect() {
      this.$refs.topicTbl.selectCancel()
      this.selectedTopic = null
    },

    topicFilter() {
      this.$refs.topicTbl.onFilter(this.filterRadio)
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
