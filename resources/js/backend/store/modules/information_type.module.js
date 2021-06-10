import axios from "axios";

import { API_INFORMATION_TYPE } from "./../../common/API.service";

function indexInformationTypes(it){
  if(it['data'] == undefined) return it;
  it['data'].forEach(function(row, index) {
    row.it_index = index;
  });
  // console.log(brands)
  return it;
}

// Default State
const getDefaultState = () => {
  return {
    information_types: [],
    information_type_all: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  information_types: (state) => state.information_types,
  information_type_all: (state) => state.information_type_all,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_information_types({ commit }, params) {
    try {
      const resp = await axios.get(API_INFORMATION_TYPE, { params });
      commit("set_information_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_information_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_INFORMATION_TYPE + `/get-information-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_information_type_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_INFORMATION_TYPE + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_all_information_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_information_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_INFORMATION_TYPE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_information_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_information_type", params);
      resolve(params);
    });
  },

  update_information_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_information_type", params);
      resolve(params);
    });
  },

  remove_from_information_type_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_information_type_array", params);
      resolve(params);
    });
  },

  delete_information_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_INFORMATION_TYPE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_information_types(context) {
    context.commit('reset_information_types')
  },
};

const mutations = {
  set_information_types(state, params) {
    state.information_types = indexInformationTypes(params);
  },

  set_all_information_type(state, params) {
    state.information_type_all = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_information_type(state, params) {
    if(state.information_types['data'] == undefined) state.information_types.push(params);
  },

  update_information_type(state, params) {
    let index = params.index;
    state.information_types['data'][index] = params;
  },

  remove_from_information_type_array(state, params) {
    let index = params.information_type_index;
    state.information_types.splice(index, 1);
    state.information_types = indexInformationTypes(state.information_types);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
