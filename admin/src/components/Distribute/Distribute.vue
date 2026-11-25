<template>
  <h3>メール配信予約</h3>
  <div class="contents">

    <!-- 一覧条件  ---------------------------------------------------------------------------->

    <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false">
    </el-date-picker>
    ～
    <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false">
    </el-date-picker>
    <el-button @click="loadDistribute" style="margin-left:20px;">再読み込み</el-button>


    <!-- 予約一覧  ---------------------------------------------------------------------------->

    <distribute-table ref="distributeTbl" :gridData="distributeList" :changeFunction="selectDistribute"></distribute-table>


    <hr>

    <!-- 新規・編集  ---------------------------------------------------------------------------->

    <el-checkbox v-model="newMode" @change="onChangeNewMode" :disabled="!auth.DistributeAdd">
      <span style="font-size:16px;padding-right: 1rem;">新規入力</span>
    </el-checkbox>
    <el-button type="danger" :disabled="!auth.DistributeDel || selectedDist === null || selectedDist.status !== 0" @click="delDistributeConfirm">
      予約取り消し
    </el-button>

    <div ref="distFormArea" style="padding:10px;padding-bottom:30px;background-color:#f0f8ff">
      <el-row style="margin-bottom:5px;">
        <el-col :span="8" style="font-size:18px;">
          配信予約名
          <el-input v-model="distributeName" style="width:90%" :disabled="!editMode"></el-input>
          <transition name="el-zoom-in-top">
            <div v-show="distributeNameErr !== ''" style="color:#b22222">
              {{ distributeNameErr }}
            </div>
          </transition>
        </el-col>
        <el-col :span="8" style="font-size:18px;">
          プリセット選択<br/>
          <el-select v-model="focusPresetId" placeholder="プリセットを選択" style="width:90%" :disabled="!editMode">
            <template v-for="item in presetList">
              <el-option :label="item.name" :value="item.id">
              </el-option>
            </template>
          </el-select>
          <transition name="el-zoom-in-top">
            <div v-show="presetValErr !== ''" style="color:#b22222">
              {{ presetValErr }}
            </div>
          </transition>
        </el-col>

        <el-col :span="8" style="font-size:18px;">
          配信日時<br/>
          <el-date-picker v-model="distributeAt" :editable="false" type="date" :disabled="!editMode" placeholder="日付">
          </el-date-picker>
          <el-time-select v-model="distributeTime" :editable="false" start="00:00" step="00:05" end="23:55" :disabled="!editMode" placeholder="時間">
          </el-time-select>
          <transition name="el-zoom-in-top">
            <div v-show="distributeDatetimeErr !== ''" style="color:#b22222">
              {{ distributeDatetimeErr }}
            </div>
          </transition>
        </el-col>
      </el-row>
      <el-row style="margin-bottom:5px;">
        <el-col :span="8" style="font-size:18px;">
          配信メール<br/>
          <el-select v-model="mailId" placeholder="配信するメールを選択" style="width:90%" :disabled="!editMode">
            <template v-for="item in mailList">
              <el-option :label="item.name" :value="item.id">
              </el-option>
            </template>
          </el-select>
          <transition name="el-zoom-in-top">
            <div v-show="mailValErr !== ''" style="color:#b22222">
              {{ mailValErr }}
            </div>
          </transition>
        </el-col>
      </el-row>
      <div style="margin-top:10px;text-align:center">
        <el-button type="primary" v-if="newMode" :disabled="!auth.DistributeAdd" style="width:250px" @click="addDistribute">
          新規予約
        </el-button>
        <el-button type="primary" v-else-if="!newMode" :disabled="!auth.DistributeMod || selectedDist === null || !editMode" style="width:250px" @click="modDistribute">
          修正
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
import * as Define from '../../js/Define.js'
import Common from '../../js/Common.js'
import DistributeTable from './DistributeTable.vue'
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";

