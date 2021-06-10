import axios from "axios";
import { API_TYPE_TERMS, API_TERMS, API_TYPE_DATES, API_ARTICLES, API_AUTHOR_TYPE, API_HTML_TAGS } from '../../common/API.service'

// Default State
const getDefaultState = () => {
   return {
      type_dates_items: [],
      html_tags_items: [],
      type_terms_items: [],
      terms_items: [],
      term_descriptions_items: [],
      linked_terms_items: [],
      linked_terms_details_item: [],
      linked_provider_terms_items: [],
      professional_terms_items: [],
      services_terms_items: [],
      category_services_terms_items: [],
      services_category_terms_items: [],
      term_connections: [],
      notes_terms_items: [],
      articles_items: [],
      type_author: [],
      response_item: '',
      response_status: false,
      show_value: false,
   };
};

const state = getDefaultState(); // Declare State


const getters = {
   get_response_status: (state) => state.response_status,

   get_response_item: (state) => state.response_item,

   get_type_terms_items: (state) => state.type_terms_items,

   get_type_dates_items: (state) => state.type_dates_items,

   get_html_tags_items: (state) => state.html_tags_items,

   get_terms_items: (state) => state.terms_items,

   get_term_descriptions_items: (state) => state.term_descriptions_items,

   get_articles_items: (state) => state.articles_items,

   get_linked_terms_items: (state) => state.linked_terms_items,

   get_linked_terms_details_item: (state) => state.linked_terms_details_item,

   get_linked_provider_terms_items: (state) => state.linked_provider_terms_items,

   get_professional_terms_items: (state) => state.professional_terms_items,

   get_services_terms_items: (state) => state.services_terms_items,

   get_category_services_terms_items: (state) => state.category_services_terms_items,

   get_services_category_terms_items: (state) => state.services_category_terms_items,

   get_notes_terms_items: (state) => state.notes_terms_items,

   get_type_author_items: (state) => state.type_author,

   get_term_connections_items: (state) => state.term_connections,
};


