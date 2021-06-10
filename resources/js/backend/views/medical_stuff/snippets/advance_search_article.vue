<style scoped>
.popover {
  max-width: 100% !important; /* Max Width of the popover (depending on the container!) */
}
</style>
<template>
  <b-popover target="advanced-search-show-article" triggers="click " placement="top">
    <template #title>
      <h5 class="mb-0 p-1">
        <i class="fa fa-search" aria-hidden="true"></i>
        {{ $t("advanced_search") }}
      </h5>
    </template>
    <form
      class="p-2 mb-0"
      style="width: 600px !important"
      @submit.prevent="onSubmitSearchForm"
    >
      <div class="form-row mb-0">
        <div class="col">
          <v-select
            id="auhtors"
            :options="parent.actors_list"
            v-model="form.authors"
            name="auhtors"
            label="full_name"
            multiple
            required
            :placeholder="`Choose an ${$t('item_author_idea')}`"
          >
          </v-select>
        </div>
        <div class="col">
          <v-select
            id="terms"
            :options="terms"
            v-model="form.terms"
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
      </div>
      <div class="form-row mb-0">
        <div class="col">
           <v-select
            id="type_of_date"
            :options="type_dates_list"
            v-model="form.type_dates"
            name="type_of_date"
            label="base_name"
            required
            :placeholder="`Choose a ${$t('label.type_of_date')}`"
            @input="checkDateType"
          >
          </v-select>
        </div>
      </div>
      <div class="form-row mb-0" v-if="form.is_date">
        <div class="col">
           <datepicker
            input-class="form-control bg-white"
            v-model="form.from"
            :format="`dd MMM yyyy`"
            :placeholder="`Date Start`"
          ></datepicker>
        </div>
        <div class="col">
           <datepicker
            input-class="form-control bg-white"
            v-model="form.to"
            :format="`dd MMM yyyy`"
            :placeholder="`Date End`"
          ></datepicker>
        </div>
      </div>
      <div class="form-row mb-0">
        <div class="col">
          <span class="float-right">
            <b-button variant="light" pill @click="onCancel" class="mr-1">
              {{ $t("cancel") }}
            </b-button>
            <b-button
              :disabled="is_data_empty"
              variant="primary"
              pill
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
  props: ["parent", "test"],

  data() {
    return {
      isDisable: true,
      actors_list: [],
      terms: [],
      form: new Form({
        type_dates: "",
        authors: "",
        terms: "",
        from: "",
        to: "",
        is_date: false,
      }),
    };
  },

  mounted() {
    this.loadItems();
  },

  watch:{
    // 'form.type_dates' : function( val ) {
    //   this.form.is_date = false;
    //   if( val !== null ) {
    //     this.form.is_date = true;
    //   }
    // }
  },  

  computed: {
    ...mapGetters("categ_terms", {
      // terms: "get_terms_items",
      articles: "get_articles_items",
      termtypes: "get_type_terms_items",
      type_dates_list: "get_type_dates_items",
    }),

    is_data_empty() {
      let array = Object.values(this.form.data());
      let k = true;
      array.forEach((ele) => {
        if (ele && ele.length > 0) {
          k = false;
        }
        if( this.form.type_dates !== '' && this.form.type_dates !== null ){
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
      "get_advance_search_article",
      "get_type_dates"
    ]),

    checkDateType() {
      this.form.is_date = false;
      if( this.form.type_dates !== null ) {
        this.form.is_date = true;
      }
      // this.onSubmitSearchInput();
    },

    onSubmitSearchInput() {
      this.onSubmitSearch();
    },
    
    onSubmitSearchForm() {
      this.onSubmitSearch();
      this.parent.isLinked = true;
      this.$root.$emit("bv::hide::popover", "advanced-search-show-article");
    },

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
      this.loadTerms();
      this.get_type_dates(params).then((_) => {});
    },

    onSubmitSearch() {
      // this.parent.isLinked = true;
      // this.$root.$emit("bv::hide::popover", "advanced-search-show");

      let params = {
        ...this.set_params(),
        type_dates: this.form.type_dates,
        authors: this.set_item_search(this.form.authors),
        terms: this.set_item_search(this.form.terms),
        from: this.form.from,
        to: this.form.to,
      };
      this.get_advance_search_article(params).then((_) => {
        this.parent.isLinked = false;
        this.parent.total_rows = this.parent.items.length;
        this.$emit("search-items", this.form.data());
      });
    },

    onCancel() {
      this.form.is_date = false;
      this.form.reset();
      this.parent.loadArticles();
      this.$root.$emit("bv::hide::popover", "advanced-search-show-article");
    },

    set_item_search(data) {
      if (!data) {
        return "";
      }
      return data.map((x) => parseInt(x.id));
    },

    loadTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terms = items;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    onReset() {
      for (let field in this.form.data()) {
        this[field] = [];
      }
    },
  },
};
</script>
