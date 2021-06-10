import axios from "axios";

import { API_CAMPAIGNS } from "../../common/API.service";

// Default State
const getDefaultState = () => {
  return {
    campaigns: [],
    campaignStatus: false,
    campaign: "",
    campaign_email: "",
  };
};

// State
const state = getDefaultState();

const getters = {
  campaign: (state) => state.campaign,

  campaign_items: (state) => state.campaigns,

  campaign_status: (state) => state.campaignStatus,

  campaign_email: (state) => state.campaign_email
};

const actions = {
  async get_campaigns({ commit }, params) {
    try {
      const resp = await axios.get(API_CAMPAIGNS, { params });
      commit("set_campaigns", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_campaign_email({ commit }, params) {
    try {
      const resp = await axios.get("/api/campaign_emails/" + params.id, { params });
      commit("set_campaign_email", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_campaign({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CAMPAIGNS + "/post_campaign", params)
        .then((resp) => {
          resolve(resp);
          commit("set_campaign", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  post_campaign_email({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CAMPAIGNS + "/post", params)
        .then((resp) => {
          resolve(resp);
          commit("set_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  delete_campaign({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_CAMPAIGNS + "/delete", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
  
  send_campaign_email({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CAMPAIGNS + "/sendCampaignEmail", params)
        .then((resp) => {
          resolve(resp);
          commit("set_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  send_email({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_CAMPAIGNS + "/sendEmail", params)
        .then((resp) => {
          resolve(resp);
          commit("set_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },

  remove_recipient({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_CAMPAIGNS + "/remove_recipient", { params })
        .then((resp) => {
          resolve(resp);
          commit("set_status", resp.data);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_campaign(state, params) {
    state.campaign = params;
  },

  set_campaigns(state, params) {
    state.campaigns = params;
  },

  set_status(state, params) {
    state.campaignStatus = params;
  },

  set_campaign_email(state, params){
    state.campaign_email = params;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
