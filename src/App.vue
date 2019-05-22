
<template>
  <div id="app" class="application theme--light">
    <vue-extend-layouts />
  </div>
</template>

<script>
  /* eslint-disable no-unused-vars */
  import VueExtendLayouts from 'vue-extend-layout'
  import {AUTH_LOGOUT} from "./store/actions/auth";

  export default {
  name: 'App',
  components: { VueExtendLayouts },

  created: function () {
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
  },


}
</script>

<style>
  @import 'assets/css/style.css';
</style>