<script>
import { Line } from 'vue-chartjs'

export default {
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
          pointRadius: 5,
          pointBackgroundColor: "rgba(201,45,62,0.29)"
        },
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  }),

  mounted () {
    this.renderChart(this.chartdata, this.options)
  }
}
</script>