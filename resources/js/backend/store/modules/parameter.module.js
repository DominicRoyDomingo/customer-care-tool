import axios from "axios";

import { API_PARAMETERS } from "./../../common/API.service";

function indexParameters(parameters){
  if(parameters['data'] == undefined) return parameters;
  parameters['data'].forEach(function(row, index) {
    row.parameter_index = index;
  });
  // console.log(brands)
  return parameters;
}

// Default State
const getDefaultState = () => {
  return {
    parameters: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  parameters: (state) => state.parameters,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_parameters({ commit }, params) {
    try {
      const resp = await axios.get(API_PARAMETERS, { params });
      commit("set_parameters", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_parameter_name({ commit }, params) {
    try {
      const resp = await axios.get(API_PARAMETERS + `/get-parameter-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_parameter({ commit }, params) {
    console.log(params.get('name'))
    return new Promise((resolve, reject) => {
      axios
        .post(API_PARAMETERS, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PARAMETERS + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_parameter({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_parameter", params);
      resolve(params);
    });
  },

  update_parameter({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_parameter", params);
      resolve(params);
    });
  },

  remove_from_parameter_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_parameter_array", params);
      resolve(params);
    });
  },

  delete_parameter({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PARAMETERS, { params })
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
  set_parameters(state, params) {
    state.parameters = indexParameters(params);
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_parameter(state, params) {
    if(state.parameters['data'] == undefined) state.parameters.push(params);
  },

  update_parameter(state, params) {
    let index = params.index;
    state.parameters['data'][index] = params;
  },

  remove_from_parameter_array(state, params) {
    let index = params.parameter_index;
    state.parameters.splice(index, 1);
    state.parameters = indexParameters(state.parameters);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
