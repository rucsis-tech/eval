<template>
  <h3>売上・取引履歴
    <span style="font-size: 1rem;">全売上総額：￥{{ paymentTotal }}</span>
  </h3>
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

    <el-col :span="10" class="left-border" style="display:flex;">
      <div style="margin-right:15px">
        UID
        <el-input type="text" v-model="uid" style="width:6rem;"></el-input>
      </div>


      <div style="display:flex;margin-right: 30px;">
        <div style="width:4.5rem;margin-top:4px">
          決済種別
        </div>
        <div>
          <el-select v-model="method" label="決済" style="width:6rem;">
            <el-option label="ALL" value="ALL"></el-option>
            <el-option label="クレカ" value="credit"></el-option>
            <el-option label="銀行振込" value="ganb"></el-option>
            <el-option label="調整決済" value="adjust"></el-option>
          </el-select>
        </div>
      </div>


      <div style="display:flex;margin-right: 30px;">
        <div style="width:3rem;margin-top:4px">
          商品
        </div>
        <div>
          <el-select v-model="kind" label="決済" style="width:10rem;">
            <el-option label="全体" value="ALL"></el-option>
            <el-option label="サブスク全て" value="subsc"></el-option>
            <el-option label="WinSalon" value="winsalon"></el-option>
            <el-option label="WINDEX" value="windex"></el-option>
            <el-option label="├BASIC" value="basic"></el-option>
            <el-option label="└PREMIUM" value="premium"></el-option>
            <el-option label="ポイント" value="point"></el-option>
          </el-select>
        </div>
      </div>
    </el-col>

    <el-col :span="6" class="left-border">
      <el-button :disabled="!auth.Sales" @click="loadSalesData" style="margin:3px 20px;">再読み込み</el-button>
      <el-checkbox v-model="staff" style="margin-left:0.5rem;">STAFFを含む</el-checkbox>
      <el-checkbox v-model="purchased" style="margin-left:0.5rem;">決済完了のみ</el-checkbox>
    </el-col>

  </el-row>

  <hr>

  <!-- グラフ ---------------------------------------------------------------------------------------------->

  <el-row class="contents">
    <el-col :span="17" style="border-right: solid 1px #ddd;">
      <div>
        <bar-chart :height="350" :data="barData" :options="chartOptions"></bar-chart>
      </div>
      <vue-slider v-if="barCount > cDisplayCount" v-model="sliderVal" :max="barCount - cDisplayCount" :speed="0" :tooltip="false"></vue-slider>
      <!--bar-chart :height="350" :chart-data="barData" :options="chartOptions"></bar-chart-->

      <!--vue-slider v-if="labels.length > cDisplayCount" v-model="sliderVal" :max="labels.length - cDisplayCount" :speed="0" :tooltip="false"></vue-slider-->
    </el-col>
    <el-col :span="7" style="padding:0px;">
      <!--pie-chart :height="350" :chart-data="pieData"></pie-chart-->
    </el-col>
  </el-row>

  <!-- テーブル ---------------------------------------------------------------------------------------------->

  <div class="contents">
    <sales-table :gridData="salesList" :auth="auth">
    </sales-table>
  </div>

</template>

<script>

import SalesTable from './SalesTable.vue'
import vueSlider from 'vue-3-slider-component'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

import {Bar} from 'vue-chartjs'
//import Pie from '../Graphs/PieChart'
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'sales',
  mixins: [AppVue],
  components: {
    'bar-chart': Bar,
    //'pie-chart': Pie,
    'vue-slider': vueSlider,
    'sales-table': SalesTable
  },

  computed: {
    barData() {
      return {
        labels: this.labels,
        datasets: [{
          label: '金額',
          data: this.data
        }],
      }
    },
  },

  methods: {
    logined() {
      this.setAuth('sales')
      this.loadSalesData()
    },
    // 販売データの取得
    loadSalesData() {
      const data = new FormData()
      data.append('kind', this.kind)
      data.append('term', this.term)
      data.append('staff', this.staff)
      data.append('purchased', this.purchased)
      data.append('method', this.method)
      if (this.term === 'TERM') {
        data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
        data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      }
      if (this.uid) data.append('uid', this.uid)

      this.post(Define.API_SALES, data).then((response) => {
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


    setChartData() {
      //this.sliderVal = this.bars.length < this.sliderVal + this.cDisplayCount ? this.bars.length : this.bars.length - this.cDisplayCount
      this.createBarData()
      //this.pieData = pie
    },
    createBarData() {
      this.labels = []
      this.data = []

      let offset = (this.barCount <= this.cDisplayCount) ? 0 : this.sliderVal;

      for (let key in this.bars) {
        if (--offset < 0 && this.labels.length < this.cDisplayCount) {
          this.labels.push(key)
          this.data.push(this.bars[key])
        }
      }
    },
  },

  watch: {
    sliderVal: function (newVal, oldVal) {
      this.createBarData()

    }
  },

  props: [],
  data() {
    return {
      since: (new Date()).setTime((new Date()).getTime() - (86400 * 30) * 1000),
      until: (new Date()).setTime((new Date()).getTime()),
      term: 'THIS_MONTH',
      kind: 'ALL',
      staff: true,
      purchased: true,
      uid: '',
      method: 'ALL',

      salesList: [],
      bars: [],
      barCount: 0,
      labels: [],
      data: [],
      sliderVal: 0,
      cDisplayCount: 31,

      paymentTotal: '',
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {}
      },
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
