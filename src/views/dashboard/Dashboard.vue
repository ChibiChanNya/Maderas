<template>
  <section id="dashboard">

    <h1 class="text-md-center my-4">Dashboard</h1>
    <v-card>
      <v-container fluid grid-list-xl>
        <v-layout row wrap justify-space-around>
          <v-flex xs12 sm6>
            <low-stock-list :items="low_stock"></low-stock-list>
          </v-flex>
          <v-flex xs12 sm6 >
            <pending-invoices></pending-invoices>
          </v-flex>
          <v-flex xs12 sm6>
            <ledger-history></ledger-history>
          </v-flex>
          <v-flex xs12 sm6>
            <provider-payables></provider-payables>
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
  data(){
    return{
    //  Dummy Data for displaying on dashboard.
      /* Used to display how many products are in inventory compared to how many are required by total orders - shipments */
      low_stock: [
        { product_id: 1, product_name: "Tarima A", contracts: [ "42334", "1332" ],  stock: 3, requested: 5 },
        { product_id: 2, product_name: "Tarima B", contracts: [ "42334" ],  stock: 2, requested: 8 }
      ]
    }
  }
}
</script>

<style scoped>

</style>