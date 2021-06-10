import axios from "axios";

import 	{ 	API_QUESTIONS,
			API_QUESTIONS_SHOW,
			API_QUESTIONS_UPDATE,
			API_QUESTIONS_DELETE,
			API_QUESTION_CHOICE,
			API_QUESTIONS_BRAND
		} from "./../../../common/API.service";

export default{
	getQuestions: async({commit}, params) => {
		return new Promise((resolve, reject) => {
			try {
			 	axios
		        .get(API_QUESTIONS, {params})
		        .then((resp) => {
		        	commit('setQuestionList', resp.data)
		        	commit('setQuestionListItems', resp.data.data)
		          	resolve(resp.data.data);
		        })
		        .catch((error) => reject(error));
		      	
		    } catch (error) {
		      	console.log(error);
		    }
	    });
	},

	storeQuestion({ commit }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .post(API_QUESTIONS, params)
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},

  	storeQuestionBrand({ commit }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .post((API_QUESTIONS_BRAND).replace("{id}",params.question_id), params.formData)
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},

  	setCurrentQuestion: async({commit}, params) => {
		try {
			const resp = await axios.get((API_QUESTIONS_SHOW).replace("{id}",params.id), {params});
	      	commit('setCurrentQuestion', resp.data)
	    } catch (error) {
	      	console.log(error);
	    }
	},

	updateQuestion({ commit }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .post((API_QUESTIONS_UPDATE).replace("{id}", params.id), params.formData)
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},

  	deleteQuestion({ commit, dispatch }, params) {
	    return new Promise((resolve, reject) => {
	      axios
	        .delete((API_QUESTIONS_DELETE).replace("{id}", params.id), {params})
	        .then((resp) => {
	          resolve(resp);
	        })
	        .catch((error) => reject(error));
	    });
  	},
	getChoices: async({commit}, params) => {
		try {
	      	const resp = await axios.get(API_QUESTION_CHOICE,{ params });
	      	commit('setChoices', resp.data.data)
	    } catch (error) {
	      	console.log(error);
	    }
	},
}