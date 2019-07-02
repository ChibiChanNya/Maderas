export default  {

  methods: {

    formatted_date(date) {
      return date ? this.$moment(date).format("DD/M/YYYY") : "";
    },

    client_name(id) {
      const client = this.clients.length > 0 && this.clients.find((prov) => prov.id === id);
      return (client && client.name) || "Cliente no encontrado";
    },

    product_name(id) {
      const product = (this.products.length > 0 && this.products.find((mat) => mat.id === id));
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