<script>
import {Line} from 'vue-chartjs'
import datalabels from 'chartjs-plugin-datalabels'
import Vue2Filters from 'vue2-filters'
import { get_income_dash } from '../../api/dashboardController'

export default {
  mixins: [Vue2Filters.mixin],
  extends: Line,
  data: () => ({
    chartdata: {
      labels: ['Enero', 'Febrero', "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
      datasets: [
        {
          label: 'Ingresos',
          data: [0,0,0,0,0,0,0,0,0,0,0,0],
          backgroundColor: 'rgba(0, 0, 0, 0.0)',
          borderColor: "#f81e2d",
          pointRadius: 1,
          pointBackgroundColor: "rgba(201,45,62,0.29)",
        },
      ]
    },
    options: {
      plugins: {
        datalabels: {
          color: "#ffff",
          backgroundColor: "rgba(248,30,45,0.9)",
          padding: 3,
          anchor: 'end',
          borderRadius: 20,
          formatter: function(value) {
            return "$"+value
          }
        }
      },
      responsive: true,
      maintainAspectRatio: false
    }
  }),

  async mounted() {
    const { data } = await get_income_dash()
    const months = []
    for (let prop in data) {
      if (Object.prototype.hasOwnProperty.call(data, prop)) {
       months.push(data[prop])
      }
    }
    this.chartdata.datasets[0].data = months
    this.renderChart(this.chartdata, this.options)
  }
}
</script>