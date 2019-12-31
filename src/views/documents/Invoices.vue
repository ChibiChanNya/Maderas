<template>
  <section id="suppliers">

    <h1 class="text-md-center my-4 section-header">Lista de Facturas</h1>
    <v-card>
      <v-card-title>
        <v-spacer/>
        <v-text-field
          v-model="search"
          @input="isTyping = true"
          append-icon="search"
          label="Buscar..."
          single-line
          hide-details
        />
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
        :loading="loading"
        :pagination.sync="pagination"
        :total-items="total_items"
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ props.item['RazonSocialReceptor'] }}</td>
            <td class="">{{ props.item['Folio'] }}</td>
            <td class="">{{ props.item['Total'] | currency('$') }}</td>
            <td class="">
              <template v-if="props.item['FechaTimbrado']">
                {{ props.item['FechaTimbrado'] | moment('DD/M/YYYY')}}
              </template>
              <template v-else>
                --
              </template>
            </td>
            <td class="">{{ props.item['Status'] }}</td>

            <td class="justify-start layout px-0">
              <v-btn flat small color="primary" dark @click="emailInvoice(props.item['UID'])">Enviar Correo</v-btn>

              <v-icon
                small
                class="mr-4"

                @click="deleteInvoice(props.item['UID'])"
              >
                delete
              </v-icon>
            </td>
          </tr>
        </template>

        <template v-slot:no-data>
          <h1 v-if="loading" class="text-md-center my-3">
            <v-icon>timelapse</v-icon>
            Cargando datos ...
          </h1>
          <h1 v-else class="text-md-center my-3">
            <v-icon>warning</v-icon>
            No se encontraron datos
          </h1>
        </template>
        <template v-slot:pageText="props">
          Elementos {{ props.pageStart }} - {{ props.pageStop }} de {{ props.itemsLength }}
        </template>

      </v-data-table>
    </v-card>


  </section>
</template>

<script>
import { index_cfdi, send_cfdi, cancel_cfdi } from '../../api/documents_controller'
import utils from '../../mixins/utils'
import server_pagination from '../../mixins/server_pagination'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'IngresosEgresos',
  mixins: [utils, server_pagination, Vue2Filters.mixin],

  data() {
    return {
      index_fn: index_cfdi,
      delete_fn: cancel_cfdi,

      pagination: {
        sortBy: 'date',
      },
      loading: true,
      dialog: false,
      search: '',
      headers: [
        { text: 'Receptor', value: 'RazonSocialReceptor', align: 'center' },
        { text: 'Folio', align: 'center', value: 'Folio' },
        { text: 'Total', align: 'center', value: 'Total' },
        { text: 'Fecha', value: 'FechaTimbrado' },
        { text: 'Status', value: 'status', align: 'center' },
        { text: 'Acciones', value: 'id', align: 'center' },
      ],
      items: [],
      total_items: 0,
    }
  },
  methods: {
    async emailInvoice(uid) {
      this.loading = true
      try {
        await send_cfdi({ cfdi_uid: uid })
      } catch (err) {
        this.$store.commit('setSnack', { text: err, color: 'red' })
      } finally {
        this.loading = false
      }
    },

    async deleteInvoice(uid) {
      this.$dialog
        .confirm('¿Estás seguro de que quieres cancelar esta factura? No podrá recuperarse')
        .then((dialog) => {
          this.delete_fn({cfdi_uid: uid}).then(() => {
            this.$store.commit('setSnack', {text: "Factura cancelada exitosamente", color: 'success'});
          }).catch(err => {
            this.$store.commit('setSnack', {text: err.status || err, color: 'red'});
          }).finally( ()=> {
            this.pagination.page = 1
            dialog.close();
          })
        })
    },
  },

}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>