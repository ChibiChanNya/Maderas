<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4 section-header">Historial de Ingresos y Egresos</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Nuevo Registro</v-btn>
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
                                                    v-model="editedItem.type"
                                                    hint="Tipo de Acción"
                                                    :items="actions"
                                                    item-text="name"
                                                    item-value="value"
                                                    label="Tipo de acción realizada"
                                                    persistent-hint
                                                    single-line
                                                    :rules="required"
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-select
                                                    v-model="editedItem.provider_id"
                                                    hint="Proveedor"
                                                    :items="providers"
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Elije al proveedor"
                                                    persistent-hint
                                                    single-line
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs12 sm8>
                                            <v-text-field v-model.lazy="editedItem.concept"
                                                          :rules="required"
                                                          label="Concepto"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm4>
                                            <v-text-field v-model.number="editedItem.amount" type="number"
                                                          :rules="[moneyRules, required]"
                                                          label="Monto"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm4>
                                            <v-text-field v-model="editedItem.invoice"
                                                          label="Factura"></v-text-field>
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
                        <td class="">{{ props.item.type }}</td>
                        <td class="">{{ props.item.target }}</td>
                        <td class="">{{ props.item.concept }}</td>
                        <td class="">{{ props.item.amount }}</td>
                        <td class="">{{ props.item.date || "--" | moment('DD/M/YYYY')}}</td>

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
  import {index_clients} from '../../api/production_controller';
  import {index_suppliers} from '../../api/materials_controller';
  import {
    create_ledger,
    update_ledger,
    remove_ledger,
    index_ledger,
  } from '../../api/documents_controller';
  import utils from "../../mixins/utils"
  import server_pagination from "../../mixins/server_pagination"

  export default {
    name: "IngresosEgresos",
    mixins: [utils, server_pagination],

    data() {
      return {
        index_fn: index_ledger,
        delete_fn: remove_ledger,

        loading: true,
        dialog: false,
        search: '',
        headers: [
          {text: 'Tipo de Acción', value: 'type', align: 'center'},

          {
            text: 'Proveedor',
            align: 'center',
            value: 'provider'
          },
          {text: 'Concepto', value: 'concept'},
          {text: 'Usuario', value: 'user', align: 'center'},
          {text: "Monto", value: "amount", align: 'center'},
          {text: 'Fecha', value: 'date', align: 'center'},
        ],

        items: [],
        clients: [],
        providers: [],

        valid_form: true,

        actions: [
          {name: "Ingreso", value: "ingreso"},
          {name: "Salida de Producto", value: "salida"},
          {name: "Pago", value: "pago"},
        ],

        editedIndex: -1,
        editedItem: {
          type: null,
          provider: null,
          concept: '',
          amount: 0,
          date: new Date().toISOString().slice(0, 10),
        },
        defaultItem: {
          type: null,
          provider: null,
          concept: '',
          amount: 0,
          date: new Date().toISOString().slice(0, 10),
        },

      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Registro' : 'Editar Registro'
      },

    },

    mounted() {
      this.axios.all([index_clients(), index_suppliers()])
          .then(this.axios.spread(function (clients, providers) {
                // Both requests are now complete
                this.clients = clients.data;
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

      editItem(item) {
        this.editedIndex = this.items.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      save() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          // Editing an User
          if (this.editedIndex > -1) {
            update_ledger(this.editedItem).then(() => {
              Object.assign(this.items[this.editedIndex], this.editedItem);
              this.$store.commit('setSnack', {text: "Registro actualizado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
            //  Creating a new User
          } else {
            create_ledger(this.editedItem).then(() => {
              this.items.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Registro creado exitosamente", color: 'success'});
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