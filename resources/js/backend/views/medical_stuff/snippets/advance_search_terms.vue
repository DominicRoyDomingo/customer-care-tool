<style scoped>
.advace-search-card {
  position: absolute;
  right: 0;
  z-index: 999;
  width: 700px;
}
.pagination {
  display: flex;
  margin: 0.25rem 0.25rem 0;
}
.pagination button {
  flex-grow: 1;
}
.pagination button:hover {
  cursor: pointer;
}
</style>
<template>
  <div class="card animated fadeIn advace-search-card shadow mt-1">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary text-uppercase">
        <i class="fa fa-search text-info" aria-hidden="true"></i>
        {{ $t("advanced_search") }}
      </h6>
    </div>
    <b-overlay
      id="overlay-background"
      :show="isLoading"
      :variant="'light'"
      opacity="0.85"
      :blur="'1px'"
      rounded="sm"
    >
      <div class="card-body">
        <form @submit.prevent="on_submit_search">
          <div class="form-row">
            <div class="col">
              <v-select
                id="term_types"
                :options="termtypes"
                v-model="form.type_terms"
                @input="on_input"
                name="term_types"
                label="base_name"
                multiple
                required
                :placeholder="`Choose a ${$t('label.type_of_term')}`"
              >
                <template #option="{ on_select_term_type_name }">
                  <span v-html="on_select_term_type_name" />
                </template>
              </v-select>
            </div>
            <div class="col">
              <v-select
                id="specializations"
                :options="specializations"
                v-model="form.specializations"
                @input="on_input"
                name="specializations"
                label="base_name"
                multiple
                required
                :placeholder="`Choose a ${$t('table.specialization')}`"
              >
                <template #option="{ on_select_description_name }">
                  <span v-html="on_select_description_name" />
                </template>
              </v-select>
            </div>
          </div>
          <div class="form-row mb-0">
            <div class="col">
              <v-select
                id="terms"
                :options="articles"
                v-model="form.articles"
                @input="on_input"
                name="terms"
                label="base_name"
                multiple
                required
                :placeholder="`Choose a ${$t('linked_article')}`"
              >
                <template #option="{ article_title }">
                  <span v-html="article_title" />
                </template>
              </v-select>
            </div>
          </div>

          <div class="form-row">
            <div class="col">
              <v-select
                :options="paginated"
                v-model="form.terms"
                @input="on_input"
                label="base_name"
                multiple
                :placeholder="`Choose a ${$t('linked_term')}`"
              >
                <template #option="{ term_name }">
                  <li class="text-capitalize" v-html="term_name" />
                </template>

                <template #list-footer>
                  <li class="mt-2 mb-1">
                    <div class="row">
                      <div class="col-sm-6">
                        <b-button
                          size="sm"
                          variant="light"
                          block
                          :disabled="!hasPrevPage || isPrevLoading"
                          @click="onPrevTermPage"
                        >
                          <b-spinner
                            v-if="isPrevLoading"
                            small
                            label="Small Spinner"
                          />
                          <span v-else>Prev</span>
                        </b-button>
                      </div>
                      <div class="col-sm-6">
                        <b-button
                          size="sm"
                          :disabled="!hasNextPage || isNextLoading"
                          variant="light"
                          block
                          @click="onNextTermPage"
                        >
                          <b-spinner
                            small
                            v-if="isNextLoading"
                            label="Small Spinner"
                          />
                          <span v-else>Next</span>
                        </b-button>
                      </div>
                    </div>
                  </li>
                </template>
              </v-select>
            </div>
          </div>
          <div class="form-row">
            <div class="col mb-0">
              <b-button
                v-if="!is_data_empty"
                variant="info"
                pill
                size="sm"
                class="text-white mt-2"
                @click="form.reset()"
              >
                <b-icon icon="arrow-clockwise" />
                {{ $t("reset") }}
              </b-button>
              <span class="float-right">
                <b-button variant="light" pill @click="on_cancel" class="mr-1">
                  {{ $t("cancel") }}
                </b-button>
                <b-button
                  variant="primary"
                  pill
                  :disabled="is_data_empty || btnLoading"
                  class="text-white"
                  type="submit"
                >
                  <b-spinner v-if="btnLoading" small label="Small Spinner" />

                  <span v-else>{{ $t("table.search") }}</span>
                </b-button>
              </span>
            </div>
          </div>
        </form>
      </div>
    </b-overlay>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Form from "../../../helpers/form";

