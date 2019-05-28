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
                    <v-list-tile router :to="item.url" :key="item.text" v-else>
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

        <v-toolbar color="indigo" dark fixed app>
            <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
            <v-toolbar-title>Application</v-toolbar-title>
        </v-toolbar>
        <v-content>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-content>
        <v-footer color="indigo" app>
            <span class="white--text">&copy; Its your app</span>
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
            {icon: 'account_box', url: '/produccion/clientes', text: 'Clientes'},
            {icon: 'assignment', url: '/produccion/pedidos', text: 'Pedidos'},
            {icon: 'local_shipping', url: '/produccion/envios', text: 'Envíos'},
            {icon: 'notes', url: '/produccion/reportes', text: 'Reportes'},
          ]
        },
        {icon: 'storage', url: '/materia', text: 'Materia Prima', permission: 4},
        {icon: 'attach_file', url: '/documentos', text: 'Documentos', permission: 8},
      ],
    }),

    computed: {
      //Filter menu if user lacks permissions for some of the modules
      menu_permissions() {
        const access = localStorage.permissions;
        return this.menu.filter(({permission}) => (permission & access) === permission)
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
            this.$store.dispatch(AUTH_REFRESH).then(() => {
              this.$store.commit('setSnack', {text: "Token Refreshed", color: 'info'});
            }).catch(() => {
              this.$router.push('/login');
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