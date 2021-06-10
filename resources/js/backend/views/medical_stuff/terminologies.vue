<style scoped>
.link-term-disabled {
  background-color: #f2f2f2;
  cursor: no-drop;
}
.link-term-disabled .d-icon,
.link-term-disabled .d-span {
  color: #c8ced3 !important;
}

.link-article-disabled {
  background-color: #f2f2f2;
  cursor: no-drop;
}
.link-article-disabled .d-icon,
.link-article-disabled .d-span {
  color: #c8ced3 !important;
}
.no-pointer {
  cursor: not-allowed;
}
</style>
<template>
  <div class="animated fadeIn">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <v-col cols="12" sm="10" md="10">
            <h4 class="card-title">
              {{ $t("label.categorized_terms") }}
              <small class="text-muted text-capitalize">
                {{ $t("label.terminilogies") }}
              </small>
            </h4>
          </v-col>
          <v-col cols="12" sm="2" md="2">
            <v-btn color="success" block tile @click="onAdd">
              <i class="fas fa-notes-medical"></i>
              &nbsp;{{ $t("button.new") }} {{ $t("label.terminilogy") }}
            </v-btn>
          </v-col>
        </div>

        <div class="row mt-2" v-if="isSearchItem">
          <TermSearchItem
            :search_items="searchItems"
            :parent="this"
            :form="form"
            @search-success="advance_search_success"
          />
        </div>

        <div class="row mt-4 mb-0">
          <div class="col-md-8">
            <b-form inline>
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="showEntriesOpt"
                v-model="perPage"
              />
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("label.entries") }}
              </label>
              <b-input-group-prepend>
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      title="Filter by no Translations."
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      @click="on_reset_search"
                      style="height: 33px; min-width: 64px; padding: 0 16px"
                    >
                      <v-icon :size="18"> mdi-filter-menu </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item
                        v-for="(option, index) in sortTranslationOptions"
                        :key="index"
                      >
                        <v-list-item-content
                          @click="sort_by_language(option.value)"
                        >
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>

              <b-input-group-prepend class="ml-2">
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      title="Sort By"
                      @click="on_reset_search"
                      depressed
                      style="height: 33px; min-width: 64px; padding: 0 16px"
                    >
                      <v-icon :size="18"> mdi-sort </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item
                        v-for="(option, index) in sortingTermsOptions"
                        :key="index"
                      >
                        <v-list-item-content
                          @click="sort_by_terms(option.value)"
                        >
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>
            </b-form>
          </div>
          <div class="col-md-4">
            <div class="">
              <b-form inline @submit.prevent="on_search_submit">
                <el-input
                  prefix-icon="el-icon-search"
                  v-model="filter"
                  :disabled="showAdvanceSearch"
                  autocomplete="off"
                  @keyup="on_search_key"
                  :placeholder="$t('type_and_enter')"
                  clearable
                >
                  <el-button
                    slot="append"
                    :disabled="filter.length > 0 || isAdvanceSearch"
                    type="primary"
                    @click="on_advance_search"
                  >
                    <b-spinner
                      v-if="isAdvanceSearch"
                      small
                      label="Small Spinner"
                    />
                    <i v-else class="el-icon-caret-bottom" />
                  </el-button>
                </el-input>
              </b-form>
            </div>
            <AdvanceSearchTerms
              v-if="showAdvanceSearch"
              :parent="this"
              @search-items="advance_search_term_success"
            />
          </div>
        </div>

        <div class="row mb-0">
          <div class="col-md-12 mb-0">
            <b-overlay
              id="overlay-background"
              :show="isLinked"
              :variant="'light'"
              opacity="0.85"
              :blur="'1px'"
              rounded="sm"
            >
              <b-table
                class="mb-0"
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>
                <template v-slot:cell(term_name)="data">
                  <span>
                    <ul class="list-unstyled pl-0">
                      <b-media tag="li">
                        <template v-if="data.item.img_url" #aside>
                          <b-img
                            :src="data.item.img_url"
                            alt="placeholder"
                            style="width: 50px; height: 30px"
                          />
                        </template>
                        <a
                          :href="data.item.route_show"
                          class="mt-0 mb-1 font-weight-bold text-capitalize"
                          v-html="data.item.term_name"
                        />
                      </b-media>
                    </ul>
                  </span>
                </template>

                <template v-slot:cell(term_type_name)="data">
                  <div
                    v-for="termType in data.item.has_term_types"
                    :key="termType.id"
                  >
                    <span
                      class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="view_item_name(termType, 'type')"
                    />
                  </div>
                </template>

                <template v-slot:cell(description_name)="data">
                  <div
                    v-for="spec in data.item.has_specializations"
                    :key="spec.id"
                  >
                    <span
                      class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="view_item_name(spec, 'spec')"
                    />
                  </div>
                </template>

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="#c8ced3"
                        small
                        v-bind="attrs"
                        v-on="on"
                        :disabled="data.item.is_loading"
                        @click="on_remove_advace_search"
                      >
                        <span v-if="data.item.is_loading" class="text-center">
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="red"
                            indeterminate
                          >
                          </v-progress-circular>
                        </span>
                        <span v-else>{{ $t("button.more_actions") }}</span>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item
                        link
                        v-for="action in actions"
                        :key="action.value"
                        @click="on_action(data.item, action.value)"
                        :class="{
                          'link-term-disabled':
                            !data.item.has_term_types &&
                            action.value === 'term',
                          'link-article-disabled':
                            !data.item.has_term_types &&
                            action.value === 'article',
                        }"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                            :class="{
                              'd-icon':
                                action.value === 'article' ||
                                action.value === 'term',
                            }"
                          />
                          <span
                            v-html="action.title"
                            :class="{
                              'd-span':
                                action.value === 'article' ||
                                action.value === 'term',
                            }"
                          />
                        </v-list-item-title>
                      </v-list-item>
                      <v-spacer></v-spacer>

                      <v-list-item link @click="on_delete(data.item)">
                        <v-list-item-title>
                          <b-icon icon="trash-fill" variant="danger" />

                          <span>
                            {{ $t("button.delete") }}
                          </span>
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>
        </div>

        <div class="row mt-0" v-if="total_rows > 0">
          <div class="col-md-6">
            <span>
              {{ $t("showing") }}
              <strong v-text="showing.from" />
              {{ $t("label.to") }}
              <strong v-text="showing.to" />
              of
              <strong v-text="showing.total" />
              {{ $t("label.entries") }}
            </span>
          </div>
          <div class="col-md-6">
            <b-pagination
              v-model="currentPage"
              :total-rows="total_rows"
              :per-page="perPage"
              align="right"
            />
          </div>
        </div>

        <TermNoteModal
          v-if="Object.values(itemSelected).length > 0"
          :itemSelected="itemSelected"
          :parent="this"
          @done-success="load_items"
          @close-modal="close_modal"
        />
        <CreateMedicalTermModal
          v-if="isCreatedTerm"
          :parent="this"
          @done-success="create_success"
          @close-modal="close_modal"
        />
        <LinkTerminologies
          v-if="Object.values(itemSelected).length > 0"
          :parent="this"
          :items="items"
          @link-success="link_success"
          @close-modal="close_modal"
        />
        <LinkArticles
          v-if="itemSelected"
          :id="itemSelected.id"
          :parent="this"
          @link-success="link_success"
          @close-modal="close_modal"
        />
        <LinkBrand :parent="this" @done-success="load_items" />
        <CreateBrand :parent="this" @done-success="create_brand_success" />
        <CreateIcon
          :parent="this"
          @done-success="create_icon_success"
          @close-modal="close_modal"
        />
        <ShowIcons
          :parent="this"
          @done-success="create_icon_success"
          @close-modal="close_modal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import MedicalMixin from "./mixins/medicalMixin";
