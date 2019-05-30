/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import axios from 'axios';

const apiCall = ({url, method, ...args}) => new Promise((resolve, reject) => {
  try {
    console.log('request: ', {method: method, url: url, body: args.data});
    axios({method: method, url: `${process.env.VUE_APP_SERVER}${url}`, data: args.data}).then(response => {
      console.log('response: ', response.data);
      resolve(response);
    }).catch (({response}) => {
      console.log("Axios error");
      console.log(response);
      reject(response);
    });
  } catch (err) {
    reject(err);
  }
});

export default apiCall