const actions = {
   // type of terms actions


   async get_type_terms({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TYPE_TERMS}`, { params });
         commit('set_type_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_term_type({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TYPE_TERMS}`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_term_type({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TYPE_TERMS}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },


   // type of dates actions
   // Actions api for type of dates
   async get_type_dates({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TYPE_DATES}`, { params });
         commit('set_type_dates_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_type_date({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TYPE_DATES}`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_type_date({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TYPE_DATES}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

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

   post_html_tag({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_HTML_TAGS}`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_html_tag({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_HTML_TAGS}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },


   update_image({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_ARTICLES}/update-image`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_image({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_ARTICLES}/delete-image`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   // Articles dates actions
   // Actions api for Articles
   async get_articles({ commit }, params) {
      try {
         const resp = await axios.get(`${API_ARTICLES}`, { params });
         commit('set_articles_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_article({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_ARTICLES}`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   async show_article({ commit }, params) {
      try {
         let id = params.article_id;
         delete params.article_id

         const resp = await axios.get(`${API_ARTICLES}/${id}`, { params });
         commit('set_response_item', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   delete_article({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_ARTICLES}`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   // Terms dates actions
   // Actions api for Terms

   async get_terms({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}`, { params });
         commit('set_term_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_provider_type_terms({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/provider-type`, { params });
         return resp.data
      } catch (error) {
         console.log(error)
      }
   },

   post_term_note() {

   },

   async get_category_of_services_terms({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/category-services`, { params });
         commit('set_category_of_services_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_services_category_terms({ commit }, params) {
      try {
         let id = params.categoryId
         const resp = await axios.get(`${API_TERMS}/services/${id}`, { params });
         commit('set_services_category_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },



   async get_advance_search({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/advance-search`, { params });
         commit('set_term_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_advance_search_article({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/advance-search-article`, { params });
         commit('set_articles_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async show_term({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/show`, { params });
         commit('set_response_item', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   // async sort_type_terms({ commit }, params) {
   //    try {
   //       const resp = await axios.get(`${API_TERMS}/sort`, { params });
   //       commit('set_term_items', resp.data)
   //    } catch (error) {
   //       console.log(error)
   //    }
   // },


   async get_linked_terms({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms`, { params });
         commit('set_linked_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }

   },
   async get_linked_terms_actor({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms-actor`, { params });
         commit('set_linked_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_term_icons({ commit }, params) {
      try {
         let id = params.id
         const resp = await axios.get(`/api/terms/term-icons/${id}`, { params });
         return resp.data
      } catch (error) {
         console.log(error)
      }
   },

   async get_linked_terms_details({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms-details`, { params });
         commit('set_linked_terms_details_item', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_linked_provider_terms({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms-providers`, { params });
         commit('set_linked_provider_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_professional_terms({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms-professional`, { params });
         commit('set_professional_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_services_terms({ commit }, params) {
      try {
         const resp = await axios.get(`/api/links/terms-services`, { params });
         commit('set_services_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },


   get_linked_term_id({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .get(`${API_TERMS}/linked-id`, { params })
            .then((resp) => {
               resolve(resp)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_term({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TERMS}/post`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_term({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TERMS}/delete`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   async get_notes_article({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/get-notes`, { params });
         commit('set_notes_terms_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   // Term Descriptions
   async get_terms_descriptions({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/descriptions`, { params });
         commit('set_term_descriptions_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   post_to_action_route({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TERMS}/post-notes`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_term_description({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TERMS}/descriptions`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_term_description({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TERMS}/descriptions`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   post_provider_term({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TERMS}/post-icon`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_term_icon({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TERMS}/term-icons`, { params })
            .then((resp) => {
               resolve(resp)
               return resp.data;
            })
            .catch(error => {
               reject(error)
            })
      });
   },


   async get_term_connections({ commit }, params) {
      try {
         const resp = await axios.get(`${API_TERMS}/descriptions/term-connections`, { params });
         commit('set_term_connections_items', resp.data)
      } catch (error) {
         console.log(error)
      }

   },

   post_term_connection_description({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_TERMS}/descriptions/term-connections`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },


   delete_term_connection_description({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_TERMS}/descriptions/term-connections`, { params })
            .then((resp) => {
               resolve(resp)
               commit('set_response_status', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   // TYPE OF AUTHOR
   async get_type_authors({ commit }, params) {
      try {
         const resp = await axios.get(`${API_AUTHOR_TYPE}`, { params });
         commit('set_type_authors_items', resp.data)
      } catch (error) {
         console.log(error)
      }
   },

   async get_type_author_name({ commit }, params) {
      try {
         const resp = await axios.get(API_AUTHOR_TYPE + `/get-author-type-name?api_token=${params.get('api_token')}&id=${params.get('id')}&lang=${params.get('lang')}`, { params });
         return resp.data
      } catch (error) {
         console.log(error);
      }
   },

   post_to_type_author_route({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .post(`${API_AUTHOR_TYPE}/post-type-author`, params)
            .then((resp) => {
               resolve(resp)
               commit('set_response_item', resp.data)
            })
            .catch(error => {
               reject(error)
            })
      });
   },

   delete_type_author({ commit }, params) {
      return new Promise((resolve, reject) => {
         axios
            .delete(`${API_AUTHOR_TYPE}/delete-type-author`, { params })
            .then((resp) => {
               resolve(resp);
               commit("set_response_status", resp.data);
            })
            .catch((error) => reject(error));
      });
   },

   reset_terms(context) {
      context.commit('reset_terms')
   },

   reset_linked_terms(context) {
      context.commit('reset_linked_terms')
   },


}

const mutations = {
   set_term_items(state, params) {
      state.terms_items = params;
   },
   set_term_descriptions_items(state, params) {
      state.term_descriptions_items = params;
   },
   set_linked_terms_items(state, params) {
      state.linked_terms_items = params;
   },

   set_linked_terms_details_item(state, params) {
      state.linked_terms_details_item = params;
   },
   set_linked_provider_terms_items(state, params) {
      state.linked_provider_terms_items = params;
   },
   set_professional_terms_items(state, params) {
      state.professional_terms_items = params;
   },
   set_services_terms_items(state, params) {
      state.services_terms_items = params;
   },
   set_category_of_services_terms_items(state, params) {
      state.category_services_terms_items = params;
   },
   set_services_category_items(state, params) {
      state.services_category_terms_items = params;
   },
   set_type_terms_items(state, params) {
      state.type_terms_items = params;
   },
   set_type_dates_items(state, params) {
      state.type_dates_items = params;
   },

   set_html_tags_items(state, params) {
      state.html_tags_items = params;
   },

   set_articles_items(state, params) {
      state.articles_items = params;
   },
   set_response_item(state, params) {
      state.response_item = params;
   },
   set_response_status(state, params) {
      state.response_status = params;
   },
   set_notes_terms_items(state, params) {
      state.notes_terms_items = params;
   },
   set_type_authors_items(state, params) {
      state.type_author = params;
   },
   set_term_connections_items(state, params) {
      state.term_connections = params;
   },
   set_show_value(state, params) {
      state.show_value = params
   },

   reset_terms(state) {
      state.get_terms_items = [];
   },

   reset_linked_terms(state) {
      state.linked_terms_items = [];
   },
}

export default {
   namespaced: true,
   state,
   getters,
   actions,
   mutations,
};
