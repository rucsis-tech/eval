<template>
<div class="contents">
  <el-row class="header-row" style="margin-top:8px;">
    <el-col :span="12" class="right-border">
      <el-radio class="radio" v-model="type" label="1">全期間</el-radio>
      <el-radio class="radio" v-model="type" label="2">指定期間</el-radio>
      <el-date-picker
        v-model="since"
        type="date"
        placeholder="前"
        :clearable="false"
        :picker-options="pickerOptions">
      </el-date-picker>
      ～
      <el-date-picker
        v-model="until"
        type="date"
        placeholder="後"
        :clearable="false"
        :picker-options="pickerOptions">
      </el-date-picker>
    </el-col>
    <el-col :span="5" class="right-border" style="margin-top:3px">
      <el-radio class="radio" v-model="unit" label="0">月次</el-radio>
      <el-radio class="radio" v-model="unit" label="1">開催週</el-radio>
      <el-radio class="radio" v-model="unit" label="2">日次</el-radio>
    </el-col>
    <el-col :span="7">
      <el-button @click="getKpiData">再読み込み</el-button>
    </el-col>
  </el-row>
  <div class="row">
    <div class="span12">&emsp;</div>
  </div>
  <el-row>
    <el-col :span="4">
      <div class="graph-set" style="margin-top:30px;text-align:center;font-weight:bold;">表示セット</div>
      <el-button class="graph-set" @click="displayClick(1)">グラフをすべてOFF</el-button><br/>
      <el-button class="graph-set">セット1</el-button><br/>
      <el-button class="graph-set">セット2</el-button><br/>
      <el-button class="graph-set">セット3</el-button><br/>
      <el-button class="graph-set">セット4</el-button><br/>
      <el-button class="graph-set">セット5</el-button><br/>
    </el-col>
    <el-col :span="20">
      <chart :height="350" :chart-data="graphData" :options="chartOptions"></chart>
      <el-row style="margin:5px auto;">
        <el-col :span="1" style="text-align:right">
          <el-button @click="prev()"
            style="width:100%;height:30px;padding:5px;margin-right:3px"
            :disabled="+sliderOptions.value <= 0">前</el-button>
        </el-col>
        <el-col :span="22" style="text-align:center">
          <vue-slider ref="sliderScroll"
            style="margin-top:5px"
            v-bind="sliderOptions"
            v-model="sliderOptions.value"
            @callback="changeSlider"
            ></vue-slider>
        </el-col>
        <el-col :span="1" style="text-align:left;">
          <el-button @click="next()"
            style="width:100%;height:30px;padding:5px;margin-left:3px"
            :disabled="sliderOptions.value >= sliderOptions.max">次</el-button>
        </el-col>
      </el-row>
    </el-col>
  </el-row>
  <el-row>
    <el-col :span="24">
      <el-table
        :data="gridData"
        height="400"
        border
        :row-class-name="setRowStyle"
        style="width: 98.8%;font-size:14px;">
        <el-table-column
           label=""
           width="80">
         <template #default="scope">
           <el-switch
              v-model="scope.row.switch"
              :on-color="scope.row.sColor"
              :off-color="hiddenColor"
              @change="changeSwitch(scope.row)"
              on-text=""
              off-text="">
            </el-switch>
         </template>
        </el-table-column>
        <el-table-column
          prop="category"
          width="165"
          label="集計項目">
        </el-table-column>
        <template v-for="item in gridColumn">
          <el-table-column
            :prop="item.name"
            :label="item.name"
            :formatter="numberFormatter"
            width="98">
          </el-table-column>
        </template>
      </el-table>
    </el-col>
  </el-row>
  <div style="margin:10px;text-align:right">
    <el-button v-if="auth.KpiCbCopy"
              v-clipboard:copy="cbText"
              v-clipboard:success="onCbCopy">クリップボードコピー</el-button>
  </div>
</div>
</template>

