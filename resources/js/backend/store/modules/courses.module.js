import axios from "axios";
import { API_COURSES } from "./../../common/API.service";


const getDefaultState = () => {
   return {
      course_type_items: [],
      course_items: [],
      response_status: false
   };
};

const state = getDefaultState();


const getters = {
   course_types_items: (state) => state.course_types_items,
   get_response_status: (state) => state.response_status,
};

const actions = {
   async load_course_types({ commit }, params) {
      try {
         const resp = await axios.get(`${API_COURSES}`, { params });
         commit('set_course_types', resp.data)
      } catch (error) {
         console.log(error)
      }
   }
}
const mutations = {
   set_course_types(state, params) {
      state.terms_items = params;
   },
}




export default {
   namespaced: true,
   state,
   getters,
   actions,
   mutations,
};