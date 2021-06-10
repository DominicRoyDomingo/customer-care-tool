import axios from "axios";

import { API_WORKSPACE } from "./../../common/API.service";

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
    workspaces: [],
    allWorkspaces: [],
    active_workspace: '',
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  workspaces: (state) => state.workspaces,
  allWorkspaces: (state) => state.allWorkspaces,
  active_workspace: (state) => state.active_workspace,
  get_response_status: (state) => state.response_status,
};

const actions = {

  async get_workspaces({ commit }, params) {
      try {
          const resp = await axios.get(API_WORKSPACE , { params });
          commit("set_workspaces", resp.data);
      } catch (error) {
          console.log(error);
      }
  },

  async get_all_workspaces({ commit }, params) {
    try {
      const resp = await axios.get(API_WORKSPACE + `/all?api_token=${params['api_token']}`, { params });
      commit("set_all_workspaces", resp.data);
    } catch (error) {

    }
  },

  async get_active_workspace({ commit }, params) {
    try {
      const resp = await axios.get(API_WORKSPACE + `/active?api_token=${params['api_token']}`, { params });
      commit("set_all_workspaces", resp.data);
    } catch (error) {

    }
  },

  delete_workspace({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_WORKSPACE, { params })
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
  set_workspaces(state, params) {
    state.workspaces = params;
  },
  set_all_workspaces(state, params) {
    state.allWorkspaces = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
