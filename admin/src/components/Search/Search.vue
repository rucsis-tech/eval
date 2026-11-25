<template>
  <div class="top contents">
    <h3>会員検索</h3>
  </div>

  <div class="contents">
    <el-row class="header-buttons">

      <!-- 操作ボタン -------------------------------------------------------------------------------------------------------------------------->
      <el-col :span="2">
        <el-button size="small" @click="onSearch" :disabled="memberList.length > 0 ? true : false">検索</el-button>
      </el-col>
      <el-col :span="2">
        <el-button size="small" @click="onClickClearForm">クリア</el-button>
      </el-col>
      <el-col :span="2">
        <el-button size="small" @click="onClickReload" :disabled="memberList.length > 0 ? false : true">再読</el-button>
      </el-col>

      <el-col :span="4">
      </el-col>

      <!-- 会員送りボタン -------------------------------------------------------------------------------------------------------------------------->

      <el-col :span="2">
        <el-button type="info" size="small" @click="prev" :disabled="selectedMemberIndex <= 1 ? true : false">
          <el-icon>
            <ArrowLeft/>
          </el-icon>
        </el-button>
      </el-col>

      <el-col :span="2" style="margin-top:3px;text-align:right;">
        <el-input-number size="small" style="width:60px;font-size:20px;" v-model="selectedMemberIndex" :min="0" :max="+memberList.length" :disabled="memberList.length > 0 ? false : true" @keyup.enter.native="loadMember(null)" :controls="false"></el-input-number>
      </el-col>
      <el-col :span="1" style="text-align:center;font-size:20px;line-height:1.8;">/</el-col>
      <el-col :span="1" style="text-align:left;font-size:20px;line-height:1.8;">{{ memberList.length }}</el-col>

      <el-col :span="2">
        <el-button type="info" size="small" @click="next" :disabled="selectedMemberIndex >= memberList.length ? true : false">
          <el-icon>
            <ArrowRight/>
          </el-icon>
        </el-button>
      </el-col>

    </el-row>
  </div>

  <!-- 会員情報FORM -------------------------------------------------------------------------------------------------------------------------->
  <div style="margin:0;padding:0">

    <el-row @click="onClickStatus(status.created_at)" :class="[memberDetail.locked_at ? 'form-area-leave' : 'form-area']">


      <!--  左パネル 入力エリア ------------------------------------------------------------------------->
      <el-col :span="6" style="padding-right:8px;">
        <el-row>
          <el-col :span="12">
            <el-input v-model="memberDetail.uid" size="large" :disabled="memberList.length > 0 ? true : false" @keyup.enter.native="onSearch" placeholder="UID" style="font-size:20px;font-weight:bold"></el-input>
            <div style="display:flex;margin:2px 0;">
              <div style="font-size:0.8rem;color:#666;margin:2px 6px 0 0;">会員番号</div>
              <el-input type="number" v-model="memberDetail.mid" size="small" :disabled="memberList.length > 0 ? true : false" @keyup.enter.native="onSearch" placeholder="会員番号" style="width:5rem"></el-input>
            </div>
          </el-col>
          <el-col :span="12" style="padding:0 1rem;">
            <el-checkbox v-model="staff_flag" class="form-dst" style="margin-top: 0;"><span>スタッフ</span></el-checkbox>
            <el-checkbox v-if="(memberDetail.subscription_id <= 0)" v-model="memberDetail.debug_subscription" class="form-dst"><span>デバッグ購読</span></el-checkbox>
          </el-col>


        </el-row>

        <el-row style="margin-top:4px;">
          <el-col :span="24">
            <el-input v-model="memberDetail.nickname" type="text" @keyup.enter.native="onSearch" placeholder="ニックネーム"></el-input>
          </el-col>
        </el-row>


        <el-row>
          <el-col :span="24">
            <el-input v-model="memberDetail.email" type="text" style="font-size:13px;" @keyup.enter.native="onSearch" placeholder="メールアドレス"></el-input>
          </el-col>
        </el-row>

        <el-row>
          <el-col :span="24">
            <el-select v-model="memberDetail.mail_status" class="" placeholder="配信状況">
              <el-option v-for="item in mailDisables" :key="item.value" :label="item.label" :value="item.value"/>
            </el-select>
          </el-col>
        </el-row>

        <el-row style="margin-top:4px;">
          <el-col :span="8">
            <el-input v-model="memberDetail.ad_code" type="text" @keyup.enter.native="onSearch" :maxlength="5" placeholder="広告コード"></el-input>
          </el-col>
          <el-col :span="16">
            <!--el-input v-model="memberDetail.tel" type="text" @keyup.enter.native="onSearch" placeholder="電話番号" style="font-size:13px;"></el-input-->
          </el-col>
        </el-row>


        <!--el-row style="margin-top:4px;">
          <el-col :span="8">
            <el-input v-model="memberDetail.zip" type="text" @keyup.enter.native="onSearch" placeholder="〒番号" style="font-size:13px;"></el-input>
          </el-col>
          <el-col :span="16">
            <el-input v-model="memberDetail.pref" type="text" @keyup.enter.native="onSearch" :maxlength="5" placeholder="都道府県"></el-input>
          </el-col>
        </el-row-->

        <!--el-row>
          <el-col :span="24">
            <el-input v-model="memberDetail.city" type="text" @keyup.enter.native="onSearch" placeholder="住所"></el-input>
          </el-col>
        </el-row-->


        <el-input v-model="memberDetail.notes1" type="textarea" placeholder="備考1" style="margin-top:4px;font-size:13px;height:50px;"></el-input>
        <el-input v-model="memberDetail.notes2" type="textarea" placeholder="備考2" style="margin-top:4px;font-size:13px;height:50px;"></el-input>
        <el-input v-model="memberDetail.notes3" type="textarea" placeholder="備考3" style="margin-top:4px;font-size:13px;height:50px;"></el-input>


        <el-row style="margin-top: 1rem;">
          <el-col :span="24" class="text-right">
            <el-button @click="onClickModify" :disabled="memberList.length === 0 || !auth.MemberMod">修正</el-button>
          </el-col>
        </el-row>
        <div v-if="memberDetail.locked_at" style="color:red;">退会済み</div>

      </el-col>


      <!-- 右パネル -------------------------------------------------------------------------------------------------------------------------->

      <el-col :span="18">
        <!-- パネル選択ボタン -------------------------------------------------------------------------------------------------------------------------->

        <el-row>
          <el-col :span="24" style="margin:0 4px;">
            <el-radio-group v-model="viewIndex" @change="onChangeView">

              <el-radio-button value="0">会員詳細</el-radio-button>
              <el-radio-button value="1">取引履歴</el-radio-button>
              <el-radio-button value="5">入手ポイント</el-radio-button>
              <el-radio-button value="2">ポイント利用履歴</el-radio-button>
              <!--el-radio-button value="3">獲得スコア履歴</el-radio-button-->
              <el-radio-button value="4" style="margin-left:1rem;">検索結果一覧</el-radio-button>

            </el-radio-group>
          </el-col>
        </el-row>

        <!-- 個別コンポーネント -------------------------------------------------------------------------------------------------------------------------->

        <router-view ref="childView" :memberDetail="memberDetail" :parentEvent="onEvent"></router-view>


      </el-col>
    </el-row>

    <!-- チェックボックス -------------------------------------------------------------------------------------------------------------------------->

    <div style="padding:5px;">
      <el-checkbox-group v-model="cbxGroup">
        <div v-for="(value, index) in cbxList" style="float:left;overflow:hidden;width:50px;">
          <el-tooltip effect="dark" :content="checkNames.length === 0 ? '' : checkNames[index].name" placement="top">
            <el-checkbox :label="value" :value="value"></el-checkbox>
          </el-tooltip>
        </div>
        <div style="clear:both;"></div>
      </el-checkbox-group>
    </div>

  </div>
