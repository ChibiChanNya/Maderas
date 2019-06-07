<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4">Lista de Proveedores</h1>
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
                                            <v-text-field v-model="editedItem.rfc"
                                                          :rules="rfcRules"
                                                          label="RFC"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-text-field v-model="editedItem.clabe"
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
                                        <v-flex xs12 sm6>
                                            <v-text-field v-model="editedItem.money_debt"
                                                          :rules="moneyRules"
                                                          label="Saldo"></v-text-field>
                                        </v-flex>

                                    </v-layout>
                                </v-container>
                            </v-form>

                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
                            <v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
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
                        <td class="">{{ props.item.clabe }}</td>
                        <td class="">{{ props.item.bank }}</td>
                        <td class="">{{ props.item.description }}</td>
                        <td v-bind:class="{ 'green--text': props.item.money_debt >0, 'red--text': props.item.money_debt <0}">${{ Number(props.item.money_debt).toFixed(2) }}</td>
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
  import {index_suppliers, create_supplier, update_supplier, remove_supplier} from '../../api/materials_controller';

  export default {
    name: "MaterialsInventory",

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
          {text: 'CLABE', value: 'clabe'},
          {text: 'Banco', value: 'bank'},
          {text: 'Descripción', value: 'description'},
          {text: 'Saldo', value: 'money_debt'},
          {text: 'Acciones', value: 'id'},
        ],

        items: [],

        valid_form: true,

        nameRules: [
          v => !!v || 'Campo requerido',
          v => (v && v.length <= 70) || 'Máximo 70 caracteres'
        ],

        rfcRules: [
          v => (!v || (v.length === 13 || v.length === 14)) || 'RFC debe estar compuesto por 13 or 14 símbolos'
        ],

        clabeRules: [
          v => (!v || (!isNaN(v) && v.length === 18)) || 'La Clabe debe star compuesta por 18 números'
        ],

        descRules: [
          v => !!v || 'Campo requerido',
          v => (v && v.length >= 3) || 'Mínimo 3 caracteres'
        ],

        moneyRules: [
          v => (!v || (!isNaN(v) )) || 'Debe ser una cantidad'
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
        },
        defaultItem: {
          name: '',
          business_name: '',
          rfc: '',
          clabe:'',
          bank: '',
          description: '',
          logo:'/fd/',
          money_debt: 0,
        },

      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Proveedor' : 'Editar Proveedor'
      },

    },

    mounted() {
      index_suppliers()
          .then(({data}) => {
            this.items = data;
          })
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

      deleteItem(item) {
        const index = this.items.indexOf(item);
        confirm('¿Estás seguro de que quieres borrar a este proveedor? Se eliminara su historial de pedidos e insumos.') && remove_supplier({id: item.id}).then(() => {
          this.items.splice(index, 1);
          this.$store.commit('setSnack', {text: "Proveedor borrado exitosamente", color: 'success'});

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
            update_supplier(this.editedItem).then(() => {
              Object.assign(this.items[this.editedIndex], this.editedItem);
              this.$store.commit('setSnack', {text: "Proveedor actualizado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
            //  Creating a new User
          } else {
            create_supplier(this.editedItem).then(() => {
              this.items.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Proveedor creado exitosamente", color: 'success'});
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