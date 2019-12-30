<script>
import {Pie} from 'vue-chartjs'
import datalabels from 'chartjs-plugin-datalabels'

export default {
  extends: Pie,
  props: {
    chartdata: {
      required: true,
      type: Object
    },
    options: {
      required: true,
      type: Object
    }
  },

  mounted() {
    this.renderChart(this.chartdata, this.options)
  }
}
</script>