import TermMixin from "./mixins/termMixin";

// Modals
import CreateMedicalTermModal from "./modals/create-term.modal";
import LinkTerminologies from "./modals/link/link-terms.modal";
import LinkArticles from "./modals/link/link-articles.modal";
import LinkBrand from "./modals/link/link-brand.modal";
import CreateIcon from "./modals/create-icon.modal";
import ShowIcons from "./modals/show-icons.modal";
import AdvanceSearchTerms from "./snippets/advance_search_terms";
import CreateBrand from "./../includes/brands/CreateBrandComponent";
import TermNoteModal from "./modals/term-note.modal";

// snippets
import TermSearchItem from "./snippets/advance_search/term_search_item";

export default {
  mixins: [MedicalMixin, TermMixin],

  components: {
    CreateMedicalTermModal,
    CreateIcon,
    ShowIcons,
    LinkTerminologies,
    LinkArticles,
    LinkBrand,
    CreateBrand,
    AdvanceSearchTerms,
    TermSearchItem,
    TermNoteModal,
  },

  data() {
    return {
      isCreatedTerm: false,
      to_advance_search: false,
      isLoading: true,
      btnloading: false,
      isLinked: false,
      filterBrand: "",
      filterArticle: "",
      sortbyTerm: "",
      form: {},
      searchItems: {},
      medtermId: [],
      linkedBrands: [],
      linkedMedicalTerm: [],
      linkedMedicalArticles: [],
      termIcons: [],
      newSpecializationCategories: [],
      items: [],

      show_rows_total: 10,

      formData: new FormData(),

      actions: [
        {
          value: "edit",
          title: this.$t("button.edit"),
          icon: "pencil-square",
          variant: "success",
        },
        {
          value: "add_notes",
          title: this.$t("table.add_notes"),
          icon: "clipboard-plus",
          variant: "primary",
        },
        {
          value: "brand",
          title: this.$t("label.link_to_brand"),
          icon: "link45deg",
          variant: "primary",
        },
        {
          value: "term",
          title: "Link to " + this.$t("label.terminilogies"),
          icon: "link45deg",
          variant: "primary",
        },
        {
          value: "article",
          title: "Link to " + this.$t("label.articles"),
          icon: "link45deg",
          variant: "primary",
        },
      ],

      tableHeaders: [
        {
          key: "term_name",
          label: this.$t("label.terminilogy"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "term_type_name",
          label: this.$t("label.type_of_term"),
          thClass: "text-left",
          thStyle: { width: "25%" },
        },
        {
          key: "description_name",
          label: this.$t("label.specializations"),
          thClass: "text-left",
          thStyle: { width: "25%" },
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          tdClass: "text-center",
        },
      ],

      sortingTermsOptions: [
        { value: "all", text: "All" },
        { value: "least_lt", text: "Least number of linked terms" },
        { value: "most_lt", text: "Most number of linked terms" },
        { value: "least_la", text: "Least number of linked articles" },
        { value: "most_la", text: "Most number of linked articles" },
        { value: "least_tt", text: "Least number of type of terms" },
        { value: "most_tt", text: "Most number of type of terms" },
        { value: "least_spec", text: "Least number of specializations" },
        { value: "most_spec", text: "Most number of specializations" },
      ],

      sortTranslationOptions: [
        { value: "all", text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
        { value: "no-tt", text: "No Type of Terms" },
        { value: "no-spec", text: "No Specializations" },
        { value: "no-la", text: "No Linked Articles" },
        { value: "no-lt", text: "No Linked Terms" },
      ],

      modal: {
        createBrand: {
          name: "brand_add",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "content_add_new_button",
            loading: false,
          },
        },
      },
    };
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isLinked = true;
        this.currentPage = 1;
        this.load_items();
      }
    },

    currentPage(value) {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.load_items();
    },

    perPage() {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.load_items();
    },
  },

  created() {
    this.loadBrands();
  },

  mounted() {
    this.load_items();
  },

  computed: {
    ...mapGetters("categ_terms", {
      status: "get_response_status",
      linkedTerms: "get_linked_terms_items",
      providerTypeTerms: "get_linked_provider_terms_items",
    }),

    ...mapGetters("brand", {
      itemBrands: "brands",
    }),

    show_page_total() {
      return this.currentPage;
    },

    isSearchItem() {
      if (_.isEmpty(this.searchItems)) {
        return false;
      }
      let k = false;

      Object.values(this.searchItems).forEach((ele) => {
        if (ele && ele.length > 0) {
          k = true;
        }
      });

      return k;
    },
  },

  methods: {
    ...mapActions("categ_terms", [
      "get_terms",
      "delete_term",
      "get_linked_terms",
      "get_linked_term_id",
      "get_linked_provider_terms",
      "get_advance_search",
      "get_term_icons",
    ]),

    ...mapActions("brand", ["get_brands"]),

    load_items() {
      let params = {
        ...this.termDefaultParams,
        ...this.set_params_detail(),
      };

      axios
        .get("/api/terms", { params })
        .then((resp) => {
          let data = resp.data;
          this.total_rows = data.total;
          this.items = data.data;

          this.showing.to = data.to;
          this.showing.from = data.from;
          this.showing.total = data.total;
        })
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
    },

    on_advance_search() {
      this.isAdvanceSearch = true;

      setTimeout(() => {
        this.$nextTick(() => {
          this.showAdvanceSearch = true;
          this.$root.showAdvanceSearch = true;
        });
        this.isAdvanceSearch = false;
      }, 1000);
    },

    advance_search_term_success(form, data) {
      this.searchItems = form;

      this.total_rows = data.total;
      this.items = data.data;

      this.showing.to = data.to;
      this.showing.from = data.from;
      this.showing.total = data.total;

      this.isLinked = false;
      this.isAdvanceSearch = false;
      this.showAdvanceSearch = false;
    },

    on_search_submit(value) {
      this.isLinked = true;
      this.currentPage = 1;
      this.load_items();
    },

    on_search_key() {
      this.searchItems = {};
      this.on_remove_advace_search();
    },

    advance_search_success(data) {
      this.total_rows = data.total;
      this.items = data.data;

      this.showing.to = data.to;
      this.showing.from = data.from;
      this.showing.total = data.total;

      this.isLinked = false;
      this.isAdvanceSearch = false;
      this.showAdvanceSearch = false;
    },

    close_modal() {
      this.isCreatedTerm = false;
      this.itemSelected = "";
      this.mtForm.reset();
      this.articleForm.reset();
    },

    on_reset_search() {
      this.filter = "";
      this.on_remove_advace_search();
    },

    sort_by_language(value) {
      this.isLinked = true;
      this.sortbyTerm = null;
      this.sortbyLang = value;
      this.currentPage = 1;
      this.searchItems = {};
      this.load_items();
      this.on_remove_advace_search();
    },

    sort_by_terms(value) {
      this.isLinked = true;
      this.sortbyLang = null;
      this.currentPage = 1;
      this.sortbyTerm = value;
      this.searchItems = {};
      this.load_items();
      this.on_remove_advace_search();
    },

    set_params_detail() {
      return {
        filter: this.filter,
        sortbyLang: this.sortbyLang,
        sortbyTerm: this.sortbyTerm,
        perPage: this.perPage,
        page: this.currentPage,
        to_advance_search: this.to_advance_search,
        type_terms: this.set_item_search(this.searchItems.type_terms),
        specializations: this.set_item_search(this.searchItems.specializations),
        terms: this.set_item_search(this.searchItems.terms),
        articles: this.set_item_search(this.searchItems.articles),
      };
    },

    filterbyLang(value) {
      this.isLoading = true;
      this.sortbyLang = value;
      this.sortbyTerm = null;
      this.load_items();
      this.on_remove_advace_search();
    },

    on_action(item, title) {
      let linkMsg = this.$t("label.linking");
      let linkErrMsg = this.$t("errors.linking_error");

      this.$nextTick(() => {
        this.itemSelected = item;
      });

      switch (title) {
        case "show":
          this.onShow(item);
          break;
        case "edit":
          this.on_edit(item);
          break;

        case "brand":
          this.on_linked_brand(item);
          break;
        case "term":
          if (!item.has_term_types) {
            linkMsg += ` ${this.$t("label.terminilogies")} ${linkErrMsg}`;
            this.errorToast(this.$t("errors.error"), linkMsg);
            return;
          }

          this.isLinked = true;
          this.on_linked_term(item);
          break;
        case "article":
          if (!item.has_term_types) {
            linkMsg += ` ${this.$t("label.articles")} ${linkErrMsg}`;
            this.errorToast(this.$t("errors.error"), linkMsg);
            return;
          }

          this.isLinked = true;
          this.on_linked_article(item);
          break;

        case "add_notes":
          this.on_add_note(item);
          // console.log(item);
          break;
      }
    },

    on_add_note(item) {
      this.mtForm.id = item.id;
      this.isLinked = true;

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("term-note-modal");
      }, 1000);
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = this.$t("label.terminilogies");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          this.delete_term(params)
            .then((_) => {
              this.deleteToast(baseName, records);
              this.items.splice(
                this.getRemoveItemIndex(this.items, item.id),
                1
              );
            })
            .catch((error) => {
              console.log(error);
            });
        }
      });
    },

    onShow(item) {
      this.mtForm.reset();
      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("show-medical-term-modal");
    },

    onAdd() {
      this.on_remove_advace_search();
      this.isCreatedTerm = true;
      this.isLinked = true;
      this.mtForm.reset();
      
      setTimeout(() => {
        this.isLinked = false;
        this.btnloading = false;
        this.mtForm.action = "Add";
        this.$bvModal.show("term-modal");
      }, 1000);
    },

    create_success(item) {
      const records = this.$t("label.terminilogies");
      if (this.mtForm.action === "Add") {
        this.storeToast(item.term_name, records);
      } else {
        this.updateToast(item.id, records);
      }
      this.mtForm.reset();
      this.itemSelected = "";
      this.isCreatedTerm = false;
      this.load_items();
    },

    link_success() {
      this.load_items();
    },

    create_brand_success() {
      this.loadBrands();
      this.$bvModal.show("link-brand-modal");
    },

    create_icon_success(item) {
      if (this.iconForm.action === "Add") {
        this.makeToast(
          "success",
          this.$t("new_record_created"),
          `${this.$t("created_message_icon")} ${this.iconForm.term_name}`
        );
      } else {
        this.updateToast(item.id, records);
      }
      this.mtForm.reset();
      this.iconForm.reset();
      this.load_items();
    },

    on_linked_article(item) {
      // this.form.id = item.id;
      this.mtForm.id = item.id;
      this.itemSelected = item;

      this.linkedMedicalArticles = [];
      if (item.medical_articles.length > 0) {
        item.medical_articles.forEach((ma) => {
          this.linkedMedicalArticles.push(ma.id);
        });
      }

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("link-to-terminology-modal");
      }, 1000);
    },

    onAddIcon(item) {
      this.mtForm.id = item.id;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };

      this.get_linked_provider_terms(params).then((_) => {
        this.iconForm.term_name = item.base_name;
        this.iconForm.action = "Add";
        this.iconForm.term = item.id;
        this.$bvModal.show("icon-modal");
      });
    },

    onShowIcon(item) {
      this.mtForm.id = item.id;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        id: item.id,
      };

      this.get_term_icons(params).then((data) => {
        if (data.length == 0) {
          this.errorToast(
            `${this.$t("errors.error")}!`,
            this.$t("errors.no_existing_icon")
          );
          this.close_modal();
          return;
        }
        this.termName = item.base_name;
        this.termIcons = data;
        this.$bvModal.show("icon-show-modal");
      });
    },

    on_linked_brand(item) {
      this.mtForm.id = item.id;
      this.brandForm.linkedType = "terminology";
      this.brandForm.brands = item.brands;

      this.$bvModal.show("link-brand-modal");
    },

    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {});
    },

    on_linked_term(item) {
      this.linkedMedicalTerm = [];
      this.mtForm.id = item.id;
      // this.itemSelected = item;

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        medical_terms: item.medical_terms,
      };

      this.get_linked_term_id(params).then((resp) => {
        let data = resp.data;
        data.forEach((id) => {
          this.linkedMedicalTerm.push(id);
        });
        this.isLinked = false;
        this.$bvModal.show("link-term-modal");
      });
    },

    on_edit(item) {
      this.mtForm.reset();
      this.isCreatedTerm = true;

      this.language = item.base_language;
      this.mtForm.id = item.id;
      this.mtForm.name = item.base_name;
      this.mtForm.action = "Update";

      this.mtForm.term_types = this.get_item_array(item.has_term_types, "type");

      this.mtForm.specializations = this.get_item_array(
        item.has_specializations,
        "spec"
      );

      this.mtForm.file = item.img_url;
      this.mtForm.icon = item.icon_url;

      this.isLinked = true;
      setInterval(() => {
        this.isLinked = false;

        // Open Modal
        this.$bvModal.show("term-modal");
      }, 1000);
    },

    on_remove_advace_search() {
      this.showAdvanceSearch = false;
      this.isAdvanceSearch = false;
      this.to_advance_search = false;
    },
  },
};
</script>
