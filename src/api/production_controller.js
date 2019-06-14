/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"
import f_apiCall from "./api_fake"


export const index_products = () => new Promise((resolve, reject) => {
  apiCall({url: '/products/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update_product = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/products/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_product = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/products/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_product = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/products/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const index_clients= () => new Promise((resolve, reject) => {
  apiCall({url: '/clients/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update_client = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/clients/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_client = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/clients/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_client = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/clients/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const index_orders = () => new Promise((resolve, reject) => {
  apiCall({url: '/orders/clients/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  });
});
export const update_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/clients/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/clients/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_order = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/orders/clients/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});