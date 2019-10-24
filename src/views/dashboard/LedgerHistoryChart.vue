<script>
import {Line} from 'vue-chartjs'
import datalabels from 'chartjs-plugin-datalabels'
import Vue2Filters from 'vue2-filters'

export default {
  mixins: [Vue2Filters.mixin],
  extends: Line,
  data: () => ({
    chartdata: {
      labels: ['Enero', 'Febrero', "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto"],
      datasets: [
        {
          label: 'Ingresos',
          data: [40, 20, -10, 50, 0, -30, 10, 60],
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

  mounted() {
    this.renderChart(this.chartdata, this.options)
  }
}
</script>