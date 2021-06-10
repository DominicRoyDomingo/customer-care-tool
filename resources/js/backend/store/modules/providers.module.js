import axios from "axios";

import { API_PROVIDERS } from "./../../common/API.service";

function indexProviders(providers){
  providers.data.forEach(function(row, index) {
    row.provider_index = index;
  });
  // console.log(brands)
  return providers;
}

function indexProviderUpdate(providers){
  providers.forEach(function(row, index) {
    row.provider_index = index;
  });
  // console.log(brands)
  return providers;
}

// Default State
const getDefaultState = () => {
  return {
    providers: [],
    provider: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  providers: (state) => state.providers,
  get_response_status: (state) => state.response_status,
  provider: (state) => state.provider,
};

const actions = {
  async get_providers({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDERS, { params });
      commit("set_providers", resp.data);
    } catch (error) {
    }
  },

  async get_algolia_summary({ commit }, params) {
    try {
      const resp = await axios.get(`${API_PROVIDERS}/algolia-summary`, { params });
      return resp.data
    } catch (error) {
    }
  },

  
  async get_provider_name({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDERS + `/get-provider-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
    }
  },

  async get_provider({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDERS + `/get-provider?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}&brandId=${params.get('brand_id')}`, { params });
      commit("set_provider", resp.data);
    } catch (error) {
    }
  },

  async add_provider_profile({ commit }, params) {
    try {
      const resp = await axios.get(API_PROVIDERS + `/add-provider-profile?api_token=${params.get('api_token')}&id=${params.get('id')}&brand_id=${params.get('brand_id')}&lang=${params.get('lang')}`, { params });
      commit("set_response_status", resp.data);
    } catch (error) {
    }
  },
  
  post_provider({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_actor({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/link-actor`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_add_to_group({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/add-to-group`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_change_account_status({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/change-account-status`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_sync_to_algolia({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/sync-to-algolia`, params)
        .then((resp) => {
          resolve(resp);
          return resp.data
        })
        .catch((error) => reject(error));
    });
  },

  post_sync_reference({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROVIDERS + `/add-to-sync-reference`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_provider({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_provider", params);
      resolve(params);
    });
  },

  update_provider({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_provider", params);
      resolve(params);
    });
  },

  remove_from_provider_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_provider_array", params);
      resolve(params);
    });
  },

  delete_provider({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROVIDERS, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_image({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROVIDERS + '/delete-image', { params })
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
  set_providers(state, params) {
    // if(state.providers['data'] == undefined) {
    //   state.providers = indexProviders(params);
    //   return;
    // }

    // if(indexProviders(params)['data'].length == 0) { 
    //   if(indexProviders(params)['current_page'] == 1) state.providers.total = 0;
    //   state.response_status = false;
    //   return;
    // };
    
    // indexProviders(params)['data'].forEach(function(data){
    //   state.providers['data'].push(data);
    // });
    // const uniqueProviders = [...new Map(state.providers['data'].map(provider => [provider.name, provider])).values()]
    // state.providers['data'] = uniqueProviders;
    // state.providers = indexProviders(state.providers)
    // state.providers.total = params.total
    // state.response_status = true;
    state.providers = indexProviders(params)
    state.providers.total = params.total
    state.response_status = true;
  },

  set_provider(state, params) {
    state.provider = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_provider(state, params) {
    state.providers['data'].push(params);
    state.providers = indexProviders(state.providers)
    state.providers.total = state.providers.total + 1;
  },

  update_provider(state, params) {
    let index = params.index;
    state.providers['data'][index] = params;
    state.providers = indexProviders(state.providers)
    state.response_status = true;
  },

  remove_from_provider_array(state, params) {
    let index = params.provider_index;
    state.providers['data'].splice(index, 1);
    state.providers = indexProviders(state.providers);
    state.providers.total = state.providers.total - 1;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
