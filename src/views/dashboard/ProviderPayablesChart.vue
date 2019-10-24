<script>
import {Pie} from 'vue-chartjs'

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