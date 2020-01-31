<template>
  <section id="suppliers">

    <h1 class="text-md-center my-4 section-header">{{total_items}} Pedidos a Proveedores</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent scrollable>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-0" v-on="on">Registrar Pedido</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text class="pt-0" style="height: 500px">
              <v-form ref="form" v-model="valid_form" lazy-validation>
                <v-container grid-list-md class="py-0">
                  <v-layout wrap justify-center>
                    <v-flex xs12 sm8>
                      <v-select
                        v-model="editedItem.provider_id"
                        hint="Proveedor"
                        :items="providers"
                        item-text="name"
                        item-value="id"
                        label="Elije al proveedor"
                        persistent-hint
                        single-line
                        :rules="required"
                      ></v-select>
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
                      ></v-select>
                    </v-flex>

                    <v-flex xs12 class="mt-3">
                      <v-textarea outline auto-grow rows="2"
                                  v-model="editedItem.description"
                                  :rules="required"
                                  label="Descripción del pedido"></v-textarea>
                    </v-flex>

                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog1"
                        :return-value.sync="editedItem.request_date"
                        persistent
                        v-model="modal_date_1"
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
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="editedItem.request_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer></v-spacer>
                          <v-btn flat color="primary" @click="modal_date_1 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog1.save(editedItem.request_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>
                    <!-- DLEIVERY DATE -->
                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog2"
                        :return-value.sync="editedItem.delivery_date"
                        persistent
                        v-model="modal_date_2"
                        lazy
                        full-width
                        width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            label="Fecha de entrega"
                            prepend-icon="event"
                            clearable
                            :value="formatted_date(editedItem.delivery_date)"
                            :rules="after_order_date_rule"
                            @click:clear="editedItem.delivery_date = null"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="editedItem.delivery_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer></v-spacer>
                          <v-btn flat color="primary" @click="modal_date_2 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog2.save(editedItem.delivery_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>
                    <!-- PAYMENT DATE -->
                    <v-flex xs12 sm6>
                      <v-dialog
                        ref="dialog3"
                        :return-value.sync="editedItem.payment_date"
                        persistent
                        lazy
                        full-width
                        v-model="modal_date_3"
                        width="290px"
                      >
                        <template v-slot:activator="{ on }">
                          <v-text-field
                            label="Fecha de pago"
                            prepend-icon="event"
                            clearable
                            :value="formatted_date(editedItem.payment_date)"
                            :rules="after_order_date_rule"
                            @click:clear="editedItem.payment_date = null"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker v-model="editedItem.payment_date" scrollable
                                       locale="es-mx"
                        >
                          <v-spacer></v-spacer>
                          <v-btn flat color="primary" @click="modal_date_3 = false">Cancelar
                          </v-btn>
                          <v-btn flat color="primary"
                                 @click="$refs.dialog3.save(editedItem.payment_date)">OK
                          </v-btn>
                        </v-date-picker>
                      </v-dialog>
                    </v-flex>

                    <v-flex xs6 md6>
                      <v-text-field :value="totalPrice"
                                    color="green"
                                    readonly
                                    persistent-hint
                                    prefix="$"
                                    label="Precio Total"/>
                    </v-flex>
                    <v-flex xs6 md6>
                      <v-text-field v-model.number="editedItem.remaining_cost"
                                    :rules="moneyRules"
                                    type="number"

                                    label="Restante por Pagar"></v-text-field>
                    </v-flex>
                    <v-flex xs6 md6>
                      <v-text-field v-model="editedItem.invoice"
                                    label="# Factura"></v-text-field>
                    </v-flex>

                    <v-flex xs12>
                      <h3>Lista de insumos</h3>
                    </v-flex>
                    <v-layout
                      v-for="(mat, index) in editedItem.order_details" :key="index">

                      <v-flex xs9>
                        <v-select
                          v-model="mat.material_id"
                          hint="Insumo"
                          :disabled="isEditingLocked"
                          item-value="id"
                          label="Elije un insumo"
                          :items="material_choices"
                          persistent-hint
                          single-line
                          :rules="required">

                          <template v-slot:item="props">
                            {{ props.item.name }} - {{ props.item.type }}
                          </template>
                          <template v-slot:selection="props">
                            {{ props.item.name }} - {{ props.item.type }}
                          </template>
                        </v-select>
                      </v-flex>
                      <v-flex xs2>
                        <v-text-field v-model="mat.units"
                                      :rules="numberRules"
                                      :disabled="isEditingLocked"
                                      type="number"
                                      label="Cantidad"></v-text-field>
                      </v-flex>
                      <v-flex xs1>
                        <v-btn flat icon style="align-self:center"
                               :disabled="isEditingLocked"
                               @click="removeMaterial(mat)">
                          <v-icon class="red--text">close</v-icon>
                        </v-btn>
                      </v-flex>
                    </v-layout>
                    <template v-if="editedItem.order_details.length === 0">
                      <h4>No se han registrado insumos para este pedido</h4>
                    </template>
                    <v-flex>
                      <v-btn flat color="info"
                             :disabled="isEditingLocked"
                             @click="addMaterial">
                        {{ isEditingLocked ? 'Productos ya no son modificables' : 'Agregar Insumo' }}
                      </v-btn>
                    </v-flex>
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
        <!--        <v-text-field-->
        <!--          v-model="search"-->
        <!--          @input="isTyping = true"-->
        <!--          append-icon="search"-->
        <!--          label="Buscar..."-->
        <!--          single-line-->
        <!--          hide-details-->
        <!--        ></v-text-field>-->
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
          <tr>
            <td class="">{{ provider_name(props.item.provider_id) }}</td>
            <td class="">
              <template v-if="props.item.request_date">
                {{ props.item.request_date | moment('DD/M/YYYY')}}
              </template>
              <template v-else>
                --
              </template>
            </td>
            <td class="">{{ status_name(props.item.status) || '--'}}</td>
            <td>
              <template v-if="props.item.order_details.length >0">
                <v-layout v-for="(material, index) in props.item.order_details" :key="index">
                  <v-flex>
                    {{ material_name(material.material_id)}}
                  </v-flex>
                  <v-flex>
                    x {{ material.units || 0}}
                  </v-flex>
                </v-layout>
              </template>
              <template v-else>
                <div class="text-md-left pa-2" v-if="props.item.status === 'pendiente'">
                  Sin productos
                </div>
              </template>
            </td>


            <td class="">{{ props.item.total_cost | currency('$')}}</td>
            <td class="">
              <template v-if="isNumber(props.item.remaining_cost)">
                {{props.item.remaining_cost | currency('$')}}
              </template>
              <template v-else>
                ----
              </template>
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
  add_operation,
  create_order,
  index_materials,
  index_orders,
  index_suppliers,
  remove_order,
  revert_operation,
  update_order,
} from '../../api/materials_controller'
import utils from '../../mixins/utils'
import server_pagination from '../../mixins/server_pagination'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'MaterialOrders',

  mixins: [utils, server_pagination, Vue2Filters.mixin],

  data() {
    return {
      index_fn: index_orders,
      delete_fn: remove_order,

      loading: true,
      dialog: false,
      modal_date_1: false,
      modal_date_2: false,
      modal_date_3: false,
      search: '',
      pagination: {
        sortBy: 'request_date',
      },
      headers: [
        { text: 'Proveedor', value: 'provider_id', align: 'center' },
        { text: 'Fecha Solicitud', value: 'request_date', align: 'center' },
        { text: 'Status', value: 'status', align: 'center' },
        { text: 'Insumos', value: 'payment_date', align: 'center' },
        { text: 'Costo Total', value: 'total_cost', align: 'center' },
        { text: 'Por Pagar', value: 'remaining_cost', align: 'center' },
        { text: 'Acciones', value: 'id', align: 'center', sortable: false },
      ],
      items: [],
      total_items: 0,
      providers: [],
      materials: [],

      status_list: [
        { name: 'Pendiente', value: 'pendiente' },
        { name: 'Entregado', value: 'entregado' },
        { name: 'Pagado', value: 'pagado' },
      ],

      valid_form: true,

      after_order_date_rule: [
        v => {
          const request_date = this.editedItem.request_date && this.$moment(this.editedItem.request_date.slice(0, 10), 'YYYY-M-DD')
          const current = v && this.$moment(v, 'DD/M/YYYY')
          return (!v || !request_date.isAfter(current)) || 'No puede ser antes de la fecha de pedido'
        },
      ],

      editedIndex: -1,
      editedItem: {
        id: '',
        provider_id: '',
        description: '',
        order_details: [],
        request_date: new Date().toISOString().slice(0, 10),
        delivery_date: '',
        payment_date: '',
        status: null,
        remaining_cost: 0,
        total_price: 0,
        invoice: '',
        operation_dispatched: false,
      },
      defaultItem: {
        id: '',
        provider_id: '',
        description: '',
        order_details: [],
        request_date: new Date().toISOString().slice(0, 10),
        delivery_date: '',
        payment_date: '',
        status: null,
        total_price: 0,
        remaining_cost: 0,
        invoice: '',
        operation_dispatched: false,
      },

    }
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Pedido' : 'Editar Pedido'
    },
    material_choices() {
      return (this.providers.length > 0 && this.editedItem.provider_id && this.materials.length > 0 && this.materials.filter((mat) => mat.provider_id === this.editedItem.provider_id)) || []
    },
    totalPrice() {
      if (this.editedItem){
        if(this.isProductDelivered(this.editedItem) && this.editedIndex > -1) return this.editedItem.total_price;
        else return this.calculateTotalPrice(this.editedItem) || 0
      }

    },
    isEditingLocked(){
      if (this.editedItem){
        return this.editedItem.operation_dispatched || this.isProductDelivered(this.editedItem)
      }
    }
  },
  mounted() {
    this.axios.all([index_materials(), index_suppliers()])
      .then(this.axios.spread(function(materials, providers) {
          // Both requests are now complete
          this.materials = materials.data
          this.providers = providers.data

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

    addMaterial() {
      this.editedItem.order_details.push({ material_id: null, units: 0 })
    },

    removeMaterial(item) {
      const index = this.editedItem.order_details.indexOf(item)
      this.editedItem.order_details.splice(index, 1)
    },

    editItem(item) {
      this.editedIndex = this.items.findIndex((order) => order.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },

    isProductDelivered(item) {
      return item && ['entregado', 'pagado'].includes(item.status)
    },

    calculateTotalPrice(item) {
      const details = item.order_details || []
      return details.reduce((total, next) => {
        if (next.material_id && next.units > 0)
          return total + (this.getMaterialPrice(next.material_id) * next.units)
        else return total
      }, 0)
    },

    async save() {
      if (this.$refs.form.validate()) {
        if( !(this.editedItem.order_details && this.editedItem.order_details.length > 0)){
          this.$store.commit('setSnack', { text: 'Asegúrate de agregar por lo menos 1 producto', color: 'warning' })
          return;
        }
        this.loading = true
        let makeOperation = 0
        /* Set the  calculated cost as the total_cost */
        this.editedItem.total_cost = this.totalPrice
        // Editing an Order

        const oldItem = this.items[this.editedIndex]
        /* Check if the prder is moving from pending/nonexistent -> delivered or forward*/
        if (!this.isProductDelivered(oldItem) && this.isProductDelivered(this.editedItem)) {
          makeOperation = await this.$dialog
            .confirm('El cambió de status resultará en agregar los nuevos insumos a inventario, ¿continuar?')
            .then((dialog) => {
              dialog.close()
              return 1
            }).catch(() => {
              return 'stop'
            })
        }
        /* Check if an order is being reverted */
        else if (this.isProductDelivered(oldItem) && !this.isProductDelivered(this.editedItem)) {
          makeOperation = await this.$dialog
            .confirm('El cambió de status resultará en sacar los insumos del inventario por cancelación, ¿continuar?')
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
        if (this.editedIndex > -1) {

          update_order(this.editedItem).then(async ({ data: newItem }) => {
            this.editedItem.id = newItem.id
            this.$set(this.items, this.editedIndex, JSON.parse(JSON.stringify(this.editedItem)))
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
          //  Creating a new Order
        } else {
          create_order(this.editedItem).then(({ data: newItem }) => {
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

    make_operation() {
      if (this.editedItem.order_details.length < 1) {
        return
      }
      this.loading = true
      const id = this.editedItem.id
      add_operation(id).then(() => {
        this.editedItem.operation_dispatched = true
        this.items[this.editedIndex].operation_dispatched = true
        this.$store.commit('setSnack', { text: 'Insumos agregados al inventario', color: 'info' })
      }).catch(err => {
        this.$store.commit('setSnack', { text: err, color: 'red' })
      }).finally(() => {
        this.loading = false
      })
    },

    revert_operation() {
      if (this.editedItem.order_details.length < 1) {
        return
      }
      this.loading = true
      const id = this.editedItem.id
      revert_operation(id).then(() => {
        this.editedItem.operation_dispatched = false
        this.items[this.editedIndex].operation_dispatched = false
        this.$store.commit('setSnack', { text: 'Insumos eliminados del inventario', color: 'info' })
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