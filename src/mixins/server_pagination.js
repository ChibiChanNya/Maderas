import debounce from "lodash.debounce";

export default {
  data() {
    return {
      isTyping: false
    }
  },
  watch: {
    pagination: {
      handler() {
        this.index_details()
            .then(data => {
              this.items = data.items;
              this.total_items = data.total;
            })
      },
      deep: true
    },

    search: debounce(function () {
      this.isTyping = false;
    }, 500),

    isTyping: function (value) {
      if (!value) {
        this.index_details()
            .then(data => {
              console.log("GOT SEARCH RESULTS", data);
              this.items = data.items;
              this.total_items = data.total_items;
            })
      }
    }
  },

  methods: {
    index_details: function () {
      console.log("Index!");
      this.loading = true;
      return new Promise((resolve, reject) => {
        const {sortBy, descending, page, rowsPerPage} = this.pagination;

        let items = this.items;

        if (this.pagination.sortBy) {
          items = items.sort((a, b) => {
            const sortA = a[sortBy];
            const sortB = b[sortBy];

            if (descending) {
              if (sortA < sortB) return 1;
              if (sortA > sortB) return -1;
              return 0
            } else {
              if (sortA < sortB) return -1;
              if (sortA > sortB) return 1;
              return 0
            }
          })
        }

        if (rowsPerPage > 0) {
          this.items = items.slice((page - 1) * rowsPerPage, page * rowsPerPage);
        }
        const search_params = {};
        if (sortBy) search_params.sort = `${sortBy}__${descending ? "desc" : "asc"}`;
        search_params.page = page || 1;
        search_params.per_page = rowsPerPage || 10;
        if (this.search) search_params.search = this.search;

        this.index_fn(search_params).then(res => {
          console.log("Index finishes", res.data.data);
          resolve({
            items: res.data.data,
            total: res.data.total
          })
        }).catch(err => {
          reject(err);
        }).finally(() => {
          this.loading = false;
        });
      });

    }
  },

};