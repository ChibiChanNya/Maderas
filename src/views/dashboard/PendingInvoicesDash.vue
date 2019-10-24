<template>
  <v-card elevation="2" class="fill-height">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Facturas por cobrar</span>
    </v-card-title>
    <v-card-text>
      <table class="mx-auto">
        <thead>
        <tr>
          <th class="text-left"># Factura</th>
          <th class="text-left">Fecha de Pago</th>
          <th class="text-left">Contrato</th>
          <th class="text-left">Monto a Pagar</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in items" :key="item.invoice">
          <td>{{ item.invoice }}</td>
          <td >{{ item.paymentDate | moment('DD/M/YYYY') }}</td>
          <td >{{ item.contract }}</td>
          <td >{{ item.amount | currency('$')}}</td>
        </tr>
        </tbody>
      </table>
      <div class="font-weight-bold text-md-right">
        <p >TOTAL: <span class="red--text ml-4">{{ totalAmount | currency('$')}}</span></p>
      </div>
    </v-card-text>

  </v-card>
</template>

<script>
export default {
  name: "PendingInvoicesDash",
  data() {
    return {
      items: [
        { invoice: 55545, paymentDate: new Date(), status: "pendiente", contract: 554335, amount: 5000 },
        { invoice: 12334, paymentDate: new Date(), status: "entregado", contract: 111144, amount: 1000 },
        { invoice: 654457, paymentDate: new Date(), status: "pendiente", contract: 123000, amount: 4500 },
      ]
    }
  },
  computed: {
    totalAmount(){
      return this.items.reduce((a, b)=> {
        return a + b.amount
      }, 0)
    }
  }
}
</script>

<style scoped>
th, td {
  padding: 0.5em;
  text-align: center;
}

th {
  font-size: 1.2em;
}

td.large {
  font-size: 1.2em;
}
</style>