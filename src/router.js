import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import About from './views/About'
import Login from './views/Login'
import store from './store'


Vue.use(Router);

// Blocked logged users from going to /login
const ifNotAuthenticated = (to, from, next) => {
  if (!store.getters.isAuthenticated) {
    next();
    return
  }
  next('/')
};

// Guard againsts users not logged in
const ifAuthenticated = (to, from, next) => {
  if (store.getters.isAuthenticated) {
    next();
    return
  }
  next('/login');
};

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login,
      beforeEnter: ifNotAuthenticated,
      meta: {
        layout: 'Auth'
      }
    },
    {
      path: '/',
      name: 'home',
      beforeEnter: ifAuthenticated, // Using guard before entering the route
      component: Home,
      meta: {
        layout: 'App'
      }
    },
    {
      path: '/about',
      name: 'about',
      component: About,
      beforeEnter: ifAuthenticated,
      meta: {
        layout: 'App'
      }
    },

    {
      path: '/users',
      name: 'users',
      component: () => import('./views/users/UserIndex.vue'),
      beforeEnter: ifAuthenticated,
      meta: {
        layout: 'App'
      }
    },

    {
      path: '*',
      name: 'Error',
      component: () => import('./views/404.vue'),
      meta: {
        layout: 'App' // name of the layout
      }
    }
  ]
})