</template>

<script>
import Common, {Define} from '../../js/Common.js'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'search',
  mixins: [AppVue],

  created() {
    this.onLogin()
  },
  methods: {
    logined() {
      this.setAuth('member');
      this.onChangeView(this.viewIndex)
      this.loadCheckNames()

      this.prefList = Define.prefList
      // 会員検索に反映処理
      this.memberList = this.$store.state.memberList
      if (this.memberList > 0) {
        this.selectedMemberIndex = 1
        this.loadMember()
        // this.$store.commit('setMemberIdList', [])
        return
      }

      // 前回の絞り込み状態を再読み込み
      let data = this.$store.state.searchConditions
      if (data !== null && this.memberList.length === 0) {
        Common.loadStart()
        this.post(Define.API_SEARCH, data).then(this.searchRes)
      }
    },

    onEvent(event) {
      if (event.event) this.childEvent = event.event
      if (event.loadMember) this.loadMember(event.loadMember)
    },

    //---------------------------------------------------------------------
    // 検索実施

    onSearch() {
      if (this.memberList.length > 0) return
      const data = {}
      // UID
      if (this.memberDetail.uid) data.uid = {mode: 1, value: this.memberDetail.uid}
      // 会員番号
      if (this.memberDetail.mid) data.mid = {mode: 1, value: this.memberDetail.mid}
      // スタッフ
      if (this.staff_flag) data.staff_flag = {mode: 1, value: this.staff_flag}

      // nickname
      if (this.memberDetail.nickname) data.nickname = {mode: 1, value: this.memberDetail.nickname}
      // code
      if (this.memberDetail.code) data.code = {mode: 1, value: this.memberDetail.code}

      // メールアドレス
      if (this.memberDetail.email) data.email = {mode: 1, value: this.memberDetail.email}

      // メール配信状況
      if (this.memberDetail.mail_status != undefined) data.mail_status = {mode: 1, value: this.mail_status.mail}

      // 連絡先TEL
      if (this.memberDetail.tel) data.tel = {mode: 1, value: this.memberDetail.tel}

      // 広告コード
      if (this.memberDetail.ad_code) data.ad_code = {mode: 1, value: this.memberDetail.ad_code}
      // 住所
      if (this.memberDetail.zip) data.zip = {mode: 1, value: this.memberDetail.zip}
      if (this.memberDetail.pref) data.pref = {mode: 1, value: this.memberDetail.pref}
      if (this.memberDetail.city) data.city = {mode: 1, value: this.memberDetail.city}

      // 備考1
      if (this.memberDetail.notes1) data.notes1 = {mode: 1, value: this.memberDetail.notes1}
      // 備考2
      if (this.memberDetail.notes2) data.notes2 = {mode: 1, value: this.memberDetail.notes2}
      // 備考3
      if (this.memberDetail.notes3) data.notes3 = {mode: 1, value: this.memberDetail.notes3}

      if (this.memberDetail.bonus_priority) data.bonus_priority = {mode: 1, value: this.memberDetail.bonus_priority}


      // チェックボックス
      const checkData = this.makeCheckData(this.cbxGroup, this.cbxList);
      let check_flag = {mode: 1}
      for (let i = 0; i < checkData.length; i++) {
        check_flag['check' + (i + 1)] = checkData[i];
        if (checkData[i].indexOf('1') > -1) {
          data.check_flag = check_flag;
        }
      }

      if (Object.keys(data).length > 0) {
        this.$store.commit('setSearchConditions', data)
        this.post(Define.API_SEARCH, data).then(this.searchRes)
      } else {
        this.message('条件が入力されていません')
      }
    },

    searchRes(response) {
      this.memberList = response.list
      this.$store.state.memberList = this.memberList
      if (this.memberList.length === 0) {
        this.message('該当者がいません')
        return
      } else {
        this.selectedMemberIndex = 1
        this.loadMember()
        if (this.viewIndex === '4') {
          this.childEvent({refresh: 1})
        }
      }
    },

    //---------------------------------------------------------------------
    //　情報の変更・修正

    onClickModify() {
      const data = new FormData()

      data.append('uid', this.memberDetail.uid)
      data.append('email', this.memberDetail.email ?? '')
      data.append('mail_status', this.memberDetail.mail_status ?? 0)
      data.append('nickname', this.memberDetail.nickname ?? '')
      data.append('code', this.memberDetail.code ?? '')
      data.append('tel', this.memberDetail.tel ?? '')
      data.append('ad_code', this.memberDetail.ad_code ?? '')
      data.append('pref', this.memberDetail.pref ?? '')
      data.append('staff_flag', this.staff_flag ? 1 : 0)
      data.append('debug_subscription', this.memberDetail.debug_subscription ? 1 : 0)

      data.append('notes1', this.memberDetail.notes1 ?? '')
      data.append('notes2', this.memberDetail.notes2 ?? '')
      data.append('notes3', this.memberDetail.notes3 ?? '')

      data.append('bonus_priority', this.memberDetail.bonus_priority ?? 0)

      data.append('zip', this.memberDetail.zip ?? '')
      data.append('city', this.memberDetail.city ?? '')

      // チェックリスト
      const checkData = this.makeCheckData(this.cbxGroup, this.cbxList);
      for (let i = 0; i < checkData.length; i++) {
        data.append('check' + (i + 1), checkData[i])
      }

      this.post(Define.API_MEMBER_MOD, data).then((response) => {
        this.message('会員情報を修正しました', 'success')
      }).finally(() => {
        this.loadMember()
      })
    },

    //---------------------------------------------------------------------
    // 会員情報取得

    loadMember(uid) {
      var data = new FormData()
      if (uid) {
        data.append('uid', uid)
      } else {
        if (this.selectedMemberIndex > this.memberList.length) {
          this.selectedMemberIndex = this.memberList.length
        } else if (this.selectedMemberIndex < 1) {
          this.selectedMemberIndex = 1
        }
        data.append('uid', this.memberList[this.selectedMemberIndex - 1]['uid'])
      }
      this.post(Define.API_M_DETAIL, data).then((response) => {
        this.memberDetail = response.member
        if (!this.memberDetail.uid) {
          this.message('該当者がいません')
          return
        }
        for (let i = 0; i < this.memberList.length; i++) {
          if (this.memberList[i].uid === this.memberDetail.uid) {
            this.selectedMemberIndex = i + 1;
          }
        }

        this.child({refresh: true});
        this.setForm()
      })
    },

    child(event) {
      if (this.childEvent) {
        setTimeout(() => {
          this.childEvent(event);
        }, 0);
      }
    },

    //---------------------------------------------------------------------
    // 会員情報表示設定

    setForm() {
      this.staff_flag = this.memberDetail.staff_flag ? true : false;
      this.sumPay = Common.numberFormat(this.memberDetail.buy_sum)
      this.sumPoint = Common.numberFormat(this.memberDetail.item_sum)
      this.balance = Common.numberFormat(this.memberDetail.point_balance)
      this.sumTicket = Common.numberFormat(this.memberDetail.use_ticket_count)

      const cGroup = []
      const flags = this.memberDetail.flags
      this.cbxList.forEach(function (item, i) {
        let segment = Math.trunc(i / 32) + 1
        let bit = i % 32
        let mask = 1
        mask <<= bit
        if (flags[segment] & mask) {
          cGroup.push(item)
        }
      })
      this.cbxGroup = cGroup
    },

    //---------------------------------------------------------------------
    // 操作

    onClickReload() {
      this.loadMember()
    },
    prev() {
      this.selectedMemberIndex--
      this.loadMember()
    },
    next() {
      this.selectedMemberIndex++
      this.loadMember()
    },
    onClickClearForm() {
      this.sumPay = ''
      this.sumPoint = ''
      this.balance = ''
      this.staff_flag = false;

      this.memberList = []
      this.cbxGroup = []
      this.memberDetail = []
      this.selectedMemberIndex = 0

      if (this.viewIndex === '4') {
        //this.$refs.childView.clearMemberList()
      } else {
        this.$refs.childView.historyList = []
      }
      this.$store.commit('setSearchConditions', null)
      this.$store.commit('setMemberList', [])
    },

    onChangeView(index) {
      var viewName = ''
      switch (+index) {
        case 0:
          viewName = ''
          break
        case 1:   // 取引履歴
          viewName = 'payment_history'
          break
        case 5:   // ポイント取引履歴
          viewName = 'point_purchased'
          break
        case 2:   // ポイント取引履歴
          viewName = 'point'
          break
        case 3:   // 取引履歴
          viewName = 'score'
          break
        case 4:   // 会員一覧
          viewName = 'list'
          break
      }
      this.$router.replace('/search/' + viewName)
    },

    //---------------------------------------------------------------------
    // チェックフラグの名称取得

    loadCheckNames() {
      this.post(Define.API_CHECK_NAMES, {'type': 1}).then(this.loadCheckNamesRes)
    },
    loadCheckNamesRes(response) {
      this.checkNames = response.check_names
    },


    //---------------------------------------------------------------------
    // 未使用

    zipToCity() {
      if (this.memberZip === '') {
        this.message('郵便番号が入力されていません')
        return
      }
      var data = new FormData()
      data.append('zipcode', this.memberZip)
      this.post(Define.API_ZIP_TO_ADDRESS, data).then(this.zipToCityRes)
    },
    zipToCityRes(response) {
      if (typeof (response.data) === 'string' || response.data.length === 0) {
        this.message('住所の取得に失敗しました')
      } else {
        var adrData = response.data
        this.memberCity = adrData.address1 + adrData.address2 + adrData.address3
      }
    },
  }
  ,

  data() {
    let state = this.$store.state;

    return {

      mailDisables: Common.mailDisables,

      viewIndex: '0',
      listLoading: false,
      prefList: [],
      memberList: [],
      checkNames: [],
      cbxGroup: [],
      selectedMemberIndex: 0,

      cbxList: Common.createCheckList(this.$store.state.consts.flags.count),

      // 個別会員情報
      memberDetail: {
        uid: '',
        LQ: [],
        Score: [],
        Exchange: [],
      },
      staff_flag: false,
      sumPay: '',
      sumPoint: '',
      sumTicket: '',
      balance: '',
      refresh: null,

    }
  }
  ,
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.header-buttons button {
  width: 95%;
  margin-top: 4px;
  margin-bottom: 0;
}

.paging-area button {
  width: 95%;
  margin-top: 0;
}

.paging-area input {
  width: 100%;
  height: 28px;
  margin-top: 0;
  padding: 3px;
}


.form-area {
  background-color: #b0c4de;
  padding: 5px;
  margin-top: 0px;
}

.form-area-leave {
  background-color: #cd5c5c;
  padding: 5px;
  margin-top: 0px;
}

.form-dst {
  margin-left: 5px;
  margin-top: 5px;
}

.form-dst span {
  font-size: 15px;
  font-weight: 500;
}

.form-tel {
  margin: 0;
}

.form-zip {
  width: 90%;
}

.zip-to-city {
  margin-right: 15px;
}

.zip-to-city:hover {
  cursor: pointer;
}

.pay_point {
  background-color: #fff;
  padding: 6px;
  margin-left: 5px;
}
</style>
