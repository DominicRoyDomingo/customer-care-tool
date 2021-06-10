import axios from "axios";

import { API_TEMPLATE } from "./../../common/API.service";
import { template } from "lodash";

function indexTemplates(templates){
  /*
    let double_template = JSON.parse(JSON.stringify(templates));
    templates = templates.concat(double_template);
  */
 
  templates.forEach(function(row, index) {
    row.template_index = index;
  });

  return templates;
}

// Default State
const getDefaultState = () => {
  return {
    templates: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  templates: (state) => state.templates,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_templates({ commit }, params) {
    console.log("API_TEMPLATE");
    console.log(API_TEMPLATE);
    try {
      const resp = await axios.get(API_TEMPLATE, { params });
      commit("set_templates", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_template({ commit }, params) {
    try {
      const resp = await axios.get(API_TEMPLATE + "/" + params.id, { params });
      commit("set_templates", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_template({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_TEMPLATE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_template({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_template", params);
      resolve(params);
    });
  },

  update_template({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_template", params);
      resolve(params);
    });
  },

  remove_from_templates_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_templates_array", params);
      resolve(params);
    });
  },

  delete_template({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_TEMPLATE, { params })
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
  set_templates(state, params) {
    state.templates = indexTemplates(params);
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_template(state, params) {
    params.template_index = state.templates.length;
    state.templates.push(params);
  },

  update_template(state, params) {
    let index = params.template_index;
    state.templates[index] = params;
  },

  remove_from_templates_array(state, params) {
    let index = params.template_index;
    state.templates.splice(index, 1);
    state.templates = indexTemplates(state.templates);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
