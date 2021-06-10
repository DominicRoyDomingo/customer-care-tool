import axios from "axios";

import { API_PROVIDER_GROUP } from "./../../common/API.service";

function indexProviderGroups(provider_groups){
  if(provider_groups['data'] == undefined) return provider_group;
  provider_groups['data'].forEach(function(row, index) {
    row.provider_group_index = index;
  });
  // console.log(brands)
  return provider_groups;
  provider_groups.forEach(function(row, index) {
    row.provider_group_index = index;
  });
  // console.log(brands)
  return provider_groups;
}

// Default State
const getDefaultState = () => {
  return {
    provider_groups: [],
    provider_group_all: [],
    provider_group: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  provider_groups: (state) => state.provider_groups,
  provider_group_all: (state) => state.provider_group_all,
  get_response_status: (state) => state.response_status,
  provider_group: (state) => state.provider_group,
};

const actions = {
  async get_provider_groups({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_GROUP, { params });
      commit("set_provider_groups", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_provider_group_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_PROVIDER_GROUP + `/all?api_token=${params.api_token}&lang=${params.lang}`, { params });
      commit("set_provider_group_all", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_provider_group_name({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_GROUP + `/get-provider-group-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  async get_provider_group({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDER_GROUP + `/get-provider-group?api_token=${params.api_token}&id=${params.id}&lang=${params.lang}&brandId=${params.brand_id}`, { params });
      commit("set_provider_group", resp.data);
    } catch (error) {
    }
  },
  
  post_provider_group({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDER_GROUP, params)
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
        .post(API_PROVIDER_GROUP + '/category/categories', params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_provider_group({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_provider_group", params);
      resolve(params);
    });
  },

  update_provider_group({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_provider_group", params);
      resolve(params);
    });
  },

  remove_from_provider_group_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_provider_group_array", params);
      resolve(params);
    });
  },

  delete_provider_group({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROVIDER_GROUP, { params })
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
  set_provider_groups(state, params) {
    state.provider_groups = indexProviderGroups(params);
  },

  set_provider_group_all(state, params) {
    state.provider_group_all = params;
  },

  set_provider_group(state, params) {
    state.provider_group = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_provider_group(state, params) {
    state.provider_groups.push(params);
  },

  update_provider_group(state, params) {
    let index = params.index;
    state.provider_groups['data'][index] = params;
    state.provider_groups = indexProviderGroups(state.provider_groups)
    state.response_status = true;
  },

  remove_from_provider_group_array(state, params) {
    let index = params.provider_group_index;
    state.provider_groups.splice(index, 1);
    state.provider_groups = indexProviderGroups(state.provider_groups);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
