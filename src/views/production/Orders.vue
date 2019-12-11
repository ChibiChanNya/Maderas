<template>
  <section id="suppliers">

    <h1 class="text-md-center my-4 section-header">{{total_items}} Pedidos por Clientes</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent scrollable>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">Registrar Pedido</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text class="pt-0" style="height: 500px">
              <v-form ref="form" v-model="valid_form" lazy-validation>
                <v-container grid-list-md class="py-0">
                  <v-layout wrap justify-center>
                    <v-flex xs12 sm6>
                      <v-select
                        v-model="editedItem.client_id"
                        hint="Cliente"
                        :items="clients"
                        item-text="name"
                        item-value="id"
                        label="Elije al cliente"
                        persistent-hint
                        single-line
                        :rules="required"
                      />
                    </v-flex>

                    <v-flex xs4 sm6>
                      <v-text-field v-model="editedItem.user"
                                    label="Usuario"/>
                    </v-flex>

                    <v-flex xs12 class="mt-3">
                      <v-textarea outline auto-grow rows="2"
                                  v-model="editedItem.description"
                                  :rules="required"
                                  label="Descripción del pedido"/>
                    </v-flex>

                    <v-flex xs6 sm4>
                      <v-select
                        v-model="editedItem.status"
                        :items="status_list"
                        label="Status"
                        item-text="name"
                        item-value="value"
                        persistent-hint
                        :rules="required"
                      />
                    </v-flex>

                    <v-flex xs6 sm4>
                      <v-text-field :value="totalPrice"
                                    color="green"
                                    readonly
                                    persistent-hint
                                    prefix="$"
                                    label="Precio Total"/>
                    </v-flex>
                    <v-flex xs6 sm4>
                      <v-text-field v-model="editedItem.contract"
                                    label="No. Contrato"/>
                    </v-flex>

                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog1"
                        v-model="modal_date_1"
                        :return-value.sync="editedItem.request_date"
                        persistent
                        lazy
                        full-width
                        width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            label="Fecha de solicitud"
                            prepend-icon="event"
                            readonly
                            clearable
                            :value="formatted_date(editedItem.request_date)"
                            @click:clear="editedItem.request_date = null"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker v-model="editedItem.request_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer/>
                          <v-btn flat color="primary" @click="modal_date_1 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog1.save(editedItem.request_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog2"
                        :return-value.sync="editedItem.finish_date"
                        persistent
                        v-model="modal_date_2"
                        lazy
                        full-width
                        width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            label="Fecha de terminación"
                            prepend-icon="event"
                            clearable
                            :value="formatted_date(editedItem.finish_date)"
                            :rules="after_order_date_rule"
                            @click:clear="editedItem.finish_date = null"
                            readonly
                            v-on="on"
                          />
                        </template>
                        <v-date-picker v-model="editedItem.finish_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer/>
                          <v-btn flat color="primary" @click="modal_date_2 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog2.save(editedItem.finish_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>

                    <v-flex xs12>
                      <h3>Productos Solicitados</h3>
                    </v-flex>
                    <v-layout
                      v-for="(product, index) in editedItem.order_details" :key="index">

                      <v-flex xs9>
                        <v-select
                          v-model="product.product_id"
                          hint="Producto"
                          item-value="id"
                          label="Elije un producto"
                          :items="products"
                          persistent-hint
                          single-line
                          :rules="required">

                          <template v-slot:item="props">
                            {{ props.item.name }}
                          </template>
                          <template v-slot:selection="props">
                            {{ props.item.name }}
                          </template>
                        </v-select>
                      </v-flex>
                      <v-flex xs2>
                        <v-text-field v-model="product.units"
                                      :rules="numberRules"
                                      type="number"
                                      label="Cantidad"/>
                      </v-flex>
                      <v-flex xs1>
                        <v-btn flat icon style="align-self:center"
                               @click="removeProduct(product)">
                          <v-icon class="red--text">close</v-icon>
                        </v-btn>
                      </v-flex>
                    </v-layout>
                    <template v-if="editedItem.order_details.length === 0">
                      <h4>No se han registrado productos para este pedido</h4>
                    </template>
                    <v-flex>
                      <v-btn flat color="info" @click="addProduct">Agregar nuevo producto
                      </v-btn>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-form>

            </v-card-text>

            <v-card-actions>
              <v-spacer/>
              <v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
              <v-btn color="blue darken-1" flat @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-spacer/>
        <v-text-field
          v-model="search"
          append-icon="search"
          @input="isTyping = true"
          label="Buscar..."
          single-line
          hide-details
        />
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
        :loading="loading"
        :search="search"
        :pagination.sync="pagination"
        rows-per-page-text="Elementos por página"
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ client_name(props.item.client_id) }}</td>
            <td class="">{{ status_name(props.item.status) }}</td>
            <td class="">{{ props.item.user }}</td>
            <td class="">{{ props.item.description }}</td>
            <td>
              <template v-if="props.item.order_details.length >0">
                <v-layout v-for="(product, index) in props.item.order_details" :key="index" justify-start>
                  <v-flex>
                    {{product_name(product.product_id)}}
                  </v-flex>
                  <v-flex>
                    {{product.units}}
                  </v-flex>
                </v-layout>
              </template>
              <template v-else>
                <div class="text-md-left pa-2" v-if="props.item.status === 'pendiente'">
                  Sin productos
                </div>
              </template>
            </td>

            <td class="">{{ props.item.total_cost }}</td>
            <td class="justify-start layout px-0">
              <v-btn flat small color="blue" @click="props.expanded = !props.expanded">DETALLES
              </v-btn>
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
            <v-layout row>
              <v-divider vertical/>
              <v-flex xs6>
                <h3 class="pa-2">Entregas</h3>
                <template v-if="getShipments(props.item).length >0">
                  <tr>
                    <th>Costo</th>
                    <th>Status</th>
                    <th>Fecha de Entrega</th>
                  </tr>
                  <tr v-for="item in getShipments(props.item)" :key="item.id">
                    <td>{{ item.cost | currency('$')}}</td>
                    <td>{{status_name_shipment(item.status)}}</td>
                    <td>{{ (item.delivery_date || '--') | moment('DD/M/YYYY') }}</td>
                  </tr>
                </template>
                <template v-else>
                  <h3 class="py-3">No se ha preparado ningun envío</h3>
                </template>
              </v-flex>
            </v-layout>

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
  create_order,
  index_clients,
  index_orders,
  index_products,
  index_shipments_lite,
  remove_order,
  update_order,
} from '../../api/production_controller'
import utils from '../../mixins/utils'
import server_pagination from '../../mixins/server_pagination'
import Vue2Filters from 'vue2-filters'


