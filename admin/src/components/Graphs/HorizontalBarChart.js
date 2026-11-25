import { Bar } from 'vue-chartjs'
import HorizontalBarReactive from './HorizontalBarReactive'

export default Bar.extend({
  mixins: [HorizontalBarReactive],
  mounted () {
    this.renderChart(this.chartData, this.options)
  }
})
