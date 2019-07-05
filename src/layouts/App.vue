<template>
    <v-app id="inspire">
        <v-navigation-drawer
                fixed
                v-model="drawer"
                app
        >
            <v-list dense>
                <template v-for="item in menu_permissions">
                    <!--                    This item is a Dropdown-->
                    <v-list-group v-if="item.options" :key="item.text" v-model="item.active" :prepend-icon="item.icon"
                                  no-action>

                        <template v-slot:activator>
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>{{ item.text }}</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </template>

                        <v-list-tile
                                v-for="subItem in item.options"
                                :key="subItem.text"
                                :to="subItem.url"
                                @click="drawer = false"
                                router
                        >
                            <v-list-tile-action>
                                <v-icon>{{ subItem.icon }}</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ subItem.text }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>

                    </v-list-group>

                    <!--                    Not a Dropdown-->
                    <v-list-tile router :to="item.url"
                                 @click="drawer = false"
                                 :key="item.text" v-else>
                        <v-list-tile-action>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{item.text}}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>


                </template>

                <v-list-tile @click="logout">
                    <v-list-tile-action>
                        <v-icon>exit_to_app</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Salir</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer>

        <v-toolbar color="white" light fixed app justify-flex-start>
            <v-toolbar-side-icon class="brown--text" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
            <v-img class="mx-3" aspect-ratio="1" max-width="80px" max-height="60px" contain
                   :src="require('@/assets/img/Madereria Logo.png')"></v-img>
            <v-toolbar-title>Hola {{user_name}}</v-toolbar-title>
        </v-toolbar>
        <v-content>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-content>
        <v-footer color="brown" app class="px-3">
            <span class="white--text">&copy; 2019 - Powered by <a href="https://phoenixdevelopment.mx">Phoenix Development</a></span>
        </v-footer>
    </v-app>
</template>

<script>
  import {AUTH_LOGOUT, AUTH_REFRESH} from '../store/actions/auth'

  export default {
    name: 'appLayout',

    data: () => ({
      drawer: null,

      menu: [
        {icon: 'home', url: '/', text: 'Inicio', permission: 0},
        {icon: 'bar_chart', url: '/dashboard', text: 'Dashboard', permission: 1},
        {icon: 'supervised_user_circle', url: '/users', text: 'Usuarios', permission: 16},
        {
          icon: 'dashboard', text: 'Producción', active: false, permission: 2, options: [
            {icon: 'list', url: '/production/inventory', text: 'Inventario'},
            {icon: 'account_box', url: '/production/clients', text: 'Clientes'},
            {icon: 'assignment', url: '/production/orders', text: 'Pedidos'},
            {icon: 'local_shipping', url: '/production/shipments', text: 'Envíos'},
            {icon: 'notes', url: '/production/reports', text: 'Reportes'},
          ]
        },
        {
          icon: 'storage', text: 'Materia Prima', permission: 4, options: [
            {icon: 'list', url: '/materials/inventory', text: 'Inventario'},
            {icon: 'account_box', url: '/materials/suppliers', text: 'Proveedores'},
            {icon: 'assignment', url: '/materials/orders', text: 'Pedidos'},
          ]
        },
        {
          icon: 'attach_file', text: 'Documentos', permission: 8, options: [
            {icon: 'fas fa-money-check-alt', url: '/documents/income', text: "Ingresos/Egresos"}
          ]
        },
      ],
    }),

    computed: {
      //Filter menu if user lacks permissions for some of the modules
      menu_permissions() {
        const access = parseInt(this.$store.getters.getPermissions, 2);
        return this.menu.filter(({permission}) => (permission & access) === permission)
      },

      user_name() {
        return this.$store.getters.getName;
      }

    },

    props: {
      source: String
    },

    methods: {
      logout() {
        this.$store.dispatch(AUTH_LOGOUT).then(() => {
          this.$store.commit('setSnack', {text: "Sesión Cerrada exitosamente", color: 'info'});
        })
      },

      refresh_token() {
        setInterval(() => {
          if (this.$store.getters.isAuthenticated) {
            this.$store.dispatch(AUTH_REFRESH, {username: this.$store.getters.getUsername}).then(() => {
              this.$store.commit('setSnack', {text: "Token Refreshed", color: 'info'});
            }).catch(() => {
              this.$store.commit('setSnack', {
                text: "Tu sesión ha expirado, por favor ingresa de nuevo.",
                color: 'warning'
              });
            })
          }
        }, 300000);
      },

    },

    mounted() {
      this.refresh_token();

    }
  }
</script>