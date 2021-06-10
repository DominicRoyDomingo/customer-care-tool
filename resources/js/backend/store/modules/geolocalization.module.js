import axios from "axios";

import { API_GEOLOCALIZATION } from "../../common/API.service";

function indexGeolocalization(geolocalization){
  if(geolocalization['data'] == undefined) return geolocalization;
  geolocalization['data'].forEach(function(row, index) {
    row.geolocalization_index = index;
  });
  // console.log(brands)
  return geolocalization;
}

// Default State
const getDefaultState = () => {
  return {
    geolocalization_cities: [],
    geolocalization_provinces: [],
    geolocalization_regions: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  geolocalization_cities: (state) => state.geolocalization_cities,
  geolocalization_provinces: (state) => state.geolocalization_provinces,
  geolocalization_regions: (state) => state.geolocalization_regions,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_geolocalization_cities({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION+"/cities", { params });
      commit("set_geolocalization_cities", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_geolocalization_provinces({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION+"/provinces", { params });
      commit("set_geolocalization_provinces", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_algolia_summary({ commit }, params) {
    try {
      const resp = await axios.get(`${API_GEOLOCALIZATION}/algolia-summary`, { params });
      return resp.data
    } catch (error) {
    }
  },
  async get_geolocalization_regions({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION+"/regions", { params });
      commit("set_geolocalization_regions", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_geolocalization_article({ commit }, params) {
    try {
      const resp = await axios.get(API_GEOLOCALIZATION, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },

  post_sync_to_algolia({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_GEOLOCALIZATION + `/sync-to-algolia`, params)
        .then((resp) => {
          resolve(resp);
          return resp.data
        })
        .catch((error) => reject(error));
    });
  },
  
  post_geolocalization({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_GEOLOCALIZATION, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_geolocalization_image({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(`${API_GEOLOCALIZATION}/create-images`, params)
        .then((resp) => {
          resolve(resp);
          return resp.data
        })
        .catch((error) => reject(error));
    });
  },

  post_change_publish_status({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(`${API_GEOLOCALIZATION}/change-publish-status`, params)
        .then((resp) => {
          resolve(resp);
          return resp.data
        })
        .catch((error) => reject(error));
    });
  },
  
  add_geolocalization({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_geolocalization", params);
      resolve(params);
    });
  },

  update_geolocalization({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_geolocalization", params);
      resolve(params);
    });
  },

  remove_from_geolocalization_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_geolocalization_array", params);
      resolve(params);
    });
  },

  delete_geolocalization({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_GEOLOCALIZATION, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_geolocalize_image({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(`${API_GEOLOCALIZATION}/delete-image`, { params })
        .then((resp) => {
          resolve(resp);
          return resp.data

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_geolocalizations(context) {
    context.commit('reset_geolocalizations')
  },
};

const mutations = {
  set_geolocalization_cities(state, params) {
    state.geolocalization_cities = indexGeolocalization(params);
  },

  set_geolocalization_provinces(state, params) {
    state.geolocalization_provinces = indexGeolocalization(params);
  },

  set_geolocalization_regions(state, params) {
    state.geolocalization_regions = indexGeolocalization(params);
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_geolocalization(state, params) {
    if(state.geolocalizations['data'] == undefined) state.geolocalizations.push(params);
  },

  update_geolocalization(state, params) {
    let index = params.index;
    state.geolocalizations[index] = params;
  },

  remove_from_geolocalization_array(state, params) {
    let index = params.geolocalization_index;
    state.geolocalizations.splice(index, 1);
    state.geolocalizations = indexGeolocalization(state.geolocalizations);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
