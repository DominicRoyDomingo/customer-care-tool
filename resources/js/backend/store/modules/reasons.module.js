import axios from "axios";

import { API_REASONS } from "./../../common/API.service";

function indexRequestType(reasons){
  if(reasons['data'] == undefined) return reasons;
  reasons['data'].forEach(function(row, index) {
    row.reasons = index;
  });
  return reasons;
}

// Default State
const getDefaultState = () => {
  return {
    reasons: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  reasons: (state) => state.reasons,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_reasons({ commit }, params) {
    try {
      const resp = await axios.get(API_REASONS, { params });
      commit("set_reasons", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_reasons_name({ commit }, params) {
    try {
      const resp = await axios.get(API_REASONS + `/get-reasons-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_reasons({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_REASONS, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_reasons({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_REASONS + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_reasons({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_reasons", params);
      resolve(params);
    });
  },

  update_reasons({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_reasons", params);
      resolve(params);
    });
  },
  remove_from_reasons_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_reasons_array", params);
      resolve(params);
    });
  },

  delete_reasons({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_REASONS, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_reasons(context) {
    context.commit('reset_reasons')
  },
};

const mutations = {
  set_reasons(state, params) {
    state.reasons = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_reasons(state, params) {
    if(state.reasons['data'] == undefined) state.reasons.push(params);
  },

  update_reasons(state, params) {
    let index = params.index;
    state.reasons['data'][index] = params;
  },

  remove_from_reasons_array(state, params) {
    let index = params.reasons_index;
    state.reasons.splice(index, 1);
    state.reasons = indexRequestType(state.reasons);
  },

  reset_reasons(state) {
    state.reasons = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
