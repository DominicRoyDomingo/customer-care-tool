import axios from "axios";

import { API_ACTOR } from "./../../common/API.service";

function indexActors(actors){
  if(actors['data'] == undefined) return actors;
  actors['data'].forEach(function(row, index) {
    row.actor_index = index;
  });
  // console.log(brands)
  return actors;
}

// Default State
const getDefaultState = () => {
  return {
    actors: [],
    actor_all: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  actors: (state) => state.actors,
  actor_all: (state) => state.actor_all,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_actors({ commit }, params) {
    try {
      const resp = await axios.get(API_ACTOR, { params });
      commit("set_actors", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_actor_all({ commit }, params) {
    // console.log(params);
    try {
      const resp = await axios.get(API_ACTOR + `/all?api_token=${params.api_token}&lang=${params.lang}&brand_id=${params.brand_id}&locale=${params.locale}`, { params });
      commit("set_all_actor", resp.data);
    } catch (error) {
      console.log(error);
    }
  },
  
  post_actor({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ACTOR, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  update_information_list({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_ACTOR + '/update-information-list', params)
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
        .post(API_ACTOR + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  
  add_actor({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_actor", params);
      resolve(params);
    });
  },

  update_actor({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_actor", params);
      resolve(params);
    });
  },

  remove_from_actor_array({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("remove_from_actor_array", params);
      resolve(params);
    });
  },

  delete_actor({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_ACTOR, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_actors(context) {
    context.commit('reset_actors')
  },
};

const mutations = {
  set_actors(state, params) {
    state.actors = indexActors(params);
  },

  set_all_actor(state, params) {
    state.actor_all = params;
  },

  set_categories(state, params) {
    state.categories = params;
  },
  set_response_status(state, params) {
    state.response_status = params;
  },

  add_actor(state, params) {
    state.actors['data'].push(params);
  },

  update_actor(state, params) {
    let index = params.index;
    state.actors['data'][index] = params;
  },

  remove_from_actor_array(state, params) {
    let index = params.actor_index;
    state.actors.splice(index, 1);
    state.actors = indexActors(state.actors);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
