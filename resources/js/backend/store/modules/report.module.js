import axios from "axios";

import { API_REPORTS } from "./../../common/API.service";

// Default State
const getDefaultState = () => {
	return {
		insight_item: {}
	};
};

// State
const state = getDefaultState();

const getters = {
	get_insight_items: (state) => state.insight_item,

	get_response_status: (state) => state.response_status,
};

const actions = {
	async get_insights({ commit }, params) {
		try {
			const resp = await axios.get(`${API_REPORTS}/insights`, { params });
			commit("set_insights", resp.data);
		} catch (error) {
			console.log(error);
		}
	},

	async get_terms({ commit }, params) {
		try {
			const resp = await axios.get(`${API_REPORTS}/terms`, { params });
			commit("set_insights", resp.data);
		} catch (error) {
			console.log(error);
		}
	},

};

const mutations = {
	set_insights(state, params) {
		state.insight_item = params;
	},

	set_response_status(state, params) {
		state.response_status = params;
	},
};

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations,
};
