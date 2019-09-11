<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4 section-header">Lista de Clientes</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Agregar Cliente</v-btn>
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
                                            <v-text-field v-model="editedItem.description"
                                                          label="Descripción"></v-text-field>
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
                    hide-actions
            >
                <template v-slot:items="props">
                    <tr>
                        <td class="">{{ props.item.name }}</td>
                        <td class="">{{ props.item.business_name }}</td>
                        <td class="">{{ props.item.rfc }}</td>
                        <td class="">{{ props.item.description }}</td>

                        <td>
                            <v-btn flat small :color="incomplete_orders(props.item).length > 0? 'red' : 'green'" @click="props.expanded = !props.expanded">
                                {{incomplete_orders(props.item).length}} pedido(s)
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
                        <template v-if="incomplete_orders(props.item).length >0">
                            <tr>
                                <th># Contrato</th>
                                <th>Desc. Pedido</th>
                                <th>Fecha Solicitud</th>
                                <th>Status</th>
                            </tr>
                            <tr v-for="order in incomplete_orders(props.item)" :key="order.id">
                                <td class="text-xs-left">
                                    {{ order.contract || "--"}}
                                </td>
                                <td class="text-xs-left">
                                    {{ order.description || "--"}}
                                </td>
                                <td class="text-xs-center">
                                    {{order.request_date || "--" | moment('DD/M/YYYY') }}
                                </td>
                                <td class="text-xs-center">
                                    {{ status_name(order.status) || "--"}}
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <h3 class="py-3">No hay pedidos pendientes para este cliente</h3>
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
    index_clients,
    create_client,
    update_client,
    remove_client,
    index_orders_lite
  } from '../../api/production_controller';
  import utils from "../../mixins/utils"

  export default {
    name: "ProductsClientes",
    mixins: [utils],

    data() {
      return {
        index_fn: index_clients,
        delete_fn: remove_client,

        loading: true,
        dialog: false,
        search: '',
        headers: [
          {text: 'Nombre', value: 'name', align: 'center'},

          {
            text: 'Razón Social',
            align: 'center',
            value: 'business_name'
          },
          {text: 'RFC', value: 'rfc', align: 'center'},
          {text: 'Descripción', value: 'description'},
          {text: "Pedidos incompletos", value: "id", sortable: false},
          {text: 'Acciones', value: 'id', sortable: false},
        ],

        items: [],
        orders: [],

        valid_form: true,

        status_list: [
          {name: "Pendiente", value: "pendiente"},
          {name: "Entregado", value: "entregado"},
          {name: "Pagado", value: "pagado"},
        ],

        editedIndex: -1,
        editedItem: {
          name: '',
          business_name: '',
          description: '',
          rfc: '',
        },
        defaultItem: {
          name: '',
          business_name: '',
          description: '',
          rfc: '',
        },

      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Cliente' : 'Editar Cliente'
      },

    },

    mounted() {
      this.axios.all([index_clients(), index_orders_lite()])
          .then(this.axios.spread(function (providers, orders) {
                // Both requests are now complete
                this.items = providers.data;
                this.orders = orders.data;
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

      incomplete_orders(item) {
        const orders = this.orders.filter((order) => order.client_id === (item && item.id) && order.status !== "completo") || [];
        return orders || [];
      },

      calc_debt(item) {
        return this.unpaid_orders(item).reduce(function (a, b) {
          return parseInt(a) + parseInt(b.total_cost);
        }, 0);
      },

      editItem(item) {
          this.editedIndex = this.items.findIndex( (client) => client.id === item.id);
          this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]));
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      save() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          // Editing an User
          if (this.editedIndex > -1) {
            update_client(this.editedItem).then(() => {
              Object.assign(this.items[this.editedIndex], this.editedItem);
              this.$store.commit('setSnack', {text: "Cliente actualizado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
            //  Creating a new User
          } else {
            create_client(this.editedItem).then(() => {
              this.items.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Cliente creado exitosamente", color: 'success'});
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