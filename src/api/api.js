/* eslint-disable no-console */
/* eslint-disable no-unused-vars */
import axios from 'axios';
import store from '../store'


const apiCall = ({url, method, ...args}) => new Promise((resolve, reject) => {
  try {
    console.log('request: ', {method: method, url: url, body: args.data});
    axios({method: method, url: `${process.env.VUE_APP_SERVER}${url}`, data: args.data}).then(response => {
      console.log('response: ', response.data);
      resolve(response);
    }).catch (({response}) => {
      console.log("Axios error");
      console.log(response);
      if(response.status === 500)
        reject("Error de Servidor, contacta a los desarrolladores");
      else if(response.status === 401){
        reject("No cuentas con los permisos necesarios para realizar esta acción");
      }
      else if(response.status === 400){
        reject("Tu sesión ha expirado");
        store.dispatch("AUTH_LOGOUT");
      }
      else if(response.status === 504){
        reject("Sin respuesta del servidor. Intenta de nuevo más tarde");
      }
      else
        reject(response);
    });
  } catch (err) {
    reject(err);
  }
});

export default apiCall