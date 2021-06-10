import axios from "axios";

import { API_ALGOLIA_CLASS } from "./../../common/API.service";

function indexAlgoliaClasses(algoliaClass){
  if(algoliaClass['data'] == undefined) return algoliaClass;
  algoliaClass['data'].forEach(function(row, index) {
    row.algolia_class_index = index;
  });
  // console.log(brands)
  return algoliaClass;
  algolia_classes.forEach(function(row, index) {
    row.algolia_class_index = index;
  });
  // console.log(brands)
  return algolia_classes;
}

// Default State
const getDefaultState = () => {
  return {
    algolia_classes: [],
    algolia_class_all: [],
    algolia_class: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  algolia_classes: (state) => state.algolia_classes,
  algolia_class_all: (state) => state.algolia_class_all,
  get_response_status: (state) => state.response_status,
  algolia_class: (state) => state.algolia_class,
};

const actions = {
  async get_algolia_classes({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_CLASS, { params });
      commit("set_algolia_classes", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_algolia_class_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_ALGOLIA_CLASS + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_algolia_class_all", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_algolia_class_name({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_CLASS + `/get-provider-group-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_algolia_class({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_CLASS + `/get-provider-group?api_token=${params.api_token}&id=${params.id}&lang=${params.lang}&brandId=${params.brand_id}`, { params });
      commit("set_algolia_class", resp.data);
    } catch (error) {
    }
  },
  
  post_algolia_class({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ALGOLIA_CLASS, params)
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
        .post(API_ALGOLIA_CLASS + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_algolia_class({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_algolia_class", params);
      resolve(params);
    });
  },

  update_algolia_class({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_algolia_class", params);
      resolve(params);
    });
  },

  remove_from_algolia_class_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_algolia_class_array", params);
      resolve(params);
    });
  },

  delete_algolia_class({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ALGOLIA_CLASS, { params })
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
  set_algolia_classes(state, params) {
    state.algolia_classes = indexAlgoliaClasses(params);
  },

  set_algolia_class_all(state, params) {
    state.algolia_class_all = params;
  },

  set_algolia_class(state, params) {
    state.algolia_class = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_algolia_class(state, params) {
    state.algolia_classes.push(params);
  },

  update_algolia_class(state, params) {
    let index = params.index;
    state.algolia_classes['data'][index] = params;
    state.algolia_classes = indexAlgoliaClasses(state.algolia_classes)
    state.response_status = true;
  },

  remove_from_algolia_class_array(state, params) {
    let index = params.algolia_class_index;
    state.algolia_classes.splice(index, 1);
    state.algolia_classes = indexAlgoliaClasses(state.algolia_classes);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
