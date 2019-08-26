/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"
import f_apiCall from "./api_fake"


export const index_materials = () => new Promise((resolve, reject) => {
  apiCall({url: '/materials/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update_material = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/materials/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_material = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/materials/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_material = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/materials/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const index_suppliers = () => new Promise((resolve, reject) => {
  apiCall({url: '/providers/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update_supplier = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/providers/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_supplier = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/providers/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_supplier = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/suppliers/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const index_orders = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/list', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  });
});

export const index_orders_lite = () => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/list_lite', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  });
});

export const update_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const add_operation = (id) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/make_operation', method: 'POST', data: { id: id, operation: "add"} }).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const revert_operation = (id) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/providers/make_operation', method: 'POST', data: { id: id, operation: "revert"} }).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});