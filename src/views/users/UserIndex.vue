<template>
    <section id="users">

        <h1 class="text-md-center my-4">Usuarios en la plataforma</h1>

        <v-data-table
                :headers="headers"
                :items="users"
                class="elevation-1"
                :loading="loading"
        >
            <template v-slot:items="props">
                <td class="text-xs-center">{{ props.item.name }}</td>
                <td class="text-xs-center">{{ props.item.username }}</td>
                <td class="text-xs-center">
                    <v-icon>{{ (props.item.permissions & 1) === 1? 'check' : ''}}</v-icon>
                </td>
                <td class="text-xs-center">
                    <v-icon>{{ (props.item.permissions & 16) === 16? 'check' : ''}}</v-icon>
                </td>
                <td class="text-xs-center">
                    <v-icon>{{ (props.item.permissions & 4) === 4? 'check' : ''}}</v-icon>
                </td>
                <td class="text-xs-center">
                    <v-icon>{{ (props.item.permissions & 2) === 2? 'check' : ''}}</v-icon>
                </td>
                <td class="text-xs-center">
                    <v-icon>{{ (props.item.permissions & 8) === 8? 'check' : ''}}</v-icon>
                </td>
            </template>

            <template v-slot:no-data>
                <h1 v-if="loading" class="text-md-center my-3"><v-icon>timelapse</v-icon> Cargando datos ...</h1>
                <h1 v-else class="text-md-center my-3"> <v-icon>warning</v-icon> No se encontraron datos</h1>
            </template>

        </v-data-table>

    </section>
</template>

<script>
  import apiCall from '../../utils/api_fake'

  export default {
    name: "UserIndex",

    data() {
      return {

        loading: true,
        headers: [
          {
            text: 'Nombre',
            align: 'left',
            value: 'name'
          },
          {text: 'Nombre de Usuario', value: 'username'},
          {text: 'Dashboard'},
          {text: 'Usuarioz'},
          {text: 'Materia Prima'},
          {text: 'Producción'},
          {text: 'Documentos'},
        ],

        users: [],
      }
    },


    mounted() {
      apiCall({url: 'users', method: 'GET'})
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

<style>
    table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
        padding: 0 18px;
    }
</style>