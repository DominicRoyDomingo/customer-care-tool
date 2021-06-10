import axios from "axios";

import { API_LOCATION } from "./../../common/API.service";

function indexArray(array) {
  array.forEach(function(row, index) {
    row.array_index = index;
  });

  return array;
}

// Default State
const getDefaultState = () => {
  return {
    countries: [],
    provinces: [],
    regions: [],
    cities: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
    countries: (state) => state.countries,
    provinces: (state) => state.provinces,
    regions: (state) => state.regions,
    cities: (state) => state.cities,
    get_response_status: (state) => state.response_status,
};

const actions = {
    get_countries({ commit }, params) {
        return new Promise((resolve, reject) => {
          axios.get(API_LOCATION + "/countries", { params })
          .then((resp)=>{
            commit("set_countries", resp.data);
            resolve(resp);
          })
          .catch((error)=>{
            console.log(error);
            reject(error);
          });
      });
    },

    async get_regions({ commit }, params) {
      return new Promise((resolve, reject) => {
          axios.get(API_LOCATION + "/regions/" + params.country_id, { params })
          .then((resp)=>{
            commit("set_regions", resp.data);
            resolve(resp);
          })
          .catch((error)=>{
            console.log(error);
            reject(error);
          });
      });
    },
  
    async filter_regions({ commit }, params) {
      try {
        const resp = await axios.get(API_LOCATION + "/filter/regions/", { params });
        commit("set_regions", resp.data);
      } catch (error) {
        console.log(error);
      }
    },

    async get_provinces({ commit }, params) {
      return new Promise((resolve, reject) => {
          axios.get(API_LOCATION + "/provinces/" + params.country_id, { params })
          .then((resp)=>{
            commit("set_provinces", resp.data);
            resolve(resp);
          })
          .catch((error)=>{
            console.log(error);
            reject(error);
          });
      });
    },
  
    async filter_provinces({ commit }, params) {
      try {
        const resp = await axios.get(API_LOCATION + "/filter/provinces/", { params });
        commit("set_provinces", resp.data);
      } catch (error) {
        console.log(error);
      }
    },
    
    async get_cities({ commit }, params) {
        try {
          const resp = await axios.get(API_LOCATION + "/cities/" + params.province_id, { params });
          commit("set_cities", resp.data);
        } catch (error) {
          console.log(error);
        }
    },
  
    async filter_cities({ commit }, params) {
        try {
          const resp = await axios.get(API_LOCATION + "/filter/cities/", { params });
          commit("set_cities", resp.data);
        } catch (error) {
          console.log(error);
        }
    },
        
    add_province({ commit }, params) {
        return new Promise((resolve, reject) => {
          axios
            .post(API_LOCATION + "/provinces",  params )
            .then((resp) => {
              let province = resp.data;
              commit("add_province", province);
              commit("set_response_status", resp.data);
              resolve(resp);
            })
            .catch((error) => reject(error));
        });
    },

    update_province({ commit, state }, params) {
      return new Promise((resolve, reject) => {
        axios
          .put(API_LOCATION + "/provinces/" + params.province_id,  params )
          .then((resp) => {
            state.provinces[params.index].name = params.name;
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    async delete_province({ commit }, params) {
      console.log("delete_params");
      console.log(params);

      return new Promise((resolve, reject) => {
        axios
          .delete(API_LOCATION + "/provinces/" + params.province_id, { params })
          .then((resp) => {
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    add_city({ commit }, params) {
        return new Promise((resolve, reject) => {
          axios
            .post(API_LOCATION + "/cities",  params )
            .then((resp) => {
              let city = resp.data;
              commit("add_city", city);
              commit("set_response_status", resp.data);
              resolve(resp);
            })
            .catch((error) => reject(error));
        });
    },
    
    update_city({ commit, state }, params) {
      return new Promise((resolve, reject) => {
        axios
          .put(API_LOCATION + "/cities/" + params.city_id,  params )
          .then(resp => {
            state.cities[params.index].name = params.name;
            commit("set_response_status", resp.data);
            resolve( resp.data);
          })
          .catch((error) => reject(error));
      });
    },

    async delete_city({ commit }, params) {
      console.log("delete_params");
      console.log(params);

      return new Promise((resolve, reject) => {
        axios
          .delete(API_LOCATION + "/cities/" + params.city_id, { params })
          .then((resp) => {
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    add_region({ commit }, params) {
      return new Promise((resolve, reject) => {
        axios
          .post(API_LOCATION + "/regions",  params )
          .then((resp) => {
            let region = resp.data;
            commit("add_region", region);
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    update_region({ commit, state }, params) {
      return new Promise((resolve, reject) => {
        axios
          .put(API_LOCATION + "/regions/" + params.region_id,  params )
          .then((resp) => {
            state.regions[params.index].region = params.region;
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    async delete_region({ commit }, params) {
      console.log("delete_params");
      console.log(params);

      return new Promise((resolve, reject) => {
        axios
          .delete(API_LOCATION + "/regions/" + params.region_id, { params })
          .then((resp) => {
            commit("set_response_status", resp.data);
            resolve(resp);
          })
          .catch((error) => reject(error));
      });
    },

    remove_from_regions_array({state}, params) {
      let index = params.array_index;
      state.regions.splice(index, 1);
      state.regions = indexArray(state.regions);
    },

    remove_from_provinces_array({state}, params) {
      let index = params.array_index;
      state.provinces.splice(index, 1);
      state.provinces = indexArray(state.provinces);
    },
    
    remove_from_cities_array({state}, params) {
      let index = params.array_index;
      console.log("Delete index: " + index);
      console.log(state.cities);
      state.cities.splice(index, 1);
      state.cities = indexArray(state.cities);
    },
};

const mutations = {
    set_countries(state, params) {
      state.countries = params;
    },
    set_regions(state, params) {
      state.regions = indexArray(params);
    },
    set_provinces(state, params) {
      state.provinces = indexArray(params);
    },
    set_cities(state, params) {
      state.cities = indexArray(params);
    },
    add_province(state, params){
      params.array_index = state.provinces.length;
      state.provinces.push(params);
    },

    add_region(state, params){
      params.array_index = state.regions.length;
      state.regions.push(params);
    },
    add_city(state, params){
      params.array_index = state.cities.length;
      state.cities.push(params);
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
