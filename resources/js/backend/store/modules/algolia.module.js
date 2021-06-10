import axios from "axios";

import { API_ALGOLIA } from "../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_algolia_summary({ commit }, params) {
    try {
      const resp = await axios.get(`${API_ALGOLIA}/summary`, { params });
      //≈
      return resp.data
    } catch (error) {
    }
  },

  async check_permission({ commit }, params) {
    try {
      const resp = await axios.get(`${API_ALGOLIA}/check-permission`, { params });
      //≈
      return resp.data
    } catch (error) {
    }
  },

  // post_publish_item_sync({ commit }, params) {
  //   return new Promise((resolve, reject) => {
  //     axios
  //       .post(API_ARTICLES_ALGOLIA + `/sync`, params)
  //       .then((resp) => {
  //         resolve(resp);
  //         return resp.data
  //       })
  //       .catch((error) => reject(error));
  //   });
  // },

  post_courses_sync({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(`/api/course/sync`, params)
        .then((resp) => {
          resolve(resp);
          return resp.data
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
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
