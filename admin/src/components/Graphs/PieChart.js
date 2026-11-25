import { Pie } from 'vue-chartjs'
import PieReactive from './PieReactive'

export default Pie/*.extend({
  mixins: [PieReactive],
  mounted () {
    this.renderChart(this.chartData, {responsive: true, maintainAspectRatio: false})
  }
})*/
