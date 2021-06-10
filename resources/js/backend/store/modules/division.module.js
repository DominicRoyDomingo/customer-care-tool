import axios from "axios";

import { API_DIVISIONS } from "./../../common/API.service";

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
    divisions: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  divisions: (state) => state.divisions,
  get_response_status: (state) => state.response_status,
};

const actions = {
    async get_divisions({ commit }, params) {
        try {
            const resp = await axios.get(API_DIVISIONS + `?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
            commit("set_divisions", resp.data);
        } catch (error) {
            console.log(error);
        }
    },

    async get_all_division({ commit }, params) {
      try {
  
        const resp = await axios.get(API_DIVISIONS + `/all?api_token=${params.api_token}`, { params });
        return resp.data
  
      } catch (error) {
  
      }
    },
};


const mutations = {
  set_divisions(state, params) {
    state.divisions = params;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
