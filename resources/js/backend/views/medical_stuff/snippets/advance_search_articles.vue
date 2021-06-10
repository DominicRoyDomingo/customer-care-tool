<style scoped>
.advace-search-card {
  position: absolute;
  right: 0;
  z-index: 999;
  width: 700px;
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
                id="auhtors"
                :options="actors"
                v-model="form.author"
                name="auhtors"
                label="full_name"
                multiple
                required
                :placeholder="`Choose an ${$t('item_author_idea')}`"
              />
            </div>
            <div class="col">
              <v-select
                id="item_type"
                :options="itemTypes"
                v-model="form.item_types"
                @input="on_input"
                name="item_type"
                label="base_name"
                multiple
                required
                :placeholder="`Choose a ${$t('type_of_publising_item')}`"
              >
                <template #option="{ item_type_name }">
                  <span v-html="item_type_name" />
                </template>
              </v-select>
            </div>
          </div>

          <div class="form-row">
            <div class="col">
              <v-select
                id="term"
                name="term"
                :options="paginated"
                v-model="form.term"
                @input="on_input"
                @search="on_search_item_term"
                :filterable="false"
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
                          @click="on_paged_item('prev')"
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
                          @click="on_paged_item('next')"
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
            <div class="col">
              <v-select
                id="term"
                :options="typeDates"
                v-model="form.type_dates"
                @input="on_input"
                name="term"
                label="base_name"
                :placeholder="`Choose a ${$t('label.type_of_date')}`"
              >
                <template #option="{ type_date_name }">
                  <span v-html="type_date_name" />
                </template>
              </v-select>
            </div>
          </div>

          <div class="form-row animated fadeIn" v-if="form.type_dates">
            <div class="col">
              <DateRangePicker
                :dateModel="dateModel"
                :is_disabled="!form.type_dates"
                @date="on_get_date"
              />
            </div>
          </div>

          <div class="form-row">
            <div class="col mb-0">
              <b-button
                v-if="!is_field_empty"
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
                  :disabled="is_field_empty || btnLoading"
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
import * as moment from "moment/moment";

import Form from "../../../helpers/form";
import DateRangePicker from "../../Reports/snippets/date_range_picker.vue";
import { search_articles } from "./../mixins/query";
export default {
  components: {
    DateRangePicker,
  },

  props: ["parent"],

  data() {
    // first day of the week
    let startDate = this.local_string(moment().startOf("week"));

    // Last day of the week
    let endDate = this.local_string(moment().endOf("week"));

    return {
      isDisable: true,
      isLoading: true,
      btnLoading: false,

      actors: [],
      terms: [],
      itemTypes: [],

      dateModel: {
        startDate: startDate,
        endDate: endDate,
      },

      form: new Form({
        id: "",
        term: "",
        author: "",
        type_dates: "",
        item_types: "",
      }),

      params: {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      },

      isNextLoading: false,
      isPrevLoading: false,
      currentPage: 1,
      perPage: 25,
      lastPage: 0,

      search: "",
    };
  },

  computed: {
    // ...mapGetters("actor", {
    //   actors: "actors",
    // }),

    ...mapGetters("categ_terms", {
      typeDates: "get_type_dates_items",
    }),

    is_field_empty() {
      let array = Object.values(this.form.data());
      let k = true;
      array.forEach((ele) => {
        if (ele && ele.length > 0) k = false;
      });

      if (this.form.type_dates) {
        k = false;
      }

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

  mounted() {
    this.load_items();
  },

  methods: {
    // ...mapActions("categ_terms", ["reset_terms"]),

    on_submit_search() {
      this.parent.isLinked = true;
      this.btnLoading = true;

      let params = {
        ...this.params,
        authors: this.parent.set_item_search(this.form.author),
        terms: this.parent.set_item_search(this.form.term),
        item_types: this.parent.set_item_search(this.form.item_types),
        type_dates: this.form.type_dates ? this.form.type_dates.id : "",
        from: this.form.type_dates ? this.dateModel.startDate : "",
        to: this.form.type_dates ? this.dateModel.endDate : "",
      };

      search_articles(params)
        .then((data) => {
          this.$emit(
            "search-items",
            this.get_form_data(),
            data,
            this.form,
            params
          );
          this.parent.showAdvanceSearch = false;
        })
        .finally(() => {
          this.btnLoading = false;
        });
    },

    load_items() {
      let params = {
        ...this.params,
        perPage: this.perPage,
        page: this.currentPage,
      };

      this.load_item_types(params);
      this.load_term_items(params);
      this.load_actor_items(params);
    },

    on_paged_item(action) {
      // On page Action
      if (action === "next") {
        // next paginate
        this.isNextLoading = true;
        this.currentPage += 1;
      } else if (action === "prev") {
        // next paginate
        this.isPrevLoading = true;
        this.currentPage -= 1;
      }

      let params = {
        ...this.params,
        perPage: this.perPage,
        page: this.currentPage,
      };

      this.load_term_items(params);
    },

    on_get_date(date) {
      this.dateModel = date;
      console.log(date);
    },

    load_item_types(params) {
      axios.get("/api/select/item_types/all", { params }).then((resp) => {
        this.itemTypes = resp.data;
        // this.isLoading = false;
      });
    },

    load_actor_items(params) {
      this.actors = [];
      axios.get("/api/select/actors/all", { params }).then((resp) => {
        this.actors = resp.data;
      });
    },

    load_term_items(params) {
      axios
        .get("/api/terms", { params })
        .then((response) => {
          let data = response.data;

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

    on_cancel() {
      this.form.reset();
      this.parent.showAdvanceSearch = false;
    },

    on_input(value) {
      if (this.is_field_empty) {
        // this.parent.isLinked = true;
        // this.parent.loadItems();
      }
    },

    on_search_item_term(value) {
      let params = {
        ...this.params,
        perPage: this.perPage,
        page: this.currentPage,
        filter: value,
      };

      if (value.length > 2) {
        this.load_term_items(params);
      }
    },

    local_string(value) {
      const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
      };
      const d = new Date(value);
      return d.toLocaleString("en", options);
    },

    get_form_data() {
      return {
        type_dates: {
          name: this.form.type_dates
            ? this.form.type_dates.type_date_name
            : null,
          startDate: this.form.type_dates ? this.dateModel.startDate : null,
          endDate: this.form.type_dates ? this.dateModel.endDate : null,
        },
        item_types: this.form.item_types
          ? this.set_form_data(this.form.item_types, "item_types")
          : null,
        author: this.form.author
          ? this.set_form_data(this.form.author, "author")
          : null,
        term: this.form.term
          ? this.set_form_data(this.form.term, "term")
          : null,
      };
    },

    set_form_data(data, type) {
      if (data.length < 1) {
        return null;
      }

      let array = [];
      data.forEach((ele) => {
        if (type === "item_types") {
          array.push(ele.item_type_name);
        } else if (type === "author") {
          array.push(ele.full_name);
        } else if (type === "term") {
          array.push(ele.term_name);
        }
      });
      return array;
    },
  },
};
</script>

 