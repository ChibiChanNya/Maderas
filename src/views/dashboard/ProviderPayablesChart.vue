<script>
import {Pie} from 'vue-chartjs'
import Labels from 'chartjs-plugin-labels'

export default {
  extends: Pie,
  data: () => ({

    chartdata: {

      labels: ['Proveedor 1', 'Proveedor 2', "Proveedor 3"],
      datasets: [
        {
          data: [3000.00, 4000.00, 6000.00],
          backgroundColor: [getRandomColor(), getRandomColor(), getRandomColor()],
        },
      ]
    },
    options: {
      plugins: {
        labels: {
          textShadow: true,
          render: function (args) {
            return args.label+ '\n $' + args.value;
          },
          fontColor: "#fff", fontSize: 16
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
function getRandomColor() {
  let letters = '0123456789ABCDEF';
  let color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}
</script>