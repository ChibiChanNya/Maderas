<template>
  <v-card elevation="2">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Proveedores por pagar</span>
    </v-card-title>
    <v-card-text>
      <div class="font-weight-bold text-xs-center">
        <p>TOTAL: <span class="red--text ml-4">{{ totalAmount | currency('$')}}</span></p>
      </div>
      <provider-payables-chart :options="options" :chartdata="chartdata"></provider-payables-chart>
    </v-card-text>

  </v-card>
</template>

<script>
import ProviderPayablesChart from "./ProviderPayablesChart";

export default {
  name: "ProviderPayablesDash",
  components: {ProviderPayablesChart},
  data() {
    return {
      providers: [
        {name: "Provider 1", amount: 3000.00},
        {name: "Provider 2", amount: 2000.50},
        {name: "Provider 3", amount: 2400.00},
      ]
    }
  },
  computed: {
    totalAmount() {
      return this.providers.reduce((a, b) => {
        return a + b.amount
      }, 0)
    },
    chartdata() {
      const labels = this.providers.map((item) => item.name);
      const datasets = [
        {
          data: this.providers.map((item) => item.amount),
          backgroundColor: this.providers.map((item) => getRandomColor()),
        },
      ];
      return {labels, datasets}
    },
    options() {
      return {
        plugins: {
          labels: {
            textShadow: true,
            render: function (args) {
              return args.label + '\n $' + args.value;
            },
            fontColor: "#fff", fontSize: 16
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
    }
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

<style scoped>

</style>