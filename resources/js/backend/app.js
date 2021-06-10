import "@coreui/coreui";
import Vue from "vue";
import $ from "jquery";
import _ from "lodash";

window.$ = window.jQuery = $;

import { BootstrapVue, IconsPlugin } from "bootstrap-vue";

// import "bootstrap-vue/dist/bootstrap-vue.css";
import "./plugins/progressbar.js";
import "./plugins/element-ui.js";
import "./plugins/loading.js";
import vuetify from "./plugins/vuetify";
import VueGoodTablePlugin from "vue-good-table";
import "vue-good-table/dist/vue-good-table.css";
import JsonExcel from "vue-json-excel";
import ScrollBar from '@morioh/v-perfect-scrollbar'
import CKEditor from '@ckeditor/ckeditor5-vue2';
import ElementUI from 'element-ui';
import { ElementTiptapPlugin } from 'element-tiptap';
// import ElementUI's styles
import 'element-ui/lib/theme-chalk/index.css';
// import this package's styles
import 'element-tiptap/lib/index.css';

import VueRouter from "vue-router";

import Sortable from 'sortablejs'

Vue.directive('sortable', {
  inserted: function (el, binding) {
    new Sortable(el, binding.value || {})
  },
  multiDrag: true, // Enable multi-drag
  selectedClass: 'selected', // The class applied to the selected items
  fallbackTolerance: 3, // So that we can select items on mobile
  animation: 150
})

// import Sortable from 'vue-sortable'

// // Vue.use(Sortable)

// Vue.directive('sortable', {
//   inserted: function (el, binding) {
//     new Sortable(el, binding.value || {})
//   }
// })

Vue.use(VueRouter);

// use ElementUI's plugin
Vue.use(ElementUI);
Vue.use(ElementTiptapPlugin, {
  // lang: "zh",
  // spellcheck: false,
});
Vue.use(CKEditor);
Vue.use(VueGoodTablePlugin);
Vue.component("downloadExcel", JsonExcel);
//Import Vue

// import BootstrapVue from "bootstrap-vue";
// import ElementUI from "element-ui";
// import VueTableDynamic from 'vue-table-dynamic'
// Vue.use(VueTableDynamic)
// Vue.use(ElementUI);
// Optionally install the BootstrapVue icon components plugin
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(ScrollBar);
Vue.prototype.$eventBus = new Vue()
import http from "./shared/http.js";

window.$http = http;
Vue.use(require("vue-moment"));

import "vuejs-datepicker";
import "jquery-confirm";
import "datatables";
import "select2";

window.Vue = Vue;

//Vue Select
import vSelect from "vue-select";

Vue.component("v-select", vSelect);

const user = document.head.querySelector('meta[name="user"]');
let org_id = document.head.querySelector('meta[name="org_id"]');
let brand = document.head.querySelector('meta[name="brand_id"]');

if (user) {
  Vue.prototype.$user = JSON.parse(user.getAttribute("content"));
  Vue.prototype.$org_id = JSON.parse(org_id.getAttribute("content"));
  Vue.prototype.$brand = JSON.parse(brand.getAttribute("content"));
  Vue.prototype.$userId = Vue.prototype.$user.id;
}


import store from "./store";
import i18n from "./i18n.js";

import "./filters";

//global registration
// import VueFormWizard from "vue-form-wizard";
// import "vue-form-wizard/dist/vue-form-wizard.min.css";
// Vue.use(VueFormWizard);

import wysiwyg from "vue-wysiwyg";

Vue.use(wysiwyg, {}); // config is optional. more below

// const files = require.context("./", true, /\.vue$/i);

// files.keys().map((key) =>
//   Vue.component(
//     key
//       .split("/")
//       .pop()
//       .split(".")[0],
//     files(key).default
//   )
// );

const files = require.context('./', true, /\.vue$/i, 'lazy').keys();

files.forEach(file => {
  Vue.component(file.split('/').pop().split('.')[0], () => import(`${file}`));
});


