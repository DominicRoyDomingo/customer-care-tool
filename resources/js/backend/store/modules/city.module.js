import axios from "axios";

import { API_CITIES } from "./../../common/API.service";

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
    cities: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  cities: (state) => state.cities,
  get_response_status: (state) => state.response_status,
};

const actions = {
    reset_cities(context) {
      context.commit('reset_cities')
    },

    async get_cities({ commit }, params) {
        try {
            const resp = await axios.get(API_CITIES + `?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}${params.get('country_id') ? `&country=true`: ''}`, { params });
            commit("set_cities", resp.data);
        } catch (error) {
            console.log(error);
        }
    },

    async get_all_city({ commit }, params) {
      try {
  
        const resp = await axios.get(API_CITIES + `/all?api_token=${params.api_token}`, { params });
        return resp.data
  
      } catch (error) {
  
      }
    },
};


const mutations = {
  set_cities(state, params) {
    state.cities = params;
  },

  reset_cities(state) {
    state.cities = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
