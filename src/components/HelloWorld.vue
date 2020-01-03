<template>
  <v-container>
    <v-layout
      text-xs-center
      wrap
      justify-center
    >
      <v-flex xs12>
        <v-img
          :src="require('@/assets/img/Madereria Logo.png')"
          class="my-3"
          contain
          height="250"
        />
      </v-flex>

      <v-flex mb-4 md8>
        <h2 class="display-2 font-weight-bold mb-3">
          Bienvenidos al sistema de administración de empaques, maderas y tarimas Cuautla
        </h2>
      </v-flex>

    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data: () => ({})
  }
</script>

<style>

</style>
