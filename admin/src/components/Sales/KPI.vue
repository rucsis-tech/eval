<template>
  <h3>KPI
  </h3>
  <!-- 上部ヘッダ ---------------------------------------------------------------------------------------------->
  <el-row class="contents">
    <el-col :span="7">
      <el-radio class="radio" v-model="term" label="ALL" style="margin-right:15px;">全期間</el-radio>
      <el-radio class="radio" v-model="term" label="TERM" style="margin-right: 10px;">期間指定</el-radio>
      <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false" style="width:140px;" @change="type='TERM'" :disabled="term!='TERM'">
      </el-date-picker>
      ～
      <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false" style="width:140px;" @change="type='TERM'" :disabled="term!='TERM'">
      </el-date-picker>
    </el-col>

    <el-col :span="10" class="left-border" style="display:flex;">
      <el-radio class="radio" v-model="category" label="DAY" style="margin-right:15px;">日次</el-radio>
      <el-radio class="radio" v-model="category" label="WEEK" style="margin-right: 10px;">開催週</el-radio>
      <el-radio class="radio" v-model="category" label="MONTH" style="margin-right: 10px;">月次</el-radio>
    </el-col>

    <el-col :span="6" class="left-border">
      <el-button :disabled="!auth.Sales" @click="loadSalesData" style="margin:3px 20px;">再読み込み</el-button>
    </el-col>

  </el-row>

  <!-- グラフ ---------------------------------------------------------------------------------------------->

  <el-row class="">

    <el-col :span="24" style="border-right: solid 1px #ddd;">
      <div>
        <bar-chart :height="350" :data="barData" :options="chartOptions"></bar-chart>
      </div>
      <!--vue-slider v-if="barCount > cDisplayCount" v-model="sliderVal" :max="barCount - cDisplayCount" :speed="0" :tooltip="false"></vue-slider-->
      <!--bar-chart :height="350" :chart-data="barData" :options="chartOptions"></bar-chart-->

      <!--vue-slider v-if="labels.length > cDisplayCount" v-model="sliderVal" :max="labels.length - cDisplayCount" :speed="0" :tooltip="false"></vue-slider-->
    </el-col>
  </el-row>

  <!-- 表示調整タブ ---------------------------------------------------------------------------------------------->
  <el-row class="contents">
    <el-col :span="2">
      <el-switch v-model="allDisplay"/>
      <span style="font-size:14px;padding:8px 4px;">全表示</span>
    </el-col>

    <el-col :span="18" style="display:flex;">
      <div style="margin-right:20px">
        <el-button @click="onClickOffGraph">全てOFF</el-button>
        <el-button @click="onClickDefaultGraph">デフォルト</el-button>
      </div>

      <el-radio-group v-model="setIndex" @change="onChangeSet">
        <el-radio-button value="1" style="margin:0 8px;">セット1</el-radio-button>
        <el-radio-button value="2" style="margin:0 8px;">セット2</el-radio-button>
        <el-radio-button value="3" style="margin:0 8px;">セット3</el-radio-button>
        <el-radio-button value="4" style="margin:0 8px;">セット4</el-radio-button>
        <el-radio-button value="5" style="margin:0 8px;">セット5</el-radio-button>
      </el-radio-group>


    </el-col>

    <el-col :span="4">
      <el-button size="small" :disabled="!auth.SalesCb" @click="onCbCopy">クリップボードコピー</el-button>
    </el-col>
  </el-row>

  <!-- テーブル ---------------------------------------------------------------------------------------------->
  <el-row class="contents">

    <div class="" style="overflow: auto;font-size: 0.8rem;">
      <table id="kpi-table" class="data-table">
        <thead>
        <tr>
          <th class="fix-col" style="width:100px;">分類</th>
          <th class="fix-col" style="width:220px;">集計項目</th>
          <th v-for="label in labels" style="width:80px;text-align: center;background-color: #f2f2f2">{{ label }}</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="dataset in datasets">
          <th class="fix-col" v-if="dataset.enabled || allDisplay">
            <div v-if="dataset.type=='line'" :style="'text-align:center;width:18px;color:white;background-color:'+dataset.backgroundColor">√</div>
            <div v-if="dataset.type=='bar'" style="display:flex;">
              <div :style="'text-align:center;width:18px;color:white;background-color:'+dataset.backgroundColor">❙❙|</div>
              <select v-model="dataset.stack" style="margin-left: 4px;">
                <option v-for="(value,key) in stacks" :value="key">{{ value }}</option>
              </select>
            </div>
          </th>
          <th class="fix-col" v-if="dataset.enabled || allDisplay">
            <label style="cursor: pointer;font-size:14px;">
              <input v-model="dataset.enabled" type="checkbox" style="transform:scale(1.4);margin-right:6px;">
              <span :style="(dataset.enabled)?'':'color:#888;'">{{ dataset.label }}</span>
            </label>
          </th>
          <td v-for="data in dataset.data" style="text-align: right;" v-if="dataset.enabled || allDisplay">
            {{ Common.numberFormat(data) }}
          </td>
        </tr>
        </tbody>
      </table>
    </div>

  </el-row>

