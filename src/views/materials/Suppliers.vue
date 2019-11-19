<template>
  <section id="suppliers">

    <h1 class="text-md-center my-4 section-header">Lista de Proveedores</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">Agregar Proveedor</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-form ref="form" v-model="valid_form" lazy-validation>
                <v-container grid-list-md class="py-0">
                  <v-layout wrap justify-center>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.name"
                                    :rules="nameRules"
                                    label="Nombre"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.business_name"
                                    label="Razón Social"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model.lazy="editedItem.rfc"
                                    :rules="rfcRules"
                                    label="RFC"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model.lazy="editedItem.clabe"
                                    :rules="clabeRules"
                                    label="CLABE"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.bank"
                                    label="Bank"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.description"
                                    label="Descripción"></v-text-field>
                    </v-flex>
                    <!--                                        <v-flex xs12 sm6>-->
                    <!--                                            <v-text-field v-model.number="editedItem.money_debt"-->
                    <!--                                                          :rules="moneyRules"-->
                    <!--                                                          label="Saldo"></v-text-field>-->
                    <!--                                        </v-flex>-->

                  </v-layout>
                </v-container>
              </v-form>

            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
              <v-btn color="blue darken-1" flat @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-spacer></v-spacer>
        <v-text-field
          v-model="search"
          append-icon="search"
          label="Buscar..."
          single-line
          hide-details
        ></v-text-field>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
        :loading="loading"
        :search="search"
        :custom-sort="customSort"
        hide-actions
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ props.item.name || '--'}}</td>
            <td class="">{{ props.item.business_name || '--'}}</td>
            <td class="">{{ props.item.rfc || '--'}}</td>
            <td class="">{{ props.item.clabe || '--'}}</td>
            <td class="">{{ props.item.bank || '--'}}</td>
            <td class="">{{ props.item.description || '--'}}</td>
            <td>
              <v-btn flat small color="red" @click="props.expanded = !props.expanded">${{
                Number(calc_debt(props.item)).toFixed(2) }}
              </v-btn>
            </td>
            <td class="justify-start layout px-0">
              <v-icon
                small
                class="mr-5 "
                @click="editItem(props.item)"
              >
                edit
              </v-icon>
              <v-icon
                small
                class="mr-4"

                @click="deleteItem(props.item)"
              >
                delete
              </v-icon>
            </td>
          </tr>
        </template>

        <template v-slot:expand="props">
          <div class="grey lighten-3 pl-2">
            <template v-if="unpaid_orders(props.item).length >0">
              <tr>
                <th>Desc. Pedido</th>
                <th>Fecha Solicitud</th>
                <th>Fecha Entrega</th>
                <th>Status</th>
                <th>Por Pagar</th>
                <th># Factura</th>
                <th>Fecha para Pago</th>
              </tr>
              <tr v-for="order in unpaid_orders(props.item)" :key="order.id">
                <td class="text-xs-left">
                  {{ order.description || ''}}
                </td>
                <td class="text-xs-center">
                  {{order.request_date || '--' | moment('DD/M/YYYY') }}
                </td>
                <td class="text-xs-center">
                  {{order.delivery_date || '--' | moment('DD/M/YYYY')}}
                </td>
                <td class="text-xs-center">
                  {{ status_name(order.status) || '--'}}
                </td>
                <td class="text-xs-center">
                  {{order.remaining_cost | currency('$') || '----'}}
                </td>
                <td class="text-xs-center">
                  {{order.invoice || '--'}}
                </td>
                <td class="text-xs-center">
                  {{order.payment_date || '--' | moment('DD/M/YYYY')}}
                </td>
              </tr>
            </template>
            <template v-else>
              <h3 class="py-3">No hay pedidos sin pagar para este proveedor</h3>
            </template>

          </div>

        </template>

        <template v-slot:no-data>
          <h1 v-if="loading" class="text-md-center my-3">
            <v-icon>timelapse</v-icon>
            Cargando datos ...
          </h1>
          <h1 v-else class="text-md-center my-3">
            <v-icon>warning</v-icon>
            No se encontraron datos
          </h1>
        </template>
        <template v-slot:pageText="props">
          Elementos {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
        </template>

      </v-data-table>
    </v-card>


  </section>
</template>

<script>
import {
  create_supplier,
  index_orders_lite,
  index_suppliers,
  remove_supplier,
  update_supplier,
} from '../../api/materials_controller'
import utils from '../../mixins/utils'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'MaterialsSuppliers',
  mixins: [utils, Vue2Filters.mixin],

  data() {
    return {
      index_fn: index_suppliers,
      delete_fn: remove_supplier,

      loading: true,
      dialog: false,
      search: '',
      headers: [
        { text: 'Nombre', value: 'name', align: 'center' },

        {
          text: 'Razón Social',
          align: 'center',
          value: 'business_name',
        },
        { text: 'RFC', value: 'rfc' },
        { text: 'CLABE', value: 'clabe' },
        { text: 'Banco', value: 'bank' },
        { text: 'Descripción', value: 'description' },
        { text: 'Por Pagar', value: 'debt' },
        { text: 'Acciones', value: 'id', sortable: false },
      ],

      items: [],
      orders: [],

      valid_form: true,

      status_list: [
        { name: 'Pendiente', value: 'pendiente' },
        { name: 'Entregado', value: 'entregado' },
        { name: 'Pagado', value: 'pagado' },
      ],


      editedIndex: -1,
      editedItem: {
        name: '',
        business_name: '',
        rfc: '',
        clabe: '',
        bank: '',
        description: '',
        logo: '/fd/',
        money_debt: 0,
        order_details: [],
      },
      defaultItem: {
        name: '',
        business_name: '',
        rfc: '',
        clabe: '',
        bank: '',
        description: '',
        logo: '/fd/',
        money_debt: 0,
        order_details: [],
      },

    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Proveedor' : 'Editar Proveedor'
    },

  },

  mounted() {
    this.axios.all([index_suppliers(), index_orders_lite()])
      .then(this.axios.spread(function (providers, orders) {
          // Both requests are now complete
          this.items = providers.data
          this.orders = orders.data
        }.bind(this),
      ))
      .catch(error => {
        this.$store.commit('setSnack', { text: error, color: 'red' })
      })
      .finally(() => {
        this.loading = false
      })
  },

  methods: {

    unpaid_orders(item) {
      return this.orders.filter((order) => order.provider_id === item.id && order.status !== 'pagado')
    },

    calc_debt(item) {
      return this.unpaid_orders(item).reduce(function (a, b) {
        return parseInt(a) + (parseInt(b.remaining_cost) || 0)
      }, 0)
    },


    editItem(item) {
      this.editedIndex = this.items.findIndex((supplier) => supplier.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },

    save() {
      if (this.$refs.form.validate()) {
        this.loading = true
        // Editing an User
        if (this.editedIndex > -1) {
          update_supplier(this.editedItem).then(({ data: newItem }) => {
            this.$set(this.items, this.editedIndex, newItem)
            this.$store.commit('setSnack', { text: 'Proveedor actualizado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
          //  Creating a new User
        } else {
          create_supplier(this.editedItem).then(({ data: newItem }) => {
            this.items.push(newItem)
            this.$store.commit('setSnack', { text: 'Proveedor creado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
        }

      }
    },


    customSort(items, index, isDesc) {
      items.sort((a, b) => {
        if (index === 'debt') {
          return this.compare(this.calc_debt(a), this.calc_debt(b), isDesc)
        } else {
          return this.compare(a[index], b[index], isDesc)
        }
      })
      return items
    },


  },

}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>