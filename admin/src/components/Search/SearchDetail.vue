<template>
  <div ref="test" class="r-white" style="height:500px;padding-bottom:1rem;"><!-- style="margin-left:5px;margin-top:5px;padding-left:10px;background-color:#fff;height:493px;"-->
    <el-row style="padding-top:12px; line-height:1.5em;">
      <el-col :span="8">


        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 入会日</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.created_at }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 購入総額</el-col>
          <el-col :span="12" style="text-align:right;">
            ￥{{ Common.numberFormat(memberDetail.payment_sum) }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 購入回数</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.purchased_count }}回
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 最終購入日時</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.paymented_at }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> ポイント消費回数</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.point_used_count }}回
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> ポイント消費日時</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.point_used_at }}
          </el-col>
        </el-row>


        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 最終ログイン日時</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.last_login_at === '0000-00-00 00:00:00' ? '-' : memberDetail.last_login_at }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 最終アクセス日時</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.accessed_at === '0000-00-00 00:00:00' ? '-' : memberDetail.accessed_at }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 配信許諾</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.accept }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;"> 退会日時</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.locked_at ? memberDetail.locked_at : '-' }}
          </el-col>
        </el-row>


        <!--el-row class="detail-row">
          <el-col :span="8" style="text-align:left;"> 銀行口座</el-col>
          <el-col :span="16" style="text-align:right;">
            {{ memberDetail.bank ? memberDetail.bank.bank_name + memberDetail.bank.branch_name + ' ' + memberDetail.bank.account_type + memberDetail.bank.account_number : '' }}
          </el-col>
        </el-row-->

        <!--el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">口座名義</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.bank ? memberDetail.bank.holder_name : '' }}
          </el-col>
        </el-row-->
      </el-col>


      <el-col :span="8" style="margin-left: 2rem;">


        <el-row class="detail-row">
          <el-col :span="8" style="text-align:left;">サブスク購読</el-col>
          <el-col :span="16" style="text-align:right;">
            <span v-if="memberDetail.Subscription">{{ memberDetail.Subscription.item_id }}: {{ memberDetail.Subscription.name }}</span>
          </el-col>
        </el-row>


        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">サブスク期限</el-col>
          <el-col :span="12" style="text-align:right;">
            <span v-if="memberDetail.Subscription">
              {{ memberDetail.Subscription.expire_at }}
              <span v-if="memberDetail.Subscription.mode==''">（自動）</span>
              <span v-else-if="memberDetail.Subscription.mode=='CANCEL'">（停止）</span>
              <span v-else-if="memberDetail.Subscription.mode=='CONTINUE'">（継続済）</span>
            </span>
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">総獲得ポイント</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.acquire_point }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">所有有償ポイント</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.premium_point }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">所有ギフトポイント</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.gift_point }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">ポイント最短失効</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.point_expire_at }}
          </el-col>
        </el-row>

        <el-row class="detail-row">
          <el-col :span="12" style="text-align:left;">総獲得スコア</el-col>
          <el-col :span="12" style="text-align:right;">
            {{ memberDetail.score }}
          </el-col>
        </el-row>

      </el-col>

      <div :class="[memberDetail.sub_jadge === '0' ? 'monthly_leave_detail' : 'monthly_enable_detail']">
      </div>

    </el-row>


    <el-row v-if="!memberDetail.locked_at" :disabled="!auth.MemberMod">
      <el-col :span="12" style="text-align:left;">
        <div class="contents">
          <el-button @click="isDialogLeaveShow=true">退会処理</el-button>
        </div>
      </el-col>
    </el-row>


    <el-dialog title=" 本当に退会処理を行いますか？" v-model="isDialogLeaveShow" class="DialogLeave">
      <el-row style="padding:0;margin:0;">
        <el-col :span="12">
          認証ID: {{ memberDetail.uid }}<br>
          {{ memberDetail.nickname }}<br>
          {{ memberDetail.mail }}<br>
        </el-col>
        <el-col :span="12" class="right">
          <el-button type="primary" @click="isDialogLeaveShow=false">キャンセル</el-button>
          <el-button type="danger" @click="onClickLeave">退会処理</el-button>
        </el-col>
      </el-row>
    </el-dialog>

  </div>


</template>
<script>
import Common, {Define} from '../../js/Common.js'
import AppVue from "../../mixins/AppVue.js";


export default {
  name: 'memberDetail',

  mixins: [AppVue],

  created() {
  },
  methods: {
    onEvent(event) {
      if (event.refresh) this.refresh()
    },
    refresh(event) {

    },
    onClickLeave() {
      const data = {uid: this.memberDetail.uid, mode: 'leave'}
      this.post(Define.API_MEMBER_LEAVE, data).then((response) => {
        this.message('会員情報を修正しました', 'success')
      }).finally(() => {
        this.loadMember()
      })
      this.isDialogLeaveShow = false
    },

  },

  mounted() {
    this.parentEvent({event: this.onEvent});
  },
  props: ['memberDetail', 'parentEvent'],
  data() {
    return {
      index: 0,
      isDialogLeaveShow: false,
      Common: Common,
    }
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.monthly_enable_detail .detail-row {
  color: #555;
}

.monthly_leave_detail .detail-row {
  color: #f00;
}

.detail-row {
  border-bottom: solid 1px #bbb;
  padding-left: 3px;
  padding-right: 3px;
  margin-top: 5px;
  color: #555;
  font-size: 14px;
}

</style>

<style>
.DialogLeave {
  width: 40rem;
}
</style>