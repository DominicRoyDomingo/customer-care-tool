import axios from "axios";

import { API_SERVICES } from "./../../common/API.service";

function indexServices(services){
  if(services['data'] == undefined) return services;
  services['data'].forEach(function(row, index) {
    row.service_index = index;
  });
  // console.log(brands)
  return services;
}

// Default State
const getDefaultState = () => {
  return {
    services: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  services: (state) => state.services,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_services({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICES, { params });
      commit("set_services", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_service_name({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICES + `/get-service-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }

  },
  async check_if_has_link_or_article({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICES + `/check-link-or-article?api_token=${params.api_token}&id=${params.id}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async check_medical_type({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICES + `/check-medical-type?api_token=${params.get('api_token')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_service({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_SERVICES, params)
        .then((resp) => {
          resolve(resp);
          if(resp == false) {
            return resp;
          }
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_SERVICES + `/link-brand`, params)
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
        .post(API_SERVICES + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_service({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_service", params);
      resolve(params);
    });
  },

  update_service({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_service", params);
      resolve(params);
    });
  },

  remove_from_service_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_service_array", params);
      resolve(params);
    });
  },

  delete_service({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_SERVICES, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_services(context) {
    context.commit('reset_services')
  },
};

const mutations = {
  set_services(state, params) {
    state.services = indexServices(params);
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_service(state, params) {
    if(state.services['data'] == undefined) state.services.push(params);
  },

  update_service(state, params) {
    let index = params.index;
    console.log(params)
    state.services['data'][index] = params;
  },

  remove_from_service_array(state, params) {
    let index = params.service_index;
    state.services.splice(index, 1);
    state.services = indexServices(state.services);
  },

  reset_services(state) {
    state.services = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
