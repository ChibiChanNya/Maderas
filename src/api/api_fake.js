/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import mock_clients from "./mock-clients";
import mock_orders from "./mock-orders";
import mock_users from "./mock-users";
import mock_products from "./mock-products";
import mock_client_orders from "./mock-client-orders";
import mock_shipments from "./mock-shipments";
import mock_ledger from "./mock-ledger"
import {AUTH_LOGOUT} from '../store/actions/auth'
import store from "../store"

const mocks = {
  '/auth/login': {'POST': {access_token: 'This-is-a-mocked-token', permissions: 31}},
  'user/me': {'GET': {name: 'doggo', title: 'sir'}},
  'users': {'GET': mock_users},
  'about': {'GET': {error: 401}},
  '/auth/refresh': {'POST': {access_token: 'new_token'}},
  '/auth/logout': {'POST': "ok"},
  '/orders/providers/list': {'GET': {total: mock_orders.length, data: mock_orders}},
  '/clients/list': {'GET': mock_clients},
  '/products/list': {'GET': mock_products},
  '/orders/clients/list': {'GET': {total: mock_client_orders.length, data: mock_client_orders}},
  '/orders/shipments/list': {'GET': {total: mock_shipments.length, data: mock_shipments}},
  '/orders/clients/list_lite': {'GET': mock_client_orders},
  '/orders/shipments/list_lite': {'GET': mock_shipments},
  '/ledger/list': {'GET': {total: mock_ledger.length, data: mock_ledger}},


};

const fApiCall = ({url, method, args}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      console.log(`Mocked '${url}' - ${method || 'GET'}`);
      if (args) console.log("Args", args);
      let result = mocks[url][method || 'GET'];

      if (result.error === 401) {
        reject(new Error(result.error));
        store.dispatch(AUTH_LOGOUT);
        return;
      }
      resolve({data: mocks[url][method || 'GET']});
      console.log('response: ', mocks[url][method || 'GET']);
    } catch (err) {
      console.log("error", err);
      reject(new Error(err));
    }
  }, 1000);
});

export default fApiCall