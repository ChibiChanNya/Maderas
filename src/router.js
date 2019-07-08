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
  this.$store.commit('setSnack', {text: "Debes iniciar sesión para continuar", color: 'warning'});
};

//Check for permissions
const hasPermissions = (to, from, next) => {
  if (store.getters.isAuthenticated) {
    if ((parseInt(store.getters.getPermissions, 2) & to.meta.permission) > 0) {
      next();
    } else {
      next(false);
      this.$store.commit('setSnack', {text: "No tienes permisos para acceder a este módulo", color: 'error'});
    }
  } else {
    next('login');
    this.$store.commit('setSnack', {text: "Debes iniciar sesión para continuar", color: 'warning'});
  }
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
      component: () => import('./views/users/Index.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 16,
      }
    },

    {
      path: '/materials/inventory',
      name: 'materials-inventory',
      component: () => import('./views/materials/Inventory.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 4,
      }
    },
    {
      path: '/materials/suppliers',
      name: 'materials-suppliers',
      component: () => import('./views/materials/Suppliers.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 4,
      }
    },

    {
      path: '/materials/orders',
      name: 'materials-orders',
      component: () => import('./views/materials/Orders.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 4,
      }
    },

    {
      path: '/production/inventory',
      name: 'production-inventory',
      component: () => import('./views/production/Inventory.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 2,
      }
    },
    {
      path: '/production/clients',
      name: 'production-clients',
      component: () => import('./views/production/Clients.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 2,
      }
    },

    {
      path: '/production/orders',
      name: 'production-orders',
      component: () => import('./views/production/Orders.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 2,
      }
    },

    {
      path: '/production/shipments',
      name: 'production-shipments',
      component: () => import('./views/production/Shipments.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 2,
      }
    },

    {
      path: '/documents/income',
      name: 'documents-income',
      component: () => import('./views/documents/Ledger.vue'),
      beforeEnter: hasPermissions,
      meta: {
        layout: 'App',
        permission: 8,
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