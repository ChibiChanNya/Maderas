/* eslint-disable promise/param-names */
/* eslint-disable no-unused-vars */
import {AUTH_REQUEST, AUTH_ERROR, AUTH_SUCCESS, AUTH_LOGOUT, AUTH_REFRESH} from '../actions/auth'
import {USER_REQUEST} from '../actions/user'
import apiCall from '../../utils/api_fake'
import axios from 'axios'
import router from '../../router'

const state = {token: localStorage.getItem('user-token') || '', status: '', hasLoadedOnce: false};

const getters = {
  isAuthenticated: state => !!state.token,
  authStatus: state => state.status,
};

const actions = {

  [AUTH_REQUEST]: ({commit, dispatch}, user) => {
    return new Promise((resolve, reject) => {
      commit(AUTH_REQUEST);
      apiCall({
        url: '/auth/login',
        data: {name: user.username, password: user.password, device_ip: "0.0.0.0"},
        method: 'POST'
      })
          .then(resp => {
            localStorage.setItem('user-token', resp.access_token);
            localStorage.setItem('permissions', resp.permissions);
            // Here set the header of your ajax library to the token value.
            // example with axios
            axios.defaults.headers.common['Authorization'] = resp.access_token;
            commit(AUTH_SUCCESS, resp);
            // dispatch(USER_REQUEST);
            resolve(resp)
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
      apiCall({url: '/auth/logout', method: 'POST'});
      commit(AUTH_LOGOUT);
      localStorage.removeItem('user-token');
      localStorage.removeItem('permissions');
      router.push('/login');
      resolve();
    })
  },

  [AUTH_REFRESH]: ({commit, dispatch}) => {
    return new Promise((resolve, reject) => {
      apiCall({url: '/auth/refresh', method: 'POST'}).then(resp => {
        localStorage.setItem('user-token', resp.access_token);
        axios.defaults.headers.common['Authorization'] = resp.access_token;
        commit(AUTH_SUCCESS, resp);
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