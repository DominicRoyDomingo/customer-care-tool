import axios from "axios";

import { API_BRAND } from "./../../common/API.service";

function indexBrands(brands){
  brands.forEach(function(row, index) {
    row.brand_index = index;
    row.categories = row.categories.map(category => category.category_id);
  });
  // console.log(brands)
  return brands;
}

// Default State
const getDefaultState = () => {
  return {
    brands: [],
    categories: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  brands: (state) => state.brands,
  categories: (state) => state.categories,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_brands({ commit }, params) {
    try {
      const resp = await axios.get(API_BRAND, { params });
      commit("set_brands", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_brand({ commit }, params) {
    try {
      const resp = await axios.get(API_BRAND + "/" + params.id, { params });
      commit("set_brands", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_categories({ commit }, params) {
    try {
      const resp = await axios.get(API_BRAND + "/category/categories", { params });
      commit("set_categories", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_brand({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_BRAND, params)
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
        .post(API_BRAND + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_brand({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_brand", params);
      resolve(params);
    });
  },

  update_brand({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_brand", params);
      resolve(params);
    });
  },

  remove_from_brands_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_brands_array", params);
      resolve(params);
    });
  },

  delete_brand({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_BRAND, { params })
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
  set_brands(state, params) {
    state.brands = indexBrands(params);
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_brand(state, params) {
    params.brand_index = state.brands.length;
    state.brands.push(params);
  },

  update_brand(state, params) {
    let index = params.brand_index;
    state.brands[index] = params;
  },

  remove_from_brands_array(state, params) {
    let index = params.brand_index;
    state.brands.splice(index, 1);
    state.brands = indexBrands(state.brands);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
