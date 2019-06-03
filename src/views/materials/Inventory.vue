<template>
    <section id="users">

        <h1 class="text-md-center my-4">Inventario de Insumos</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Crear Insumo</v-btn>
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
                                            <v-text-field v-model="editedItem.type"
                                                          :rules="typeRules"
                                                          label="Tipo"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-select
                                                    v-model="editedItem.provider"
                                                    hint="Proveedor"
                                                    :items="providers"
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Elije al proveedor"
                                                    persistent-hint
                                                    return-object
                                                    single-line
                                            ></v-select>
                                        </v-flex>

                                        <v-flex sm6>
                                            <v-text-field v-model="editedItem.price"
                                                          :rules="priceRules"
                                                          label="Precio"></v-text-field>
                                        </v-flex>
                                        <v-flex sm4>
                                            <v-text-field v-model="editedItem.stock"
                                                          :rules="priceRules"
                                                          label="Stock Disponible"></v-text-field>
                                        </v-flex>
                                        <v-flex sm4>
                                            <v-text-field v-model="editedItem.pending_stock"
                                                          :rules="priceRules"
                                                          label="Stock Pendiente"></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                </v-container>
                            </v-form>

                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
                            <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
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
                        <td class="">{{ props.item.type }}</td>
                        <td class="">{{ props.item.provider.name }}</td>
                        <td class="">{{ props.item.price }}</td>
                        <td class="">{{ props.item.stock }}</td>
                        <td class="">{{ props.item.pending_stock }}</td>
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
    index_materials,
    create_material,
    remove_material,
    update_material,
    index_providers
  } from '../../api/materials_controller';

  export default {
    name: "MaterialsInventory",

    data() {
      return {

        loading: true,
        dialog: false,
        search: '',
        headers: [
          {text: 'Nombre', value: 'name'},

          {
            text: 'Tipo',
            align: 'left',
            value: 'type'
          },
          {text: 'Proveedor', value: 'provider'},
          {text: 'Precio más reciente', value: 'price'},
          {text: 'Stock Disponible', value: 'stock'},
          {text: 'Stock Pendiente', value: 'pending_stock'},
          {text: 'Acciones', value: 'id', sortable: false},
        ],

        items: [],

        valid_form: true,

        nameRules: [
          v => !!v || 'Nombre requerido',
          v => (v && v.length <= 30) || 'Máximo 30 caracteres'
        ],
        typeRules: [
          v => !!v || 'Tipo requerido',
        ],
        priceRules: [
          v => !!v || 'Precio requerido',
          v => (!isNaN(v) && v > 0) || "Debe ser un número positivo",
        ],


        editedIndex: -1,
        editedItem: {
          id: '',
          name: '',
          type: '',
          provider: {},
          price: '',
          stock: 0,
          pending_stock: 0,
        },
        defaultItem: {
          id: '',
          name: '',
          type: '',
          provider: {},
          price: '',
          stock: 0,
          pending_stock: 0,
        },

        providers: [
          {id: 1, name: "Empresa 1"},
          {id: 2, name: "Empresa 2"},
          {id: 3, name: "Empresa 3"},
        ],
      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Insumo' : 'Editar Insumo'
      },

    },


    mounted() {
      this.axios.all([index_materials(), index_providers()])
          .then(this.axios.spread(function (materials, providers) {
            // Both requests are now complete
            this.items = materials;
            this.providers = providers;
          }))
          .catch(error => {
            this.$store.commit('setSnack', {text: error, color: 'red'});
          })
          .finally(() => {
            this.loading = false;
          })
    },

    methods: {

      editItem(item) {
        this.editedIndex = this.users.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      deleteItem(item) {
        const index = this.users.indexOf(item);
        confirm('¿Estás seguro de que quieres borrar este insumo?') && remove_material({id: item.id}).then(() => {
          this.users.splice(index, 1);
          this.$store.commit('setSnack', {text: "Insumo borrado exitosamente", color: 'success'});

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
            update_material(this.editedItem).then(() => {
              Object.assign(this.users[this.editedIndex], this.editedItem);
              this.$store.commit('setSnack', {text: "Usuario actualizado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
            //  Creating a new User
          } else {
            this.calc_permissions_decimal();
            create_material(this.editedItem).then(() => {
              this.users.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Usuario creado exitosamente", color: 'success'});
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