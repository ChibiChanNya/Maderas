/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import apiCall from "./api"
import fApiCall from "./api_fake"



export const login = (user) => new Promise((resolve, reject) => {
  apiCall({url: '/auth/login', method: 'POST', data: user}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const refresh = ( username ) => new Promise((resolve, reject) => {
  apiCall({url: '/auth/refresh', method: 'POST', data:{username: username }}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});

export const logout = () => new Promise((resolve, reject) => {
  apiCall({url: '/auth/logout', method: 'POST'}).then(resp => {
    resolve(resp);
  }).catch(err => {
    reject(err);
  })
});
