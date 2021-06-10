import axios from "axios";
import {API_HTML_TAGS, API_ITEMTYPE } from '../../common/API.service'

// Default State
const getDefaultState = () => {
   return {
      response_status: false,
      html_tags_items: [],
      publishing_type_items: [],
   };
};

const state = getDefaultState(); // Declare State

const getters = {
   get_response_status: (state) => state.response_status,
   get_html_tags_items: (state) => state.html_tags_items,
   get_publishing_type_items: (state) => state.publishing_type_items, 
};


const actions = {
   // html tags actions
   // Actions api for html tags
   async get_html_tags({ commit }, params) {
      try {
         const resp = await axios.get(`${API_HTML_TAGS}`, { params });
         commit('set_html_tags_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_publishing_type_items({ commit }, params) {
      try {
         const resp = await axios.get(`${API_ITEMTYPE}`, { params });
         commit('set_publishing_type_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async show_publishing_type_items({ commit }, params) {
      try {
         let id = params.article_id;
         delete params.article_id

         const resp = await axios.get(`${API_ARTICLES}/${id}`, { params });
         commit('set_response_item', resp.data)
      } catch (error) {
         console.log(error)
      }
   },
}

const mutations = {
   set_html_tags_items(state, params) {
      state.html_tags_items = params;
   },

   set_publishing_type_items(state, params) {
      state.publishing_type_items = params;
   },
}

export default {
   namespaced: true,
   state,
   getters,
   actions,
   mutations,
};
