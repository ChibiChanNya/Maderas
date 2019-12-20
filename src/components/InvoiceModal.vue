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
            <v-btn color="blue darken-1" type="button" flat @click="dialog = false">Cerrar</v-btn>
            <v-btn color="blue darken-1" type="submit" flat>Crear Factura</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
  </v-layout>
</template>

<script>
import {get_cfdi_uses, get_payment_forms, get_payment_methods} from '../api/documents_controller'
import utils from '../mixins/utils'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'InvoiceModal',
  mixins: [utils, Vue2Filters.mixin],
  props: {
    shipment_id: {
      type: String,
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
  },
  data() {
    return {
      dialog: false,
      cfdi_uses: [],
      payment_methods: [],
      payment_forms: [],
      loading: false,
      valid_form: true
    }
  },

  async mounted() {
    const { data: { data: cfdiUses } } = await get_cfdi_uses()
    const { data: { data: paymentMethods } } = await get_payment_methods()
    const { data: { data: paymentForms } } = await get_payment_forms()
    this.cfdi_uses = cfdiUses
    this.payment_forms = paymentForms
    this.payment_methods = paymentMethods
  },
  methods: {
    create_cfdi() {
      if (this.$refs.form.validate()) {
        this.loading = true
        console.log("todo...")
      }
      else{
        console.log('not ready')
      }
      this.loading = false
    },
  },
}
</script>

<style scoped>

</style>