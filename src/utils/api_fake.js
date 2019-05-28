/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import mock_users from "./mock-users";
import {AUTH_LOGOUT} from '../store/actions/auth'
import store from "../store"

const mocks = {
      '/auth/login': {'POST': {access_token: 'This-is-a-mocked-token', permissions: 31}},
      'user/me': {'GET': {name: 'doggo', title: 'sir'}},
      'users': {'GET': mock_users },
      'about': {'GET' : {error: 401}},
      '/auth/refresh': {'POST': {access_token: 'new_token'} },
};

const apiCall = ({url, method, ...args}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      console.log(`Mocked '${url}' - ${method || 'GET'}`);
      let result = mocks[url][method || 'GET'];
      if(result.error === 401){
        reject(new Error(result.error));
        store.dispatch(AUTH_LOGOUT);
        return;
      }
      resolve(mocks[url][method || 'GET']);
      console.log('response: ', mocks[url][method || 'GET']);
    } catch (err) {
      console.log("error",err);
      reject(new Error(err));
    }
  }, 1000);
});

export default apiCall