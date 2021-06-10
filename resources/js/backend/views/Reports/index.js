import Vue from "vue";

Vue.component("insight-index", () => import("./components/insights/index.vue"));
Vue.component("statistic-index", () => import("./components/statistic/index.vue"));
Vue.component("summary-index", () => import("./components/summary/index.vue"));