<script>
// 集計データ（新たに要素を追加するにはここ記述）
const kpiData = {
  NUSR: {label: '会員登録者数', gType: 'line', unit: '人', show: true},
  USR: {label: '会員登録者数(累計)', gType: 'line', unit: '人', show: true},

  AU: {label: '会員ログイン者数', gType: 'line', unit: '人', show: true},

  // M: {label: '会員数', gType: 'line', unit: '人', show: false},
  // MC: {label: '会員数（累計）', gType: 'line', unit: '人', show: false},

  NM: {label: '退会者数', gType: 'line', unit: '人', show: false},
  NMC: {label: '退会者数（累計）', gType: 'line', unit: '人', show: false},

  DST: {label: '配信有効者数', gType: 'line', unit: '人', show: false},
  NDST: {label: '配信無効者数', gType: 'line', unit: '人', show: false},

  TDAM: {label: 'ドロップ数（累計）', gType: 'line', unit: '人', show: false},
  DRMR: {label: 'ドロップ復帰数', gType: 'line', unit: '人', show: false},

  P: {label: '課金額', gType: 'bar', unit: '円', show: false},
  PU: {label: '課金者数', gType: 'line', unit: '人', show: false},
  AP: {label: '平均課金額', gType: 'bar', unit: '円', show: false},
  PR: {label: '課金率', gType: 'line', unit: '%', show: false},
  CP: {label: 'コイン購入額', gType: 'bar', unit: '円', show: false},
  ACP: {label: '平均コイン購入額', gType: 'bar', unit: '円', show: false},

  CB: {label: 'コイン購入枚数', gType: 'bar', unit: '枚', show: false},
  CBU: {label: 'コイン購入者数', gType: 'line', unit: '人', show: false},
  ACB: {label: '平均コイン購入枚数', gType: 'bar', unit: '枚', show: false},

  CU: {label: 'コイン利用枚数', gType: 'bar', unit: '枚', show: false},
  CUU: {label: 'コイン利用者数', gType: 'line', unit: '人', show: false},
  ACU: {label: '平均コイン利用枚数', gType: 'bar', unit: '枚', show: false},

  FGU: {label: '無料ガチャ利用者数', gType: 'line', unit: '人', show: false}
}

import KpiChart from '../Graphs/KpiChart'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import vueSlider from 'vue-slider-component'

