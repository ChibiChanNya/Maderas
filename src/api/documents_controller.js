/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"
import f_apiCall from "./api_fake"


export const index_ledger = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/ledger/list', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update_ledger = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/ledger/update', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_ledger = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/ledger/create', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove_ledger = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/ledger/delete', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_payment_methods = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/payment_methods', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});


export const index_cfdi = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/list', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_payment_forms = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/payment_forms', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_cfdi_uses = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/cfdi_uses', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create_cfdi = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/create_cfdi', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const send_cfdi = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/send', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const cancel_cfdi = (item) => new Promise((resolve, reject) => {
  apiCall({url: '/invoices/cancel', method: 'POST', data: item}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});