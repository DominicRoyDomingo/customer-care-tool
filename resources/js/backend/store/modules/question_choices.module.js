import axios from "axios";
import {API_QUESTION_CHOICE} from '../../common/API.service'

// Default State
const getDefaultState = () => {
   return {
      response_item: '',
   };
};

const state = getDefaultState(); // Declare State

const getters = {
   // get_specific_prefilledsections: (state) => state.prefilledsections, 
   get_response_item: (state) => state.response_item,
};


const actions = {
   delete_choice({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_QUESTION_CHOICE}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   update_choice({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_PREFILLED_SECTION}/update`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_choice({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_QUESTION_CHOICE}`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },
}

const mutations = {
   // set_specific_prefilledsections(state, params) {
   //    state.prefilledsections = params;
   // },

   set_response_item(state, params) {
      state.response_item = params;
   },
}

export default {
   namespaced: true,
   state,
   getters,
   actions,
   mutations,
};
