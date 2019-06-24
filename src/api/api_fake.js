/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import mock_users from "./mock-users";
import mock_orders from "./mock-orders";
import {AUTH_LOGOUT} from '../store/actions/auth'
import store from "../store"

const mocks = {
      '/auth/login': {'POST': {access_token: 'This-is-a-mocked-token', permissions: 31}},
      'user/me': {'GET': {name: 'doggo', title: 'sir'}},
      'users': {'GET': mock_users },
      'about': {'GET' : {error: 401}},
      '/auth/refresh': {'POST': {access_token: 'new_token'} },
      '/auth/logout': {'POST': "ok" },
      '/orders/providers/list' : {'GET': {total_items:mock_orders.length, items: mock_orders}},
};

const fApiCall = ({url, method, args}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      console.log(`Mocked '${url}' - ${method || 'GET'}`);
      if(args) console.log("Args", args);
      let result = mocks[url][method || 'GET'];

      if(result.error === 401){
        reject(new Error(result.error));
        store.dispatch(AUTH_LOGOUT);
        return;
      }
      resolve({data: mocks[url][method || 'GET'] });
      console.log('response: ', mocks[url][method || 'GET']);
    } catch (err) {
      console.log("error",err);
      reject(new Error(err));
    }
  }, 1000);
});

export default fApiCall