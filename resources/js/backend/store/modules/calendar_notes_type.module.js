import axios from "axios";

import { API_CALENDAR_NOTES_TYPE } from "./../../common/API.service";

function indexRequestType(calendar_notes_type){
  if(calendar_notes_type['data'] == undefined) return calendar_notes_type;
    calendar_notes_type['data'].forEach(function(row, index) {
    row.calendar_notes_type_index = index;
  });
  return calendar_notes_type;
}

// Default State
const getDefaultState = () => {
  return {
    calendar_notes_type: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  calendar_notes_type: (state) => state.calendar_notes_type,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_calendar_notes_type({ commit }, params) {
    try {
      const resp = await axios.get(API_CALENDAR_NOTES_TYPE, { params });
      commit("set_calendar_notes_type", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_calendar_notes_type_name({ commit }, params) {
    try {
      const resp = await axios.get(API_CALENDAR_NOTES_TYPE + `/get-calendar-notes-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_calendar_notes_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CALENDAR_NOTES_TYPE, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_calendar_notes_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CALENDAR_NOTES_TYPE + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_calendar_notes_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_calendar_notes_type", params);
      resolve(params);
    });
  },

  update_calendar_notes_type({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_calendar_notes_type", params);
      resolve(params);
    });
  },
  remove_from_calendar_notes_type_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_calendar_notes_type_array", params);
      resolve(params);
    });
  },

  delete_calendar_notes_type({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_CALENDAR_NOTES_TYPE, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_calendar_notes_type(context) {
    context.commit('reset_calendar_notes_type')
  },
};

const mutations = {
  set_calendar_notes_type(state, params) {
    state.calendar_notes_type = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_calendar_notes_type(state, params) {
    if(state.calendar_notes_type['data'] == undefined) state.calendar_notes_type.push(params);
  },

  update_calendar_notes_type(state, params) {
    let index = params.index;
    state.calendar_notes_type['data'][index] = params;
  },

  remove_from_calendar_notes_type_array(state, params) {
    let index = params.calendar_notes_type_index;
    state.calendar_notes_type.splice(index, 1);
    state.calendar_notes_type = indexRequestType(state.calendar_notes_type);
  },

  reset_calendar_notes_type(state) {
    state.calendar_notes_type = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
