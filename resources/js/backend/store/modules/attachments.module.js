import axios from "axios";

import {
  API_DOCUMENT_CATEGORIES,
  API_DOCUMENT_TYPES,
} from "./../../common/API.service";

const getDefaultState = () => {
  return {
    categories: [],
    types: [],
    response_status: false,
  };
};

const state = getDefaultState();

const getters = {
  categories: (state) => state.categories,
  types: (state) => state.types,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_categories({ commit }, params) {
    try {
      const resp = await axios.get(API_DOCUMENT_CATEGORIES, { params });
      commit("set_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  async get_types({ commit }, params) {
    try {
      const resp = await axios.get(API_DOCUMENT_TYPES, { params });
      commit("set_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  store_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_DOCUMENT_CATEGORIES, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
  delete_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_DOCUMENT_CATEGORIES, { params })
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
  store_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_DOCUMENT_TYPES, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
  delete_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_DOCUMENT_TYPES, { params })
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_categories(state, params) {
    state.categories = params;
  },
  set_types(state, params) {
    state.types = params;
  },
  set_response_status(state, params) {
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
