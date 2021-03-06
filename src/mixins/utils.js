export default {

  data() {
    return {
      nameRules: [
        v => !!v || 'Campo requerido',
        v => (v && v.length <= 70) || 'Máximo 70 caracteres'
      ],

      rfcRules: [
        v => (!v || (v.length === 12 || v.length === 13) || 'RFC debe estar compuesto por 12 or 13 símbolos')
      ],

      clabeRules: [
        v => (!v || (!isNaN(v) && v.length === 18)) || 'La Clabe debe star compuesta por 18 números'
      ],

      descRules: [
        v => !!v || 'Campo requerido',
        v => (v && v.length >= 3) || 'Mínimo 3 caracteres'
      ],

      moneyRules: [
        v => (!v || (!isNaN(v))) || 'Debe ser una cantidad'
      ],
      numberRules: [
        v => (!isNaN(v) && v >= 0) || "Debe ser un número positivo",
      ],
      required: [
        v => !!v || 'Campo requerido',
      ],
      typeRules: [
        v => !!v || 'Tipo requerido',
      ],
      priceRules: [
        v => (!isNaN(v) && v >= 0) || "Debe ser un número positivo",
      ],
      stockRules: [
        v => (!v || (!isNaN(v) && v >= 0)) || "Debe ser un número positivo",
      ],
      zipCodeRules: [
        v => (!isNaN(v) && v.length === 5) || "Código postal consiste en 5 números"
      ],
      emailRules: [
        v => !!v || 'E-mail requerido',
        v => /.+@.+/.test(v) || 'E-mail must be valid',
      ],
    }
  },

  methods: {

    formatted_date(date) {
      return date && date.length>0 ? this.$moment(date).format("DD/M/YYYY") : "";
    },

    client_name(id) {
      const client = this.clients.length > 0 && this.clients.find((prov) => prov.id === id);
      return (client && client.name) || "Cliente no encontrado";
    },

    product_name(id) {
      const product = (this.products && this.products.length > 0 && this.products.find((mat) => mat.id === id));
      return (product && product.name) || "Producto no encontrado";
    },

    getProductPrice(id){
      const product = (this.products && this.products.length > 0 && this.products.find((mat) => mat.id === id));
      return product.price || 0
    },
    getMaterialPrice(id){
      const material = (this.materials && this.materials.length > 0 && this.materials.find((mat) => mat.id === id));
      return material.recent_price || 0
    },

    status_name(val) {
      const status = val && this.status_list.find((stat) => stat.value === val);
      return (status && status.name) || "Null";
    },

    isNumber(val) {
      return val > 0;
    },

    provider_name(id) {
      const provider = this.providers.length > 0 && this.providers.find((prov) => prov.id === id);
      return (provider && provider.name) || "Proveedor no encontrado";
    },

    material_name(id) {
      const item = this.materials.find((mat) => mat.id === id);
      if (item && item.name)
        return `${item.name} - ${item.type}`;
      else return "Insumo no encontrado";
    },

    compare(a, b, isDesc) {
      if (!isDesc) {
        return a < b ? -1 : 1;
      } else {
        return b < a ? -1 : 1;
      }
    },

    async deleteItem(item) {
      const index = this.items.indexOf(item);
      this.$dialog
          .confirm('¿Estás seguro de que quieres borrar este elemento? No podrá recuperarse')
          .then((dialog) => {
            this.delete_fn({id: item.id}).then(() => {
              this.items.splice(index, 1);
              this.$store.commit('setSnack', {text: "Elemento borrado exitosamente", color: 'success'});

            }).catch(err => {
              this.$store.commit('setSnack', {text: err.status || err, color: 'red'});
            }).finally( ()=> {
              dialog.close();
          })
      })
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.$refs.form.reset();
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

  },


};