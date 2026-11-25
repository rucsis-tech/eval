import { Bar } from 'vue-chartjs'
import BarReactive from './BarReactive'

export default Bar/*.extend({
  mixins: [BarReactive],
  mounted () {
    this.renderChart(this.chartData, this.options)
  }
})

*/
