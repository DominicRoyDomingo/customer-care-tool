import axios from "axios";

import { API_SERVICES_EXCLUSIVE } from "./../../common/API.service";

function indexServicesExclusives(servicesExclusive){
  if(servicesExclusive['data'] == undefined) return servicesExclusive;
  servicesExclusive['data'].forEach(function(row, index) {
    row.services_exclusive_index = index;
  });
  // console.log(brands)
  return servicesExclusive;
}

// Default State
const getDefaultState = () => {
  return {
    services_exclusives: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  services_exclusives: (state) => state.services_exclusives,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_services_exclusives({ commit }, params) {
    try {
      const resp = await axios.get(API_SERVICES_EXCLUSIVE, { params });
      commit("set_services_exclusives", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  post_services_exclusive({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_SERVICES_EXCLUSIVE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  update_services_exclusive({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_services_exclusive", params);
      resolve(params);
    });
  },

  remove_from_services_exclusive_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_services_exclusive_array", params);
      resolve(params);
    });
  },

  delete_services_exclusive({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_SERVICES_EXCLUSIVE, { params })
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
  set_services_exclusives(state, params) {
    state.services_exclusives = indexServicesExclusives(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_service(state, params) {
    if(state.services_exclusives['data'] == undefined) state.services_exclusives.push(params);
  },

  update_services_exclusive(state, params) {
    let index = params.index;
    state.services_exclusives['data'][index] = params;
  },

  remove_from_services_exclusive_array(state, params) {
    let index = params.service_index;
    state.services_exclusives.splice(index, 1);
    state.services_exclusives = indexServicesExclusives(state.services_exclusives);
  },

  reset_services_exclusives(state) {
    state.services_exclusives = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
