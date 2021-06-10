import axios from "axios";

import { API_JOB } from "../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    job_items: [],
    job_response_status: false,
    job_categories: [],
    filtered_job_items: [],

    //For changing professions, and descriptions items
    loaded_categories: [],
    loaded_professions: [],
    loaded_descriptions: [],
  };
};

// State
const state = getDefaultState();

const getters = {
  get_job_items: (state) => state.job_items,
  get_job_status: (state) => state.job_response_status,
  get_job_categories: (state) => state.job_categories,
  get_filtered_job_items: (state) => state.job_categories,
};

const actions = {
  async get_jobs({ commit }, params) {
    try {
      const resp = await axios.get(API_JOB + "", { params });
      commit("set_job_items", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_job_categories({ commit }, params) {
    try {
      const resp = await axios.get(API_JOB + "/get_categories", { params });
      commit("set_job_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_filtered_jobs({ commit }, params) {
    try {
      const resp = await axios.get(API_JOB + "/get_filtered", { params });
      commit("set_filtered_job_items", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_filtered_job_professions({ commit }, params) {
    try {
      const resp = await axios.get(API_JOB + "/get_filtered_job_professions", { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  post_job_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_JOB + "/post_job_category", params)
        .then((resp) => {
          resolve(resp);
          commit("set_job_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_job_profession({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_JOB + "/post_job_profession", params)
        .then((resp) => {
          resolve(resp);
          // commit("set_job_items", resp.data);
          commit("set_job_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_job_description({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_JOB + "/post_job_description", params)
        .then((resp) => {
          resolve(resp);
          commit("set_job_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_job_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_JOB + "/delete_job_category", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_job_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_job_profession({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_JOB + "/delete_job_profession", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_job_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_job_description({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_JOB + "/delete_job_description", { params })
        .then((resp) => {
          resolve(resp);
          // commit("set_job_items", resp.data);
          commit("set_job_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_job_items(state, params) {
    state.job_items = params;
  },
  set_job_categories(state, params) {
    state.job_categories = params;
  },
  set_job_status(state, params) {
    state.job_response_status = params;
  },
  set_filtered_job_items(state, params) {
    state.filtered_job_items = params;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
