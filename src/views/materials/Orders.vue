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
                <v-container grid-list-md>
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


                    <v-flex xs6 md4>
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
                            :value="editedItem.request_date | moment('DD/M/YYYY')"
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

                    <v-flex xs12 sm6 v-if="editedIndex > -1">
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
                            :value="editedItem.delivery_date | moment('DD/M/YYYY')"
                            :rules="after_order_date_rule"
                            readonly
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

                    <v-flex xs12 sm6 v-if="editedIndex > -1">
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
                            :value="editedItem.payment_date | moment('DD/M/YYYY')"
                            :rules="after_order_date_rule"
                            readonly
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
                      <v-text-field v-model.number="editedItem.total_cost"
                                    :rules="numberRules"
                                    type="number"
                                    label="Precio Total"></v-text-field>
                    </v-flex>
                    <v-flex xs6 md6 v-if="editedIndex > -1">
                      <v-text-field v-model.number="editedItem.remaining_cost"
                                    :rules="numberRules"
                                    type="number"

                                    label="Restante por Pagar"></v-text-field>
                    </v-flex>
                    <v-flex xs6 md6 v-if="editedIndex > -1">
                      <v-text-field v-model="editedItem.invoice"
                                    label="# Factura"></v-text-field>
                    </v-flex>

                    <v-flex xs12 v-if="editedIndex > -1">
                      <h3>Insumos recibidos</h3>
                    </v-flex>
                    <template v-if="editedIndex > -1">
                      <template
                        v-for="(mat, index) in editedItem.order_details">

                        <v-flex xs9 :key="index">
                          <v-select
                            v-model="mat.material_id"
                            hint="Insumo"
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
                        <v-flex xs2 :key="mat.material_id">
                          <v-text-field v-model="mat.units"
                                        :rules="numberRules"
                                        type="number"
                                        label="Cantidad"></v-text-field>
                        </v-flex>
                        <v-flex xs1 :key="mat.material_id">
                          <v-btn flat icon style="align-self:center"
                                 @click="removeMaterial(mat)">
                            <v-icon class="red--text">close</v-icon>
                          </v-btn>
                        </v-flex>
                      </template>
                      <template v-if="editedItem.order_details.length === 0">
                        <h4>No se han registrado insumos para este pedido</h4>
                      </template>
                      <v-flex>
                        <v-btn flat color="info" @click="addMaterial">Agregar nuevo insumo
                        </v-btn>
                      </v-flex>
                    </template>

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
        :pagination.sync="pagination"
        :total-items="total_items"
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ provider_name(props.item.provider_id) }}</td>
            <td class="">{{ (props.item.request_date || '--' ) | moment('DD/M/YYYY') }}</td>
            <td class="">{{ props.item.total_cost | currency('$') || '----'}}</td>
            <td class="">{{ (props.item.delivery_date || '--' ) | moment('DD/M/YYYY') }}</td>
            <td class="">{{ status_name(props.item.status) || '--'}}</td>
            <td class="">
              <v-btn flat small :color="props.item.status === 'pagado'? 'blue' : 'red'"
                     @click="props.expanded = !props.expanded">
                <template v-if="isNumber(props.item.remaining_cost)">
                  {{props.item.remaining_cost | currency('$')}}
                </template>
                <template v-else>
                  ----
                </template>
              </v-btn>
            </td>
            <td class="">{{ props.item.invoice || '--'}}</td>
            <td class="">{{ (props.item.payment_date || '--' ) | moment('DD/M/YYYY') }}</td>
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
            <p class="text-md-left pa-2"><strong>Descripción: </strong> {{ props.item.description }}</p>

            <template v-if="props.item.order_details.length >0">
              <tr>
                <th>Insumo Recibido</th>
                <th>Unidades</th>

              </tr>
              <tr v-for="material in props.item.order_details" :key="material.material_id">
                <td class="text-xs-left">
                  {{ material_name(material.material_id)}}
                </td>
                <td class="text-xs-left">
                  {{ material.units || 0}}
                </td>
              </tr>
            </template>
            <template v-else>
              <div class="text-md-left pa-2" v-if="props.item.status === 'pendiente'">
                Pedido pendiente de recibir
              </div>
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
  index_suppliers,
  index_materials,
  index_orders,
  update_order,
  create_order,
  remove_order
} from '../../api/materials_controller';
import utils from "../../mixins/utils"
import server_pagination from "../../mixins/server_pagination"
import Vue2Filters from 'vue2-filters'

