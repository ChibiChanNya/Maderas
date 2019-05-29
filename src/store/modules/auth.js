/* eslint-disable promise/param-names */
/* eslint-disable no-unused-vars */
import {AUTH_REQUEST, AUTH_ERROR, AUTH_SUCCESS, AUTH_LOGOUT, AUTH_REFRESH} from '../actions/auth'
import {USER_REQUEST} from '../actions/user'
import {login, refresh, logout} from '../../api/auth_controller'
import axios from 'axios'
import router from '../../router'

const state = {token: localStorage.getItem('user-token') || '', status: '', hasLoadedOnce: false};

const getters = {
  isAuthenticated: state => !!state.token,
};

const actions = {

  [AUTH_REQUEST]: ({commit, dispatch}, user) => {
    return new Promise((resolve, reject) => {
      commit(AUTH_REQUEST);
      login(user)
          .then(({data}) => {
            localStorage.setItem('user-token', data.access_token);
            // localStorage.setItem('permissions', data.permissions);
            // Here set the header of your ajax library to the token value.
            // example with axios
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
            commit(AUTH_SUCCESS, data);
            dispatch(USER_REQUEST);
            resolve(data)
          })
          .catch(err => {
            commit(AUTH_ERROR, err);
            localStorage.removeItem('user-token');
            reject(err)
          })
    })
  },

  [AUTH_LOGOUT]: ({commit}) => {
    return new Promise((resolve, reject) => {
      logout()
          .then( () => {
            resolve();
          })
          .catch(err => {
            reject(err);
          })
          .finally( () => {
            commit(AUTH_LOGOUT);
            localStorage.removeItem('user-token');
            router.push('/login');
          })
    })
  },

  [AUTH_REFRESH]:
      ({commit, dispatch}, username) => {
        return new Promise((resolve, reject) => {
          refresh(username).then(({data}) => {
            localStorage.setItem('user-token', data.access_token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
            commit(AUTH_SUCCESS, data);
            resolve();
          }).catch(err => {
            dispatch(AUTH_LOGOUT);
            reject(err);
          })
        });
      },

};

const mutations = {
  [AUTH_REQUEST]: (state) => {
    state.status = 'loading'
  },
  [AUTH_SUCCESS]: (state, resp) => {
    state.status = 'success';
    state.token = resp.access_token;
    state.hasLoadedOnce = true;
  },
  [AUTH_ERROR]: (state) => {
    state.status = 'error';
    state.hasLoadedOnce = true;
  },
  [AUTH_LOGOUT]: (state) => {
    state.token = '';
  }
};

export default {
  state,
  getters,
  actions,
  mutations,
}