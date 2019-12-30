/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"


export const get_inventory_dash = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/dashboard/graph_one', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_invoices_dash = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/dashboard/graph_two', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_income_dash = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/dashboard/graph_three', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const get_providers_dash = (args) => new Promise((resolve, reject) => {
  apiCall({url: '/dashboard/graph_four', method: 'GET', params: args}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});