export default {
  name: "MaterialOrders",

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
        sortBy: 'request_date'
      },
      headers: [
        {text: 'Proveedor', value: 'provider_id', align: 'center'},
        {text: 'Fecha Solicitud', value: 'request_date', align: 'center'},
        {text: 'Costo Total', value: 'total_cost', align: 'center'},
        {text: 'Fecha Entrega', value: 'delivery_date', align: 'center'},
        {text: 'Status', value: 'status', align: 'center'},
        {text: 'Por Pagar', value: 'remaining_cost', align: 'center'},
        {text: '# Factura', value: 'invoice', align: 'center'},
        {text: 'Fecha de Pago', value: 'payment_date', align: 'center'},
        {text: 'Acciones', value: 'id', align: 'center', sortable: false},
      ],
      items: [],
      total_items: 0,
      providers: [],
      materials: [],

      status_list: [
        {name: "Pendiente", value: "pendiente"},
        {name: "Entregado", value: "entregado"},
        {name: "Pagado", value: "pagado"},
      ],

      valid_form: true,

      after_order_date_rule: [
        v => {
          const request_date = this.$moment(this.editedItem.request_date.slice(0,10), 'YYYY-M-DD');
          const current = this.$moment(v, 'DD/M/YYYY');
          return (!v || request_date.isBefore(current)) || "Debe ser posterior a fecha de pedido";
        }
      ],

      editedIndex: -1,
      editedItem: {
        id: '',
        provider_id: '',
        description: '',
        order_details: [""],
        total_cost: null,
        request_date: this.$moment('DD/M/YYYY'),
        delivery_date: this.$moment('DD/M/YYYY'),
        payment_date: this.$moment('DD/M/YYYY'),
        status: null,
        remaining_cost: 0,
        invoice: '',
      },
      defaultItem: {
        id: '',
        provider_id: '',
        description: '',
        order_details: [""],
        total_cost: 0,
        request_date: new Date().toISOString().slice(0, 10),
        delivery_date: new Date().toISOString().slice(0, 10),
        payment_date: new Date().toISOString().slice(0, 10),
        status: null,
        remaining_cost: 0,
        invoice: '',
      },

    }
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Pedido' : 'Editar Pedido'
    },

    material_choices() {
      return (this.providers.length > 0 && this.editedItem.provider_id && this.materials.length > 0 && this.materials.filter((mat) => mat.provider_id === this.editedItem.provider_id)) || [];
    }

  },

  mounted() {
    this.axios.all([index_materials(), index_suppliers()])
        .then(this.axios.spread(function (materials, providers) {
              // Both requests are now complete
              this.materials = materials.data;
              this.providers = providers.data;

            }.bind(this)
        ))
        .catch(error => {
          this.$store.commit('setSnack', {text: error, color: 'red'});
        })
        .finally(() => {
          this.loading = false;
        })
  },

  methods: {

    addMaterial() {
      this.editedItem.order_details.push({material_id: null, units: 0});
    },

    removeMaterial(item) {
      const index = this.editedItem.order_details.indexOf(item);
      this.editedItem.order_details.splice(index, 1);
    },

    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.editedItem = JSON.parse(JSON.stringify(item));
      this.$refs.form.resetValidation();
      this.dialog = true;
    },


    save() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        // Editing an User
        if (this.editedIndex > -1) {
          update_order(this.editedItem).then(() => {
            this.items[this.editedIndex] = JSON.parse(JSON.stringify(this.editedItem));
            this.$store.commit('setSnack', {text: "Pedido actualizado exitosamente", color: 'success'});
            this.close();
          }).catch(err => {
            this.$store.commit('setSnack', {text: err, color: 'red'});
          }).finally(() => {
            this.loading = false
          });
          //  Creating a new User
        } else {
          create_order(this.editedItem).then(() => {
            this.items.push(JSON.parse(JSON.stringify(this.editedItem)));
            this.$store.commit('setSnack', {text: "Pedido creado exitosamente", color: 'success'});
            this.close();
          }).catch(err => {
            this.$store.commit('setSnack', {text: err, color: 'red'});
          }).finally(() => {
            this.loading = false
          });
        }

      }
    },


  }
}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>