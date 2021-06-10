import axios from "axios";

import { API_COLOR_CODING } from "./../../common/API.service";

function indexRequestType(color_coding){
  if(color_coding['data'] == undefined) return color_coding;
  color_coding['data'].forEach(function(row, index) {
    row.color_coding = index;
  });
  return color_coding;
}

// Default State
const getDefaultState = () => {
  return {
    color_coding: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  color_coding: (state) => state.color_coding,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_color_coding({ commit }, params) {
    try {
      const resp = await axios.get(API_COLOR_CODING, { params });
      commit("set_color_coding", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_color_coding_name({ commit }, params) {
    try {
      const resp = await axios.get(API_COLOR_CODING + `/get-color-coding-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_color_coding({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_COLOR_CODING, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_color_coding({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_COLOR_CODING + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_color_coding({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_color_coding", params);
      resolve(params);
    });
  },

  update_color_coding({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_color_coding", params);
      resolve(params);
    });
  },
  remove_from_color_coding_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_color_coding_array", params);
      resolve(params);
    });
  },

  delete_color_coding({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_COLOR_CODING, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_color_coding(context) {
    context.commit('reset_color_coding')
  },
};

const mutations = {
  set_color_coding(state, params) {
    state.color_coding = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_color_coding(state, params) {
    if(state.color_coding['data'] == undefined) state.color_coding.push(params);
  },

  update_color_coding(state, params) {
    let index = params.index;
    state.color_coding['data'][index] = params;
  },

  remove_from_color_coding_array(state, params) {
    let index = params.color_coding_index;
    state.color_coding.splice(index, 1);
    state.color_coding = indexRequestType(state.color_coding);
  },

  reset_color_coding(state) {
    state.color_coding = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