Vue.component("select2", () => import("./views/components/select2.vue"));

Vue.component("datepicker", () => import("vuejs-datepicker"));

//Profile Components
Vue.component(
  "client-engagements-modal",
  () => import("./views/profiles/modals/client-engagements.modal.vue")
);
Vue.component(
  "edit-client-engagement-modal",
  () => import("./views/profiles/modals/edit-client-engagement.modal.vue")
);

// jobs components
Vue.component("job-category", () => import("./views/jobs/category.vue"));
Vue.component("job-profession", () => import("./views/jobs/profession.vue"));
Vue.component(
  "job-description",
  () => import("./views/jobs/description.vue")
);

Vue.component(
  "activity-index",
  () => import("./views/actions/activity.vue")
);

// Engagement
Vue.component(
  "engagement-index",
  () => import("./views/actions/engagement.vue")
);

Vue.component(
  "administrative-index",
  () => import("./views/actions/administrative.vue")
);

Vue.component("brands-index", () => import("./views/brands/index.vue"));

Vue.component(
  "contact-types-index",
  () => import("./views/contact-types/index.vue")
);

Vue.component("profiles-index", () => import("./views/profiles/index.vue"), {
  props: ["loaded_brand"],
});

Vue.component(
  "locations-index",
  () => import("./views/locations/index.vue")
);

Vue.component(
  "statistics-index",
  () => import("./views/statistics/index.vue")
);

Vue.component("profiles-show", () => import("./views/profiles/show.vue"));

// campaign mail
Vue.component("campaign-index", () => import("./views/campaign/index.vue"));
// campaign mail
Vue.component(
  "template-index",
  () => import("./views/email_template/index.vue")
);

// Workforce Management Start
Vue.component(
  "workforce-management-request-type-index",
  () => import("./views/workforce_management/RequestTypeComponent.vue")
);

Vue.component(
  "workforce-management-reasons-index",
  () => import("./views/workforce_management/ReasonsComponent.vue")
);

Vue.component(
  "workforce-management-color-coding-index",
  () => import("./views/workforce_management/ColorCodingComponent.vue")
);

Vue.component(
  "workforce-management-approval-settings-index",
  () => import("./views/workforce_management/ApprovalSettingsComponent.vue")
);

Vue.component(
  "workforce-management-default-groups-index",
  () => import("./views/workforce_management/DefaultGroupsComponent.vue")
);

Vue.component(
  "workforce-management-calendar-notes-index",
  () => import("./views/workforce_management/CalendarNotesComponent.vue")
);

Vue.component(
  "workforce-management-calendar-notes-type-index",
  () => import("./views/workforce_management/CalendarNotesTypeComponent.vue")
);

// Workforce Management End

Vue.component(
  "organization-categories",
  () => import("./views/OrganizationCategoriesComponent.vue")
);

Vue.component(
  "document-category",
  () => import("./views/attachments/category.vue")
);

Vue.component("document-type", () => import("./views/attachments/type.vue"));

Vue.component(
  "service-type",
  () => import("./views/ServiceTypeComponent.vue")
);
Vue.component(
  "provider-type",
  () => import("./views/ProviderTypeComponent.vue")
);
Vue.component(
  "physical-code-type",
  () => import("./views/PhysicalCodeTypeComponent.vue")
);
Vue.component(
  "information-type",
  () => import("./views/InformationTypeComponent.vue")
);
Vue.component(
  "provider-services",
  () => import("./views/ProviderServicesComponent.vue")
);

Vue.component(
  "provider-groups-and-providers",
  () => import("./views/ProviderGroupsAndProvidersComponent.vue")
);

Vue.component(
  "provider-groups",
  () => import("./views/ProviderGroupsComponent.vue")
);

Vue.component(
  "platform-type-component",
  () => import("./views/PlatformTypeComponent.vue")
);

