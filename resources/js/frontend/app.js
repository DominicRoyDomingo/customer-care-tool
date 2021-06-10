/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// import deps
import "babel-polyfill";
import "../bootstrap";
import "../plugins";
import Vue from "vue";
import vuetify from "./plugins/vuetify";
import i18n from "./../backend/i18n.js";

window.Vue = Vue;

Vue.component("master-layout", require("./layouts/MasterLayout.vue").default);

new Vue({
  vuetify,
  i18n,
}).$mount("#app");
