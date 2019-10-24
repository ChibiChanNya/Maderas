<template>
  <div id="app" class="application theme--light" v-if="finished">
    <vue-extend-layouts/>
    <Snackbar/>
  </div>
</template>

<script>
/* eslint-disable no-unused-vars */
import VueExtendLayouts from 'vue-extend-layout'
import {AUTH_LOGOUT, AUTH_REFRESH} from "./store/actions/auth";
import Snackbar from './components/Snackbar'

export default {
  name: 'App',
  components: {VueExtendLayouts, Snackbar},
  data(){
    return{
      finished: false
    }
  },

  mounted: async function () {
    this.axios.interceptors.response.use(undefined, function (err) {
      return new Promise(function (resolve, reject) {
        if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
          // if you ever get an unauthorized, logout the user
          this.$store.dispatch(AUTH_LOGOUT);
          // you can also redirect to /login if needed !
          this.router.push('home');
        }
        throw err;
      });
    });
    try {
      await this.$store.dispatch(AUTH_REFRESH, {username: this.$store.getters.getUsername})

    } catch(e){
      this.$store.commit('setSnack', {
        text: "Tu sesión ha expirado, por favor ingresa de nuevo.",
        color: 'warning'
      });
    }
    this.finished = true
  },
}
</script>

<style>
@import 'assets/css/style.css';
</style>