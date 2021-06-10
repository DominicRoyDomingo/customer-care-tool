<style scoped>
.badge-close {
  margin-top: -10px;
  margin-right: -15px;
}
</style>
<template>
  <div class="col-md-12">
    <h6 class="font-weight-bold">{{ $t("filter_by") }} :</h6>

    <b-button
      variant="light"
      pill
      class="mb-1"
      v-if="search_items.type_terms && search_items.type_terms.length > 0"
    >
      {{ $t("label.term_types") }} :
      <span
        class="text-info text-capitalize mr-1"
        v-for="typeT in search_items.type_terms"
        :key="typeT.id"
        v-html="`${typeT.term_type_name}, `"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('type_terms')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1"
      v-if="
        search_items.specializations && search_items.specializations.length > 0
      "
    >
      {{ $t("label.specializations") }} :
      <span
        class="text-info text-capitalize"
        v-for="spec in search_items.specializations"
        :key="spec.id"
        v-html="`${spec.description_name}, `"
      />

      <b-badge
        variant="danger"
        @click="on_close_search_item('specializations')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1"
      v-if="search_items.terms && search_items.terms.length > 0"
    >
      {{ $t("label.terminilogies") }} :
      <span
        class="text-info text-capitalize"
        v-for="term in search_items.terms"
        :key="term.id"
        v-html="`${term.term_name}, `"
      />
      <b-badge
        variant="danger"
        @click="on_close_search_item('terms')"
        class="badge-close float-right"
        title="Close"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>

    <b-button
      variant="light"
      pill
      class="mb-1"
      v-if="search_items.articles && search_items.articles.length > 0"
    >
      {{ $t("label.articles") }} :
      <span
        class="text-info text-capitalize"
        v-for="art in search_items.articles"
        :key="art.id"
        v-html="`${art.article_title}, `"
      />

      <b-badge
        variant="danger"
        @click="on_close_search_item('articles')"
        title="Close"
        class="badge-close float-right"
      >
        <i class="fa fa-times" aria-hidden="true"></i>
      </b-badge>
    </b-button>
  </div>
</template>

<script>
export default {
  props: ["search_items", "form", "parent"],

  methods: {
    on_close_search_item(type) {
      this.parent.to_advance_search = true;
      switch (type) {
        case "type_terms":
          this.search_items.type_terms = "";
          break;

        case "specializations":
          this.search_items.specializations = "";
          break;

        case "terms":
          this.search_items.terms = "";
          break;

        case "articles":
          this.search_items.articles = "";
          break;
      }

      this.parent.isLinked = true;
      if (!this.parent.isSearchItem) {
        this.parent.searchItems = {};
        this.parent.load_items();
        return;
      }

      let params = {
        ...this.parent.termDefaultParams,
        ...this.parent.set_params_detail(),
      };

      axios.get("/api/terms", { params }).then((resp) => {
        const data = resp.data;
        this.$emit("search-success", data);
      });
    },
  },
};
</script>

<style>
</style>