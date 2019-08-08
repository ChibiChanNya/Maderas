import Vue from 'vue'
import './plugins/vuetify'
import App from './App.vue'
import router from './router'
import store from './store/index.js'
import axios from 'axios'
import VueAxios from 'vue-axios'
const moment = require('moment');
require('moment/locale/es');
import VuetifyConfirm from 'vuetify-confirm'
import Vue2Filters from 'vue2-filters'
import VuejsDialog from 'vuejs-dialog';
import VuejsDialogMixin from 'vuejs-dialog/dist/vuejs-dialog-mixin.min.js'; // only needed in custom components

// include the default style
import 'vuejs-dialog/dist/vuejs-dialog.min.css';

Vue.use(VuejsDialog, {
  html: false,
  loader: true,
  reverse: true,
  verification: "BORRAR",
  verificationHelp: 'Escribe "[+:verification]" en el recuadro para continuar',
  okText: 'Aceptar',
  cancelText: 'Cancelar',
  animation: 'fade'
});
Vue.use(Vue2Filters);

Vue.use(VuetifyConfirm, {
  buttonTrueText: 'Confirmar',
  buttonFalseText: 'Cancelar',
  color: 'error',
  icon: 'warning',
  title: 'Advertencia',
  property: '$confirm'
});

Vue.use(require('vue-moment'), {
  moment
});


Vue.use(VueAxios, axios);

Vue.config.productionTip = false;


const token = localStorage.getItem('user-token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app');
