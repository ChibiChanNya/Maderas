<template>
    <div class="about">
        <h1>This is an about page</h1>
    </div>
</template>

<script>
  import apiCall from '../api/api_fake'

  export default {
    name: "About",

    mounted() {
      apiCall({url: 'about', method: 'GET'})
          .then(data => {
            this.users = data;
            this.loading = false;
          })
          .catch(error => {
            this.$store.commit('setSnack', {text: error, color: 'red'});
            this.loading = false;
          })
    }
  }
</script>