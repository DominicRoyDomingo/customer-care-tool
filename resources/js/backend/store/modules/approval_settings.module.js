import axios from "axios";

import { API_APPROVAL_SETTINGS } from "./../../common/API.service";

function indexRequestType(approval_settings){
  if(approval_settings['data'] == undefined) return approval_settings;
    approval_settings['data'].forEach(function(row, index) {
    row.approval_settings_index = index;
  });
  return approval_settings;
}

// Default State
const getDefaultState = () => {
  return {
    approval_settings: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  approval_settings: (state) => state.approval_settings,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_approval_settings({ commit }, params) {
    try {
      const resp = await axios.get(API_APPROVAL_SETTINGS, { params });
      commit("set_approval_settings", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_approval_settings_name({ commit }, params) {
    try {
      const resp = await axios.get(API_APPROVAL_SETTINGS + `/get-approval-settings-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_approval_settings({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_APPROVAL_SETTINGS, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_approval_settings({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_APPROVAL_SETTINGS + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_approval_settings({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_approval_settings", params);
      resolve(params);
    });
  },

  update_approval_settings({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_approval_settings", params);
      resolve(params);
    });
  },
  remove_from_approval_settings_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_approval_settings_array", params);
      resolve(params);
    });
  },

  delete_approval_settings({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_APPROVAL_SETTINGS, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_approval_settings(context) {
    context.commit('reset_approval_settings')
  },
};

const mutations = {
  set_approval_settings(state, params) {
    state.approval_settings = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_approval_settings(state, params) {
    if(state.approval_settings['data'] == undefined) state.approval_settings.push(params);
  },

  update_approval_settings(state, params) {
    let index = params.index;
    state.approval_settings['data'][index] = params;
  },

  remove_from_approval_settings_array(state, params) {
    let index = params.approval_settings_index;
    state.approval_settings.splice(index, 1);
    state.approval_settings = indexRequestType(state.approval_settings);
  },

  reset_approval_settings(state) {
    state.approval_settings = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
