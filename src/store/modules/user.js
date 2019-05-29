import {USER_REQUEST, USER_ERROR, USER_SUCCESS} from '../actions/user'
import Vue from 'vue'
import {AUTH_LOGOUT} from '../actions/auth'
import {me} from '../../api/users_controller'

const state = {
  status: '', profile: {
    username: localStorage.getItem('username') || '',
    permissions: localStorage.getItem('permissions') || '',
    full_name: localStorage.getItem('full_name') || '',
  },
};

const getters = {
  isProfileLoaded: state => !!state.profile.name,
  getPermissions: state => state.profile.permissions,
  getUsername: state => state.profile.username,
  getName: state => state.profile.full_name,
};

const actions = {
  [USER_REQUEST]: ({commit, dispatch}) => {
    commit(USER_REQUEST);
    me()
        .then(resp => {
          localStorage.setItem('username', resp.data.username);
          localStorage.setItem('name', resp.data.full_name);
          localStorage.setItem('permissions', resp.data.permissions);
          commit(USER_SUCCESS, resp.data);

        })
        .catch(() => {
          commit(USER_ERROR);
          // if resp is unauthorized, logout, to
          dispatch(AUTH_LOGOUT)
        })
  },
};

const mutations = {
  [USER_REQUEST]: (state) => {
    state.status = 'loading'
  },
  [USER_SUCCESS]: (state, resp) => {
    state.status = 'success';
    Vue.set(state, 'profile', resp)
  },
  [USER_ERROR]: (state) => {
    state.status = 'error';
  },
  [AUTH_LOGOUT]: (state) => {
    state.profile = {};
  }
}

export default {
  state,
  getters,
  actions,
  mutations,
}