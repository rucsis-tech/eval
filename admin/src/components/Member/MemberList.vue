<template>
  <h3>全会員一覧</h3>
  <div class="contents">

    <!-- リクエスト条件 ---------------------------------------------------------------------------------------------->

    <el-row>
      <el-col :span="10">
        集計対象期間
        <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false" style="width:140px;">
        </el-date-picker>
        ～
        <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false" style="width:140px;">
        </el-date-picker>
      </el-col>

      <el-col :span="10">
        <el-button :disabled="!auth.ListExec" @click="loadMemberList" style="margin-left:20px;">一覧を取得</el-button>
      </el-col>



      <el-col :span="4">
        <el-button :disabled="!auth.ListExec || !memberList.length" @click="onClickClipboard">クリップボードコピー</el-button>
      </el-col>
    </el-row>
  </div>

  <div class="h-border" style="margin-top:5px;width:100%;"></div>


  <!-- 会員リスト ---------------------------------------------------------------------------------------------->

  <div class="contents">

    <member-table :gridData="memberList">
    </member-table>

  </div>


</template>

<script>

import MemberListTable from './MemberListTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";

const date = new Date()

export default {
  name: 'memberList',
  mixins: [AppVue, AppLoader],
  components: {
    'member-table': MemberListTable
  },
  props: [
    'setIdFunc',
    'getIdFunc'
  ],
  data() {
    return {
      since: date.setTime(date.getTime()),
      until: date.setTime(date.getTime()),

      memberList: [],

    }
  },
  methods: {
    logined() {
      this.setAuth('member')
    },
    //---------------------------------------------------------------------
    // 会員リスト取得
    loadMemberList() {
      const data = {}

      data.since = Common.dateFormat(this.since, 'YYYY-MM-DD')
      data.until = Common.dateFormat(this.until, 'YYYY-MM-DD')

      this.post(Define.API_MEMBER_LIST, data).then((response) => {
        this.memberList = response.list
      })
    },

    //---------------------------------------------------------------------
    // クリップボード

    onClickClipboard() {
      this.copyToClipboard(this.getMemberText())
    },

    // 一覧テキスト生成
    getMemberText() {
      let text = '認証ID\t'
          + 'ニックネーム\t'
          + '広告コード\t'
          + '入会日時\t'

          + 'VIP保有\t'
          + 'VIP空数\t'
          + 'Lite保有\t'
          + 'Lite空数\t'

          + '期間指名数\t'

          + '総獲得スコア\t'

          + 'VIP申込数\t'
          + 'VIP申込額\t'
          + 'VIP購入数\t'
          + 'VIP購入額\t'

          + 'Lite申込数\t'
          + 'Lite申込額\t'
          + 'Lite購入数\t'
          + 'Lite購入額\t'

          + 'スタッフ\t'
          + '退会\t'
          + '\n'

      this.memberList.forEach(function (item, i) {
        text += item.uid + '\t'
            + item.nickname + '\t'
            + item.ad_code + '\t'
            + item.created_at + '\t'

            + item.vip_count + '\t'
            + item.vip_empty + '\t'
            + item.lite_count + '\t'
            + item.lite_empty + '\t'

            + (item.registered ? item.registered : 0) + '\t'

            + item.acquired_score + '\t'

            + (item.apply_quantity ?? 0) + '\t'
            + (item.apply_payment ?? 0) + '\t'
            + (item.approved_quantity ?? 0) + '\t'
            + (item.approved_payment ?? 0) + '\t'

            + (item.lite_apply_quantity ?? 0) + '\t'
            + (item.lite_apply_payment ?? 0) + '\t'
            + (item.lite_approved_quantity ?? 0) + '\t'
            + (item.lite_approved_payment ?? 0) + '\t'

            + item.staff_flag + '\t'
            + item.locked_at + '\t'
            + '\n'
      })


      return text;
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
