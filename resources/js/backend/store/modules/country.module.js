import axios from "axios";

import { API_COUNTRIES } from "./../../common/API.service";

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
    countries: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  countries: (state) => state.countries,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_countries({ commit }, params) {
    try {
      const resp = await axios.get(API_COUNTRIES, { params });
      commit("set_countries", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_all_city({ commit }, params) {
    try {

      const resp = await axios.get(API_COUNTRIES + `/cities?api_token=${params.api_token}`, { params });
      return resp.data

    } catch (error) {

    }
  },

  async get_all_province({ commit }, params) {
    try {

      const resp = await axios.get(API_COUNTRIES + `/provinces?api_token=${params.api_token}`, { params });
      return resp.data

    } catch (error) {

    }
  },

  async get_all_region({ commit }, params) {
    try {

      const resp = await axios.get(API_COUNTRIES + `/regions?api_token=${params.api_token}`, { params });
      return resp.data

    } catch (error) {

    }
  },
};

const mutations = {
  set_countries(state, params) {
    state.countries = params;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
