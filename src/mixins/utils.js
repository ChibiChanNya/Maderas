export default  {

  data(){
    return{
      nameRules: [
        v => !!v || 'Campo requerido',
        v => (v && v.length <= 70) || 'Máximo 70 caracteres'
      ],

      rfcRules: [
        v => (!v || (v.length === 13 || v.length === 14)) || 'RFC debe estar compuesto por 13 or 14 símbolos'
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
        v => !!v || 'Campo requerido',
        v => (!isNaN(v) && v > 0) || "Debe ser un número positivo",
      ],
      required: [
        v => !!v || 'Campo requerido',
      ],
      typeRules: [
        v => !!v || 'Tipo requerido',
      ],
      priceRules: [
        v => !!v || 'Campo requerido',
        v => (!isNaN(v) && v > 0) || "Debe ser un número positivo",
      ],
      stockRules: [
        v => (!v || (!isNaN(v) && v >= 0)) || "Debe ser un número positivo",
      ],
    }
  },

  methods: {

    formatted_date(date) {
      return date ? this.$moment(date).format("DD/M/YYYY") : "";
    },

    client_name(id) {
      const client = this.clients.length > 0 && this.clients.find((prov) => prov.id === id);
      return (client && client.name) || "Cliente no encontrado";
    },

    product_name(id) {
      const product = (this.products && this.products.length > 0 && this.products.find((mat) => mat.id === id));
      return (product && product.name) || "Producto no encontrado";
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
    }
  },

};