</template>

<script>

import SalesTable from './SalesTable.vue'
import vueSlider from 'vue-3-slider-component'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

import * as ChartJS from 'chart.js';
import {Bar} from 'vue-chartjs'
import 'chart.js/auto';

export default {
  name: 'sales',
  mixins: [AppVue],
  components: {
    'bar-chart': Bar,
    'vue-slider': vueSlider,
  },

  computed: {
    barData() {
      const datasets = [];
      for (let name in this.datasets) {
        const dataset = this.datasets[name];
        if (dataset.type === 'line' && dataset.enabled) datasets.push(dataset);
        dataset.stack;
      }
      for (let name in this.datasets) {
        const dataset = this.datasets[name];
        if (dataset.type === 'bar' && dataset.enabled) datasets.push(dataset);
      }

      return {
        datasets: datasets,
        labels: this.labels,

      }
    },
  },

  methods: {
    logined() {
      this.setAuth('sales')
      this.loadSalesData()
    },
    // データの取得
    loadSalesData() {
      const data = new FormData()
      data.append('category', this.category)
      data.append('term', this.term)
      if (this.term === 'TERM') {
        data.append('since', Common.dateFormat(this.since, 'YYYY-MM-DD'))
        data.append('until', Common.dateFormat(this.until, 'YYYY-MM-DD'))
      }

      this.post(Define.API_KPI, data).then((response) => {
        this.labels = response.labels;

        for (let name in response.datasets) {
          if (this.firstLoaded) this.datasets[name] = response.datasets[name]
          else this.datasets[name].data = response.datasets[name].data
        }

        const table = document.getElementById('kpi-table')
        table.style.width = (200 - 160 + this.labels.length * 80) + 'px'

        if (this.firstLoaded) this.onClickDefaultGraph()
        this.firstLoaded = false;
      })
    },

    onClickOffGraph() {
      this.setIndex = 0;
      this.allDisplay = true;
      for (let name in this.datasets) this.datasets[name].enabled = false;

    },
    onClickDefaultGraph() {
      this.setIndex = 0;
      this.applySet(this.defaultSet);

    },
    applySet(graphSet) {
      for (let name in graphSet) {
        const conf = graphSet[name];
        {
          for (let confName in conf) {
            this.datasets[name][confName] = conf[confName];
          }
        }
      }
    },

    onChangeSet() {

    }
    ,
    onCbCopy() {
      this.copyToClipboard(this.generateData())
    },
    generateData() {
      let texts = []

      // 項目
      let line = ['日付']
      for (let key in this.datasets) line.push(this.datasets[key].label)
      texts.push(line.join('\t'))

      for (let i = 0; i < this.labels.length; i++) {
        let line = [this.labels[i]]

        for (let key in this.datasets) line.push(this.datasets[key].data[i])

        texts.push(line.join('\t'))
      }
      return texts.join('\n')

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
      Common: Common,
      allDisplay: false,
      setIndex: 0,
      firstLoaded: true,

      since: (new Date()).setTime((new Date()).getTime() - (86400 * 30) * 1000),
      until: (new Date()).setTime((new Date()).getTime()),
      term: 'ALL',
      category: 'DAY',

      labels: [],
      datasets: {},

      stacks: {
        give: 'Pt付与',
        point: 'Pt消費',
        amount: '決済額',
        A: 'A',
        B: 'B',
        C: 'C',
      },

      defaultSet: {
        'ME-CNT': {enabled: true,},
        'WS-TAM': {enabled: true, stack: 'amount',},
        'WB-TAM': {enabled: true, stack: 'amount',},
        'WP-TAM': {enabled: true, stack: 'amount',},
        'PT-TAM': {enabled: true, stack: 'amount',},
        'GF-GVE': {enabled: true, stack: 'give',},
      },

      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
          title: {
            display: false,
          },
        },
        scales: {

          "amountChart": {
            type: "linear",
            position: "left",
            stacked: true,
            ticks: {
              // 縦軸 単位表示
              callback: function (value, index, ticks) {
                return index < ticks.length - 1 ? this.getLabelForValue(value) : ['金額', this.getLabelForValue(value)];
              }
            }
          },

          "pointChart": {
            type: "linear",
            position: "left",
            stacked: true,
            ticks: {
              // 縦軸 単位表示
              callback: function (value, index, ticks) {
                return index < ticks.length - 1 ? this.getLabelForValue(value) : ['ポイント', this.getLabelForValue(value)];
              }
            }
          },

          "memberChart": {
            type: "linear",
            position: "right",
            ticks: {
              // 縦軸 単位表示
              callback: function (value, index, ticks) {
                return index < ticks.length - 1 ? this.getLabelForValue(value) : ['人数', this.getLabelForValue(value)];
              }
            }
          }
        },
      }
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.data-table {
  border-collapse: collapse;
  table-layout: fixed;
}

td, th {
  border: 1px solid white;
  padding: 2px 4px;
}

.fix-col {
  position: sticky;
  top: 0;
  left: 0;
  background: #f2f2f2;
}
</style>
