import Vue from "vue";

Vue.component("term-type", () => import("./term_type.vue"));
Vue.component("type-date", () => import("./type_dates.vue"));
Vue.component("terminologies-list", () => import("./terminologies.vue"));
Vue.component("term-show", () => import("./terminology_show.vue"));
Vue.component("term-description", () => import("./term_description.vue"));
Vue.component("publishing-items", () => import("./publishing_item.vue"));
Vue.component("publishing-prefilledsection", () => import("./prefilledsection.vue"));
Vue.component("publishing-item-show", () => import("./publishing_item_show.vue"));
Vue.component("article-show", () => import("./article_show.vue"));
Vue.component("author-type-list", () => import("./author_type_show.vue"));
Vue.component("html-tags-priority-list", () => import("./html_tags.vue"));
Vue.component("publishing-type", () => import("./publishing_type.vue"));

