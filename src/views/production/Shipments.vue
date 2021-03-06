<template>
  <section id="suppliers">

    <h1 class="text-md-center my-4 section-header">Entregas de Producto</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">Registrar Entrega</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-form ref="form" v-model="valid_form" lazy-validation>
                <v-container grid-list-md class="py-0">
                  <v-layout wrap justify-center>
                    <v-flex xs12>
                      <v-autocomplete
                        v-model="editedItem.order_id"
                        hint="Pedido"
                        :filter="ordersFilter"
                        :items="orders"
                        item-value="id"
                        label="Elije el pedido"
                        persistent-hint
                        single-line
                        :rules="required"
                      >
                        <template v-slot:item="props">
                          {{ props.item.id }} - {{props.item.user}} - {{props.item.description}}
                        </template>
                        <template v-slot:selection="props">
                          {{ props.item.id }} - {{props.item.user}} - {{props.item.description}}
                        </template>
                      </v-autocomplete>
                    </v-flex>
                    <v-flex xs12 sm6>
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
                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog1"
                        v-model="modal_date_1"
                        :return-value.sync="editedItem.delivery_date"
                        persistent
                        lazy
                        full-width
                        width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            label="Fecha de envío"
                            prepend-icon="event"
                            readonly
                            clearable
                            :value="formatted_date(editedItem.delivery_date)"
                            @click:clear="editedItem.delivery_date = null"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker v-model="editedItem.delivery_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer/>
                          <v-btn flat color="primary" @click="modal_date_1 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog1.save(editedItem.delivery_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field v-model="editedItem.certificate"
                                    label="Cert. Tratamiento"/>
                    </v-flex>
                    <v-flex xs6 sm4>
                      <v-text-field v-model.number="editedItem.buy_order"
                                    label="Orden de Compra"/>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field :value="totalPrice"
                                    color="green"
                                    readonly
                                    persistent-hint
                                    prefix="$"
                                    label="Precio Total"/>
                    </v-flex>
                    <v-flex xs12>
                      <h3>Productos Enviados</h3>
                    </v-flex>
                    <v-layout
                      v-for="(product, index) in editedItem.shipment_details" :key="index">

                      <v-flex xs8>
                        <v-select
                          v-model="product.product_id"
                          :disabled="isEditingLocked"
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
                                      :disabled="isEditingLocked"
                                      :rules="numberRules"
                                      type="number"
                                      label="Cantidad"/>
                      </v-flex>
                      <v-flex xs1>
                        <v-btn flat icon style="align-self:center"
                               :disabled="isEditingLocked"
                               @click="removeProduct(product)">
                          <v-icon class="red--text">close</v-icon>
                        </v-btn>
                      </v-flex>
                    </v-layout>
                    <template
                      v-if="!editedItem.shipment_details || editedItem.shipment_details.length === 0">
                      <h4>No se han registrado productos para este envío</h4>
                    </template>
                    <v-flex>
                      <v-btn
                        :disabled="isEditingLocked"
                        flat color="info" @click="addProduct">
                        {{ isEditingLocked ? 'Productos ya no son modificables' : 'Agregar Producto' }}
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
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
        :loading="loading"
        :pagination.sync="pagination"
        :total-items="total_items"
        rows-per-page-text="Elementos por página"
      >
        <template v-slot:items="props">
          <tr :class="{'red lighten-4': isShipmentBehindOnPayments(props.item)}">
            <td class="">{{ order_data(props.item.order_id) }}</td>
            <td class="">{{ props.item.cost | currency('$') }}</td>
            <!-- If it's 30 days behind on payment, show alert -->
            <td v-if="isShipmentBehindOnPayments(props.item)" class="red--text text--darken-3 font-weight-bold">SIN
              PAGAR
            </td>
            <td v-else>{{ status_name(props.item.status) }}</td>
            <template v-if="props.item.delivery_date">
              {{ props.item.delivery_date | moment('DD/M/YYYY')}}
            </template>
            <template v-else>
              --
            </template>
            <td>
              <template v-if="props.item.shipment_details.length >0">
                <v-layout v-for="(details, index) in map_details(props.item)" :key="index" justify-start>
                  <v-flex>
                    {{details.product}}
                  </v-flex>
                  <v-flex>
                    x {{details.units}}
                  </v-flex>
                </v-layout>
              </template>
              <template v-else>
                <div class="text-md-left pa-2" v-if="props.item.status === 'pendiente'">
                  Sin productos
                </div>
              </template>
            </td>
            <td class="justify-start layout px-0">
              <invoice-modal :shipment_id="props.item.id" :shipment_details="map_details(props.item)"
                             :cost="Number(props.item.cost)" :cfdi_uses="cfdi_uses" :payment_forms="payment_forms"
                             :payment_methods="payment_methods"/>
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
  create_shipment,
  index_clients,
  index_orders_lite,
  index_products,
  index_shipments,
  remove_shipment,
  rest_operation,
  revert_operation,
  update_shipment,
} from '../../api/production_controller'
import utils from '../../mixins/utils'
import server_pagination from '../../mixins/server_pagination'
import Vue2Filters from 'vue2-filters'
import InvoiceModal from '../../components/InvoiceModal'
import { get_cfdi_uses, get_payment_forms, get_payment_methods } from '../../api/documents_controller'

