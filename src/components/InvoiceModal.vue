<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on }">
        <v-btn flat small color="primary" dark v-on="on">Facturar</v-btn>
      </template>
      <v-form @submit.prevent="create_cfdi" ref="form" lazy-validation v-model="valid_form">
        <v-card>
          <v-card-title>
            <span class="headline">Crear Factura sobre Entrega</span>
          </v-card-title>
          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12>
                  <h2>Productos</h2>
                  <v-layout v-for="(item, index) in shipment_details" :key="index">
                    <v-flex xs6>{{item.product}}</v-flex>
                    <v-flex xs2>x {{ item.units }}</v-flex>
                  </v-layout>
                </v-flex>
                <v-divider/>
                <v-flex xs6>
                  <h3>Monto Total:</h3>
                </v-flex>
                <v-flex xs6>
                  <h3>{{ cost | currency('$') }}</h3>
                </v-flex>
                <v-flex xs12 sm6>
                  <v-autocomplete
                    :items="payment_forms"
                    label="Forma de Pago"
                    item-text="name"
                    item-value="key"
                    v-model="payment_form"
                    :rules="required"
                  />
                </v-flex>
                <v-flex xs12 sm6>
                  <v-autocomplete
                    :items="payment_methods"
                    label="Método de Pago"
                    item-text="name"
                    item-value="key"
                    :rules="required"
                  />
                </v-flex>
                <v-flex xs12>
                  <v-select
                    :items="cfdi_uses"
                    label="Uso de Factura"
                    v-model="cfdi_use"
                    item-text="name"
                    item-value="key"
                    :rules="required"
                  />
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" :disabled="loading" type="button" flat @click="dialog = false">Cerrar</v-btn>
            <v-btn color="blue darken-1" :disabled="loading" type="submit" flat>Crear Factura</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
  </v-layout>
</template>

<script>
import { create_cfdi, get_cfdi_uses, get_payment_forms, get_payment_methods } from '../api/documents_controller'
import utils from '../mixins/utils'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'InvoiceModal',
  mixins: [utils, Vue2Filters.mixin],
  props: {
    shipment_id: {
      type: Number,
      required: true,
    },
    shipment_details: {
      type: Array,
      default: [],
    },
    cost: {
      type: Number,
      default: 0,
    },
    cfdi_uses: {
      type: Array,
      default: [],
    },
    payment_methods: {
      type: Array,
      default: [],
    },
    payment_forms: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      dialog: false,
      cfdi_use: '',
      payment_form: '',
      loading: false,
      valid_form: true,
    }
  },


  methods: {
    async create_cfdi() {
      if (this.$refs.form.validate()) {
        this.loading = true
        try {
          const item = {
            shipment_id: this.shipment_id,
            cfdi_use: this.cfdi_use,
            payment_form: this.payment_form,
          }
          const { data } = await create_cfdi(item)
          console.log('DATA', data)
          if (data.response === 'success') {
            this.$store.commit('setSnack', { text: data.message, color: 'success' })
            this.dialog = false
          } else {
            this.$store.commit('setSnack', { text: data.message, color: 'red' })
          }
        } catch (err) {
          this.$store.commit('setSnack', { text: err, color: 'red' })
        } finally {
          this.loading = false
        }
      } else {
        console.log('not ready')
      }
      this.loading = false
    },
  },
}
</script>

<style scoped>

</style>