export default {
  name: 'distribute',
  mixins: [AppVue, AppLoader],
  components: {
    'distribute-table': DistributeTable
  },
  data() {
    return {
      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime() + 3600 * 1000 * 24 * 7),

      distributeList: [],
      presetList: [],
      mailList: [],

      selectedDist: null,
      newMode: false,
      editMode: false,
      focusPresetId: '',
      presetValErr: '',
      distributeName: '',
      distributeNameErr: '',
      distributeAt: new Date(),
      distributeTime: "00:00",
      distributeDatetimeErr: '',
      mailId: '',
      mailValErr: '',


      distPickerOption: {
        type: 'min',
        week: ['月', '火', '水', '木', '金', '土', '日'],
        month: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        format: 'YYYY-MM-DD HH:mm:00',
        placeholder: '日時を選択',
        inputStyle: {
          'display': 'inline-block',
          'padding': '6px',
          'line-height': '22px',
          'font-size': '13px',
          'border': '1px solid #BFCED9',
          'border-radius': '5px',
          'color': '#5F5F5F',
          'width': '90%'
        },
        color: {
          header: '#607D8B',
          headerText: '#fff'
        }
      }
    }
  },

  methods: {
    logined() {
      this.setAuth('distribute')
      this.loadMail()
      this.loadPreset()
      this.loadDistribute()
    },
    loadDistribute() {
      Common.loadStart()
      this.distributeList = []
      this.clearDistributeSelect()
      const data = new FormData()
      data.append('type', 1)
      data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      this.post(Define.API_DISTRIBUTE, data).then((response) => {
            if (response.distributeList.length === 0) {
              this.message('該当期間の配信予約はありません')
            } else {
              this.distributeList = response.distributeList
            }
          }
      )
    },
    //--------------------------------------------
    // 新規追加
    onChangeNewMode(e) {
      this.clearDistErr()
      if (this.newMode) {
        this.$refs.distFormArea.style.backgroundColor = '#ffffe0'
        this.editMode = true
        this.clearDistForm()
        this.clearDistributeSelect()
      } else {
        this.$refs.distFormArea.style.backgroundColor = '#f0f8ff'
        if (this.selectedDist !== null && this.selectedDist.distribute_status === '0') {
          this.editMode = true
        } else {
          this.editMode = false
        }
      }
    },
    addDistribute() {
      if (!this.formCheck()) {
        return
      }
      const data = new FormData()
      data.append('type', 'ADD')
      data.append('name', this.distributeName)
      data.append('focus_preset_id', this.focusPresetId)
      data.append('mail_id', this.mailId)
      data.append('distribute_at', Common.dateFormat(this.distributeAt, 'YYYY-MM-DD') + ' ' + this.distributeTime + ':00')
      this.post(Define.API_DISTRIBUTE, data).then((response) => {

        this.message('配信を予約しました', 'success')
        this.loadDistribute()
      })
    },
    //--------------------------------------------
    // 編集
    modDistribute() {
      if (!this.formCheck()) {
        return
      }
      var data = new FormData()
      data.append('type', 'MOD')
      data.append('id', this.selectedDist.id)
      data.append('name', this.distributeName)
      data.append('focus_preset_id', this.focusPresetId)
      data.append('mail_id', this.mailId)
      data.append('distribute_at', Common.dateFormat(this.distributeAt, 'YYYY-MM-DD') + ' ' + this.distributeTime + ':00')

      this.post(Define.API_DISTRIBUTE, data).then((response) => {
        this.message('配信予約を修正しました', 'success')
        this.loadDistribute()
      })
    },

    //--------------------------------------------
    // 削除確認
    delDistributeConfirm() {
      if (this.selectedDist === null) {
        this.message('配信予約が選択されていません')
        return
      }
      this.confirm('配信予約取り消し', this.selectedDist.name + 'の予約を取り消してよろしいですか？', 'info', this.delDistribute)
    },
    delDistribute() {
      var data = new FormData()
      data.append('type', 'DEL')
      data.append('id', this.selectedDist.id)
      data.append('update_user', this.$store.state.userInfo.user_num)
      this.post(Define.API_DISTRIBUTE, data).then((response) => {
        if (typeof (response.data) === 'string') {
          this.alert('配信予約取消に失敗しました。' + response.data)
        } else {
          this.message('配信予約を取り消しました', 'success')
          this.loadDistribute()
        }
      })
    },
    //--------------------------------------------
    // 入力内容確認
    formCheck() {
      if (this.distributeName === '') {
        this.message('配信予約名が入力されていません')
        return false
      }
      if (this.distributeAt === '') {
        this.message('配信日が入力されていません')
        return false
      }
      if (this.distributeTime === '') {
        this.message('配信時間が入力されていません')
        return false
      }
      if (this.distributeTime === undefined) {
        this.message('配信時間はリストから選択してください')
        return false
      }
      if (this.focusPresetId === '') {
        this.message('プリセットが選択されていません')
        return false
      }
      if (this.mailId === '') {
        this.message('配信メールが選択されていません')
        return false
      }
      return true
    },


    selectDistribute(row, event, column) {
      if (this.newMode) {
        this.clearDistributeSelect()
        return
      }
      this.selectedDist = row
      this.focusPresetId = row.focus_preset_id
      this.distributeName = row.name
      this.distributeAt = Common.dateFormat(row.distribute_at, 'YYYY-MM-DD')
      this.distributeTime = Common.dateFormat(row.distribute_at, 'hh:mm')

      this.mailId = row.mail_id
      if (row.status === 0) {
        this.editMode = true
      } else {
        this.editMode = false
      }
    },
    clearDistributeSelect() {
      if (this.$refs.distributeTbl) {
        this.$refs.distributeTbl.selectCancel()
      }
      this.selectedDist = null
    },

    clearDistForm() {
      this.focusPresetId = ''
      this.distributeName = ''
      this.distributeAt = ''
      this.distributeTime = ''
      this.mailId = ''
      this.clearDistErr()
    },
    clearDistErr() {
      this.presetValErr = ''
      this.distributeNameErr = ''
      this.distributeDatetimeErr = ''
      this.mailValErr = ''
    },

    //--------------------------------------------
    loadMail() {
      this.mailData = []
      this.selectedMail = null
      var data = new FormData()
      data.append('type', 'LIST')
      data.append('since', Common.dateFormat(new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 14), 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(new Date().setTime(new Date().getTime()), 'YYYY-MM-DD'))
      this.post(Define.API_MAIL, data).then((response) => {

        if (response.mailList.length === 0) {
          this.message('2週間以内に登録されたメールがありません')
        } else {
          this.mailList = response.mailList
        }
      })
    },

    customClass(row, col) {
      return 'custom-picker'
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
