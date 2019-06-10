<template>
    <section id="users">

        <h1 class="text-md-center my-4">Usuarios en la plataforma</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Crear Usuario</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-form ref="form" v-model="valid_form" lazy-validation>
                                <v-container grid-list-md>
                                    <v-layout wrap>
                                        <v-flex xs12 sm6>
                                            <v-text-field v-model="editedItem.username"
                                                          :rules="usernameRules"
                                                          label="Nombre de Usuario"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-text-field v-model="editedItem.full_name"
                                                          :rules="nameRules" label="Nombre Completo"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-text-field type="password" v-model="editedItem.password"
                                                          :rules="passwordRules" label="Contraseña"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-text-field type="password" v-model.lazy="editedItem.re_password"
                                                          :rules="re_passwordRules"
                                                          label="Repetir Contraseña"></v-text-field>
                                        </v-flex>
                                        <v-flex sm4>
                                            <v-checkbox v-model="permissions_array.dashboard"
                                                        label="Dashboard"></v-checkbox>
                                        </v-flex>
                                        <v-flex sm4>
                                            <v-checkbox v-model="permissions_array.usuarios"
                                                        label="Usuarios"></v-checkbox>
                                        </v-flex>

                                        <v-flex sm4>
                                            <v-checkbox v-model="permissions_array.materia_prima"
                                                        label="Materia Prima"></v-checkbox>
                                        </v-flex>

                                        <v-flex sm4>
                                            <v-checkbox v-model="permissions_array.produccion"
                                                        label="Producción"></v-checkbox>
                                        </v-flex>

                                        <v-flex sm4>
                                            <v-checkbox v-model="permissions_array.documentos"
                                                        label="Documentos"></v-checkbox>
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
                    :items="users"
                    class="elevation-1"
                    :loading="loading"
                    :search="search"
                    hide-actions
            >
                <template v-slot:items="props">
                    <tr>
                        <td class="">{{ props.item.username }}</td>
                        <td class="">{{ props.item.full_name }}</td>
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
                        <td>
                            <v-btn flat small color="primary" @click="props.expanded = !props.expanded">Permisos</v-btn>
                            <v-btn flat small color="primary" @click="view_history(props.item)">Actividades</v-btn>
                        </td>
                    </tr>
                </template>
                <template v-slot:expand="props">
                    <div class="grey lighten-3 pl-5">
                        <tr>
                            <th>Dashboard</th>
                            <th>Usuarios</th>
                            <th>Materia Prima</th>
                            <th>Producción</th>
                            <th>Documentos</th>

                        </tr>
                        <tr>
                            <td class="text-xs-center">
                                <v-icon :color="(parseInt(props.item.permissions, 2) & 1) === 1? 'green' : 'red'">
                                    {{(parseInt(props.item.permissions, 2) & 1) === 1? 'check' : 'close'}}
                                </v-icon>
                            </td>
                            <td class="text-xs-center">
                                <v-icon :color="(parseInt(props.item.permissions, 2) & 16) === 16? 'green' : 'red'">
                                    {{(parseInt(props.item.permissions, 2) & 16) === 16? 'check' : 'close'}}
                                </v-icon>
                            </td>
                            <td class="text-xs-center">
                                <v-icon :color="(parseInt(props.item.permissions, 2) & 4) === 4? 'green' : 'red'">
                                    {{(parseInt(props.item.permissions, 2) & 4) === 4? 'check' : 'close'}}
                                </v-icon>
                            </td>
                            <td class="text-xs-center">
                                <v-icon :color="(parseInt(props.item.permissions, 2) & 2) === 2? 'green' : 'red'">
                                    {{(parseInt(props.item.permissions, 2) & 2) === 2? 'check' : 'close'}}
                                </v-icon>
                            </td>
                            <td class="text-xs-center">
                                <v-icon :color="(parseInt(props.item.permissions, 2) & 8) === 8? 'green' : 'red'">
                                    {{(parseInt(props.item.permissions, 2) & 8) === 8? 'check' : 'close'}}
                                </v-icon>
                            </td>
                        </tr>
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

        <!--        HISTORY MODAL     -->
        <v-dialog
                v-model="history_dialog"
                scrollable
                max-width="400"
        >
            <v-card>
                <v-card-title class="blue white--text" dark>
                    <v-toolbar-title>Registro de <b>{{ history_name }}</b></v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="history_dialog = false">
                        <v-icon color="white">close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text style="height: 500px; padding-top:0">
                    <v-list two-line>
                        <template v-for="(item, index) in current_history">
                            <v-list-tile :key="item.id">
                                <v-list-tile-content>
                                    <v-list-tile-title>{{item.action}}</v-list-tile-title>
                                    <v-list-tile-sub-title>{{item.dt_action | moment('calendar')}}
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>

                            </v-list-tile>
                            <v-divider
                                    v-if="index + 1 < current_history.length" :key="index"
                            ></v-divider>
                        </template>
                    </v-list>
                </v-card-text>
            </v-card>

        </v-dialog>

    </section>
