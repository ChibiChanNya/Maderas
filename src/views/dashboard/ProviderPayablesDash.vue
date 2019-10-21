<template>
  <v-card elevation="2">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Proveedores por pagar</span>

    </v-card-title>
    <v-card-text>
      <provider-payables-chart></provider-payables-chart>
    </v-card-text>

  </v-card>
</template>

<script>
import ProviderPayablesChart from "./ProviderPayablesChart";
export default {
  name: "ProviderPayablesDash",
  components: { ProviderPayablesChart }
}
</script>

<style scoped>

</style>