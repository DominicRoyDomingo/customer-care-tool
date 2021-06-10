import axios from "axios";
import {API_PAYMENT_PLAN} from '../../common/API.service'

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
   delete_paymentplan({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_PAYMENT_PLAN}`, { params })
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
            .post(`${API_PAYMENT_PLAN}/update`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_paymentplan({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_PAYMENT_PLAN}`, params)
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
