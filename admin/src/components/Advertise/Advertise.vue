<template>
  <h3>広告コード一覧</h3>
  <div class="contents">

    <!-- 一覧 ------------------------------------------------------------------------------>
    <ad-table ref="adTbl" :gridData="advertiseList" :loadFunction="loadAdvertise" :changeFunction="selectAdvertise" :auth="auth">
    </ad-table>

  </div>

  <div class="contents">

    <!-- 編集 ------------------------------------------------------------------------------>

    <el-checkbox v-model="advertiseNewCheck" @change="onChangeAdvertiseNewMode" :disabled="!auth.AdvertiseAdd"><span style="font-size:16px;">新規入力</span></el-checkbox>
    <el-button type="danger" :disabled="!auth.AdvertiseDel || selectedAdvertise === null" @click="delAdvertiseConfirm">広告コード削除</el-button>
    <hr>

    <div ref="advertiseFormArea" style="padding:5px;">

      <el-row style="margin-bottom:5px;">
        <el-col :span="4" style="font-size:18px;">
          媒体コード<br/>
          <el-select v-model="adMediaId" @change="onSelectMedia" placeholder="媒体を選択" style="width:90%" :disabled="selectedAdvertise === null && !advertiseNewCheck">
            <template v-for="item in mediaList">
              <el-option :label="item.name" :value="item.id"></el-option>
            </template>
          </el-select>
        </el-col>

        <el-col :span="4" style="font-size:18px;">
          コード<span style="font-size:12px">(数字3文字)</span><br/>
          <span style="width:30%">{{ selectedMediaCode }}</span>
          <el-input v-model="adChildCode" style="width:100px" :disabled="selectedAdvertise === null && !advertiseNewCheck"></el-input>
        </el-col>

        <el-col :span="6" style="font-size:18px;">
          広告名称<br/>
          <el-input v-model="adName" style="width:90%" :disabled="selectedAdvertise === null && !advertiseNewCheck"></el-input>
        </el-col>

        <el-col :span="6" style="font-size:18px;">
          掲載開始日<br/>
          <el-date-picker :disabled="selectedAdvertise === null && !advertiseNewCheck" v-model="adDate" type="date" placeholder="日付を選択" :clearable="false" :picker-options="pickerOptions">
          </el-date-picker>
        </el-col>

        <el-col :span="4" style="font-size:18px;">
          掲載料金<br/>
          <el-input v-model="adPrice" type="number" style="width:80%" :disabled="selectedAdvertise === null && !advertiseNewCheck"></el-input>&nbsp;円
        </el-col>
      </el-row>

      <div style="font-size:18px;">
        備考<br/>
        <el-input type="textarea" v-model="adNotes" style="width:50%;height:60px;" :disabled="selectedAdvertise === null && !advertiseNewCheck"></el-input>
      </div>

      <div style="text-align:center">
        <el-button type="primary" v-if="advertiseNewCheck" :disabled="!auth.AdvertiseAdd" style="width:250px" @click="addAdvertise()">
          新規登録
        </el-button>
        <el-button type="primary" v-else-if="!advertiseNewCheck" :disabled="!auth.AdvertiseMod || selectedAdvertise === null" style="width:250px" @click="modAdvertise()">
          修正
        </el-button>
      </div>
    </div>

    <!-- 媒体編集ダイアログ ------------------------------------------------------------------------------>
    <el-row style="margin-bottom:5px;">
      <el-col :span="24" style="text-align:right">
        <el-button @click="openMediaList" :disabled="!auth.Media">媒体一覧</el-button>
      </el-col>
    </el-row>

    <el-dialog title="媒体一覧" v-model="mediaDialogVisible" size="small">

      <el-row style="margin-bottom:5px;">
        <el-col :span="12" style="font-size:18px;">
          件数：{{ mediaList.length }}
        </el-col>
        <el-col :span="12" style="text-align:right">
          <el-button type="danger" :disabled="!auth.MediaDel || selectedMedia === null" @click="delMediaConf">
            媒体削除
          </el-button>
        </el-col>
      </el-row>

      <el-table :data="mediaList" border ref="mediaTbl" style="width: 100%" @current-change="onChangeMedia" height="300" highlight-current-row>

        <el-table-column property="code" label="コード" align="center" :width="80"></el-table-column>
        <el-table-column property="type" label="種別" header-align="center" :width="120" align="center"></el-table-column>
        <el-table-column property="name" label="媒体名称" header-align="center" align="left"></el-table-column>
        <el-table-column property="url" label="参考URL" header-align="center" align="left"></el-table-column>

        <el-table-column property="note" label="" header-align="center" align="center">
          <template #default="scope">
            <el-button style="color:#4169e1;font-weight:bold" type="primary">備考</el-button>
          </template>
        </el-table-column>

      </el-table>

      <el-checkbox v-model="mediaNewCheck" @change="onChangeMediaNewMode" style="margin-top:10px;" :disabled="!auth.MediaAdd">
        <span style="font-size:16px;">新規入力</span>
      </el-checkbox>

      <div ref="mediaFormArea" style="background-color:#f0f8ff;padding:10px;">
        <el-row>
          <el-col :span="8">
            <el-input v-model="mediaCode" placeholder="媒体コード(英字2文字)" style="width:90%;"></el-input>
          </el-col>

          <el-col :span="8">
            <el-select v-model="category" placeholder="種別を選択">
              <template v-for="item in mediaCategories">
                <el-option :label="item" :value="item"></el-option>
              </template>
            </el-select>
          </el-col>

          <el-col :span="8">
            <el-input v-model="mediaName" placeholder="媒体名称" style="width:90%;"></el-input>
          </el-col>
        </el-row>

        <el-row style="margin-top:10px;">
          <el-col :span="12">
            <el-input v-model="mediaUrl" placeholder="参考URL" style="width:90%;"></el-input>
          </el-col>
          <el-col :span="12">
            <el-input v-model="mediaNote" placeholder="備考(50文字以内)" style="width:90%;"></el-input>
          </el-col>
        </el-row>

        <div style="text-align: center;margin-top: 15px;">
          <el-button type="primary" v-if="mediaNewCheck" style="width:150px;" :disabled="!auth.MediaAdd" @click="checkConfMedia(1)">
            追加
          </el-button>
          <el-button type="primary" v-else-if="!mediaNewCheck" style="width:150px;" :disabled="!auth.MediaMod || selectedMedia === null" @click="checkConfMedia(2)">
            修正
          </el-button>
        </div>

      </div>
    </el-dialog>

  </div>
