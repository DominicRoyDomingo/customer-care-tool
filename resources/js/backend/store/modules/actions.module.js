import axios from "axios";

import { API_ACTIONS } from "../../common/API.service";
import { API_BRAND } from "../../common/API.service";

function indexEngagements(engagement_items){
  engagement_items.forEach(function(row, index) {
    row.engagement_index = index;
  });

  return engagement_items;
}

// Default State
const getDefaultState = () => {
  return {
    activity_items: [],
    activity_status: false,
    engagement_items: [],
    brand_items: [],
    engagement_status: false,
    administrative_items: [],
    administrative_status: false,
  };
};

// State
const state = getDefaultState();

const getters = {
  get_activity_items: (state) => state.activity_items,
  get_engagament_items: (state) => state.engagement_items,
  get_brand_items: (state) => state.brand_items,
  get_administrative_items: (state) => state.administrative_items,
  get_activity_status: (state) => state.activity_status,
  get_engagament_status: (state) => state.engagement_status,
  get_administrative_status: (state) => state.administrative_status,
};

const actions = {
  /**
   *
   * @param {*} param0
   * @param {*} params
   */
  async get_activity({ commit }, params) {
    try {
      const resp = await axios.get(API_ACTIONS + "/activity", { params });
      commit("set_activity", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  /**
   *
   * @param {*} param0
   * @param {*} params
   */
  post_activity({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ACTIONS + "/activity/stored", params)
        .then((resp) => {
          resolve(resp);
          commit("set_activity_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  /**
   *
   * @param {*} param0
   * @param {*} params
   */
  delete_activity({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ACTIONS + "/activity/delete", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_activity_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  async get_engagaments({ commit }, params) {
    try {
      const resp = await axios.get(API_ACTIONS + "/engagements", { params });
      commit("set_engagement", resp.data);
      const brands = await axios.get(API_BRAND, { params });
      commit("set_brands", brands.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ACTIONS + "/engagements/stored", params)
        .then((resp) => {
          resolve(resp);
          commit("set_engangement_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  add_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_engagement", params);
      resolve(params);
    });
  },

  update_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_engagement", params);
      resolve(params);
    });
  },

  remove_from_engagements_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_engagements_array", params);
      resolve(params);
    });
  },

  delete_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ACTIONS + "/engagements/delete", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_engangement_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  // for administrative  =====================================>
  async get_administrative({ commit }, params) {
    try {
      const resp = await axios.get(API_ACTIONS + "/administrative", {
        params,
      });
      commit("set_administrative", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_administrative({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ACTIONS + "/administrative/stored", params)
        .then((resp) => {
          resolve(resp);
          commit("set_administrative_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_administrative({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ACTIONS + "/administrative/delete", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_administrative_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_activity(state, params) {
    state.activity_items = params;
  },
  set_activity_status(state, params) {
    state.activity_status = params;
  },
  set_administrative(state, params) {
    state.administrative_items = params;
  },
  set_engagement(state, params) {
    state.engagement_items = indexEngagements(params);
  },
  set_brands(state, params) {
    state.brand_items = params;
  },
  set_engangement_status(state, params) {
    state.engagement_status = params;
  },
  set_administrative_status(state, params) {
    state.administrative_status = params;
  },
  add_engagement(state, params) {
    params.engagement_index = state.engagement_items.length;
    state.engagement_items.push(params);
  },
  update_engagement(state, params) {
    let index = params.engagement_index;
    console.log("Updating index:" + index);
    console.log(params);
    state.engagement_items[index] = params;
  },
  remove_from_engagements_array(state, params) {
    let index = params.engagement_index;
    state.engagement_items.splice(index, 1);
    state.engagement_items = indexProfiles(params);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
