<template>
  <section id="materials_inventory">

    <h1 class="text-md-center my-4 section-header">Inventario de Insumos</h1>
    <v-card>
      <v-card-title>
        <v-dialog v-model="dialog" max-width="500px" persistent>
          <template v-slot:activator="{ on }">
            <v-btn color="primary" dark class="mb-2" v-on="on">Crear Insumo</v-btn>
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
                      <v-select
                        v-model="editedItem.provider_id"
                        hint="Proveedor"
                        :items="providers"
                        item-text="name"
                        item-value="id"
                        label="Elije al proveedor"
                        persistent-hint
                        single-line
                      ></v-select>
                    </v-flex>

                    <v-flex xs6 sm6>
                      <v-text-field v-model="editedItem.type"
                                    :rules="typeRules"
                                    label="Tipo"></v-text-field>
                    </v-flex>
                    <v-flex xs6 sm6>
                      <v-text-field v-model.number="editedItem.recent_price"
                                    :rules="priceRules"
                                    label="Precio (más reciente)"></v-text-field>
                    </v-flex>
                    <v-flex xs6 sm4>
                      <v-text-field v-model.number="editedItem.available_stock"
                                    :rules="stockRules"
                                    label="Stock Disponible"></v-text-field>
                    </v-flex>
                    <!--                                        <v-flex xs6 sm4>-->
                    <!--                                            <v-text-field v-model.number="editedItem.pending_stock"-->
                    <!--                                                          :rules="stockRules"-->
                    <!--                                                          label="Stock Pendiente"></v-text-field>-->
                    <!--                                        </v-flex>-->
                  </v-layout>
                </v-container>
              </v-form>

            </v-card-text>

            <v-card-actions>
              <v-spacer/>
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
        :custom-sort="customSort"
        hide-actions
      >
        <template v-slot:items="props">
          <tr>
            <td class="">{{ props.item.name }}</td>
            <td class="">{{ props.item.type }}</td>
            <td class="">{{ provider_name(props.item.provider_id) }}</td>
            <td class="">{{ Number(props.item.recent_price) | currency('$') || '----' }}</td>
            <td class="">{{ props.item.available_stock }}</td>
            <!--                        <td class="">-->
            <!--                            <v-btn flat small color="blue" @click="props.expanded = !props.expanded">{{ calc_pending_stock(props.item) }}</v-btn>-->
            <!--                        </td>-->
            <td class="justify-start layout px-0">
              <v-icon
                small
                class="mr-5 "
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
            </td>
          </tr>
        </template>

        <!--                <template v-slot:expand="props">-->
        <!--                    <div class="grey lighten-3 pl-5">-->
        <!--                        <template v-if="item_orders(props.item).length >0">-->
        <!--                            <tr>-->
        <!--                                <th>Unidades</th>-->
        <!--                                <th>Fecha Solicitud</th>-->
        <!--                            </tr>-->
        <!--                            <tr v-for="order in item_orders(props.item)" :key="order.id">-->
        <!--                                <td class="text-xs-center">-->
        <!--                                    {{order.units}}-->
        <!--                                </td>-->
        <!--                                <td class="text-xs-center">-->
        <!--                                    {{order.request_date | moment('DD/M/YYYY')}}-->
        <!--                                </td>-->
        <!--                            </tr>-->
        <!--                        </template>-->
        <!--                        <template v-else>-->
        <!--                            <h3 class="py-3">No hay pedidos pendientes para este insumo.</h3>-->
        <!--                        </template>-->

        <!--                    </div>-->

        <!--                </template>-->

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
  create_material,
  index_materials,
  index_suppliers,
  remove_material,
  update_material,
} from '../../api/materials_controller'
import utils from '../../mixins/utils'
import Vue2Filters from 'vue2-filters'

export default {
  name: 'MaterialsInventory',
  mixins: [utils, Vue2Filters.mixin],

  data() {
    return {
      index_fn: index_materials,
      delete_fn: remove_material,

      loading: true,
      dialog: false,
      search: '',
      headers: [
        { text: 'Nombre', value: 'name' },

        {
          text: 'Tipo',
          align: 'left',
          value: 'type',
        },
        { text: 'Proveedor', value: 'provider_id', sortable: 'false' },
        { text: 'Precio más reciente', value: 'recent_price' },
        { text: 'Stock Disponible', value: 'available_stock' },
        { text: 'Acciones', value: 'id', sortable: false },
      ],

      total_items: 0,
      pagination: {},

      items: [],
      providers: [],
      orders: [],

      valid_form: true,

      editedIndex: -1,
      editedItem: {
        id: '',
        name: '',
        type: '',
        provider_id: '',
        recent_price: '',
        available_stock: 0,
        pending_stock: 0,
      },
      defaultItem: {
        id: '',
        name: '',
        type: '',
        provider_id: '',
        recent_price: '',
        available_stock: 0,
        pending_stock: 0,
      },

    }
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Insumo' : 'Editar Insumo'
    },

  },

  mounted() {
    this.axios.all([index_materials(), index_suppliers()])
      .then(this.axios.spread(function (materials, providers) {
          // Both requests are now complete
          this.items = materials.data
          this.providers = providers.data
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


    calc_pending_stock(item) {
      return this.item_orders(item).reduce(function (a, b) {
        return a + b.units
      }, 0)
    },

    editItem(item) {
      this.editedIndex = this.items.findIndex((material) => material.id === item.id)
      this.editedItem = JSON.parse(JSON.stringify(this.items[this.editedIndex]))
      this.$refs.form.resetValidation()
      this.dialog = true
    },

    save() {
      if (this.$refs.form.validate()) {
        this.loading = true
        // Editing an User
        if (this.editedIndex > -1) {
          update_material(this.editedItem).then(({data: newItem}) => {
            this.$set(this.items, this.editedIndex, newItem)
            this.$store.commit('setSnack', { text: 'Insumo actualizado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
          //  Creating a new User
        } else {
          create_material(this.editedItem).then(({data: newItem}) => {
            this.items.push(newItem)
            this.$store.commit('setSnack', { text: 'Insumo creado exitosamente', color: 'success' })
            this.close()
          }).catch(err => {
            this.$store.commit('setSnack', { text: err, color: 'red' })
          }).finally(() => {
            this.loading = false
          })
        }

      }
    },

    customSort(items, index, isDesc) {
      items.sort((a, b) => {
        if (index === 'provider_id') {
          return this.compare(this.provider_name(a.provider_id), this.provider_name(b.provider_id), isDesc)
        } else {
          return this.compare(a[index], b[index], isDesc)
        }
      })
      return items
    },


  },

}
</script>

<style>
table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
  padding: 0 18px;
}
</style>