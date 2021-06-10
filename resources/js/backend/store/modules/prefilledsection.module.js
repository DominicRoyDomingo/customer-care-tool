import axios from "axios";
import {API_PREFILLED_SECTION} from '../../common/API.service'

// Default State
const getDefaultState = () => {
   return {
      prefilledsections: null,
      response_item: '',
   };
};

const state = getDefaultState(); // Declare State

const getters = {
   get_specific_prefilledsections: (state) => state.prefilledsections, 
   get_response_item: (state) => state.response_item,
};


const actions = {
   async get_specific_prefilledsections({ commit }, params) {
      try {
         let id = params.articleTypeID;
         delete params.article_id

         const resp = await axios.get(`${API_PREFILLED_SECTION}/retrieve/${id}`, { params });
         commit('set_specific_prefilledsections', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_delete_prefilledsection({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_PREFILLED_SECTION}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_update_prefilledsection({ commit }, params) {
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

   async get_prefilledsection_list({ commit }, params) {
      try {
         const resp = await axios.get(`${API_ITEMTYPE}`, { params });
         commit('set_publishing_type_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_create_prefilledsection({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_PREFILLED_SECTION}/store`, params)
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
   set_specific_prefilledsections(state, params) {
      state.prefilledsections = params;
   },

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
