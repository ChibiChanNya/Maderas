/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"


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

export const index_providers = () => new Promise((resolve, reject) => {
  apiCall({url: '/providers/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});