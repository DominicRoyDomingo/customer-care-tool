import axios from "axios";

import { API_PLATFORMS } from "../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    platform_items: [],
    platform_items_status: false,
    platform_types_items: [],
    platform_types_status: false,
  };
};

// State
const state = getDefaultState();

const getters = {
  get_platform_items: (state) => state.platform_items,

  get_platform_types: (state) => state.platform_types_items,

  get_platform_item_status: (state) => state.platform_items_status,

  get_platform_types_status: (state) => state.platform_types_status,
};

const actions = {
  async get_platform_items({ commit }, params) {
    try {
      const resp = await axios.get(API_PLATFORMS + "/items", { params });
      commit("set_platform_items", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_platform_item({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PLATFORMS + "/items", params)
        .then((resp) => {
          resolve(resp);
          commit("set_platform_item_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_platform_item({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PLATFORMS + "/items", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_platform_item_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  // for platform_types  =====================================>
  async get_platform_type({ commit }, params) {
    try {
      const resp = await axios.get(API_PLATFORMS + "/types", {
        params,
      });
      commit("set_platform_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_platform_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PLATFORMS + "/types", params)
        .then((resp) => {
          resolve(resp);
          commit("set_platform_types_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_platform_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PLATFORMS + "/types", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_platform_types_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_platform_types(state, params) {
    state.platform_types_items = params;
  },
  set_platform_items(state, params) {
    state.platform_items = params;
  },
  set_platform_item_status(state, params) {
    state.platform_items_status = params;
  },
  set_platform_types_status(state, params) {
    state.platform_types_status = params;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
