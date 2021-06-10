import axios from "axios";

import { API_DEFAULT_GROUPS } from "./../../common/API.service";

function indexRequestType(default_groups){
  if(default_groups['data'] == undefined) return default_groups;
  default_groups['data'].forEach(function(row, index) {
    row.default_groups_index = index;
  });
  return default_groups;
}

// Default State
const getDefaultState = () => {
  return {
    default_groups: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  default_groups: (state) => state.default_groups,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_default_groups({ commit }, params) {
    try {
      const resp = await axios.get(API_DEFAULT_GROUPS, { params });
      commit("set_default_groups", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_default_groups_name({ commit }, params) {
    try {
      const resp = await axios.get(API_DEFAULT_GROUPS + `/get-default-groups-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_default_groups({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_DEFAULT_GROUPS, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_default_groups({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_DEFAULT_GROUPS + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_default_groups({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_default_groups", params);
      resolve(params);
    });
  },

  update_default_groups({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_default_groups", params);
      resolve(params);
    });
  },
  remove_from_default_groups_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_default_groups_array", params);
      resolve(params);
    });
  },

  delete_default_groups({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_DEFAULT_GROUPS, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_default_groups(context) {
    context.commit('reset_default_groups')
  },
};

const mutations = {
  set_default_groups(state, params) {
    state.default_groups = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_default_groups(state, params) {
    if(state.default_groups['data'] == undefined) state.default_groups.push(params);
  },

  update_default_groups(state, params) {
    let index = params.index;
    state.default_groups['data'][index] = params;
  },

  remove_from_default_groups_array(state, params) {
    let index = params.default_groups_index;
    state.default_groups.splice(index, 1);
    state.default_groups = indexRequestType(state.default_groups);
  },

  reset_default_groups(state) {
    state.default_groups = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
