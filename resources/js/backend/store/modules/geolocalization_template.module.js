import axios from "axios";

import { API_GEOLOCALIZATION_TEMPLATE } from "../../common/API.service";

function indexGeolocalization(geolocalization_template){
  if(geolocalization_template['data'] == undefined) return geolocalization_template;
  geolocalization_template['data'].forEach(function(row, index) {
    row.geolocalization_template_index = index;
  });
  // console.log(brands)
  return geolocalization_template;
}

// Default State
const getDefaultState = () => {
  return {
    geolocalization_templates: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  geolocalization_templates: (state) => state.geolocalization_templates,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_geolocalization_templates({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION_TEMPLATE, { params });
      commit("set_geolocalization_templates", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_geolocalization_template({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION_TEMPLATE + `/template?api_token=${params.api_token}&lang=${params.lang}&place=${params.place}`, { params });
      return resp.data;
    } catch (error) {
      console.log(error);
    }
  },
  
  post_geolocalization_template({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_GEOLOCALIZATION_TEMPLATE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_geolocalization_template({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_geolocalization_template", params);
      resolve(params);
    });
  },

  update_geolocalization_template({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_geolocalization_template", params);
      resolve(params);
    });
  },

  remove_from_geolocalization_template_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_geolocalization_template_array", params);
      resolve(params);
    });
  },

  delete_geolocalization_template({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_GEOLOCALIZATION_TEMPLATE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_geolocalization_templates(context) {
    context.commit('reset_geolocalization_templates')
  },
};

const mutations = {
  set_geolocalization_templates(state, params) {
    state.geolocalization_templates = indexGeolocalization(params);
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_geolocalization(state, params) {
    if(state.geolocalization_templates['data'] == undefined) state.geolocalization_templates.push(params);
  },

  update_geolocalization(state, params) {
    let index = params.index;
    state.geolocalization_templates[index] = params;
  },

  remove_from_geolocalization_array(state, params) {
    let index = params.geolocalization_index;
    state.geolocalization_templates.splice(index, 1);
    state.geolocalization_templates = indexGeolocalization(state.geolocalization_templates);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
