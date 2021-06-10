<style scoped>
.popover {
  max-width: 100% !important; /* Max Width of the popover (depending on the container!) */
}
</style>
<template>
  <b-popover target="advanced-search-show" triggers="click " placement="top">
    <template #title>
      <h5 class="mb-0 p-1">
        <i class="fa fa-search" aria-hidden="true"></i>
        {{ $t("advanced_search") }}
      </h5>
    </template>

    <b-spinner v-if="parent.isAdvanceSearch" small label="Small Spinner" />

    <form
      class="p-2 mb-0"
      style="width: 600px !important"
      @submit.prevent="onSubmitSearch"
    >
      <div class="form-row mb-0">
        <div class="col">
          <v-select
            id="term_types"
            :options="termtypes"
            v-model="form.type_terms"
            @input="onInput"
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
            @input="onInput"
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
            :options="terms"
            v-model="form.terms"
            @input="onInput"
            name="terms"
            label="base_name"
            multiple
            required
            :placeholder="`Choose a ${$t('linked_term')}`"
          >
            <template #option="{ term_name }">
              <span v-html="term_name" />
            </template>
          </v-select>
        </div>
        <div class="col">
          <v-select
            id="terms"
            :options="articles"
            v-model="form.articles"
            @input="onInput"
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
      <div class="form-row mb-0">
        <div class="col">
          <span class="float-right">
            <b-button variant="light" pill @click="onCancel" class="mr-1">
              {{ $t("cancel") }}
            </b-button>
            <b-button
              variant="primary"
              pill
              :disabled="is_data_empty"
              class="text-white"
              type="submit"
            >
              {{ $t("table.search") }}
            </b-button>
          </span>
        </div>
      </div>
    </form>
  </b-popover>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Form from "./../../../helpers/form";

export default {
  props: ["parent"],

  data() {
    return {
      isDisable: true,
      specializations: [],
      terms: [],
      form: new Form({
        type_terms: "",
        specializations: "",
        terms: "",
        articles: "",
      }),
    };
  },

  created() {
    // console.log(this.parent.isAdvanceSearch);
    // if (this.parent.isAdvanceSearch) {
    //   // this.loadItems();
    // }
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
        if (ele && ele.length > 0) {
          k = false;
        }
      });

      return k;
    },
  },
  methods: {
    ...mapActions("categ_terms", [
      "get_type_terms",
      "get_terms",
      "get_articles",
      "get_advance_search",
    ]),

    set_params() {
      return {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
    },

    loadItems() {
      let params = {
        ...this.set_params(),
      };

      this.get_type_terms(params).then((_) => {});
      this.get_articles(params).then((_) => {});
      this.loadSpecializations();
      this.loadTerms();
    },

    onSubmitSearch() {
      this.parent.isLinked = true;
      this.$root.$emit("bv::hide::popover", "advanced-search-show");

      let params = {
        ...this.set_params(),
        type_terms: this.parent.set_item_search(this.form.type_terms),
        specializations: this.parent.set_item_search(this.form.specializations),
        terms: this.parent.set_item_search(this.form.terms),
        articles: this.parent.set_item_search(this.form.articles),
      };

      axios.get("/api/terms/advance-search", { params }).then((resp) => {
        let data = resp.data;
        this.$emit("search-items", this.form.data(), data);
      });
    },

    onCancel() {
      this.parent.isLinked = true;
      this.form.reset();
      this.$root.$emit("bv::hide::popover", "advanced-search-show");
      this.parent.loadItems();
    },

    loadTerms() {
      let params = {
        ...this.set_params(),
      };
      axios
        .get("/api/select/terms/all", { params })
        .then((response) => {
          this.terms = response.data;
          this.parent.isAdvanceSearch = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    loadSpecializations() {
      let params = {
        ...this.set_params(),
      };

      axios.get("/api/jobs", { params }).then((resp) => {
        this.specializations = resp.data;
      });
    },

    onInput() {
      if (this.is_data_empty) {
        this.parent.isLinked = true;
        this.parent.loadItems();
      }
    },
  },
};
</script>
