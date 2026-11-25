import { Bar } from 'vue-chartjs'
import KpiReactive from './KpiReactive'

export default Bar.extend({
  mixins: [KpiReactive],
  mounted () {
    this.renderChart(this.chartData, this.options)
  }
})
