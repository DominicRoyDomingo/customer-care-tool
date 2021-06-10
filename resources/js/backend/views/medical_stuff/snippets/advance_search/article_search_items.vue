
<template>
  <div class="col-md-12">
    <h6 class="font-weight-bold">{{ $t("filter_by") }} :</h6>

    <b-button variant="light" pill class="mb-1" v-if="search_items.author">
      {{ $t("authors") }} :
      <span
        class="text-info text-capitalize"
        v-for="author in search_items.author"
        :key="author"
        v-html="author"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('author')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button variant="light" pill class="mb-1 mr-1" v-if="search_items.term">
      {{ $t("label.terminilogies") }} :
      <span
        class="text-info text-capitalize"
        v-for="term in search_items.term"
        :key="term"
        v-html="term"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('term')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="search_items.item_types"
    >
      {{ $t("type_of_publising_item") }} :
      <span
        class="text-info text-capitalize mr-1 text-underline"
        v-for="item_types in search_items.item_types"
        :key="item_types"
        v-html="item_types"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('item_types')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1 mr-1"
      v-if="search_items.type_dates.name"
    >
      {{ $t("type_of_publising_item") }} :
      <span
        class="text-info text-capitalize"
        v-html="search_items.type_dates.name"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('type_dates')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>
  </div>
</template>

<script>
import { search_articles } from "./../../mixins/query";
export default {
  props: ["search_items", "form", "parent"],

  methods: {
    on_close_search_item(type) {
      switch (type) {
        case "author":
          this.search_items.author = null;
          this.form.author = "";
          break;
        case "item_types":
          this.search_items.item_types = null;
          this.form.item_types = "";
          break;
        case "term":
          this.search_items.term = null;
          this.form.term = "";
          break;
        case "type_dates":
          this.search_items.type_dates.name = null;
          this.search_items.type_dates.startDate = null;
          this.search_items.type_dates.endDate = null;
          this.form.type_dates.name = "";
          break;
      }

      this.parent.isLinked = true;
      if (!this.parent.isSearchItem) {
        this.parent.searchItems = {};
        this.parent.search_params = {};
        this.parent.loadArticles();
      }

      let params = {
        ...this.set_params(),
      };
      search_articles(params)
        .then((data) => {
          this.$emit("search-success", data);
        })
        .finally(() => {
          this.parent.isLinked = false;
          this.parent.algolia.showSyncButton = false;
          this.btnLoading = false;
        });
    },

    set_params() {
      return {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        authors: this.parent.set_item_search(this.form.author),
        terms: this.parent.set_item_search(this.form.term),
        item_types: this.parent.set_item_search(this.form.item_types),
        type_dates: this.form.type_dates ? this.form.type_dates.id : "",
        from: this.form.type_dates
          ? this.search_items.type_dates.startDate
          : "",
        to: this.form.type_dates ? this.search_items.type_dates.endDate : "",
      };
    },
  },
};
</script>

<style>
.badge-close {
  margin-top: -10px;
  margin-right: -15px;
}
</style>