Vue.component(
  "platform-item-component",
  () => import("./views/PlatformItemComponent.vue")
);
Vue.component("tags-component", () => import("./views/TagsComponent.vue"));
Vue.component(
  "category-component",
  () => import("./views/CategoryComponent.vue")
);
Vue.component("dates-component", () => import("./views/DatesComponent.vue"));
Vue.component(
  "content-items-component",
  () => import("./views/ContentItemsComponent.vue")
);
Vue.component("items-component", () => import("./views/ItemsComponent.vue"));
Vue.component(
  "invite-user-component",
  () => import("./views/InviteUserComponent.vue")
);
Vue.component("items-component", () => import("./views/ItemsComponent.vue"));
Vue.component(
  "services-component",
  () => import("./views/ServicesComponent.vue")
);

Vue.component(
  "actors-component",
  () => import("./views/ActorComponent.vue")
);

Vue.component(
  "geolocalizations-component",
  () => import("./views/article_content_maker/GeolocalizationComponent.vue")
);

Vue.component(
  "geolocalizations-show-component",
  () => import("./views/article_content_maker/GeolocalizationShowComponent.vue")
);

Vue.component(
  "services-exclusive-component",
  () => import("./views/ServicesExclusiveComponent.vue")
);
Vue.component(
  "providers-component",
  () => import("./views/ProvidersComponent.vue")
);
Vue.component(
  "parameters-component",
  () => import("./views/ParametersComponent.vue")
);
Vue.component(
  "course-component",
  () => import("./views/course/course-type.vue")
);
Vue.component(
  "publishing-component",
  () => import("./views/PublishingComponent.vue")
);
// Vue.component('brand-component', () => import('./views/BrandsComponent.vue'));
Vue.component(
  "content-component",
  () => import("./views/ContentComponent.vue")
);
Vue.component(
  "publishing-history",
  () => import("./views/includes/publishing/history/HistoryComponent.vue")
);
Vue.component(
  "item-history",
  () => import("./views/includes/content-items/history/HistoryComponent.vue")
);

Vue.component(
  "content-history",
  () => import("./views/includes/content/history/HistoryComponent.vue")
);
Vue.component(
  "item-type-component",
  () => import("./views/ItemTypeComponent.vue")
);
Vue.component(
  "notification-component",
  () => import("./views/NotificationComponent.vue")
);
Vue.component(
  "create-organization-component",
  () => import("./views/AddOrganizationComponent.vue")
);
Vue.component(
  "create-brand-component",
  () => import("./views/AddBrandComponent.vue")
);
Vue.component(
  "account-component",
  () => import("./views/AccountComponent.vue")
);
// Vue.component(
//   "change-brand-component",
//   () => import("./views/ChangeBrandComponent.vue")
// );
Vue.component(
  "workspace-brand-component",
  () => import("./views/WorkspaceAndBrandComponent.vue")
);
// Vue.component(
//   "workspace-component",
//   () => import("./views/WorkspaceComponent.vue")
// );


Vue.component(
  "workspaces-component",
  () => import("./views/WorkspacesComponent.vue")
);


Vue.component(
  "result-list",
  () => import("./views/result/result-lists.vue")
);


Vue.component(
  "procedure-list",
  () => import("./views/Procedure/procedure-lists.vue")
);


Vue.component

// Categorized Term Components
import './views/medical_stuff';

// Questions Components
import './views/questions';

// Reports Components
import './views/Reports';

// Reports Components
import './views/questionnaire';

// Payment Components
import './views/payments';

import './views/algolia_permission';

import './views/algolia_class';


var Notification = require("./views/NotificationComponent.vue").default;

var CreateOrganization = require("./views/AddOrganizationComponent.vue")
  .default;

var ChangeBrand = require("./views/ChangeBrandComponent.vue")
  .default;
// Vue.component('publishing-item-component', require('./views/PublishingItemComponent.vue').default);

// snippets
Vue.component("local-select", () => import("./views/snippets/local.vue"));


const router = new VueRouter({ mode: "history" });

window.app = new Vue({
  vuetify,
  store,
  router,
  el: "#app-vue",
  i18n,
});

import "./../plugins/custom.js";

// window.console.clear();
