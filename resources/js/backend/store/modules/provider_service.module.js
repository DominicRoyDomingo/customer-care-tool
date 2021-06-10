import axios from "axios";

import { API_PROVIDER_SERVICE } from "./../../common/API.service";

function indexProviderServices(provider_services){
  provider_services.data.forEach(function(row, index) {
    row.provider_service_index = index;
  });
  // console.log(brands)
  return provider_services;
}

// Default State
const getDefaultState = () => {
  return {
    provider_services: [],
    response_status: false,
    provider_id: "",
  };
};

// State
const state = getDefaultState();

const getters = {
  provider_services: (state) => state.provider_services,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_provider_services({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_SERVICE, { params });
      commit("set_provider_services", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_provider_service_items({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_SERVICE + `/get-provider-service-items?api_token=${params.api_token}&id=${params.id}&lang=${params.lang}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_all_provider_services({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_SERVICE + `/get-all-provider-services?api_token=${params.api_token}&id=${params.id}&lang=${params.lang}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_provider_service({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDER_SERVICE, params)
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
        .post(API_PROVIDER_SERVICE + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_provider_service({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_provider_service", params);
      resolve(params);
    });
  },

  update_provider_service({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_provider_service", params);
      resolve(params);
    });
  },

  remove_from_provider_service_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_provider_service_array", params);
      resolve(params);
    });
  },

  delete_provider_service({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROVIDER_SERVICE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  get_provider_id(context,payload) {
    context.commit('set_provider_id', payload)
  },
};

const mutations = {
  set_provider_services(state, params) {
    if(state.provider_services.data == undefined) {
      state.provider_services = indexProviderServices(params);
      return;
    }
    if(indexProviderServices(params).data.length == 0) { 
      state.response_status = false;
      return;
    };

    indexProviderServices(params).data.forEach(function(data){
      state.provider_services.data.push(data);
    });
    state.provider_services = indexProviderServices(state.provider_services)
    state.provider_services.total = params.total
    state.response_status = true;
  },

  set_provider_id(state,payload) {
    state.provider_id = payload
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_provider_service(state, params) {
    if(state.provider_id == params.id) {
      params.data.forEach(data => {
        state.provider_services.push(data);
      });
    }
  },

  update_provider_service(state, params) {
    let index = params.index;
    console.log(params)
    state.provider_services['data'][index] = params;
    console.log(state.provider_services['data']);
    state.provider_services = indexProviderServices(state.provider_services)
  },

  remove_from_provider_service_array(state, params) {
    let index = params.provider_service_index;
    state.provider_services.splice(index, 1);
    state.provider_services = indexProviderServices(state.provider_services);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
