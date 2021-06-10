import axios from "axios";

import { API_PUBLISH_CONTENT } from "../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    publish_content: [],
    response_item: '',
  };
};

// State
const state = getDefaultState();

const getters = {
  get_response_item: (state) => state.response_item,
};

const actions = {
  post_publishContent({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PUBLISH_CONTENT, params)
        .then((resp) => {
          resolve(resp);
          commit('set_response_item', resp.data)
        })
        .catch((error) => reject(error));
    });
  },

  autosave_publishContent({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(`${API_PUBLISH_CONTENT}/autosave`, params)
        .then((resp) => {
          resolve(resp);
          commit('set_response_item', resp.data)
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_response_item(state, params) {
    state.response_item = params;
 },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
