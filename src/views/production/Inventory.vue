<template>
  <section id="materials_inventory">

    <h1 class="text-md-center my-4 section-header">Inventario de Productos</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">Crear Producto</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-form ref="form" v-model="valid_form" lazy-validation>
                <v-container grid-list-md class="py-0">
                  <v-layout wrap justify-center>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.name"
                                    :rules="nameRules"
                                    label="Nombre"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm6>
                      <v-text-field v-model="editedItem.sku"
                                    :rules="required"
                                    label="SKU"></v-text-field>
                    </v-flex>
                    <v-flex xs12 sm12>
                      <v-text-field v-model="editedItem.description"
                                    label="Descripcion"></v-text-field>
                    </v-flex>

                    <v-flex xs6 sm3>
                      <v-text-field v-model.number="editedItem.price" type="number"
                                    :rules="priceRules"
                                    label="Precio Actual"></v-text-field>
                    </v-flex>
                    <v-flex xs6 sm3>
                      <v-text-field v-model.number="editedItem.stock" type="number"
                                    :rules="numberRules"
                                    label="Stock Disponible"></v-text-field>
                    </v-flex>
                    <v-flex xs6 sm3>
                      <v-text-field v-model.number="editedItem.box_volume" type="number"
                                    :rules="numberRules"
                                    label="Volumen Caja"/>
                    </v-flex>
                    <v-flex xs6 sm3>
                      <v-text-field v-model.number="editedItem.materials_volume" type="number"
                                    :rules="numberRules"
                                    label="Volumen Materiales"/>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field v-model.number="editedItem.width" type="number"
                                    :rules="numberRules"
                                    label="Ancho"/>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field v-model.number="editedItem.height" type="number"
                                    :rules="numberRules"
                                    label="Alto"/>
                    </v-flex>
                    <v-flex xs4>
                      <v-text-field v-model.number="editedItem.length" type="number"
                                    :rules="numberRules"
                                    label="Largo"/>
                    </v-flex>

                    <v-flex xs12 sm12>
                      <v-select
                        v-model="editedItem.unit_code"
                        :items="unit_codes"
                        label="Unidad de Medida (factura)"
                        item-value="key"
                        persistent-hint
                        :rules="required"
                      >
                        <template v-slot:item="props">
                          {{ props.item.name }} ({{props.item.key}})
                        </template>
                        <template v-slot:selection="props">
                          {{ props.item.name }} ({{props.item.key}})
                        </template>
                      </v-select>
                    </v-flex>
                    <v-flex xs12 sm12>
                      <v-autocomplete
                        v-model="editedItem.product_service_code"
                        :loading="codesLoading"
                        :search-input.sync="codeSearch"
                        cache-items
                        :items="product_codes"
                        label="Clave de Producto/Servicio"
                        placeholder="Escribe 3 caracteres para buscar más"
                        item-value="code"
                        no-data-text="No se encontraron resultados"
                        persistent-hint
                        :rules="required"
                      >
                        <template v-slot:item="props">
                          {{ props.item.text }} ({{props.item.code}})
                        </template>
                        <template v-slot:selection="props">
                          {{ props.item.text }} ({{props.item.code}})
                        </template>
                      </v-autocomplete>
                    </v-flex>
                  </v-layout>
                </v-container>
              </v-form>

            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" flat @click="close">Cancelar</v-btn>
              <v-btn color="blue darken-1" flat @click="save">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-spacer/>
        <v-text-field
          v-model="search"
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
        :search="search"
        hide-actions
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ props.item.sku }}</td>
            <td class="">{{ props.item.name }}</td>
            <td class="">{{ props.item.description }}</td>
            <td class="">{{ props.item.price | currency('$')}}</td>
            <td class="">{{ props.item.stock }}</td>
            <td class="">
              <v-btn flat small color="blue" @click="props.expanded2 = !props.expanded2">
                Pendiente
              </v-btn>
            </td>

            <td class="justify-start layout px-0">
              <v-layout row justify-center>
                <v-btn flat small color="blue" @click="props.expanded = !props.expanded">
                  Ver Medidas
                </v-btn>
                <v-icon
                  small
                  class="mx-4 "
                  @click="editItem(props.item)"
                >
                  edit
                </v-icon>
                <v-icon
                  small
                  class="mr-4"

                  @click="deleteItem(props.item)"
                >
                  delete
                </v-icon>
              </v-layout>

            </td>
          </tr>
        </template>

        <template v-slot:expand="props">
          <div class="grey lighten-3 pl-5">
            <tr>
              <th>Volumen Caja</th>
              <th>Volumen Materiales</th>
              <th>Ancho</th>
              <th>Alto</th>
              <th>Largo</th>
            </tr>
            <tr>
              <td class="text-xs-center">
                {{props.item.box_volume}} cm<sup>3</sup>
              </td>
              <td class="text-xs-center">
                {{props.item.materials_volume}} cm<sup>3</sup>
              </td>
              <td class="text-xs-center">
                {{props.item.width}} cm
              </td>
              <td class="text-xs-center">
                {{props.item.height}} cm
              </td>
              <td class="text-xs-center">
                {{props.item.length}} cm
              </td>
            </tr>

          </div>

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
import {
  create_product,
  index_orders,
  index_products,
  remove_product,
  update_product,
} from '../../api/production_controller'
import utils from '../../mixins/utils'
import Vue2Filters from 'vue2-filters'
import unit_codes from './cfdUnitCodes'
import debounce from "lodash.debounce";

