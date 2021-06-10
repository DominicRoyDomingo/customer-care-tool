import axios from "axios";

import { API_REQUEST_TYPE } from "./../../common/API.service";

function indexRequestType(request_type){
  if(request_type['data'] == undefined) return request_type;
  request_type['data'].forEach(function(row, index) {
    row.request_type_index = index;
  });
  return request_type;
}

// Default State
const getDefaultState = () => {
  return {
    request_type: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  request_type: (state) => state.request_type,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_request_type({ commit }, params) {
    try {
      const resp = await axios.get(API_REQUEST_TYPE, { params });
      commit("set_request_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_request_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_REQUEST_TYPE + `/get-request-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_request_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_REQUEST_TYPE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_request_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_REQUEST_TYPE + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_request_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_request_type", params);
      resolve(params);
    });
  },

  update_request_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_request_type", params);
      resolve(params);
    });
  },
  remove_from_request_type_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_request_type_array", params);
      resolve(params);
    });
  },

  delete_request_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_REQUEST_TYPE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_request_type(context) {
    context.commit('reset_request_type')
  },
};

const mutations = {
  set_request_type(state, params) {
    state.request_type = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_request_type(state, params) {
    if(state.request_type['data'] == undefined) state.request_type.push(params);
  },

  update_request_type(state, params) {
    let index = params.index;
    state.request_type['data'][index] = params;
  },

  remove_from_request_type_array(state, params) {
    let index = params.request_type_index;
    state.request_type.splice(index, 1);
    state.request_type = indexRequestType(state.request_type);
  },

  reset_request_type(state) {
    state.request_type = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
