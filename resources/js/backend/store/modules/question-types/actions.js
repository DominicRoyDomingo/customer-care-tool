import axios from "axios";

import { API_QUESTION_TYPES, API_QUESTION_TYPES_UPDATE, API_QUESTION_TYPES_DELETE, API_QUESTION_TYPES_SHOW } from "./../../../common/API.service";

export default{
	getQuestionTypes: async({commit}, params) => {
		try {
	      	const resp = await axios.get(API_QUESTION_TYPES,{ params });
	      	
	      	commit('setQuestionTypeList', resp.data)
	      	commit('tableSettings', resp.data)
	    } catch (error) {
	      	console.log(error);
	    }
	},

	storeQuestionType({ commit }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .post(API_QUESTION_TYPES, params)
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},

  	setCurrentQuestionType: async({commit}, params) => {
		try {
			const resp = await axios.get((API_QUESTION_TYPES_SHOW).replace("{id}",params.id), {params});
	      	commit('setCurrentQuestionType', resp.data)
	    } catch (error) {
	      	console.log(error);
	    }
	},

	updateQuestionType({ commit }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .post((API_QUESTION_TYPES_UPDATE).replace("{id}", params.id), params.formData)
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},

  	deleteQuestionType({ commit, dispatch }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete((API_QUESTION_TYPES_DELETE).replace("{id}", params.id), {params})
        .then((resp) => {
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
}