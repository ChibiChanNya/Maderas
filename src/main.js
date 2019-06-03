import Vue from 'vue'
import './plugins/vuetify'
import App from './App.vue'
import router from './router'
import store from './store/index.js'
import axios from 'axios'
import VueAxios from 'vue-axios'
const moment = require('moment');

require('moment/locale/es');

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