</template>

<script>
  import {index, create, remove, update, user_log} from '../../api/users_controller';

  export default {
    name: "UserIndex",

    data() {
      return {

        loading: true,
        dialog: false,
        history_dialog: false,
        search: '',
        headers: [
          {text: 'Nombre de Usuario', value: 'username', align: 'center'},

          {
            text: 'Nombre Completo',
            align: 'center',
            value: 'full_name'
          },
          {text: 'Acciones', value: 'permissions', sortable: false},
          {text: 'Ver más', value: 'permissions', sortable: false, align: 'center'},
        ],

        users: [],

        valid_form: true,

        usernameRules: [
          v => !!v || 'Nombre de usuario requerido',
          v => (v && v.length <= 20) || 'Máximo 20 caracteres'
        ],
        nameRules: [
          v => !!v || 'Nombre requerido',
          v => (v && v.length <= 20) || 'Máximo 20 caracteres'
        ],
        passwordRules: [
          v => (this.editedIndex >= 0 || !!v) || 'Contraseña obligatoria',
        ],
        re_passwordRules: [
          v => (!this.editedItem.password || v === this.editedItem.password) || 'Las contraseñas no coinciden',
        ],

        editedIndex: -1,
        editedItem: {
          id: '',
          username: '',
          full_name: '',
          password: '',
          re_password: '',
          description: "Nuevo usuario",
          permissions: 0,
        },
        defaultItem: {
          id: '',
          username: '',
          full_name: '',
          password: '',
          re_password: '',
          description: "Nuevo usuario",
          permissions: 0,
        },

        current_history: [],
        history_name: "",
      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nuevo Usuario' : 'Editar Usuario'
      },

      permissions_array: {
        get: function () {
          return {
            dashboard: (parseInt(this.editedItem.permissions, 2) & 1) > 0,
            usuarios: (parseInt(this.editedItem.permissions, 2) & 16) > 0,
            materia_prima: (parseInt(this.editedItem.permissions,) & 4) > 0,
            produccion: (parseInt(this.editedItem.permissions, 2) & 2) > 0,
            documentos: (parseInt(this.editedItem.permissions, 2) & 8) > 0,
          }
        },
      }
    },


    mounted() {
      index()
          .then(({data}) => {
            this.users = data;
          })
          .catch(error => {
            this.$store.commit('setSnack', {text: error, color: 'red'});
          })
          .finally(() => {
            this.loading = false;
          })
    },

    methods: {

      calc_permissions_decimal() {
        let total = 0;
        if (this.permissions_array.dashboard) total += 1;
        if (this.permissions_array.usuarios) total += 16;
        if (this.permissions_array.materia_prima) total += 4;
        if (this.permissions_array.produccion) total += 2;
        if (this.permissions_array.documentos) total += 8;
        this.editedItem.permissions = (total >>> 0).toString(2);
      },

      view_history(item) {
        this.loading = true;
        user_log(item).then(({data}) => {
          this.current_history = data;
          this.history_dialog = true;
          this.history_name = item.full_name;
        }).catch(err => {
          this.$store.commit('setSnack', {text: err, color: 'red'});
        }).finally(() => {
          this.loading = false;
        });
      },

      editItem(item) {
        this.editedIndex = this.users.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      deleteItem(item) {
        const index = this.users.indexOf(item);
        confirm('¿Estás seguro de que quieres borrar a este usuario?') && remove({id: item.id}).then(() => {
          this.users.splice(index, 1);
          this.$store.commit('setSnack', {text: "Usuario borrado exitosamente", color: 'success'});

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
            this.calc_permissions_decimal();
            if (!this.editedItem.password) {
              delete this.editedItem.password;
            }
            update(this.editedItem).then(() => {
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
            create(this.editedItem).then(() => {
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