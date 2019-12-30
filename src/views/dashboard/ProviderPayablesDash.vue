<template>
  <v-card elevation="2">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Proveedores por pagar</span>
    </v-card-title>
    <v-card-text>
      <div class="font-weight-bold text-xs-center">
        <p>TOTAL: <span class="red--text ml-4">{{ totalAmount | currency('$')}}</span></p>
      </div>
      <provider-payables-chart v-if="!loading" :options="options" :chartdata="chartdata"></provider-payables-chart>
    </v-card-text>

  </v-card>
</template>

<script>
import ProviderPayablesChart from './ProviderPayablesChart'
import { get_providers_dash } from '../../api/dashboardController'

export default {
  name: 'ProviderPayablesDash',
  components: { ProviderPayablesChart },
  data() {
    return {
      providers: [],
      loading: true,
    }
  },
  async mounted() {
    const { data } = await get_providers_dash()
    this.providers = data
    this.loading = false
  },
  computed: {
    totalAmount() {
      return this.providers.reduce((a, b) => {
        return a + b.amount
      }, 0)
    },
    chartdata() {
      const labels = this.providers.map((item) => item.name)
      const datasets = [
        {
          data: this.providers.map((item) => item.amount),
          backgroundColor: this.providers.map((item) => getRandomColor()),
        },
      ]
      return { labels, datasets }
    },
    options() {
      return {
        plugins: {
          datalabels: {
            textShadowColor: 'black',
            textShadowBlur: 4,
            formatter: function(value, context) {
              return context.chart.data.labels[context.dataIndex] + '\n $' + value
            },
            color: '#fff',
            font: {
              size: '15',
            },
          },
        },
        responsive: true,
        maintainAspectRatio: false,
      }
    },
  },
}

function getRandomColor() {
  let letters = '0123456789ABCDEF'
  let color = '#'
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)]
  }
  return color
}
</script>

<style scoped>

</style>