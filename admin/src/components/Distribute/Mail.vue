<template>
  <h3>メール管理</h3>
  <div class="contents">

    <el-row>
      <el-col :span="12">
        <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false">
        </el-date-picker>
        ～
        <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false">
        </el-date-picker>
        <el-button @click="loadMail" style="margin-left:20px;">再読み込み</el-button>
      </el-col>
      <el-col :span="8" style="margin-top:5px;">
        <!--el-radio-group v-model="filterRadio" @change="mailFilter">
          <el-radio :label="-1">未予約メール</el-radio>
          <el-radio :label="0">予約中メール</el-radio>
          <el-radio :label="1">配信済みメール</el-radio>
        </el-radio-group-->
      </el-col>
      <el-col :span="4" style="text-align:right">
        <!--el-button @click="openFromList" :disabled="!auth.From">差出人一覧</el-button-->
      </el-col>
    </el-row>

    <!-- メール一覧   --------------------------------------------------------------------------------------->

    <mail-table ref="mailTbl" :gridData="mailData" :changeFunction="selectMail"></mail-table>

    <hr>
    <!-- メール編集   --------------------------------------------------------------------------------------->

    <el-checkbox v-model="mailNewMode" @change="onChangeMailNewMode" :disabled="!auth.MailAdd">
      <span style="font-size:16px;padding-right: 1rem;">
        新規入力
      </span>
    </el-checkbox>
    <el-button type="danger" :disabled="!auth.MailDel || selectedMail === null" @click="onClickDelMailConfirm">
      メール削除
    </el-button>

    <div ref="mailFormArea" class="px-1">
      <el-row class="mb-2 px-2">
        <el-col :span="16">

          <el-row style="margin-bottom:5px;">
            <el-col :span="12">
              メール管理名
              <el-input v-model="mailName" style="width:90%" :disabled="selectedMail === null && !mailNewMode"></el-input>
              <transition name="el-zoom-in-top">
                <div v-show="mailNameErr !== ''" style="color:#b22222">{{ mailNameErr }}</div>
              </transition>
            </el-col>
            <el-col :span="12" style="font-size:18px;">
              備考<br/>
              <el-input v-model="mailNote" style="width:90%" :disabled="selectedMail === null && !mailNewMode"></el-input>
              <transition name="el-zoom-in-top">
                <div v-show="mailNoteErr !== ''" style="color:#b22222"> {{ mailNoteErr }}</div>
              </transition>
            </el-col>
          </el-row>

          <el-row style="margin-bottom:2px;">
            <el-col :span="24">
              件名<br/>
              <el-input v-model="mailSubject" style="width:95%" :disabled="selectedMail === null && !mailNewMode"></el-input>
              <transition name="el-zoom-in-top">
                <div v-show="mailSubjectErr !== ''" style="color:#b22222">
                  {{ mailSubjectErr }}
                </div>
              </transition>
            </el-col>
          </el-row>

          <el-row style="margin-bottom:2px;">
            <el-col :span="24" style="font-size:18px;">
              <span style="font-size:18px;">本文</span><br/>
              <el-input :disabled="selectedMail === null && !mailNewMode" style="width:95%" type="textarea" :rows="8" v-model="mailText">
              </el-input>
              <transition name="el-zoom-in-top">
                <div v-show="mailTextErr !== ''" style="color:#b22222">
                  {{ mailTextErr }}
                </div>
              </transition>
            </el-col>
          </el-row>

        </el-col>


        <el-col :span="8">
          差し込みコード
          <el-table :data="sashikomiData" border style="width: 100%" max-height="400">
            <el-table-column label="コード" width="200">
              <template #default="scope">
                <el-button style="font-size:16px;color:#1e90ff;" @click.native.prevent="onClickSashikomi(scope.$index)" type="default">
                  {{ scope.row.code }}
                </el-button>
              </template>
            </el-table-column>
            <el-table-column prop="discription" label="説明">
            </el-table-column>
          </el-table>
        </el-col>

      </el-row>

      <div class="d-flex">

        <el-button @click="checkMailInput" :disabled="!mailNewMode && selectedMail === null">入力確認</el-button>
        <transition name="el-zoom-in-center">
          <div class="mx-2 d-flex">

            <el-select v-model="preset" v-show="addBtnShow">
              <el-option v-for="preset in presetList" :label="preset.id+': '+preset.name" :value="preset.id"></el-option>
            </el-select>

            <el-button type="primary" :disabled="!auth.MailAdd" style="width:250px" v-show="addBtnShow" @click="onClickSendMail()">
              テスト送信
            </el-button>

            <el-button type="primary" v-if="mailNewMode" :disabled="!auth.MailAdd" style="width:250px" v-show="addBtnShow" @click="onClickAddMail()">
              新規登録
            </el-button>
            <el-button type="primary" v-else-if="!mailNewMode" :disabled="!auth.MailMod || selectedMail === null" style="width:250px" v-show="addBtnShow" @click="onClickModifyMail()">
              保存
            </el-button>

          </div>
        </transition>
      </div>

    </div>


    <!-- 差出人一覧   --------------------------------------------------------------------------------------->

    <el-dialog title="差出人一覧" v-model="fromDialogVisible" size="tiny">
      <el-row style="margin-bottom:5px;">
        <el-col :span="12" style="font-size:18px;">
          件数：{{ fromData.length }}
        </el-col>
        <el-col :span="12" style="text-align:right">
          <el-button type="danger" :disabled="!auth.FromDel || selectedFrom === null" @click="delFromConf">差出人削除
          </el-button>
        </el-col>
      </el-row>
      <el-table :data="fromData" border ref="fromTbl" style="width: 100%" @current-change="onChangeFrom" height="300" highlight-current-row>
        <el-table-column property="from_name" label="差出人名" align="center" :width="250"></el-table-column>
        <el-table-column property="from_address" label="差出人アドレス" header-align="center" align="center"></el-table-column>
      </el-table>
      <div ref="fromFormArea" style="background-color:#f0f8ff;padding-top:10px;padding-bottom:10px;">
        <el-checkbox v-model="fromNewCheck" @change="onChangeFromNewMode" :disabled="!auth.FromAdd" style="margin-left:10px;margin-bottom:10px;">
          <span style="font-size:16px;">新規入力</span>
        </el-checkbox>
        <el-row>
          <el-col :span="6">
            <el-input v-model="fName" placeholder="差出人名" style="width:140px;margin-left:10px;">
            </el-input>
          </el-col>
          <el-col :span="18">
            <el-input v-model="fMail" placeholder="差出人アドレス" style="width:350px;margin-left:30px;">
            </el-input>
          </el-col>
        </el-row>
        <div style="text-align: center;margin-top: 15px;">
          <el-button type="primary" v-if="fromNewCheck" :disabled="!auth.FromAdd" @click="checkConf(1)">
            追加
          </el-button>
          <el-button type="primary" v-else-if="!fromNewCheck" :disabled="!auth.FromMod || selectedFrom === null" @click="checkConf(2)">
            修正
          </el-button>
        </div>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import MailTable from './MailTable.vue'
