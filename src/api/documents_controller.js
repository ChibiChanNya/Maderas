/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"
import f_apiCall from "./api_fake"


export const index_ledger = () => new Promise((resolve, reject) => {
  f_apiCall({url: '/ledger/list', method: 'GET'}).then(resp => {
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
