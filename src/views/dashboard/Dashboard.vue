<template>
  <section id="dashboard">

    <h1 class="text-md-center my-4">Dashboard</h1>
    <v-card>
      <v-container fluid grid-list-xl>
        <v-layout row wrap justify-space-around>
          <v-flex xs12 sm6>
            <low-stock-list />
          </v-flex>
          <v-flex xs12 sm6 >
            <pending-invoices/>
          </v-flex>
          <v-flex xs12 sm6>
            <ledger-history/>
          </v-flex>
          <v-flex xs12 sm6>
            <provider-payables/>
          </v-flex>
        </v-layout>
      </v-container>
    </v-card>
  </section>
</template>

<script>
import LowStockList from "./LowStockDash";
import LedgerHistory from "./LedgerHistoryDash";
import PendingInvoices from "./PendingInvoicesDash";
import ProviderPayables from "./ProviderPayablesDash"
export default {
  name: "Dashboard",
  components: { LowStockList, LedgerHistory, PendingInvoices, ProviderPayables },
}
</script>

<style scoped>

</style>