export default {
  name: 'kpi',
  components: {
    'chart': KpiChart,
    'vue-slider': vueSlider
  },
  data () {
    return {
      auth: this.$store.state.marketingDivision,
      userInfo: this.$parent.userInfo,
      since: new Date().getTime(),
      until: new Date().getTime(),
      unit: '0',
      type: '1',
      resList: [],
      kpiList: [],
      graphData: [],
      datasets: [],
      gridData: [],
      gridColumn: [],
      cDisplayCount: 10,
      hiddenColor: '#8e8e8e',
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          onClick: this.legendClick
        },
        scales: {
          xAxes: [{
            display: false,
            categorySpacing: 0,
            barThickness: 15
          }],
          yAxes: [{
            id: 'y-axis-left',
            type: 'linear',
            position: 'left',
            gridLines: {
              drawOnChartArea: false
            }
          },
          {
            id: 'y-axis-right',
            type: 'linear',
            position: 'right',
            gridLines: {
              drawOnChartArea: false
            }
          }]
        }
      },
      sliderOptions: {
        value: 0,
        width: '100%',
        height: 4,
        dotSize: 14,
        min: 0,
        max: 0,
        speed: 0,
        disabled: false,
        tooltip: 'hover',
        piecewise: true,
        bgStyle: {
          'backgroundColor': '#888'
        },
        tooltipStyle: {
          'backgroundColor': '#666',
          'borderColor': '#666'
        },
        processStyle: {
          'backgroundColor': '#888'
        },
        piecewiseStyle: {
          'backgroundColor': '#888',
          'width': '8px',
          'height': '8px'
        },
        sliderStyle: {
          'backgroundColor': '#1e90ff'
        }
      },
      colors: ['#ff7473', '#ffc952', '#47b8e0', '#34314c', '#3cb37a', '#7f1184'],
      currentShowList: [],
      selectedMax: 6,
      pickerOptions: {
        shortcuts: [{
          text: '今日',
          onClick (picker) {
            picker.$emit('pick', new Date())
          }
        },
        {
          text: '先月',
          onClick (picker) {
            picker.$emit('pick', new Date().getTime() - 3600 * 1000 * 24 * 30)
          }
        },
        {
          text: '去年',
          onClick (picker) {
            picker.$emit('pick', new Date().getTime() - 3600 * 1000 * 24 * 365)
          }
        }]
      },
      cbText: ''
    }
  },
  created () {
    // データセットとテーブルの土台を作成
    var gData = this.gridData
    var colorIndex = 0
    Object.keys(kpiData).forEach(function (key, i) {
      var options = {
        name: key,
        label: kpiData[key].label,
        type: kpiData[key].gType,
        hidden: !kpiData[key].show,
        borderWidth: 3,
        data: []
      }

      gData[i] = []
      gData[i].category = kpiData[key].label
      gData[i].switch = kpiData[key].show
      gData[i].index = i

      if (colorIndex <= this.selectedMax && gData[i].switch) {
        var color = this.getGraphColor(colorIndex, false)
        options.backgroundColor = color
        options.borderColor = color
        gData[i].sColor = color
        colorIndex++
      } else {
        options.backgroundColor = this.hiddenColor
        options.borderColor = this.hiddenColor
      }

      if (kpiData[key].gType === 'bar') {
        options.yAxisID = 'y-axis-left'
      } else if (kpiData[key].gType === 'line') {
        options.fill = false
        options.lineTension = 0.3
        options.yAxisID = 'y-axis-right'
      } else {
        options.fill = true
      }
      this.datasets.push(options)
    }, this)
    // this.currentShowList.splice(0, 1)
    this.getKpiData()
  },
  methods: {
    getKpiData () {
      Common.loadStart()
      this.kpiList = []
      var data = new FormData()
      data.append('unit', this.unit)
      this.post(Define.API_KPI, data).then(this.kpiRes)
    },
    kpiRes (response) {
      if (typeof (response.data) === 'string' || response.data.length === 0) {
        this.alert( 'KPIの取得に失敗しました:' + response.data)
      } else {
        if (this.type === '1') {
          this.kpiList = response.data
        } else if (this.type === '2') {
          var since = new Date(Common.dateFormat(this.since, 'YYYY/MM/DD 00:00:00')).getTime()
          var until = new Date(Common.dateFormat(this.until, 'YYYY/MM/DD 23:59:59')).getTime()
          var kpi = this.kpiList
          var indexArr = []
          response.data.forEach(function (item, i) {
            var time = new Date(Common.dateFormat(item.src_date, 'YYYY/MM/DD 12:00:00')).getTime()
            if (time >= since && time <= until) {
              kpi.push(item)
              indexArr.push(i)
            }
          })
          if (kpi.length === 0) {
            this.message('該当期間のデータがありません。')
            Common.loadStop()
            return
          }
          // 表示カウントを下回っていたら表示カウント数データを取り直す
          if (indexArr.length < this.cDisplayCount) {
            var i
            if (response.data[indexArr[0] + this.cDisplayCount]) {
              for (i = indexArr[indexArr.length - 1]; i < indexArr[0] - 1 + this.cDisplayCount; i++) {
                kpi.push(response.data[i])
              }
            }
          }
          this.kpiList = kpi
        }
        var so = this.sliderOptions
        so.max = this.kpiList.length - this.cDisplayCount
        so.value = so.max
        // スライダーのコールバックでグラフを作成
        if (this.kpiList.length > 0) {
          this.makeData()
        }
      }
      Common.loadStop()
    },
    makeData () {
      // 非同期でグラフとテーブルの作成
      Promise.all([this.createGraphData(), this.createGridData()])
    },
    createGraphData () {
      this.graphData = []
      this.resetDataSet()
      if (this.kpiList.length === 0) {
        return
      }
      var index = this.sliderOptions.value
      var labels = []
      var loopInit = this.kpiList.length < index + this.cDisplayCount ? this.kpiList.length - this.cDisplayCount : index
      var loopCount = loopInit + this.cDisplayCount
      loopInit = loopInit < 0 ? 0 : loopInit
      for (let i = loopInit; i < loopCount; i++) {
        var item = this.kpiList[i]
        labels.push(item.date)
        this.cDataUpdate(item)
      }
      this.graphData.labels = labels
      this.graphData.datasets = this.datasets
    },
    cDataUpdate (item) {
      let arr = Object.keys(item)
      const jCount = arr.length
      const iCount = this.datasets.length
      for (let i = 0; i < iCount; i++) {
        var data = this.datasets[i]
        for (let j = 0; j < jCount; j++) {
          if (data.name === arr[j]) {
            data.data.push(item[arr[j]])
            break
          }
        }
      }
    },
    createGridData () {
      this.gridColumn = []
      this.gridData = this.resetGridData()
      var index = this.sliderOptions.value
      var loopInit = this.kpiList.length < index + this.cDisplayCount ? this.kpiList.length - this.cDisplayCount : index
      var loopCount = loopInit + this.cDisplayCount
      loopInit = loopInit < 0 ? 0 : loopInit
      for (var i = loopInit; i < loopCount; i++) {
        var item = this.kpiList[i]
        var colTmp = []
        colTmp.name = item.date
        this.gridColumn.push(colTmp)
        this.gDataUpdate(item)
      }
      this.createCbText()
    },
    gDataUpdate (item) {
      let gridData = this.gridData
      let arr = Object.keys(kpiData)
      const iCount = arr.length
      for (let i = 0; i < iCount; i++) {
        gridData[item.date] = []
        gridData[i][item.date] = item[arr[i]]
      }
    },
    resetDataSet () {
      this.datasets.forEach(function (item, i) {
        item.data = []
      })
    },
    resetGridData () {
      var gData = []
      this.gridData.forEach(function (item, i) {
        var tmp = []
        tmp.index = item.index
        tmp.switch = item.switch
        tmp.sColor = item.sColor
        tmp.category = item.category
        gData.push(tmp)
      })
      return gData
    },
    legendClick (e, p) {
      this.datasets[p.datasetIndex].hidden = !p.hidden
      var color = this.getGraphColor(p.datasetIndex, this.datasets[p.datasetIndex].hidden)
      this.datasets[p.datasetIndex].backgroundColor = color
      this.datasets[p.datasetIndex].borderColor = color
      if (p.hidden) {
        this.gridData[p.datasetIndex].sColor = color
      } else {
        this.gridData[p.datasetIndex].sColor = this.hiddenColor
      }
      this.gridData[p.datasetIndex].switch = p.hidden
      this.makeData()
    },
    changeSwitch (row) {
      var obj = {datasetIndex: row.index, hidden: row.switch}
      this.legendClick(null, obj)
    },
    getColorAtIndex (index) {
      for (var i = 0; i < this.currentShowList.length; i++) {
        if (index === this.currentShowList[i].listIndex) {
          return this.colors[this.currentShowList[i].colorIndex]
        }
      }
    },
    getGraphColor (listIndex, hidden) {
      var i
      if (hidden) {
        for (i = 0; i < this.currentShowList.length; i++) {
          if (listIndex === this.currentShowList[i].listIndex) {
            // this.usedColor &= ~(1 << this.currentShowList[i].colorIndex)
            this.currentShowList.splice(i, 1)
          }
        }
        return this.hiddenColor
      } else {
        var selectColor = ''
        var j = 0
        // 使用されていない色を調べる
        for (i = 0; i < this.selectedMax; i++) {
          var color = this.colors[i]
          var find = false
          for (j = 0; j < this.currentShowList.length; j++) {
            if (color === this.currentShowList[j].color) {
              find = true
              break
            }
          }
          if (!find) {
            selectColor = this.colors[i]
            break
          }
        }
        if (selectColor === '') {
          var hiddenListIndex = this.currentShowList[this.currentShowList.length - 1].listIndex
          selectColor = this.currentShowList[this.currentShowList.length - 1].color
          this.datasets[hiddenListIndex].backgroundColor = this.hiddenColor
          this.datasets[hiddenListIndex].borderColor = this.hiddenColor
          this.datasets[hiddenListIndex].hidden = true
          this.gridData[hiddenListIndex].switch = false
          this.gridData[hiddenListIndex].sColor = this.hiddenColor
          this.currentShowList.splice(this.currentShowList.length - 1, 1)
        }
        var colorObj = {
          listIndex: listIndex,
          color: selectColor
        }
        this.currentShowList.push(colorObj)
        return selectColor
      }
    },
    prev () {
      this.sliderOptions.value--
      this.changeSlider(null)
    },
    next () {
      this.sliderOptions.value++
      this.changeSlider(null)
    },
    changeSlider (e) {
      if (e !== null) {
        this.sliderOptions.value = e
      }
      if (this.kpiList.length > 0) {
        this.makeData()
      }
    },
    displayClick (type) {
      switch (type) {
        case 1:
          for (var i = 0; i < this.gridData.length; i++) {
            this.gridData[i].switch = false
            this.changeSwitch(this.gridData[i])
          }
          break
      }
    },
    createCbText () {
      var cbcData = [].concat(this.resetGridData())
      // var cbcData = [].concat(this.gridData)
      var text = '\t'
      for (var i = 0; i < this.kpiList.length; i++) {
        var item = this.kpiList[i]
        var colTmp = []
        colTmp.name = item.date
        let arr = Object.keys(kpiData)
        const iCount = arr.length
        for (let j = 0; j < iCount; j++) {
          cbcData[j][item.date] = item[arr[j]]
        }
        text += item.date + '\t'
//        text += Common.dateFormat(item.date, 'YYYY-MM-DD') + '\t'
      }
      text += '\n'
      for (i = 0; i < cbcData.length; i++) {
        var data = cbcData[i]
        text += data.category + '\t'
        Object.keys(data).forEach(function (key) {
          if (key === 'index') return
          if (key === 'sColor') return
          if (key === 'switch') return
          if (key === 'category') return
          text += data[key] + '\t'
        })
        text += '\n'
      }
      this.cbText = text
    },
    onCbCopy () {
      this.message('KPIデータ一覧をクリップボードにコピーしました', 'success')
    },
    numberFormatter (row, column) {
      if (row[column.property]) {
        return Common.numberFormat(row[column.property])
      } else {
        return '0'
      }
    },
    setRowStyle (row, index) {
      return 'row-style'
    }
  }
}
</script>
<style>
.el-table .row-style{
  font-size:13px;
}
</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.graph-set{
  width:150px;
  margin-top:10px;
  margin-left:30px;
}
</style>
