<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4">Lista de Clientes</h1>
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

                        <!--                        <td>-->
                        <!--                            <v-btn flat small color="red" @click="props.expanded = !props.expanded">${{-->
                        <!--                                Number(calc_debt(props.item)).toFixed(2) }}-->
                        <!--                            </v-btn>-->
                        <!--                        </td>-->
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
    index_clients,
    create_client,
    update_client,
    remove_client,
    index_orders
  } from '../../api/production_controller';

  export default {
    name: "ProductsClientes",

    data() {
      return {

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
          {text: 'RFC', value: 'rfc'},
          {text: 'Descripción', value: 'description'},
          {text: 'Acciones', value: 'id'},
        ],

        items: [],
        orders: [],

        valid_form: true,

        status_list: [
          {name: "Pendiente", value: "pending"},
          {name: "Entregado", value: "delivered"},
          {name: "Pagado", value: "paid"},
        ],

        nameRules: [
          v => !!v || 'Campo requerido',
          v => (v && v.length <= 70) || 'Máximo 70 caracteres'
        ],

        descRules: [
          v => !!v || 'Campo requerido',
          v => (v && v.length >= 3) || 'Mínimo 3 caracteres'
        ],

        moneyRules: [
          v => (!v || (!isNaN(v))) || 'Debe ser una cantidad'
        ],


        editedIndex: -1,
        editedItem: {
          name: '',
          business_name: '',
          description: '',
        },
        defaultItem: {
          name: '',
          business_name: '',
          description: '',
        },

      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Cliente' : 'Editar Cliente'
      },

    },

    mounted() {
      this.axios.all([index_clients(), index_orders()])
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

      unpaid_orders(item) {
        return this.orders.filter((order) => order.material_id === item.id && order.status !== "paid");
      },

      calc_debt(item) {
        return this.unpaid_orders(item).reduce(function (a, b) {
          return parseInt(a) + parseInt(b.total_cost);
        }, 0);
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
        confirm('¿Estás seguro de que quieres borrar a este cliente? Se eliminara su historial de pedidos e insumos.') && remove_client({id: item.id}).then(() => {
          this.items.splice(index, 1);
          this.$store.commit('setSnack', {text: "Cliente borrado exitosamente", color: 'success'});

        }).catch(err => {
          this.$store.commit('setSnack', {text: err, color: 'red'});
        });
      },

      close() {
        this.dialog = false;
        setTimeout(() => {
          this.$refs.form.reset();
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
        }, 300);
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