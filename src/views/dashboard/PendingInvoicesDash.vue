<template>
  <v-card elevation="2" class="fill-height">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Facturas por cobrar</span>
    </v-card-title>
    <v-card-text>
      <table class="mx-auto">
        <thead>
        <tr>
          <th class="text-left">Envío</th>
          <th class="text-left">Fecha de Envío</th>
          <th class="text-left">Productos</th>
          <th class="text-left">Monto a Pagar</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in items" :key="item.invoice">
          <td>{{ item.invoice }}</td>
          <td >{{ item.paymentDate | moment('DD/M/YYYY') }}</td>
<!--          <td>-->
<!--            <template v-if="props.item.shipment_details.length >0">-->
<!--              <v-layout v-for="(details, index) in map_details(props.item)" :key="index" justify-start>-->
<!--                <v-flex>-->
<!--                  {{details.product}}-->
<!--                </v-flex>-->
<!--                <v-flex>-->
<!--                  {{details.units}}-->
<!--                </v-flex>-->
<!--              </v-layout>-->
<!--            </template>-->
<!--            <template v-else>-->
<!--              <div class="text-md-left pa-2" v-if="props.item.status === 'pendiente'">-->
<!--                Sin productos-->
<!--              </div>-->
<!--            </template>-->
<!--          </td>         -->
          <td> () </td>
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
import { get_invoices_dash } from '../../api/dashboardController'

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
  async mounted(){
    const { data } = await get_invoices_dash()
    console.log('STUFF', data)
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