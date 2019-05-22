<template>
    <v-layout
            justify-center
            row
            wrap
            class="fill-height">
        <v-flex md5>
            <v-card elevation="2">
                <v-card-title class="blue"><h1 class="mx-auto white--text">Inicio de Sesión</h1></v-card-title>
                <v-form style="width:100%" ref="form" v-model="valid" lazy-validation>

                    <v-card-text class="px-5 py-3">
                        <v-text-field
                                v-model="username"
                                :rules="nameRules"
                                label="Nombre de Usuario"
                                required
                                :counter="20"
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
                                   @click="submit"
                                   :loading="loading"
                                   :disabled="!valid"
                                   class="mb-2"
                            >
                                Ingresar
                            </v-btn>
                            <v-btn color="info" @click="credentials_snack = true">¿Olvidaste tu contraseña?</v-btn>
                        </v-layout>

                    </v-card-actions>
                </v-form>
            </v-card>
        </v-flex>

        <v-snackbar
                v-model="credentials_snack"
                color="error"
                :timeout="5000"
                top
        >
            Nombre de Usuario o Contraseña incorrectos
            <v-btn
                    dark
                    flat
                    @click="credentials_snack = false"
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
      credentials_snack: false,
    }),


    methods: {
      submit() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          const { username, password } = this
          this.$store.dispatch(AUTH_REQUEST, { username, password }).then(() => {
            this.$router.push('/')
          })
        }
      },
    }
  }
</script>

<style scoped lang="sass">

    .v-btn
        min-width: 150px
        @media(max-width: 500px)
            width: 80%


</style>