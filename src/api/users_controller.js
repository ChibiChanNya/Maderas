/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"



export const me = (user) => new Promise((resolve, reject) => {
  apiCall({url: '/user/me', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const index = () => new Promise((resolve, reject) => {
  apiCall({url: '/user/list', method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const update = (user) => new Promise((resolve, reject) => {
  apiCall({url: '/user/update', method: 'POST', data: user}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const create = (user) => new Promise((resolve, reject) => {
  apiCall({url: '/user/register', method: 'POST', data: user}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const remove = (user) => new Promise((resolve, reject) => {
  apiCall({url: '/user/delete', method: 'POST', data: user}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const user_log = ({id}) => new Promise((resolve, reject) => {
  apiCall({url: `/user/user_log/${id}`, method: 'GET'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});
