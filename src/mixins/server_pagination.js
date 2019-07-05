export default  {

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

    search: {
      handler() {
        this.index_details()
            .then(data => {
              this.items = data.items;
              this.total_items = data.total_items;
            })
      },
      deep: true
    }
  },

  methods:{
    index_details() {
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

        this.index_fn({
          sort: `${sortBy}__${descending? "desc" : "asc"}`,
          page: page,
          per_page: rowsPerPage,
          search: this.search
        }).then(res => {
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

    },
  },

};