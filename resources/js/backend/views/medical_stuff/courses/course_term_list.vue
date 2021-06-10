<style  scoped>
.card-term {
  border-radius: 10px;
}
.card-term .card-header {
  border-top-right-radius: 10px;
  border-top-left-radius: 10px;
}
</style>
<template>
  <div class="">
    <div class="card">
      <div
        class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
      >
        <a
          href="javascript:;"
          class="text-capitalize text-primary font-weight-bold"
          v-html="headerTitle"
        />
        <a
          :href="`#collapseCardExample-${type}`"
          data-toggle="collapse"
          role="button"
          aria-expanded="true"
          :aria-controls="`collapseCardExample-${type}`"
          class="float-right"
        >
          <i class="fa fa-caret-down text-muted" aria-hidden="true"></i>
        </a>
      </div>
      <div class="collapse show" :id="`collapseCardExample-${type}`">
        <div class="card-body p-0">
          <div class="list-group">
            <a
              :href="term.route_show"
              class="list-group-item list-group-item-action border-right-0 border-bottom-0 border-left-0"
              v-for="term in course_terms"
              :key="term.id"
              v-html="term.term_name"
            />
          </div>
        </div>
      </div>
    </div>

    <div
      class="alert alert-secondary"
      role="alert"
      v-if="course_terms.length === 0"
    >
      <div class="text-center p-4">
        <h1 class="fa fa-link mt-3"></h1>
        <p>
          {{ $t("link_to_terminologies") }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: {
      type: Number,
    },
    parent: {
      type: Object,
    },
    course_terms: {
      type: Array,
    },
    headerTitle: {
      type: String,
    },
    type: {
      type: String,
    },
  },

  data() {
    return {
      isLoading: false,
      isLinked: false,
      termsId: [],
      terms: [],
      params: {
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
        locale: this.$i18n.locale,
      },
    };
  },

  mounted() {
    // if (this.course_terms && this.course_terms.length > 0) {
    //   this.termsId = this.course_terms.map((item) => {
    //     return item.id;
    //   });
    //   this.load_linked_terms();
    // }
  },
};
</script>

