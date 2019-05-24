<template>
    <v-snackbar v-model="show" :color="color" top right :timeout="3000">
        {{message}}
        <v-btn flat  @click.native="show = false">Cerrar</v-btn>
    </v-snackbar>
</template>

<script>
  export default {
    data () {
      return {
        show: false,
        message: '',
        color: 'error',
      }
    },

    created: function () {
      this.$store.watch(state => state.snackbar.snack, () => {
        const msg = this.$store.state.snackbar.snack;
        if (msg !== '') {
          this.show = true;
          this.message = this.$store.state.snackbar.snack;
          this.color = this.$store.state.snackbar.color;
          this.$store.commit('setSnack', {text: ''});
        }
      })
    },

  }
</script>