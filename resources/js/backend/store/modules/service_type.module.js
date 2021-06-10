import axios from "axios";

import { API_SERVICE_TYPE } from "./../../common/API.service";

function indexServiceType(serviceTypes){
  if(serviceTypes['data'] == undefined) return serviceTypes;
  serviceTypes['data'].forEach(function(row, index) {
    row.service_type_index = index;
  });
  // console.log(brands)
  return serviceTypes;
}

// Default State
const getDefaultState = () => {
  return {
    service_types: [],
    service_type_all: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  service_types: (state) => state.service_types,
  service_type_all: (state) => state.service_type_all,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_service_types({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICE_TYPE, { params });
      commit("set_service_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_service_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICE_TYPE + `/get-service-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_service_type_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_SERVICE_TYPE + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_all_service_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_service_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_SERVICE_TYPE, params)
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
        .post(API_SERVICE_TYPE + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_service_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_service_type", params);
      resolve(params);
    });
  },

  update_service_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_service_type", params);
      resolve(params);
    });
  },

  remove_from_service_type_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_service_type_array", params);
      resolve(params);
    });
  },

  delete_service_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_SERVICE_TYPE, { params })
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
  set_service_types(state, params) {
    state.service_types = indexServiceType(params);
  },

  set_all_service_type(state, params) {
    state.service_type_all = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_service_type(state, params) {
    state.service_types['data'].push(params);
  },

  update_service_type(state, params) {
    let index = params.index;
    state.service_types['data'][index] = params;
  },

  remove_from_service_type_array(state, params) {
    let index = params.service_type_index;
    state.service_types.splice(index, 1);
    state.service_types = indexServiceType(state.service_types);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
