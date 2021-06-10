import axios from "axios";

import { API_CONTACT_TYPES } from "./../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    contact_types: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  contact_types: (state) => state.contact_types,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_contact_types({ commit }, params) {
    try {
      const resp = await axios.get(API_CONTACT_TYPES, { params });
      commit("set_contact_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_contact_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CONTACT_TYPES, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
  
  delete_contact_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_CONTACT_TYPES, { params })
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
  set_contact_types(state, params) {
    state.contact_types = params;
  },
  set_response_status(state, params) {
    console.log("Setting response status");
    console.log(params);
    state.response_status = params;
  },
};

export default {
    namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