export default {
  name: 'Shipments',
  mixins: [utils, server_pagination, Vue2Filters.mixin],
  components: { InvoiceModal },
  data() {
    return {
      index_fn: index_shipments,
      delete_fn: remove_shipment,

      loading: true,
      dialog: false,
      modal_date_1: false,
      search: '',
      pagination: {
        sortBy: 'delivery_date',
      },
      headers: [
        { text: 'Pedido', value: 'order_id', align: 'center' },
        { text: 'Precio', value: 'cost', align: 'center' },
        { text: 'Status', value: 'status', align: 'center' },
        { text: 'Fecha de Envío', value: 'delivery_date', align: 'left' },
        { text: 'Detalles', value: 'details', align: 'center' },
        { text: 'Acciones', value: 'id', align: 'center' },
      ],
      items: [],
      total_items: 0,
      orders: [],
      products: [],
      clients: [],

      status_list: [
        { name: 'Pendiente', value: 'pendiente' },
        { name: 'En Producción', value: 'produccion' },
        { name: 'En Stock', value: 'stock' },
        { name: 'Standby', value: 'standby' },
        { name: 'Enviado', value: 'enviado' },
        { name: 'Facturado', value: 'facturado' },
        { name: 'Pendiente Pago', value: 'pendiente pago' },
        { name: 'Completo', value: 'completo' },
      ],

      valid_form: true,

      cfdi_uses: [],
      payment_methods: [],
      payment_forms: [],

      editedIndex: -1,
      editedItem: {
        id: '',
        order_id: '',
        cost: 0,
        certificate: '',
        delivery_date: new Date().toISOString().slice(0, 10),
        status: null,
        buy_order: null,
        shipment_details: [],
        operation_dispatched: false,
      },
      defaultItem: {
        id: '',
        order_id: '',
        cost: 0,
        certificate: '',
        delivery_date: new Date().toISOString().slice(0, 10),
        status: null,
        buy_order: null,
        shipment_details: [],
        operation_dispatched: false,
      },

    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Envío' : 'Editar Envío'
    },
    totalPrice() {
      if (this.editedItem) {
        if (this.isProductDelivered(this.editedItem) && this.editedIndex > -1) return this.editedItem.cost
        else return this.calculateTotalPrice(this.editedItem) || 0
      }
    },
    isEditingLocked() {
      if (this.editedItem) {
        return Boolean(this.editedItem.operation_dispatched || this.isProductDelivered(this.editedItem))
      }
    },
  },

  mounted() {
    this.axios.all([index_orders_lite(), index_clients(), index_products(), get_cfdi_uses(), get_payment_methods(), get_payment_forms()])
      .then(this.axios.spread(function(orders, clients, products, uses, methods, forms) {
          // Both requests are now complete
          this.orders = orders.data
          this.clients = clients.data
          this.products = products.data
          this.cfdi_uses = uses.data.data
          this.payment_methods = methods.data.data
          this.payment_forms = forms.data.data
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

    isShipmentBehindOnPayments(item) {
      const res = item.delivery_date && item.status !== 'completo' && this.$moment().diff(this.$moment(item.delivery_date), 'days') > 30
      return res
    },

    ordersFilter(item, queryText, itemText) {
      const textOne = item.id.toString()
      const textTwo = item.description.toLowerCase()
      const searchText = queryText.toLowerCase()

      return textOne.indexOf(searchText) > -1 ||
        textTwo.indexOf(searchText) > -1
    },

    map_details(item) {
      if (item.shipment_details && item.shipment_details.length > 0)
        return item.shipment_details.map((details) => ({
          product: this.product_name(details.product_id),
          units: details.units,
        }))
      else return []
    },

    order_data(id) {
      const order = this.orders && this.orders.find((order) => order.id === id)
      if (order)
        return `${order.id} - ${order.user || ''} - ${order.description}`
      else return 'Insumo no encontrado'
    },

    addProduct() {
      this.editedItem.shipment_details.push({ product_id: null, units: 0 })
    },

    removeProduct(item) {
      const index = this.editedItem.shipment_details.indexOf(item)
      this.editedItem.shipment_details.splice(index, 1)
    },

    calculateTotalPrice(item) {
      const details = item.shipment_details || []
      return details.reduce((total, next) => {
        if (next.product_id && next.units > 0)
          return total + (this.getProductPrice(next.product_id) * next.units)
        else return total
      }, 0)
    },

    editItem(item) {
      this.editedIndex = this.items.findIndex((shipment) => shipment.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },

    isProductDelivered(item) {
      return item && ['enviado', 'facturado', 'pendiente pago', 'completo'].includes(item.status)
    },

    async save() {
      if (this.$refs.form.validate()) {
        /* make sure at least one product is added */
        if (!(this.editedItem.shipment_details && this.editedItem.shipment_details.length > 0)) {
          this.$store.commit('setSnack', { text: 'Asegúrate de agregar por lo menos 1 producto', color: 'warning' })
          return
        }
        this.loading = true
        /* Set the  calculated cost as the total_cost */
        this.editedItem.cost = this.totalPrice
        const payload = JSON.parse(JSON.stringify(this.editedItem))
        // prepare to check operation
        let makeOperation = 0
        const oldItem = this.items[this.editedIndex]
        /* Check if the prder is moving from pending/nonexistant -> delivered or forward */
        if (!this.isProductDelivered(oldItem) && this.isProductDelivered(this.editedItem)) {
          makeOperation = await this.$dialog
            .confirm('El cambió de status resultará en sacar los productos seleccionados del inventario, ¿continuar?')
            .then((dialog) => {
              dialog.close()
              return 1
            }).catch(() => {
              return 'stop'
            })
        }
        /* Check if an order is being reverted */
        else if ((this.isProductDelivered(oldItem)) && !this.isProductDelivered(this.editedItem)) {
          makeOperation = await this.$dialog
            .confirm('El cambió de status resultará en regresar los productos al inventario por cancelación, ¿continuar?')
            .then((dialog) => {
              dialog.close()
              return -1
            }).catch(() => {
              return 'stop'
            })
        }
        if (makeOperation === 'stop') {
          this.close()
          this.loading = false
          return false
        }
        // Editing a Shipment
        if (this.editedIndex > -1) {
          update_shipment(payload).then(async ({ data: newItem }) => {
            this.editedItem.id = newItem.id
            this.$set(this.items, this.editedIndex, payload)
            this.$store.commit('setSnack', { text: 'Pedido actualizado exitosamente', color: 'success' })
            /* Run the make operation or reverse if required now that we have ID */
            if (makeOperation > 0) await this.make_operation()
            else if (makeOperation < 0) await this.revert_operation()
            /* Close the editor */
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })

          //  Creating a new Shipment
        } else {
          create_shipment(payload).then(async ({ data: newItem }) => {
            this.editedItem.id = newItem.id
            this.items.push(JSON.parse(JSON.stringify(this.editedItem)))
            this.$store.commit('setSnack', { text: 'Pedido creado exitosamente', color: 'success' })
            /* Run the make operation or reverse if required now that we have ID */
            if (makeOperation > 0) await this.make_operation()
            else if (makeOperation < 0) await this.revert_operation()
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
        }
      }
    },

    make_operation() {
      if (this.editedItem.shipment_details.length < 1) {
        return
      }
      this.loading = true
      const id = this.editedItem.id
      rest_operation(id).then(() => {
        this.editedItem.operation_dispatched = 1
        this.$store.commit('setSnack', { text: 'Insumos eliminados del inventario', color: 'success' })
      }).catch(err => {
        this.$store.commit('setSnack', { text: err, color: 'red' })
      }).finally(() => {
        this.loading = false
      })
    },

    revert_operation() {
      if (this.editedItem.shipment_details.length < 1) {
        return
      }
      this.loading = true
      const id = this.editedItem.id
      revert_operation(id).then(() => {
        this.editedItem.operation_dispatched = 0
        this.$store.commit('setSnack', { text: 'Insumos regresados al inventario', color: 'info' })
      }).catch(err => {
        this.$store.commit('setSnack', { text: err, color: 'red' })
      }).finally(() => {
        this.loading = false
      })
    },


  },

}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>