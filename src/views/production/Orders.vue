<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4 section-header">Pedidos por Clientes</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Registrar Pedido</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-form ref="form" v-model="valid_form" lazy-validation>
                                <v-container grid-list-md>
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
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-select
                                                    v-model="editedItem.product_id"
                                                    hint="Producto"
                                                    :items="products"
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Elije un Producto"
                                                    persistent-hint
                                                    single-line
                                                    :rules="required"
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model="editedItem.contract"
                                                          label="No. Contrato"></v-text-field>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model.number="editedItem.units"
                                                          :rules="numberRules"
                                                          type="number"
                                                          label="Cantidad"></v-text-field>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model.number="editedItem.total_cost"
                                                          :rules="numberRules"
                                                          type="number"
                                                          label="Precio Total"></v-text-field>
                                        </v-flex>
                                        <v-flex xs4 sm4>
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
                                                            v-model="editedItem.request_date"
                                                            label="Fecha de solicitud"
                                                            prepend-icon="event"
                                                            readonly
                                                            clearable
                                                            :value="formatted_date(editedItem.request_date)"
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
                                        <v-flex xs12 sm6>
                                            <v-dialog v-if="editedIndex > -1"
                                                      ref="dialog2"
                                                      :return-value.sync="editedItem.finish_date"
                                                      persistent
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
                                                            readonly
                                                            v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker v-model="editedItem.finish_date" scrollable
                                                               locale="es-mx"
                                                >
                                                    <v-spacer></v-spacer>
                                                    <v-btn flat color="primary" @click="modal_date_2 = false">Cancelar
                                                    </v-btn>
                                                    <v-btn flat color="primary"
                                                           @click="$refs.dialog2.save(editedItem.finish_date)">OK
                                                    </v-btn>
                                                </v-date-picker>
                                            </v-dialog>
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
                    :pagination.sync="pagination"
                    hide-actions
            >
                <template v-slot:items="props">
                    <tr>
                        <td class="">{{ client_name(props.item.client_id) }}</td>
                        <td class="">{{ product_name(props.item.product_id) }}</td>
                        <td class="">{{ props.item.contract }}</td>
                        <td class="">{{ props.item.units }}</td>
                        <td class="">$ {{ props.item.total_cost }}</td>
                        <td class="">{{ props.item.request_date | moment('DD/M/YYYY')}}</td>
                        <td class="">{{ props.item.finish_date | moment('DD/M/YYYY')}}</td>
                        <td class="">{{ status_name(props.item.status) }}</td>
                        <v-btn flat small color="warning" @click="props.expanded = !props.expanded">${{ Number(calc_remaining(props.item)) }}</v-btn>
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
    index_products,
    index_clients,
    index_orders,
    update_order,
    create_order,
    remove_order
  } from '../../api/production_controller';

  export default {
    name: "ClientOrders",

    data() {
      return {

        loading: true,
        dialog: false,
        modal_date_1: false,
        modal_date_2: false,
        search: '',
        pagination: {
          sortBy: 'request_date'
        },
        headers: [
          {text: 'Cliente', value: 'client_id', align: 'center'},

          {
            text: 'Producto',
            align: 'center',
            value: 'product_id'
          },
          {text: 'No. Contrato', value: 'contract', align: 'center'},

          {text: 'Unidades', value: 'units', align: 'center'},
          {text: 'Costo Total', value: 'total_cost', align: 'center'},
          {text: 'Fecha Solicitud', value: 'order_dat', align: 'center'},
          {text: 'Fecha Terminación', value: 'finish_date', align: 'center'},
          {text: 'Status', value: 'status', align: 'center'},
          {text: 'Por Entregar', value: 'remaining_cost', align: 'center'},
          {text: 'Acciones', value: 'id', align: 'center'},
        ],
        items: [],
        clients: [],
        products: [],

        status_list: [
          {name: "Pendiente", value: "pending"},
          {name: "Parcial", value: "parcial"},
          {name: "Entregado", value: "delivered"},
          {name: "Pagado", value: "paid"},
        ],

        valid_form: true,

        numberRules: [
          v => !!v || 'Campo requerido',
          v => (!isNaN(v) && v > 0) || "Debe ser un número positivo",
        ],

        required: [
          v => !!v || 'Campo requerido',
        ],

        editedIndex: -1,
        editedItem: {
          id: '',
          client_id: '',
          product_id: '',
          contract: '',
          units: 0,
          total_cost: 0,
          request_date: new Date().toISOString().slice(0, 10),
          finish_date: new Date().toISOString().slice(0, 10),
          status: null,
        },
        defaultItem: {
          id: '',
          client_id: '',
          product_id: '',
          contract: '',
          units: 0,
          total_cost: 0,
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

      material_choices(){
        return (this.providers.length > 0 && this.editedItem.provider_id && this.materials.filter((mat) => mat.provider_id === this.editedItem.provider_id) ) || [];
      }

    },

    mounted() {
      this.axios.all([index_orders(), index_products(), index_clients()])
          .then(this.axios.spread(function (orders, products, clients) {
                // Both requests are now complete
                this.products = products.data;
                this.clients = clients.data;
                this.items = orders.data;
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

      formatted_date(date) {
        return date ? this.$moment(date).format("DD/M/YYYY") : "";
      },

      client_name(id) {
        return (this.providers.length > 0 && this.providers.find((prov) => prov.id === id).name) || "Cliente no encontrado";
      },

      product_name(id) {
        return (this.materials.length > 0 && this.materials.find((mat) => mat.id === id).name) || "Producto no encontrado";
      },

      status_name(val) {
        return this.status_list.find((stat) => stat.value === val).name;
      },

      editItem(item) {
        this.editedIndex = this.items.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      deleteItem(item) {
        const index = this.items.indexOf(item);
        confirm('¿Estás seguro de que quieres borrar este pedido? Se borrarán todas las entregas.') && remove_order({id: item.id}).then(() => {
          this.items.splice(index, 1);
          this.$store.commit('setSnack', {text: "Pedido borrado exitosamente", color: 'success'});

        }).catch(err => {
          this.$store.commit('setSnack', {text: err, color: 'red'});
        });
      },

      close() {
        this.dialog = false;
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
          this.$refs.form.reset();
        }, 300);
      },

      save() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          // Editing an User
          if (this.editedIndex > -1) {
            update_order(this.editedItem).then(() => {
              Object.assign(this.items[this.editedIndex], this.editedItem);
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
              this.items.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Pedido creado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
          }

        }
      }
    },

  }
</script>

<style>
    table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
        padding: 0 18px;
    }
</style>