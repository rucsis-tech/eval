<template>
  <div class="contents">
    <el-row class="content-row">
      <el-col :span="5" class="right-border">
        総アンケート数：{{questSum}} 件
      </el-col>
      <el-col :span="5" class="right-border">
        募集中アンケート数：{{nowSum}} 件
      </el-col>
    </el-row>

    <!-- アンケートテーブル -->
    <question-table
      :gridData="questionList" :questionFunc="getAnswerData">
    </question-table>

    <div class="h-border" style="margin:10px 0;width:100%;"></div>
    <el-row>
      <el-col :span="19" style="font-weight:bold;margin:10px 0;text-align:center;">
        {{questionTitle}}
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="19" style="border-right: solid 1px #ddd;padding-right:30px;">
        <bar-chart :height="350" :chart-data="barData" :options="chartOptions"></bar-chart>
      </el-col>
      <el-col :span="5" style="padding:0px;">
        <pie-chart :height="350" :chart-data="pieData"></pie-chart>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import HorizontalBar from '../Graphs/HorizontalBarChart'
import Pie from '../Graphs/PieChart'
import QuestionTable from './QuestionnaireTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'

export default {
  name: 'question',
  props: [
    'setIdFunc',
    'getIdFunc'
  ],
  data () {
    return {
      auth: this.$store.state.marketingDivision,
      questSum: '',
      nowSum: '',
      resBar: [],
      barData: [],
      barDataSets: [],
      pieData: [],
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scaleOverride: true,
        scales: {
          xAxes: [{
            ticks: {
              beginAtZero: true
            },
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            categorySpacing: 0,
            barThickness: 20,
            gridLines: {
              display: true
            }
          }]
        },
        legend: {
          display: false
        },
        tooltips: {
          callbacks: {
            title: this.setFullLabel
          }
        }
      },
      questionList: [],
      questionTitle: ''
    }
  },
  components: {
    'bar-chart': HorizontalBar,
    'pie-chart': Pie,
    'question-table': QuestionTable
  },
  created () {
    // barチャートの土台
    this.barDataSets = [
      {
        label: '回答数',
        data: []
      }
    ]

    this.getSumData()
    this.getQuestionData()
    this.getAnswerData()
  },
  methods: {
    getQuestionData () {
      Common.loadStart()
      var data = new FormData()
      data.append('type', 1)
      this.post(Define.API_QUESTIONNAIRE, data).then(this.questionRes)
    },
    questionRes: function (response) {
      if (typeof (response.data) === 'string' || response.data.length === 0) {
        this.alert( 'アンケート取得に失敗しました。' + response.data)
      } else {
        this.questionList = response.data
      }
      Common.loadStop()
    },
    getAnswerData (questionRow) {
      // console.log(questionRow.question_num)
      if (typeof questionRow !== 'undefined') {
        var data = new FormData()
        data.append('type', 2)
        data.append('question_num', questionRow.question_num)
        this.post(Define.API_QUESTIONNAIRE, data).then(this.answerRes)
      }
    },
    answerRes: function (response) {
      this.questionTitle = response.data.questionTitle
      this.resBar = response.data.barData
      this.setChartData(response.data.pieData)
    },
    getSumData () {
      var data = new FormData()
      data.append('type', 3)
      this.post(Define.API_QUESTIONNAIRE, data).then(this.sumRes)
    },
    sumRes: function (response) {
      if (typeof (response.data) === 'string') {
        this.alert( 'アンケート総数の取得に失敗しました:' + response.data)
      } else {
        this.questSum = Common.numberFormat(response.data.sum)
        this.nowSum = Common.numberFormat(response.data.now)
      }
    },
    setChartData (pie) {
      this.createBarData()
      //this.pieData = pie
    },
    createBarData () {
      this.barData = []
      this.barDataSets.forEach(function (item, i) {
        item.data = []
      })
      var choice = this.resBar.choice
      var count = this.resBar.count
      var labels = []
      for (var i = 0; i < choice.length; i++) {
        labels.push(choice[i])
        this.barDataSets[0].data.push(count[i])
      }
      this.barData.labels = labels
      this.barData.datasets = this.barDataSets
    },
    numberFormatter (row, column) {
      if (!row[column.property]) return ''
      return Common.numberFormat(row[column.property])
    },
    setFullLabel (tooltipItem, chart) {
      return this.resBar.fullChoice[tooltipItem[0].index]
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
