import axios from "axios";

import { API_CALENDAR_NOTES } from "./../../common/API.service";

function indexRequestType(calendar_notes){
  if(calendar_notes['data'] == undefined) return calendar_notes;
    calendar_notes['data'].forEach(function(row, index) {
    row.calendar_notes_index = index;
  });
  return calendar_notes;
}

// Default State
const getDefaultState = () => {
  return {
    calendar_notes: [],
    response_status: false
  };
};

// State
const state = getDefaultState();

const getters = {
  calendar_notes: (state) => state.calendar_notes,
  get_response_status: (state) => state.response_status,
};

const actions = {
  async get_calendar_notes({ commit }, params) {
    try {
      const resp = await axios.get(API_CALENDAR_NOTES, { params });
      commit("set_calendar_notes", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  
  async get_calendar_notes_name({ commit }, params) {
    try {
      const resp = await axios.get(API_CALENDAR_NOTES + `/get-calendar-notes-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
      return resp.data
    } catch (error) {
      console.log(error);
    }
  },
  
  post_calendar_notes({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CALENDAR_NOTES, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_link_brand_calendar_notes({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CALENDAR_NOTES + `/link-brand`, params)
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  add_calendar_notes({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("add_calendar_notes", params);
      resolve(params);
    });
  },

  update_calendar_notes({ commit }, params) {
    return new Promise((resolve, reject) => {      
      commit("update_calendar_notes", params);
      resolve(params);
    });
  },
  remove_from_calendar_notes_array({ commit }, params) {
    
    return new Promise((resolve, reject) => {      
      commit("remove_from_calendar_notes_array", params);
      resolve(params);
    });
  },

  delete_calendar_notes({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_CALENDAR_NOTES, { params })
        .then((resp) => {
          resolve(resp);
          commit("set_response_status", resp.data);

          // commit("set_job_items", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  reset_calendar_notes(context) {
    context.commit('reset_calendar_notes')
  },
};

const mutations = {
  set_calendar_notes(state, params) {
    state.calendar_notes = indexRequestType(params);
  },

  set_response_status(state, params) {
    state.response_status = params;
  },

  add_calendar_notes(state, params) {
    if(state.calendar_notes['data'] == undefined) state.calendar_notes.push(params);
  },

  update_calendar_notes(state, params) {
    let index = params.index;
    state.calendar_notes['data'][index] = params;
  },

  remove_from_calendar_notes_array(state, params) {
    let index = params.calendar_notes_index;
    state.calendar_notes.splice(index, 1);
    state.calendar_notes = indexRequestType(state.calendar_notes);
  },

  reset_calendar_notes(state) {
    state.calendar_notes = [];
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