export default {
  name: 'ClientOrders',
  mixins: [utils, server_pagination, Vue2Filters.mixin],

  data() {
    return {

      index_fn: index_orders,
      delete_fn: remove_order,

      loading: true,
      dialog: false,
      modal_date_1: false,
      modal_date_2: false,
      search: '',
      pagination: {
        sortBy: 'request_date',
        rowsPerPage: 25,
      },
      headers: [
        { text: 'Cliente', value: 'client_id', align: 'center' },
        { text: 'Status', value: 'status', align: 'center' },
        { text: 'Usuario', value: 'user', align: 'center' },
        { text: 'Descripción', value: 'description', align: 'center' },
        { text: 'Detalles', value: 'details', align: 'center' },
        { text: 'Costo Total', value: 'total_cost', align: 'center' },
        { text: 'Acciones', value: 'id', align: 'center', sortable: false },
      ],
      items: [],
      total_items: 0,
      clients: [],
      products: [],
      shipments: [],

      status_list: [
        { name: 'Pendiente', value: 'pendiente' },
        { name: 'En Producción', value: 'produccion' },
        { name: 'En Stock', value: 'stock' },
        { name: 'Standby', value: 'standby' },
        { name: 'Parcial', value: 'parcial' },
        { name: 'Completo', value: 'completo' },
      ],

      valid_form: true,

      after_order_date_rule: [
        v => {
          const request_date = this.editedItem.request_date && this.$moment(this.editedItem.request_date.slice(0, 10), 'YYYY-M-DD')
          const current = this.$moment(v, 'DD/M/YYYY')
          return (!v || !request_date.isAfter(current)) || 'Debe ser posterior a fecha de pedido'
        },
      ],

      editedIndex: -1,
      editedItem: {
        id: '',
        client_id: '',
        contract: '',
        total_cost: 0,
        order_details: [],
        request_date: new Date().toISOString().slice(0, 10),
        finish_date: new Date().toISOString().slice(0, 10),
        status: null,
      },
      defaultItem: {
        id: '',
        client_id: '',
        contract: '',
        total_cost: 0,
        order_details: [],
        request_date: new Date().toISOString().slice(0, 10),
        finish_date: new Date().toISOString().slice(0, 10),
        status: null,
      },

    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Pedido' : 'Editar Pedido'
    },
    totalPrice() {
      if (this.editedItem && this.editedIndex >= 0)
        return this.calculateTotalPrice(this.editedItem)
    },
  },

  mounted() {
    this.axios.all([index_products(), index_clients(), index_shipments_lite()])
      .then(this.axios.spread(function(products, clients, shipments) {
          // Both requests are now complete
          this.products = products.data
          this.clients = clients.data
          this.shipments = shipments.data
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

    isOrderAtLeast80Percent(item) {
      const shipments = this.getShipments(item)
      console.log(shipments)
      if (!item.order_details || item.order_details.length < 1 || !shipments.length < 1)
        return
      const shippedItems = shipments.reduce((total, next) => total + this.totalItems(next.order_details), 0)
      const requestedItems = this.totalItems(item.order_details)
      // console.log(shippedItems, requestedItems)
      return shippedItems / requestedItems >= 0.8
    },

    totalItems(order_details) {
      return order_details.reduce((total, next) => total + Number(next.units), 0)
    },

    status_name_shipment(val) {
      const status_list = [
        { name: 'Pendiente', value: 'pendiente' },
        { name: 'Pendiente Tratamiento', value: 'pendiente tratamiento' },
        { name: 'Listo', value: 'listo' },
        { name: 'Enviado', value: 'enviado' },
        { name: 'Pagado', value: 'pagado' },
      ]

      const status = status_list.find(stat => stat.value === val)
      return (status && status.name) || 'Null'
    },

    getShipments(item) {
      const ships = this.shipments.filter((ship) => ship.order_id === item.id)
      return ships || []
    },

    calculateTotalPrice(item) {
      const details = item.order_details || []
      return details.reduce((total, next) => {
        if (next.product_id && next.units > 0)
          return total + (this.getProductPrice(next.product_id) * next.units)
        else return total
      }, 0)
    },

    addProduct() {
      this.editedItem.order_details.push({ product_id: null, units: 0 })
    },

    removeProduct(item) {
      const index = this.editedItem.order_details.indexOf(item)
      this.editedItem.order_details.splice(index, 1)
    },


    editItem(item) {
      this.editedIndex = this.items.findIndex((order) => order.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },


    save() {
      if (this.$refs.form.validate()) {
        this.loading = true
        // Editing an User
        /* Set the  calculated cost as the total_cost */
        this.editedItem.total_cost = this.totalPrice
        const payload = JSON.parse(JSON.stringify(this.editedItem))
        if (this.editedIndex > -1) {
          update_order(payload).then(({ data: newItem }) => {
            this.editedItem.id = newItem.id
            this.$set(this.items, this.editedIndex, payload)
            this.$store.commit('setSnack', { text: 'Pedido actualizado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
          //  Creating a new User
        } else {
          create_order(payload).then(({ data: newItem }) => {
            this.editedItem.id = newItem.id
            this.items.push(JSON.parse(JSON.stringify(this.editedItem)))
            this.$store.commit('setSnack', { text: 'Pedido creado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
        }

      }
    },


  },


}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>