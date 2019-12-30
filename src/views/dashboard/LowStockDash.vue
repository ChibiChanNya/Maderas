<template>
  <v-card elevation="2" class="fill-height">
    <v-card-title class="blue darken-1 white--text">
      <span class="title mx-auto">Inventario contra Pedidos</span>

    </v-card-title>
    <v-card-text>
      <table class="mx-auto">
        <thead>
        <tr>
          <th class="text-left">Producto</th>
          <th class="text-left">Stock</th>
          <th class="text-left">Requeridos</th>
          <th class="text-left">Contratos</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="item in low_stock" :key="item.product_id">
          <td>{{ item.product_name }}</td>
          <td class="large" :class="{'red--text': item.stock < item.requested }">{{ item.stock }}</td>
          <td class="large">{{ item.requested }}</td>
          <td>
            <span v-for="(contract, index) in item.contracts" class="mr-2">
              {{contract}}
              <span v-if="index != item.contracts.length - 1">, </span>
            </span>
          </td>
        </tr>
        </tbody>
      </table>
    </v-card-text>

  </v-card>
</template>

<script>
import { get_inventory_dash } from '../../api/dashboardController'

export default {
  name: 'LowStockList',
  data() {
    return {
      low_stock: [],
    }
  },
  async mounted() {
    const { data } = await get_inventory_dash()
    this.low_stock = data
  },
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