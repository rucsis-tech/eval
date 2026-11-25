
// 棒グラフ色
const barColors = ['#00bfff', '#2e8b57', '#ee7800']

export default {
  props: {
    chartData: {
      required: true
    },
    options: {
    }
  },
  watch: {
    'chartData': {
      deep: true,
      handler (newData, oldData) {
        if (oldData) {
          let chart = this._chart

          var i = 0
          for (i = 0; i < newData.datasets.length; i++) {
            newData.datasets[i].backgroundColor = barColors[i]
            newData.datasets[i].borderColor = barColors[i]
          }

          // Get new and old DataSet Labels
          let newDatasetLabels = newData.datasets.map((dataset) => {
            return dataset.label
          })

          let oldDatasetLabels = oldData.datasets.map((dataset) => {
            return dataset.label
          })

          // Stringify 'em for easier compare
          const oldLabels = JSON.stringify(oldDatasetLabels)
          const newLabels = JSON.stringify(newDatasetLabels)

          // Check if Labels are equal and if dataset length is equal
          if (newLabels === oldLabels && oldData.datasets.length === newData.datasets.length) {
            newData.datasets.forEach((dataset, i) => {
              chart.data.datasets[i] = dataset
            })

            chart.data.labels = newData.labels
            chart.update()
          } else {
            chart.destroy()
            this.renderChart(this.chartData, this.options)
          }
        }
      }
    }
  }
}
