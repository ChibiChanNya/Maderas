<template>
    <section id="suppliers">

        <h1 class="text-md-center my-4 section-header">Entregas de Producto</h1>
        <v-card>
            <v-card-title>
                <v-dialog v-model="dialog" max-width="500px" persistent>
                    <template v-slot:activator="{ on }">
                        <v-btn color="primary" dark class="mb-2" v-on="on">Registrar Entrega</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-form ref="form" v-model="valid_form" lazy-validation>
                                <v-container grid-list-md>
                                    <v-layout wrap justify-center>
                                        <v-flex xs12 sm6>
                                            <v-select
                                                    v-model="editedItem.order_id"
                                                    hint="Pedidos"
                                                    :items="orders"
                                                    item-text="order_date"
                                                    item-value="id"
                                                    label="Elije el pedido"
                                                    persistent-hint
                                                    single-line
                                                    :rules="required"
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model="editedItem.cost"
                                                          :rules="numberRules"
                                                          label="Costo"></v-text-field>
                                        </v-flex>

                                        <v-flex xs4 sm4>
                                            <v-select
                                                    v-model="editedItem.status"
                                                    :items="status_list"
                                                    label="Status"
                                                    item-text="name"
                                                    item-value="value"
                                                    persistent-hint
                                                    :rules="required"
                                            ></v-select>
                                        </v-flex>
                                        <v-flex xs12 sm6>
                                            <v-dialog
                                                    ref="dialog1"
                                                    v-model="modal_date_1"
                                                    :return-value.sync="editedItem.delivery_date"
                                                    persistent
                                                    lazy
                                                    full-width
                                                    width="290px"
                                            >
                                                <template v-slot:activator="{ on }">
                                                    <v-text-field
                                                            v-model="editedItem.delivery_date"
                                                            label="Fecha de solicitud"
                                                            prepend-icon="event"
                                                            readonly
                                                            clearable
                                                            :value="formatted_date(editedItem.delivery_date)"
                                                            v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker v-model="editedItem.delivery_date" scrollable
                                                               locale="es-mx"
                                                >
                                                    <v-spacer></v-spacer>
                                                    <v-btn flat color="primary" @click="modal_date_1 = false">Cancelar
                                                    </v-btn>
                                                    <v-btn flat color="primary"
                                                           @click="$refs.dialog1.save(editedItem.delivery_date)">OK
                                                    </v-btn>
                                                </v-date-picker>
                                            </v-dialog>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model="editedItem.certificate"
                                                          label="Certificado de Tratamiento"></v-text-field>
                                        </v-flex>
                                        <v-flex xs4 sm4>
                                            <v-text-field v-model.number="editedItem.invoice"
                                                          label="# Factura"></v-text-field>
                                        </v-flex>
                                        <v-flex xs12>
                                            <h3>Productos Solicitados</h3>
                                        </v-flex>
                                        <template>
                                            <template
                                                    v-for="product in editedItem.order_details">

                                                <v-flex xs9 :key="product.product_id">
                                                    <v-select
                                                            v-model="product.product_id"
                                                            hint="Producto"
                                                            item-value="id"
                                                            label="Elije un producto"
                                                            :items="products"
                                                            persistent-hint
                                                            single-line
                                                            :rules="required">

                                                        <template v-slot:item="props">
                                                            {{ props.item.name }} - {{ props.item.type }}
                                                        </template>
                                                        <template v-slot:selection="props">
                                                            {{ props.item.name }} - {{ props.item.type }}
                                                        </template>
                                                    </v-select>
                                                </v-flex>
                                                <v-flex xs2 :key="product.product_id">
                                                    <v-text-field v-model="product.units"
                                                                  :rules="numberRules"
                                                                  type="number"
                                                                  label="Cantidad"></v-text-field>
                                                </v-flex>
                                                <v-flex xs1 :key="product.product_id">
                                                    <v-btn flat icon style="align-self:center"
                                                           @click="removeProduct(product)">
                                                        <v-icon class="red--text">close</v-icon>
                                                    </v-btn>
                                                </v-flex>
                                            </template>
                                            <template v-if="editedItem.order_details.length === 0">
                                                <h4>No se han registrado productos para este pedido</h4>
                                            </template>
                                            <v-flex>
                                                <v-btn flat color="info" @click="addProduct">Agregar nuevo producto
                                                </v-btn>
                                            </v-flex>
                                        </template>

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
                <v-spacer></v-spacer>
                <v-text-field
                        v-model="search"
                        append-icon="search"
                        label="Buscar..."
                        single-line
                        hide-details
                ></v-text-field>
            </v-card-title>

            <v-data-table
                    :headers="headers"
                    :items="items"
                    class="elevation-1"
                    :loading="loading"
                    :search="search"
                    :pagination.sync="pagination"
                    hide-actions
            >
                <template v-slot:items="props">
                    <tr>
                        <td class="">{{ order_data(props.item.order_id) }}</td>
                        <td class="">{{ props.item.units }}</td>
                        <td class="">$ {{ props.item.cost }}</td>
                        <td class="">{{ status_name(props.item.status) }}</td>
                        <td class="">{{ props.item.delivery_date | moment('DD/M/YYYY')}}</td>
                        <td class="">{{ props.item.certificate }}</td>
                        <td class="">{{ props.item.invoice }}</td>
                        <v-btn flat small color="warning" @click="props.expanded = !props.expanded">EXPANDIR</v-btn>
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
    index_shipments,
    index_clients,
    index_orders,
    update_shipment,
    create_shipment,
    remove_shipment
  } from '../../api/production_controller';

  export default {
    name: "ClientOrders",

    data() {
      return {

        loading: true,
        dialog: false,
        modal_date_1: false,
        search: '',
        pagination: {
          sortBy: 'delivery_date'
        },
        headers: [
          {text: 'Pedido', value: 'order_id', align: 'center'},

          {text: 'Unidades', value: 'units', align: 'center'},
          {text: 'Costo', value: 'cost', align: 'center'},
          {text: 'Fecha Envío', value: 'delivery_date', align: 'center'},
          {text: 'Certificado de Tratamiento', value: 'certificate', align: 'center'},
          {text: '# Factura', value: 'invoice', align: 'center'},
          {text: 'Acciones', value: 'id', align: 'center'},
        ],
        items: [],
        orders: [],
        clients: [],

        status_list: [
          {name: "Espera", value: "pending"},
          {name: "Pendiente Tratamiento", value: "pending_treatment"},
          {name: "Listo", value: "ready"},
          {name: "Enviado", value: "sent"},
          {name: "Pagado", value: "paid"},
        ],

        valid_form: true,

        numberRules: [
          v => !!v || 'Campo requerido',
          v => (!isNaN(v) && v > 0) || "Debe ser un número positivo",
        ],

        required: [
          v => !!v || 'Campo requerido',
        ],

        editedIndex: -1,
        editedItem: {
          id: '',
          order_id: '',
          units: 0,
          cost: 0,
          certificate: '',
          delivery_date: new Date().toISOString().slice(0, 10),
          status: null,
          invoice: null,
        },
        defaultItem: {
          id: '',
          order_id: '',
          units: 0,
          cost: 0,
          certificate: '',
          delivery_date: new Date().toISOString().slice(0, 10),
          status: null,
          invoice: null,
        },

      }
    },

    computed: {
      formTitle() {
        return this.editedIndex === -1 ? 'Nueva Entrega' : 'Editar Entrega'
      },


    },

    mounted() {
      this.axios.all([index_shipments(), index_orders(), index_clients()])
          .then(this.axios.spread(function (shipments, orders, clients) {
                // Both requests are now complete
                this.items = shipments.data;
                this.clients = clients.data;
                this.orders = orders.data;
              }.bind(this)
          ))
          .catch(error => {
            this.$store.commit('setSnack', {text: error, color: 'red'});
          })
          .finally(() => {
            this.loading = false;
          })
    },

    methods: {

      formatted_date(date) {
        return date ? this.$moment(date).format("DD/M/YYYY") : "";
      },


      order_data(id) {
        return "Pendiente " + id
      },

      status_name(val) {
        return this.status_list.find((stat) => stat.value === val).name;
      },

      editItem(item) {
        this.editedIndex = this.items.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.$refs.form.resetValidation();
        this.dialog = true;
      },

      deleteItem(item) {
        const index = this.items.indexOf(item);
        confirm('¿Estás seguro de que quieres borrar esta entrega?') && remove_shipment({id: item.id}).then(() => {
          this.items.splice(index, 1);
          this.$store.commit('setSnack', {text: "Entrega borrada exitosamente", color: 'success'});

        }).catch(err => {
          this.$store.commit('setSnack', {text: err, color: 'red'});
        });
      },

      close() {
        this.dialog = false;
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem);
          this.editedIndex = -1;
          this.$refs.form.reset();
        }, 300);
      },

      save() {
        if (this.$refs.form.validate()) {
          this.loading = true;
          // Editing an User
          if (this.editedIndex > -1) {
            update_shipment(this.editedItem).then(() => {
              Object.assign(this.items[this.editedIndex], this.editedItem);
              this.$store.commit('setSnack', {text: "Entrega actualizada exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
            //  Creating a new User
          } else {
            create_shipment(this.editedItem).then(() => {
              this.items.push(Object.assign({}, this.editedItem));
              this.$store.commit('setSnack', {text: "Pedido creado exitosamente", color: 'success'});
              this.close();
            }).catch(err => {
              this.$store.commit('setSnack', {text: err, color: 'red'});
            }).finally(() => {
              this.loading = false
            });
          }

        }
      }
    },

  }
</script>

<style>
    table.v-table thead td:not(:nth-child(1)), table.v-table tbody td:not(:nth-child(1)), table.v-table thead th:not(:nth-child(1)), table.v-table tbody th:not(:nth-child(1)), table.v-table thead td:first-child, table.v-table tbody td:first-child, table.v-table thead th:first-child, table.v-table tbody th:first-child {
        padding: 0 18px;
    }
</style>