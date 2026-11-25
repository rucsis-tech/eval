<template>
  <h3> サブスク購入履歴 </h3>
  <!-- 上部ヘッダ ---------------------------------------------------------------------------------------------->
  <el-row class="contents">
    <el-col :span="7">
      <el-radio class="radio" v-model="term" label="THIS_MONTH" style="margin-right:15px;">今月</el-radio>
      <el-radio class="radio" v-model="term" label="LAST_MONTH" style="margin-right:15px;">先月</el-radio>
      <el-radio class="radio" v-model="term" label="TERM" style="margin-right: 10px;">期間指定</el-radio>
      <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false" style="width:140px;" @change="type='TERM'" :disabled="term!='TERM'">
      </el-date-picker>
      ～
      <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false" style="width:140px;" @change="type='TERM'" :disabled="term!='TERM'">
      </el-date-picker>
    </el-col>

    <el-col :span="12" class="left-border" style="display:flex;">
      <div>
        UID
        <el-input type="text" v-model="uid" style="width:6rem;margin-right:15px"></el-input>
      </div>

      <div style="display:flex;margin-right: 30px;">
        <div style="width:4.5rem;margin-top:4px">
          決済種別
        </div>
        <div>
          <el-select v-model="method" label="決済" style="width:6rem;">
            <el-option value="ALL">全て</el-option>
            <el-option value="credit">クレジット</el-option>
            <el-option value="ganb">銀行振込</el-option>
          </el-select>
        </div>
      </div>

      <div style="margin-right: 30px;">
        商品：
        <el-radio class="radio" v-model="type" label="ALL" style="margin-right:15px;">全て</el-radio>
        <el-radio class="radio" v-model="type" label="FIRST" style="margin-right:15px;">新規</el-radio>
        <el-radio class="radio" v-model="type" label="EXTEND">継続</el-radio>
      </div>

      <div style="margin-right: 30px;border-radius: 8px;border:1px solid #DDD;padding:8px 12px;margin-top:-10px;">
        支払：
        <el-checkbox class="radio" v-model="purchased" label="subsc" style="margin-right:15px;">有償購入</el-checkbox>
        <el-checkbox class="radio" v-model="free" label="point">無料</el-checkbox>
      </div>


    </el-col>

    <el-col :span="4" class="left-border">
      <el-button :disabled="!auth.Sales" @click="loadSalesData" style="margin:3px 20px;">再読み込み</el-button>
      <el-checkbox v-model="staff" style="width:7.5rem;margin-left:1rem;">STAFFを含む</el-checkbox>
    </el-col>

  </el-row>

  <hr>


  <!-- テーブル ---------------------------------------------------------------------------------------------->

  <div class="contents">
    <subscription-table :gridData="salesList" :auth="auth">
    </subscription-table>
  </div>


  <!-- テーブル ---------------------------------------------------------------------------------------------->

</template>

<script>

import SubscriptionTable from './SubscriptionTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";
import {API_SALES_SUBSCRIPTION} from "../../js/Define";

export default {
  name: 'sales',
  mixins: [AppVue],
  components: {
    'subscription-table': SubscriptionTable
  },

  computed: {
  },

  methods: {
    logined() {
      this.setAuth('sales')
      this.loadSalesData()
    },
    // 販売データの取得
    loadSalesData() {
      const data = new FormData()
      data.append('type', this.type)
      data.append('term', this.term)
      data.append('method', this.method)
      data.append('staff', this.staff)
      if (this.term === 'TERM') {
        data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
        data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      }
      if (this.uid) data.append('uid', this.uid)
      data.append('purchased', this.purchased)
      data.append('free', this.free)

      this.post(Define.API_SALES_SUBSCRIPTION, data).then((response) => {
        this.paymentTotal = Common.numberFormat(response.total)
        if (response.list === 0) {
          this.message('該当期間は売上がありません。')
        } else {
          this.salesList = response.list
          this.bars = response.bars
          this.barCount = Object.keys(response.bars).length
          this.setChartData()

        }

      })
    },
  },

  props: [],
  data() {
    return {
      since: (new Date()).setTime((new Date()).getTime() - (86400 * 30) * 1000),
      until: (new Date()).setTime((new Date()).getTime()),
      term: 'THIS_MONTH',
      type: 'ALL',
      staff: true,
      uid: '',
      method: 'ALL',
      purchased: true,
      free: true,

      salesList: [],
      bars: [],
      barCount: 0,
      labels: [],
      data: [],
      sliderVal: 0,
      cDisplayCount: 31,

      paymentTotal: '',

    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
