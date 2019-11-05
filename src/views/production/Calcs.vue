<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4 section-header">Cálculos Cerco Normal</h1>

        <v-container fluid grid-list-md>

            <v-layout row wrap justify-center>
                <v-flex xs8 md3>
                    <v-select
                            v-model="selected_product"
                            hint="Producto"
                            :items="products"
                            item-text="name"
                            label="Elije un Producto"
                            persistent-hint
                            single-line
                            return-object
                    ></v-select>
                </v-flex>
                <v-flex xs4 md1>
                    <v-text-field v-model.number="amount"
                                  label="Cantidad" @keyup="clear_product"></v-text-field>
                </v-flex>
                <v-flex xs4 md1>
                    <v-text-field v-model.number="size_a"
                                  label="A" @keyup="clear_product"></v-text-field>
                </v-flex>
                <v-flex xs4 md1 >
                    <v-text-field v-model.number="size_b"
                                  label="B" @keyup="clear_product"></v-text-field>
                </v-flex>
                <v-flex xs4 md1 o>
                    <v-text-field v-model.number="size_c"
                                  label="C" @keyup="clear_product"></v-text-field>
                </v-flex>
            </v-layout>

            <v-layout row wrap>
                <template v-for="(item, index) in items">
                    <v-flex
                            xs12
                            md4
                            :key="index"
                            v-if="item"
                    >
                        <v-card elevation-1>
                            <v-card-title>
                                <h1 class="text-xs-center ma-auto">{{ item.name }}</h1>
                            </v-card-title>
                            <v-data-table
                                    :items="item.data"
                                    hide-actions
                                    :headers="item.headers"
                            >
                                <template v-slot:items="props">
                                    <td class="text-xs-right">{{ props.item.per_unit }}</td>
                                    <td class="text-xs-right">{{ props.item.amount }}</td>
                                    <td class="text-xs-right">{{ props.item.height }}</td>
                                    <td class="text-xs-right">{{ props.item.width }}</td>
                                    <td class="text-xs-right">{{ props.item.length }}</td>
                                </template>
                            </v-data-table>
                        </v-card>

                    </v-flex>
                </template>
            </v-layout>


        </v-container>


    </section>
</template>

<script>
  import {
    index_products
  } from '../../api/production_controller';

  export default {
    name: "Calcs",

    data() {
      return {

        loading: true,
        products: [],
        amount: 10,
        selected_product: null,
        size_a: 330,
        size_b: 244,
        size_c: 10
      }
    },

    watch: {
      selected_product: function (prod) {
        this.size_a = prod.width;
        this.size_b = prod.length;
        this.size_c = prod.height;
      },
    },
    computed: {

      items() {

        const dimensiones = {
          name: "Base",
          headers: [
            {text: "NP", value: "per_unit", sortable: false, align: "center"},
            {text: "Cant. Pzas", value: "amount", sortable: false, align: "center"},
            {text: "Espesor", value: "height", sortable: false, align: "center"},
            {text: "Ancho", value: "width", sortable: false, align: "center"},
            {text: "Largo", value: "length", sortable: false, align: "center"}
          ],
          data: [
            {
              per_unit: 1,
              amount: this.amount,
              height: 3.2,
              width: this.size_c + 1,
              length: this.size_a + 17,
              tacon: false
            },
            {
              per_unit: 2,
              amount: this.amount * 2,
              height: 2.5,
              width: 10,
              length: this.size_a + 17,
              tacon: false
            },
            {per_unit: 2, amount: this.amount, height: "5/4", width: 6.8, length: this.size_c + 1, tacon: true},
          ]
        };
        const tapas = {
          name: "Tapas",
          headers: [
            {text: "P/U", value: "per_unit", sortable: false, align: "center"},
            {text: "Cant. Pzas", value: "amount", sortable: false, align: "center"},
            {text: "Espesor", value: "height", sortable: false, align: "center"},
            {text: "Ancho", value: "width", sortable: false, align: "center"},
            {text: "Largo", value: "length", sortable: false, align: "center"}
          ],
          data: [
            {
              per_unit: 1,
              amount: 1 * this.amount,
              height: 3.2,
              width: this.size_c + 1,
              length: this.size_a + 17,
              tacon: false
            },
            {
              per_unit: 2,
              amount: this.amount * 2,
              height: 2.5,
              width: 10,
              length: this.size_a + 17,
              tacon: false
            },
            {per_unit: 2, amount: this.amount, height: "5/4", width: 6.8, length: this.size_c + 1, tacon: true},
          ]
        };
        const laterales = {
          name: "Laterales",
          headers: [
            {text: "P/U", value: "per_unit", sortable: false, align: "center"},
            {text: "Cant. Pzas", value: "amount", sortable: false, align: "center"},
            {text: "Espesor", value: "height", sortable: false, align: "center"},
            {text: "Ancho", value: "width", sortable: false, align: "center"},
            {text: "Largo", value: "length", sortable: false, align: "center"}
          ],
          data: [
            {
              per_unit: 2,
              amount: 2 * this.amount,
              height: 2.5,
              width: this.size_c + 0.8,
              length: this.size_b + 3,
              tacon: false
            },
            {
              per_unit: 4,
              amount: this.amount * 4,
              height: 2.5,
              width: 10,
              length: this.size_b - 9,
              tacon: false
            },
          ]
        };

        return [dimensiones, tapas, laterales];

      },

    },

    methods: {
      clear_product(){
        this.selected_product = null;
      }
    },

    mounted() {
      this.axios.all([index_products()])
          .then(this.axios.spread(function (products) {
                // Both requests are now complete
                this.products = products.data;
              }.bind(this)
          ))
          .catch(error => {
            this.$store.commit('setSnack', {text: error, color: 'red'});
          })
          .finally(() => {
            this.loading = false;
          })
    },


  }
</script>

<style>
    table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
        padding: 0 12px;
    }

</style>