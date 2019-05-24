/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import mock_users from "./mock-users";

const mocks = {
      'auth': {'POST': {token: 'This-is-a-mocked-token', permissions: 31}},
      'user/me': {'GET': {name: 'doggo', title: 'sir'}},
      'users': {'GET': mock_users },
    };

const apiCall = ({url, method, ...args}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      resolve(mocks[url][method || 'GET']);
      console.log(`Mocked '${url}' - ${method || 'GET'}`);
      console.log('response: ', mocks[url][method || 'GET']);
    } catch (err) {
      reject(new Error(err));
    }
  }, 1000);
});

export default apiCall