export default {
  props: ["parent"],

  data() {
    return {
      isDisable: true,
      isLoading: true,
      btnLoading: false,
      isNextLoading: false,
      isPrevLoading: false,
      specializations: [],
      terms: [],

      form: new Form({
        type_terms: "",
        specializations: "",
        terms: "",
        articles: "",
      }),

      params: {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      },

      currentPage: 1,
      perPage: 10,
      lastPage: 0,
      search: "",
    };
  },

  mounted() {
    this.load_items();
  },

  computed: {
    ...mapGetters("categ_terms", {
      // terms: "get_terms_items",
      articles: "get_articles_items",
      termtypes: "get_type_terms_items",
    }),

    is_data_empty() {
      let array = Object.values(this.form.data());
      let k = true;
      array.forEach((ele) => {
        if (ele && ele.length > 0) k = false;
      });
      return k;
    },

    filtered() {
      return this.terms.filter((term) => term.term_name.includes(this.search));
    },

    paginated() {
      return this.terms;
    },

    hasNextPage() {
      return this.currentPage !== this.lastPage;
    },

    hasPrevPage() {
      return this.currentPage > 1;
    },
  },

  methods: {
    ...mapActions("categ_terms", ["get_type_terms", "get_articles"]),

    onNextTermPage() {
      this.isNextLoading = true;
      this.currentPage += 1;
      this.load_items();
    },

    onPrevTermPage() {
      this.isPrevLoading = true;
      this.currentPage -= 1;
      this.load_items();
    },

    load_items() {
      let params = {
        ...this.params,
        perPage: 25,
        page: this.currentPage,
      };

      this.get_type_terms(params).then((_) => {});
      this.get_articles(params).then((_) => {});
      this.load_specialization_items(params);
      this.load_term_items(params);
    },

    load_term_items(params) {
      axios
        .get("/api/terms", { params })
        .then((response) => {
          let data = response.data;

          // Terms Like
          this.terms = data.data;
          this.lastPage = data.last_page;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
          this.isNextLoading = false;
          this.isPrevLoading = false;
        });
    },

    load_specialization_items(params) {
      axios.get("/api/jobs", { params }).then((resp) => {
        this.specializations = resp.data;
      });
    },

    on_submit_search() {
      this.parent.isLinked = true;
      this.btnLoading = true;

      let params = {
        ...this.params,
        perPage: this.perPage,
        page: this.currentPage,
        type_terms: this.parent.set_item_search(this.form.type_terms),
        specializations: this.parent.set_item_search(this.form.specializations),
        terms: this.parent.set_item_search(this.form.terms),
        articles: this.parent.set_item_search(this.form.articles),
        to_advance_search: true,
      };

      axios.get("/api/terms", { params }).then((resp) => {
        const data = resp.data;
        this.btnLoading = false;
        this.$emit("search-items", this.form.data(), data);
      });
    },

    on_cancel() {
      this.form.reset();
      this.parent.showAdvanceSearch = false;

      // setTimeout(() => {
      //   this.$nextTick(() => {
      //     // this.$isAdvanceSearch = true;
      //     this.$isAdvanceSearch = false;
      //   });
      //   // this.isAdvanceSearch = false;
      // }, 100);

      // this.$nextTick(() => {
      // this.$isAdvanceSearch = false;
      // });
    },

    on_input(value) {
      if (this.is_data_empty) {
        this.parent.isLinked = true;
        this.parent.load_items();
      }
    },
  },
};
</script>

