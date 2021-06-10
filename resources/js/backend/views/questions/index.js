import Vue from "vue";

Vue.component("questions-choice", () => import("./choice/index.vue"));

//  Question types component
Vue.component("questions-question-types", () => import("./QuestionTypeComponent.vue"));

//  Question list component
Vue.component("questions-question-list", () => import("./QuestionListComponent.vue"));

