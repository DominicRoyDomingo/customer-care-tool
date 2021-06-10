import axios from "axios";

import { API_PHYSICAL_CODE_TYPE } from "./../../common/API.service";

function indexPhysicalCodeTypes(pct){
  if(pct['data'] == undefined) return pct;
  pct['data'].forEach(function(row, index) {
    row.pct_index = index;
  });
  // console.log(brands)
  return pct;
}

// Default State
const getDefaultState = () => {
  return {
    physical_code_types: [],
    physical_code_type_all: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  physical_code_types: (state) => state.physical_code_types,
  physical_code_type_all: (state) => state.physical_code_type_all,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_physical_code_types({ commit }, params) {
    try {
      const resp = await axios.get(API_PHYSICAL_CODE_TYPE, { params });
      commit("set_physical_code_types", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_physical_code_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_PHYSICAL_CODE_TYPE + `/get-physical-code-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_physical_code_type_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_PHYSICAL_CODE_TYPE + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_all_physical_code_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_physical_code_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PHYSICAL_CODE_TYPE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_physical_code_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_physical_code_type", params);
      resolve(params);
    });
  },

  update_physical_code_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_physical_code_type", params);
      resolve(params);
    });
  },

  remove_from_physical_code_type_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_physical_code_type_array", params);
      resolve(params);
    });
  },

  delete_physical_code_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PHYSICAL_CODE_TYPE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_physical_code_types(context) {
    context.commit('reset_physical_code_types')
  },
};

const mutations = {
  set_physical_code_types(state, params) {
    state.physical_code_types = indexPhysicalCodeTypes(params);
  },

  set_all_physical_code_type(state, params) {
    state.physical_code_type_all = params;
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_physical_code_type(state, params) {
    if(state.physical_code_types['data'] == undefined) state.physical_code_types.push(params);
  },

  update_physical_code_type(state, params) {
    let index = params.index;
    state.physical_code_types['data'][index] = params;
  },

  remove_from_physical_code_type_array(state, params) {
    let index = params.physical_code_type_index;
    state.physical_code_types.splice(index, 1);
    state.physical_code_types = indexPhysicalCodeTypes(state.physical_code_types);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
