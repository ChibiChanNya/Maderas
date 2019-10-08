<template>
  <v-card elevation="2">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Historial de Ingresos</span>

    </v-card-title>
    <v-card-text>
      <ledger-history-chart></ledger-history-chart>
    </v-card-text>

  </v-card>
</template>

<script>
import LedgerHistoryChart from "./LedgerHistoryChart";
export default {
  name: "LedgerHistoryDash",
  components: { LedgerHistoryChart }
}
</script>

<style scoped>

</style>