import axios from "axios";

import { API_ALGOLIA_PERMISSION } from "./../../common/API.service";

function indexAlogliaPermissions(algoliaPermission){
  if(algoliaPermission['data'] == undefined) return algoliaPermission;
  algoliaPermission['data'].forEach(function(row, index) {
    row.algolia_permission_index = index;
  });
  // console.log(brands)
  return algoliaPermission;
  algolia_permissions.forEach(function(row, index) {
    row.algolia_permission_index = index;
  });
  // console.log(brands)
  return algolia_permissions;
}

// Default State
const getDefaultState = () => {
  return {
    algolia_permissions: [],
    algolia_permission_all: [],
    algolia_permission: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  algolia_permissions: (state) => state.algolia_permissions,
  algolia_permission_all: (state) => state.algolia_permission_all,
  get_response_status: (state) => state.response_status,
  algolia_permission: (state) => state.algolia_permission,
};

const actions = {
  async get_algolia_permissions({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_PERMISSION, { params });
      commit("set_algolia_permissions", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_algolia_permission_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_ALGOLIA_PERMISSION + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_algolia_permission_all", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_algolia_permission_name({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_PERMISSION + `/get-provider-group-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_algolia_permission({ commit }, params) {
    try {
      const resp = await axios.get(API_ALGOLIA_PERMISSION + `/get-provider-group?api_token=${params.api_token}&id=${params.id}&lang=${params.lang}&brandId=${params.brand_id}`, { params });
      commit("set_algolia_permission", resp.data);
    } catch (error) {
    }
  },
  
  post_algolia_permission({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ALGOLIA_PERMISSION, params)
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
        .post(API_ALGOLIA_PERMISSION + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_algolia_permission({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_algolia_permission", params);
      resolve(params);
    });
  },

  update_algolia_permission({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_algolia_permission", params);
      resolve(params);
    });
  },

  remove_from_algolia_permission_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_algolia_permission_array", params);
      resolve(params);
    });
  },

  delete_algolia_permission({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ALGOLIA_PERMISSION, { params })
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
  set_algolia_permissions(state, params) {
    state.algolia_permissions = indexAlogliaPermissions(params);
  },

  set_algolia_permission_all(state, params) {
    state.algolia_permission_all = params;
  },

  set_algolia_permission(state, params) {
    state.algolia_permission = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_algolia_permission(state, params) {
    state.algolia_permissions.push(params);
  },

  update_algolia_permission(state, params) {
    let index = params.index;
    state.algolia_permissions['data'][index] = params;
    state.algolia_permissions = indexAlogliaPermissions(state.algolia_permissions)
    state.response_status = true;
  },

  remove_from_algolia_permission_array(state, params) {
    let index = params.algolia_permission_index;
    state.algolia_permissions.splice(index, 1);
    state.algolia_permissions = indexAlogliaPermissions(state.algolia_permissions);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
