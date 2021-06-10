import axios from "axios";

import { API_PROFILE } from "./../../common/API.service";

function indexProfiles(profiles) {
  profiles.forEach(function(row, index) {
    row.profile_index = index;
  });

  return profiles;
}

// Default State
const getDefaultState = () => {
  return {
    profiles: [],
    profile: {},
    country_location_statistics: [],
    province_location_statistics: [],
    city_location_statistics: [],
    category_job_statistics: [],
    profession_job_statistics: [],
    specialization_job_statistics: [],
    tabular_location_statistics: [],
    tabular_job_statistics: [],
    detailed_location_stats: [],
    detailed_job_stats: [],
    location_statistics_status: false,
    responseStatus: false,
    response: {},
  };
};

const tabular_statistics = "/admin/statistics/tabular";

// State
const state = getDefaultState();

const getters = {
  profiles: (state) => state.profiles,
  profile: (state) => state.profile,
  country_location_statistics: (state) => state.country_location_statistics,
  city_location_statistics: (state) => state.city_location_statistics,
  category_job_statistics: (state) => state.category_job_statistics,
  profession_job_statistics: (state) => state.profession_job_statistics,
  specialization_job_statistics: (state) => state.specialization_job_statistics,
  tabular_location_statistics: (state) => state.tabular_location_statistics,
  tabular_job_statistics: (state) => state.tabular_job_statistics,
  detailed_location_stats: (state) => state.detailed_location_stats,
  detailed_job_stats: (state) => state.detailed_job_stats,
  get_location_statistics_status: (state) => state.location_statistics_status,
  get_response_status: (state) => state.responseStatus,
  get_response: (state) => state.response,
};

const actions = {
  on_recipient({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .get(API_PROFILE + "/load_filtered_profiles", { params })
        .then((resp) => {
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  async get_filtered_profiles({ commit }, params) {
    try {
      const resp = await axios.get(API_PROFILE + "/load_filtered_profiles", {
        params,
      });
      commit("set_profiles", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_profiles({ commit }, params) {
    try {
      const resp = await axios.get(API_PROFILE + "/load_profiles_under_brand", {
        params,
      });
      commit("set_profiles", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_profile({ commit }, params) {
    try {
      const resp = await axios.get(API_PROFILE + "/" + params.id, { params });
      commit("set_profile", resp.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_stats({ commit }, params) {
    try {
      const location_statistics = "/admin/statistics/graph/location";
      const country = await axios.get(location_statistics + "/country", {
        params,
      });
      commit("set_country_location_statistics", country.data);

      const province = await axios.get(location_statistics + "/province", {
        params,
      });
      commit("set_province_location_statistics", province.data);

      const city = await axios.get(location_statistics + "/city", {
        params,
      });
      commit("set_city_location_statistics", city.data);

      const job_statistics = "/admin/statistics/graph/job";
      const category = await axios.get(job_statistics + "/category", {
        params,
      });
      commit("set_category_job_statistics", category.data);

      const profession = await axios.get(job_statistics + "/profession", {
        params,
      });
      commit("set_profession_job_statistics", profession.data);

      const specialization = await axios.get(
        job_statistics + "/specialization",
        {
          params,
        }
      );
      commit("set_specialization_job_statistics", specialization.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_tabular_location_statistics({ commit }, params) {
    try {
      const location = await axios.get(tabular_statistics + "/location", {
        params,
      });
      commit("set_tabular_location_statistics", location.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_tabular_job_statistics({ commit }, params) {
    try {
      const job = await axios.get(tabular_statistics + "/job", {
        params,
      });
      commit("set_tabular_job_statistics", job.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_detailed_location_stats({ commit }, params) {
    try {
      const job = await axios.get(tabular_statistics + "/detail/location", {
        params,
      });
      commit("set_detailed_location_stats", job.data);
    } catch (error) {
      console.log(error);
    }
  },

  async get_detailed_job_stats({ commit }, params) {
    try {
      const job = await axios.get(tabular_statistics + "/detail/job", {
        params,
      });
      commit("set_detailed_job_stats", job.data);
    } catch (error) {
      console.log(error);
    }
  },

  post_profile({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE, params)
        .then((resp) => {
          commit("set_response", resp.data);
          resolve(resp);
        })
        .catch((error) => {
          console.log(error);
          reject(error);
        });
    });
  },

  add_profile({ commit }, params) {
    return new Promise((resolve, reject) => {
      commit("add_profile", params);
      resolve(params);
    });
  },

  update_profile({ commit }, params) {
    return new Promise((resolve, reject) => {
      commit("update_profile", params);
      resolve(params);
    });
  },

  remove_from_profiles_array({ commit }, params) {
    return new Promise((resolve, reject) => {
      commit("remove_from_profiles_array", params);
      resolve(params);
    });
  },

  delete_profile({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .delete(API_PROFILE, { params })
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  post_client_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE + params.action_route, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  delete_client_engagement({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE + params.action_route, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  link_to_brands({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE + params.action_route, params)
        .then((resp) => {
          commit("set_response", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  post_to_action_route({ commit }, params) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE + params.action_route, params)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },

  post_form_data_to_action_route({ commit }, formData) {
    return new Promise((resolve, reject) => {
      axios
        .post(API_PROFILE + formData.get("action_route"), formData)
        .then((resp) => {
          commit("set_response_status", resp.data);
          resolve(resp);
        })
        .catch((error) => reject(error));
    });
  },
};

const mutations = {
  set_profiles(state, params) {
    state.profiles = indexProfiles(params);
  },
  set_profile(state, params) {
    state.profile = params;
  },
  set_country_location_statistics(state, params) {
    state.country_location_statistics = params;
  },
  set_province_location_statistics(state, params) {
    state.province_location_statistics = params;
  },
  set_city_location_statistics(state, params) {
    state.city_location_statistics = params;
  },
  set_category_job_statistics(state, params) {
    state.category_job_statistics = params;
  },
  set_profession_job_statistics(state, params) {
    state.profession_job_statistics = params;
  },
  set_specialization_job_statistics(state, params) {
    state.specialization_job_statistics = params;
  },
  set_tabular_location_statistics(state, params) {
    state.tabular_location_statistics = params;
  },
  set_tabular_job_statistics(state, params) {
    state.tabular_job_statistics = params;
  },
  set_detailed_location_stats(state, params) {
    state.detailed_location_stats = params;
  },
  set_detailed_job_stats(state, params) {
    state.detailed_job_stats = params;
  },
  set_location_statistics_status(state, params) {
    state.location_statistics_status = params;
  },
  set_response_status(state, params) {
    state.responseStatus = params;
  },
  set_response(state, params) {
    state.response = params;
  },
  add_profile(state, params) {
    params.profile_index = state.profiles.length;
    state.profiles.push(params);
  },
  update_profile(state, params) {
    let index = params.profile_index;
    console.log("editing " + index);
    state.profiles[index] = params;
    console.log(state.profiles[index]);
  },
  remove_from_profiles_array(state, params) {
    let index = params.profile_index;
    state.profiles.splice(index, 1);
    state.profiles = indexProfiles(state.profiles);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
