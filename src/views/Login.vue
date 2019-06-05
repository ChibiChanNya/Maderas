<template>
    <v-layout
            justify-center
            row
            wrap
            class="fill-height">
        <v-flex md5>
            <v-card elevation="2">
                <v-card-title class="blue"><h1 class="mx-auto white--text">Inicio de Sesión</h1></v-card-title>
                <v-form style="width:100%" ref="form" v-model="valid" lazy-validation @submit.prevent="submit">

                    <v-card-text class="px-5 py-3">
                        <v-text-field
                                v-model="username"
                                :rules="nameRules"
                                label="Nombre de Usuario"
                                required
                                :counter="20"
                                class="mb-3"
                        ></v-text-field>

                        <v-text-field
                                v-model="password"
                                type="password"
                                :rules="passwordRules"
                                label="Contraseña"
                                required
                        ></v-text-field>
                    </v-card-text>

                    <v-divider light class="px-3"></v-divider>

                    <v-card-actions class=" py-3 ">
                        <v-layout row wrap justify-space-around>
                            <v-btn color="success"
                                   type="submit"
                                   :loading="loading"
                                   :disabled="!valid"
                                   class="mb-2"
                            >
                                Ingresar
                            </v-btn>
                            <v-btn color="info"
                                   @click="$store.commit('setSnack', {text: 'Favor de contactar a un administrador para cambio de contraseña.', color: 'warning'})"
                            >¿Olvidaste tu contraseña?
                            </v-btn>
                        </v-layout>

                    </v-card-actions>
                </v-form>
            </v-card>
        </v-flex>

        <v-snackbar
                v-model="error_snack"
                color="error"
                :timeout="5000"
                top
        >
            {{error_text}}
            <v-btn
                    dark
                    flat
                    @click="error_snack = false"
            >
                Cerrar
            </v-btn>
        </v-snackbar>

    </v-layout>


</template>

<script>
  import {AUTH_REQUEST} from '../store/actions/auth'

  export default {
    name: "Login",

    data: () => ({
      valid: true,
      username: '',
      nameRules: [
        v => !!v || 'Nombre de usuario requerido',
        v => (v && v.length <= 20) || 'Máximo 20 caracteres'
      ],
      password: '',
      passwordRules: [
        v => !!v || 'Introduce una contraseña',
      ],
      loading: false,
      error_snack: false,
      error_text: '',
    }),


    methods: {
      submit() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          const {username, password} = this;
          this.$store.dispatch(AUTH_REQUEST, {username, password}).then(() => {
            this.$router.push('/');
          }).catch(error => {
            let error_text;
            if(error.status === 401)
              error_text = "Nombre de usuario o contraseña incorrectos";
            else
              error_text = `Sucedió un error durante la autenticación, código ${error.status}. Intenta de nuevo más tarde o conacta al administradoe de la plataforma.`;
            this.$store.commit('setSnack', {text: error_text, color: 'red'});
            this.loading = false;
          })
        }
      },


    },
  }
</script>

<style scoped lang="sass">

    .v-btn
        min-width: 150px
        @media(max-width: 500px)
            width: 80%


</style>