import * as Define from '../../js/Define.js'
import Common from '../../js/Common.js'
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";

export default {
  name: 'email',
  mixins: [AppVue, AppLoader],
  components: {
    'mail-table': MailTable
  },

  methods: {
    logined() {
      this.setAuth('mail')
      this.loadMail()
      this.loadPreset()
    },
    loadMail() {
      this.mailData = []
      this.selectedMail = null
      var data = new FormData()
      data.append('type', 'LIST')
      data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      this.post(Define.API_MAIL, data).then((response) => {
        if (response.mailList.length === 0) {
          this.message('該当期間のメールはありません')
        } else {
          this.mailData = response.mailList
        }
      })
    },

    //----------------------------------
    // 新規メールモード変更
    onChangeMailNewMode(checked) {
      this.clearErrorMessage()
      this.addBtnShow = false
      if (this.mailNewMode) {
        this.$refs.mailFormArea.style.backgroundColor = '#ffffe0'
        this.clearMailForm()
      } else {
        this.$refs.mailFormArea.style.backgroundColor = '#f0f8ff'
      }
    },
    //----------------------------------
    // 設定内容取得
    getMailContent(type) {
      return {
        type: type,
        mail_name: this.mailName,
        mail_subject: this.mailSubject,
        mail_text: this.mailText,
        mail_notes: this.mailNote,
      }
    },
    //----------------------------------
    // 新規メール
    onClickAddMail() {
      if (this.checkMailInput()) {
        const data = this.getMailContent('ADD')
        data.type =
            this.post(Define.API_MAIL, data).then((response) => {
              this.message('メールを登録しました', 'success')
              this.clearMailForm()
              this.loadMail()
              this.mailNewMode = false
            })
      }
    },
    //----------------------------------
    // TEST送信
    onClickSendMail() {
      if (this.checkMailInput()) {
        const data = this.getMailContent('TEST')
        if (this.preset) {
          data.preset_id = this.preset
          this.post(Define.API_MAIL, data).then((response) => {
            const list = [];
            for (let key in response.list) {
              list.push(response.list[key].email)
            }
            this.message('メールを送信しました<br>' + list.join("<br>"), 'success'
            )
          })
        }
      }
    },

    //----------------------------------
    // メール修正更新
    onClickModifyMail() {
      if (this.checkMailInput()) {
        const data = this.getMailContent('MOD')
        data.id = this.selectedMail.id
        this.post(Define.API_MAIL, data).then((response) => {
          this.message('メール内容を保存しました', 'success')
          this.clearMailForm()
          this.loadMail()
        })
      }
    },
    //----------------------------------
    // メール削除
    onClickDelMailConfirm() {
      if (this.selectedMail === null) {
        this.message('メールが選択されていません')
      } else {
        this.confirm('メール削除', this.selectedMail.name + 'を削除してよろしいですか？', 'info', this.delMail)
      }
    },
    delMail() {
      this.post(Define.API_MAIL, {type: 'DEL', id: this.selectedMail.id,}).then((response) => {
        this.message('メールを削除しました:', 'success')
        this.selectedMail = []
        this.loadMail()
      })
    },
    //----------------------------------
    // メール選択
    selectMail(row, event, column) {
      if (this.mailNewMode) {
        this.clearMailSelect()
        return
      }
      this.selectedMail = row
      this.fromVal = row.from_id
      this.mailSubject = row.subject
      this.mailName = row.name
      this.mailNote = row.notes
      this.mailText = row.body
      this.addBtnShow = false
    },
    //----------------------------------
    // 差し込みコードクリック
    onClickSashikomi(index) {
      let code = this.sashikomiData[index].code
      this.mailText += code
    },
    //----------------------------------
    // 入力内容確認
    checkMailInput() {
      this.clearErrorMessage()
      //this.fromValErr = (this.fromVal === '') ? '差出人が選択されていません。' : ''
      this.mailSubjectErr = (this.mailSubject === '') ? '件名が入力されていません。' : ''
      this.mailNameErr = (this.mailName === '') ? 'メール管理名が入力されていません。' : ''
      this.mailTextErr = (this.mailText === '') ? '本文が入力されていません。' : ''

      // 件名差し込みコードチェック
      let sNotExists = this.checkSasikomi(this.mailSubject)
      this.mailSubjectErr = (sNotExists) ? '件名に使用できない差し込みコードが含まれています。【' + sNotExists + '】' : ''

      // 本文差し込みコードチェック
      let bNotExists = this.checkSasikomi(this.mailText)
      this.mailTextErr = (bNotExists) ? '本文に使用できない差し込みコードが含まれています。【' + bNotExists + '】' : ''

      if (this.mailSubjectErr !== '' || this.mailNameErr !== '' || this.mailTextErr !== '') {
        console.log(this.mailNameErr)
        console.log(this.mailSubjectErr)
        console.log(this.mailTextErr)
        this.addBtnShow = false
        return false
      }
      this.addBtnShow = true
      return true
    },
    checkSasikomi(str) {
      let strArr = str.split('%%')
      const codes = []
      for (let j = 0; j < this.sashikomiData.length; j++) {
        codes.push(this.sashikomiData[j].code)
      }

      for (let i = 0; i < strArr.length; i++) {
        if (i % 2) {
          let code = '%%' + strArr[i] + '%%'
          if (codes.indexOf(code) === -1) {
            return code
          }
        }
      }
      return false
    },
    //----------------------------------
    clearMailForm() {
      this.fromVal = ''
      this.mailSubject = ''
      this.mailText = ''
      this.mailNote = ''
      this.mailName = ''
      this.addBtnShow = false
      this.clearMailSelect()
    },
    //----------------------------------
    clearErrorMessage() {
      this.fromValErr = ''
      this.mailSubjectErr = ''
      this.mailTextErr = ''
      this.mailNoteErr = ''
      this.mailNameErr = ''
    },


    //----------------------------------------------------------------------------------------------------------------
    // 差出人

    // 差出人取得
    loadFrom() {
      this.fromData = []
      var data = new FormData()
      data.append('type', 1)
      data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      this.post(Define.API_FROM, data).then((response) => {
        this.fromData = response.data
        if (this.fromData.length === 0) {
          this.message('差出人は0件です')
        }
      })
    },

    openFromList() {
      this.loadFrom()
      this.fromDialogVisible = true
    },
    checkConf(num) {
      if (this.fName === '') {
        this.message('差出人名を入力してください')
        return
      }
      if (this.fMail === '') {
        this.message('差出人アドレスを入力してください')
        return
      }
      let name = this.fName
      let mail = this.fMail
      let cnt = this.fromData.length
      for (let i = 0; i < cnt; i++) {
        if (num === 2 && this.selectedFrom.from_num === this.fromData[i].from_num) {
          continue
        }
        if (name === this.fromData[i].from_name) {
          if (num === 1) {
            this.confirm('差出人追加', this.fromData[i].from_name + 'はすでに存在していますが追加してよろしいですか？', 'info', this.addFrom)
            return
          } else if (num === 2) {
            this.confirm('差出人修正', this.fromData[i].from_name + 'はすでに存在していますが修正してよろしいですか？', 'info', this.modFrom)
            return
          }
        } else if (mail === this.fromData[i].from_address) {
          if (num === 1) {
            this.confirm('差出人追加', this.fromData[i].from_address + 'はすでに存在していますが追加してよろしいですか？', 'info', this.addFrom)
            return
          } else if (num === 2) {
            this.confirm('差出人修正', this.fromData[i].from_address + 'はすでに存在していますが修正してよろしいですか？', 'info', this.modFrom)
            return
          }
        }
      }

      if (num === 1) {
        this.addFrom()
      } else if (num === 2) {
        this.modFrom()
      }
    },
    // 差出人追加
    addFrom() {
      var data = new FormData()
      data.append('type', 2)
      data.append('from_name', this.fName)
      data.append('from_address', this.fMail)
      data.append('create_user', this.$store.state.userInfo.user_num)
      this.post(Define.API_FROM, data).then(this.addFromRes)
    },
    addFromRes: function (response) {

      this.message('差出人を追加しました。', 'success')
      this.loadFrom()
      this.fromNewCheck = false
    },
    // 差出人修正
    modFrom() {
      var data = new FormData()
      data.append('type', 3)
      data.append('from_name', this.fName)
      data.append('from_address', this.fMail)
      data.append('from_num', this.selectedFrom.from_num)
      data.append('update_user', this.$store.state.userInfo.user_num)
      this.post(Define.API_FROM, data).then(this.modFromRes)
    },
    modFromRes: function (response) {
      if (typeof (response.data) === 'string') {
        this.alert('差出人の修正に失敗しました。' + response.data)
      } else {
        this.message('差出人を修正しました。', 'success')
        this.loadFrom()
      }
    },
    // 差出人削除確認
    delFromConf() {
      this.confirm('差出人削除', this.selectedFrom.from_name + 'を削除してよろしいですか？', 'info', this.delFrom)
    },
    // 差出人削除
    delFrom() {
      var data = new FormData()
      data.append('type', 4)
      data.append('from_num', this.selectedFrom.from_num)
      data.append('update_user', this.$store.state.userInfo.user_num)
      this.post(Define.API_FROM, data).then(this.delFromRes)
    },
    delFromRes: function (response) {
      this.message('差出人を削除しました。', 'success')
      this.loadFrom()
    },
    // 差出人テーブルチェンジイベント
    onChangeFrom(val) {
      if (this.fromNewCheck) {
        this.clearFromSelect()
        val = null
      }
      this.selectedFrom = val
      this.fName = !val ? '' : val.from_name
      this.fMail = !val ? '' : val.from_address
    },
    // 差出人テーブル選択クリア
    clearFromSelect() {
      this.$refs.fromTbl.setCurrentRow()
      this.selectedFrom = null
      this.fName = ''
      this.fMail = ''
    },
    onChangeFromNewMode(e) {
      if (e.target.checked) {
        this.$refs.fromFormArea.style.backgroundColor = '#ffffe0'
        this.clearFromSelect()
      } else {
        this.$refs.fromFormArea.style.backgroundColor = '#f0f8ff'
      }
    },
    clearMailSelect() {
      this.$refs.mailTbl.selectCancel()
      this.selectedMail = null
    },

    mailFilter() {
      this.$refs.mailTbl.onFilter(this.filterRadio)
    }
  },

  data() {
    return {
      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime()),
      filterRadio: -1,
      mailData: [],
      selectedMail: null,
      sashikomiData: [
        {code: '%%id%%', discription: 'UID'},
        {code: '%%nick%%', discription: 'ニックネーム'},
        {code: '%%mail%%', discription: 'メールアドレス'},
      ],

      preset: '',
      presetList: [],

      mailNewMode: false,
      addBtnShow: false,

      mailSubject: '',
      mailSubjectErr: '',
      mailName: '',
      mailNameErr: '',
      mailNote: '',
      mailNoteErr: '',
      mailText: '',
      mailTextErr: '',

      //errorDialogVisible: false,
      //errorMsg: '',
      //editMode: 1,
      //dialogTitle: '',

      fromVal: '',
      fromValErr: '',
      fromDialogVisible: false,
      fromData: [],
      selectedFrom: null,
      fName: '',
      fMail: '',
      fromNewCheck: false
    }
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