</template>

<script>
import AdvertiseTable from './AdvertiseTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'top',
  mixins: [AppVue],
  components: {
    'ad-table': AdvertiseTable
  },
  data() {
    return {
      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime()),
      pickerOptions: {
        shortcuts: [{
          text: '今日',
          onClick(picker) {
            picker.$emit('pick', new Date())
          }
        },
          {
            text: '先月',
            onClick(picker) {
              new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 30)
              picker.$emit('pick', new Date())
            }
          },
          {
            text: '去年',
            onClick(picker) {
              new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 365)
              picker.$emit('pick', new Date())
            }
          }]
      },
      advertiseNewCheck: false,
      advertiseList: [],
      errorDialogVisible: false,
      errorMsg: '',
      editMode: 1,
      dialogTitle: '',

      selectedMediaCode: '',

      adMediaId: '',
      adChildCode: '',
      adName: '',
      adDate: '',
      adPrice: '',
      adNotes: '',

      selectedAdvertise: null,
      addBtnShow: false,


      mediaCategories: ['新聞', '雑誌', 'ブログ', 'メルマガ', 'WEB広告', 'DM', '提携先'],
      mediaDialogVisible: false,
      mediaList: [],
      selectedMedia: null,
      mediaCode: '',
      category: '',
      mediaName: '',
      mediaUrl: '',
      mediaNote: '',
      mediaNewCheck: false
    }
  },
  methods: {
    logined() {
      this.setAuth('advertise');
      this.loadAdvertise()
      this.loadMedia()
    },
    // --------------------------------------------------------------------
    // 広告コード取得
    loadAdvertise() {
      this.advertiseList = []
      this.selectedAdvertise = null
      var data = new FormData()
      data.append('type', 1)
      data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      this.post(Define.API_ADVERTISE, data).then((response) => {
        if (response.list.length === 0) {
          this.message('該当期間の広告コードはありません')
        } else {
          this.advertiseList = response.list
          this.clearAdvertiseForm()
        }
      })
    },
    // --------------------------
    // 広告選択
    selectAdvertise(row, event, column) {
      if (this.advertiseNewCheck) {
        this.clearAdvertiseSelect()
        return
      }
      this.selectedAdvertise = row
      this.adMediaId = row.media_id
      this.adChildCode = row.ad_child_code
      this.adName = row.name
      this.adDate = new Date(row.ad_date)
      this.adPrice = row.price
      this.adNotes = row.notes
      this.addBtnShow = false
    },
    // 差し込みコードクリック
    onClickSashikomi(index) {
      let code = this.sashikomiData[index].code
      this.advertiseText += code
    },
    // ---------------------------------------------------------------------
    // フォームクリア
    clearAdvertiseForm() {
      this.selectedMediaCode = ''
      this.adMediaId = ''
      this.adName = ''
      this.adChildCode = ''
      this.adDate = ''
      this.adPrice = ''
      this.adNotes = ''
      this.addBtnShow = false
      this.clearAdvertiseSelect()
    },
    // フォーム取得
    getAdvertiseForm() {
      return {
        ad_code: this.selectedMediaCode + this.adChildCode,
        media_code: this.selectedMediaCode,
        ad_child_code: this.adChildCode,
        media_id: this.adMediaId,
        name: this.adName,
        ad_date: this.adDate.toLocaleDateString(),
        price: this.adPrice,
        notes: this.adNotes,
      }
    },

    // ---------------------------------------------------------------------
    // 追加
    addAdvertise() {
      if (this.inputCheck()) {
        let data = this.getAdvertiseForm();
        data.type = 'ADD';
        this.post(Define.API_ADVERTISE, data).then((response) => {
          this.message('広告コードを登録しました', 'success')
          this.clearAdvertiseForm()
          this.loadAdvertise()
        })
      }
    },
    // ---------------------------------------------------------------------
    // 変更
    modAdvertise() {
      if (this.inputCheck()) {
        let data = this.getAdvertiseForm();
        data.type = 'MOD';
        data.id = this.selectedAdvertise.id;
        this.post(Define.API_ADVERTISE, data).then((response) => {
          this.message('広告コードを修正しました', 'success')
          this.clearAdvertiseForm()
          this.loadAdvertise()
        })
      }
    },
    // ---------------------------------------------------------------------
    // 無効化
    delAdvertiseConfirm() {
      if (this.selectedAdvertise === null) {
        this.message('広告コードが選択されていません')
        return
      }
      this.confirm('広告コード削除', '[' + this.selectedAdvertise.name + ']を削除してよろしいですか？', 'info', this.delAdvertise)
    },
    delAdvertise() {
      let data = {
        type: 'DEL',
        id: this.selectedAdvertise.id,
      }
      this.post(Define.API_ADVERTISE, data).then((response) => {
        this.message('広告コードを削除しました:', 'success')
        this.selectedAdvertise = []
        this.loadAdvertise()
      })
    },
    // ---------------------------------------------------------------------
    // 入力チェック
    inputCheck() {
      this.onSelectMedia();
      if (this.selectedMediaCode === '') {
        this.message('媒体コードが選択されていません。')
        return false
      }
      for (let i = 0; i < this.advertiseList.length; i++) {
        if (this.selectedAdvertise && this.selectedAdvertise.id === this.advertiseList[i].id) continue
        if (this.selectedMediaCode + this.adChildCode === this.advertiseList[i].ad_code) {
          this.message('入力した広告コードは既に使用されています')
          return false
        }
      }
      if (this.adName === '') {
        this.message('広告名称が入力されていません')
        return false
      }
      if (this.adDate === '') {
        this.message('掲載開始日が入力されていません')
        return false
      }
      return true
    },

    // フォーム媒体選択
    onSelectMedia() {
      for (let i = 0; i < this.mediaList.length; i++) {
        if (this.adMediaId === this.mediaList[i].id) {
          this.selectedMediaCode = this.mediaList[i].code
          break
        }
      }
    },


    // ----------------------------------------------------------------------------
    // メディア 媒体取得

    loadMedia() {
      this.mediaList = []
      this.post(Define.API_MEDIA, {}).then((response) => {
        this.mediaList = response.list
      })
    },

    //---------------------
    // アイアログOPEN
    openMediaList() {
      this.loadMedia()
      this.mediaDialogVisible = true
    },
    //---------------------
    // 内容チェック
    checkConfMedia(num) {
      if (this.mediaCode === '') {
        this.message('媒体名を入力してください')
        return
      }
      if (!this.mediaCode.match(/^[a-zA-Z]{2}$/)) {
        this.message('媒体コードは英字2文字で入力してください')
        return
      }
      if (this.mediaName === '') {
        this.message('媒体名を入力してください')
        return
      }

      let code = this.mediaCode
      let cnt = this.mediaList.length
      for (let i = 0; i < cnt; i++) {
        if (this.selectedMedia && this.selectedMedia.id === this.mediaList[i].id) {
          continue
        }
        if (code === this.mediaList[i].code) {
          this.message('入力した媒体コードは既に使用されています')
          return
        }
      }
      if (num === 1) {
        this.addMedia()
      } else if (num === 2) {
        this.modMedia()
      }
    },
    // ------------------------
    // 媒体追加
    addMedia() {
      var data = new FormData()
      data.append('type', 'ADD')
      data.append('name', this.mediaName)
      data.append('code', this.mediaCode)
      data.append('category', this.category)
      data.append('url', this.mediaUrl)
      data.append('note', this.mediaNote)
      this.post(Define.API_MEDIA, data).then((response) => {
        this.message('媒体を追加しました。', 'success')
        this.loadMedia()
        this.clearMediaSelect()
        this.mediaNewCheck = false
      })
    },

    // ------------------------
    // 媒体修正
    modMedia() {
      var data = new FormData()
      data.append('type', 'MOD')
      data.append('name', this.mediaName)
      data.append('code', this.mediaCode)
      data.append('category', this.category)
      data.append('url', this.mediaUrl)
      data.append('note', this.mediaNote)
      data.append('id', this.selectedMedia.id)
      this.post(Define.API_MEDIA, data).then((response) => {
        this.message('媒体を修正しました。', 'success')
        this.loadMedia()
        this.clearMediaSelect()
      })
    },
    // ------------------------
    // 媒体削除確認
    delMediaConf() {
      console.log(this.selectedMedia)
      this.confirm('媒体削除', this.selectedMedia.name + 'を削除してよろしいですか？', 'info', this.delMedia)
    },
    // 媒体削除
    delMedia() {
      var data = new FormData()
      data.append('type', 'DEL')
      data.append('id', this.selectedMedia.id)
      this.post(Define.API_MEDIA, data).then((response) => {
        this.message('媒体を削除しました。', 'success')
        this.loadMedia()
        this.clearMediaSelect()
      })
    },
    // ------------------------
    // 媒体テーブルチェンジイベント
    onChangeMedia(val) {
      if (this.mediaNewCheck || !val) {
        this.clearMediaSelect()
        return
      }
      this.selectedMedia = val
      this.mediaCode = val.code
      this.mediaName = val.name
      this.type = val.type
      this.mediaUrl = val.url
      this.mediaNote = val.note
    },
    // 媒体テーブル選択クリア
    clearMediaSelect() {
      this.$refs.mediaTbl.setCurrentRow()
      this.selectedMedia = null
      this.mediaCode = ''
      this.mediaName = ''
      this.type = ''
      this.mediaUrl = ''
      this.mediaNote = ''
    },
    onChangeMediaNewMode() {
      if (this.mediaNewCheck) {
        this.$refs.mediaFormArea.style.backgroundColor = '#ffffe0'
        this.clearMediaSelect()
      } else {
        this.$refs.mediaFormArea.style.backgroundColor = '#f0f8ff'
      }
    },
    clearAdvertiseSelect() {
      this.$refs.adTbl.selectCancel()
      this.selectedAdvertise = null
    },
    onChangeAdvertiseNewMode(e) {
      this.clearAdvertiseForm()
      this.addBtnShow = false
      if (this.mediaNewCheck) {
        this.$refs.advertiseFormArea.style.backgroundColor = '#ffffe0'
      } else {
        this.$refs.advertiseFormArea.style.backgroundColor = '#f0f8ff'
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
