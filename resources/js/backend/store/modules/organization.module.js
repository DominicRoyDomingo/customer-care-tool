import axios from "axios";

import { API_ORGANIZATION } from "./../../common/API.service";

// function indexBrands(brands){
//   brands.forEach(function(row, index) {
//     row.brand_index = index;
//     row.categories = row.categories.map(category => category.category_id);
//   });
//   // console.log(brands)
//   return brands;
// }

// Default State
const getDefaultState = () => {
  return {
    organizations: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  organizations: (state) => state.organization_categories,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_organization_categories({ commit }, params) {
    console.log(params)
    try {
      const resp = await axios.get(API_ORGANIZATION, { params });
      commit("set_organization_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_brand({ commit }, params) {
    try {
      const resp = await axios.get(API_ORGANIZATION + "/" + params.id, { params });
      commit("set_organization_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_categories({ commit }, params) {
    try {
      const resp = await axios.get(API_ORGANIZATION + "/category/categories", { params });
      commit("set_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_organization_category_name({ commit }, params) {
    try {
      const resp = await axios.get(API_ORGANIZATION + `/get-organization-category-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_organization({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ORGANIZATION, params)
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
        .post(API_ORGANIZATION + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_organization_category({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_organization_category", params);
      resolve(params);
    });
  },

  update_organization_category({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_organization_category", params);
      resolve(params);
    });
  },

  remove_from_organization_categories_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_organization_categories_array", params);
      resolve(params);
    });
  },

  delete_organization_category({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ORGANIZATION, { params })
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
  set_organization_categories(state, params) {
    state.organization_categories = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_organization_category(state, params) {
    state.organization_categories.push(params);
  },

  update_organization_category(state, params) {
    let index = params.index;
    state.organization_categories[index] = params;
  },

  remove_from_organization_categories_array(state, params) {
    let index = params.index;
    state.organization_categories.splice(index, 1);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
