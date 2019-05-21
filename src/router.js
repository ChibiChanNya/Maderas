import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import About from './views/About'
import Login from './views/Login'


Vue.use(Router);

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      meta: {
        layout: 'App'
      }
    },
    {
      path: '/about',
      name: 'about',
      component: About,
      meta: {
        layout: 'App'
      }
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        layout: 'Auth'
      }
    },
    {
      path: '*',
      name: 'Error',
      // component: () => import('@/pages/Error') (Optional)
      meta: {
        layout: 'Error' // name of the layout
      }
    }
  ]
})
