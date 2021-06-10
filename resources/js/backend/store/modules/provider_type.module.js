import axios from "axios";

import { API_PROVIDER_TYPE } from "./../../common/API.service";

// function indexProviderTypes(provider_types){
//   provider_types.forEach(function(row, index) {
//     row.provider_type_index = index;
//   });
//   // console.log(brands)
//   return provider_types;
// }

function indexProviderTypes(provider_types){
  if(provider_types['data'] == undefined) return serviceTypes;
  provider_types['data'].forEach(function(row, index) {
    row.provider_types_index = index;
  });
  // console.log(brands)
  return provider_types;
}

// Default State
const getDefaultState = () => {
  return {
    provider_types: [],
    provider_type_all: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  provider_types: (state) => state.provider_types,
  provider_type_all: (state) => state.provider_type_all,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_provider_types({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_TYPE, { params });
      commit("set_provider_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_provider_type_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_PROVIDER_TYPE + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_all_provider_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_provider_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_TYPE + `/get-provider-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_provider_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDER_TYPE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDER_TYPE + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_provider_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_provider_type", params);
      resolve(params);
    });
  },

  update_provider_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_provider_type", params);
      resolve(params);
    });
  },

  remove_from_provider_type_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_provider_type_array", params);
      resolve(params);
    });
  },

  delete_provider_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROVIDER_TYPE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_provider_types(state, params) {
    state.provider_types = indexProviderTypes(params);
  },

  set_all_provider_type(state, params) {
    state.provider_type_all = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_provider_type(state, params) {
    state.provider_types['data'].push(params);
  },

  update_provider_type(state, params) {
    let index = params.index;
    state.provider_types['data'][index] = params;
  },

  remove_from_provider_type_array(state, params) {
    let index = params.provider_type_index;
    state.provider_types.splice(index, 1);
    state.provider_types = indexProviderTypes(state.provider_types);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