export default {
  name: 'ProductsInventory',
  mixins: [utils, Vue2Filters.mixin],

  data() {
    return {
      index_fn: index_products,
      delete_fn: remove_product,

      codesLoading: false,
      base_product_codes: [
        { text: 'Embalaje de Madera', code: '24112901'},
        { text: 'Embalaje', code: '24112900'},
        { text: 'Madera', code: '11121600'},
        { text: 'Madera Tratada', code: '11122006'},
        { text: 'Pallets de Madera', code: '24112701'},
      ],
      product_codes: [],
      codeSearch: null,

      loading: true,
      dialog: false,
      search: '',
      headers: [
        { text: 'SKU', value: 'sku' },
        { text: 'Nombre', value: 'name' },
        { text: 'Descripción', value: 'description' },
        { text: 'Precio actual', value: 'price' },
        { text: 'Stock Disponible', value: 'stock' },
        { text: 'Stock Pendiente', value: 'id', sortable: false },
        { text: 'Acciones', value: 'id', sortable: false, align: 'center' },
      ],

      items: [],
      orders: [],

      valid_form: true,

      editedIndex: -1,
      editedItem: {
        id: '',
        sku: '',
        name: '',
        description: '',
        price: 0,
        box_volume: 0,
        materials_volume: 0,
        height: 0,
        width: 0,
        length: 0,
        stock: 0,
        product_service_code: '',
        unit_code: '',
        unit_description: '',
      },
      defaultItem: {
        id: '',
        sku: '',
        name: '',
        description: '',
        price: 0,
        box_volume: 0,
        materials_volume: 0,
        height: 0,
        width: 0,
        length: 0,
        stock: 0,
        product_service_code: '',
        unit_code: '',
        unit_description: '',
      },

    }
  },

  watch: {
    codeSearch(val){
      this.codesLoading = true
      if (val && val.length >= 3 && val !== this.editedItem.product_service_code)
        this.debouncedFetchCodes(val)
      else{
        this.product_codes = this.base_product_codes
        this.codesLoading = false;
      }
    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Producto' : 'Editar Producto'
    },

  },

  created() {
    this.unit_codes = unit_codes

    this.debouncedFetchCodes = debounce(this.fetchCFDICodes, 300)
  },


  mounted() {
    this.product_codes = this.base_product_codes
    this.axios.all([index_products(), index_orders()])
      .then(this.axios.spread(function(materials, orders) {
          // Both requests are now complete
          this.items = materials.data
          this.orders = orders.data

        }.bind(this),
      ))
      .catch(error => {
        this.$store.commit('setSnack', { text: error, color: 'red' })
      })
      .finally(() => {
        this.loading = false
      })
  },

  methods: {

    fetchCFDICodes(v) {
      setTimeout(async () => {
        const url = `https://www.plataformakoatl.com/appSupport/Services/AjaxRequestManagers/PublicToolsAjaxRequestManager.ashx?commandName=GetCodeProdServByDescription&inputParameters=%7B%22NameString%22%3A%22${v}%22%7D&`
        try {
          let response = await fetch(url, { mode: 'cors' })
          let json = await response.json()
          if (json['Status'] === 'success') {
            const codes = json.CodeProdServ
            const list = []
            for (var prop in codes) {
              if (Object.prototype.hasOwnProperty.call(codes, prop)) {
                list.push({ code: prop, text: codes[prop] })
              }
            }
            this.product_codes = list
          } else {
            console.log('failed')
          }
        } catch (e) {
          console.log(e)
        } finally {
          this.codesLoading = false
        }

      }, 500)
    },

    item_orders(item) {
      return this.orders.filter((order) => order.material_id === item.id && order.status === 'pendiente')
    },

    calc_pending_stock(item) {
      return this.item_orders(item).reduce(function(a, b) {
        return a + b.units
      }, 0)
    },


    editItem(item) {
      this.editedIndex = this.items.findIndex((product) => product.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },

    save() {
      if (this.$refs.form.validate()) {
        this.loading = true
        // Editing an User
        if (this.editedIndex > -1) {
          update_product(this.editedItem).then(({ data: newItem }) => {
            this.$set(newItem)
            this.$store.commit('setSnack', { text: 'Producto actualizado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
          //  Creating a new User
        } else {
          create_product(this.editedItem).then(({ data: newItem }) => {
            this.items.push(newItem)
            this.$store.commit('setSnack', { text: 'Producto creado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
        }

      }
    },
  }
  ,

}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>