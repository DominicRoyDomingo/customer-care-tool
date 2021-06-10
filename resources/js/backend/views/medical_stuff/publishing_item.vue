<template>
  <div class="card border-0">
    <div class="card-body">
      <div class="row">
        <div class="col-md-10">
          <h4 class="card-title text-capitalize">
            {{ $t("software_for_publishing") }}
            <small class="text-muted text-capitalize">
              {{ $t("publishing_items") }}
            </small>
          </h4>
        </div>
        <div class="col-md-2">
          <v-btn
            color="success"
            class="float-right text-uppercase"
            tile
            @click="on_add"
          >
            <i class="fas fa-notes-medical"></i>
            &nbsp; {{ $t("button.new") }} {{ $t("title") }}
          </v-btn>
        </div>
      </div>
      <div class="row mt-2" v-if="isSearchItem">
        <ArticleSearchItems
          :form="form"
          :parent="this"
          :search_items="searchItems"
          @search-success="advance_search_success"
        />
      </div>
      <div class="row mt-3">
        <v-col cols="12" sm="8" md="8">
          <v-col
            class="caption font-weight-regular text--secondary text-right"
            style="display: flex; position: absolute; margin-left: -10px"
          >
            {{ $t("button.show") }}
            <b-form-select
              :options="showEntriesOpt"
              v-model="perPage"
              style="
                border-radius: 0;
                width: 13% !important;
                margin-top: -8px;
                margin-left: 5px;
                margin-right: 5px;
              "
            />{{ $t("label.entries") }}
            <b-input-group-prepend style="margin-left: 10px; margin-top: -10px">
              <v-menu offset-y :rounded="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    color="info"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    tile
                    depressed
                    @click="on_remove_advace_search"
                    style="
                      height: 33px;
                      min-width: 64px;
                      padding: 0 16px;
                      margin-right: 7px;
                      margin-top: 5px;
                    "
                  >
                    <v-icon :size="18"> mdi-filter-menu </v-icon>
                  </v-btn>
                </template>
                <v-list dense class="profile__row-menu">
                  <v-list-item-group color="primary">
                    <v-list-item
                      v-for="(option, index) in noTranslationsOptions"
                      :key="index"
                    >
                      <v-list-item-content
                        @click="on_sort_language(option.value)"
                      >
                        <v-list-item-title>
                          {{ option.text }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
              </v-menu>

              <v-btn
                v-show="this.algolia.showSyncButton"
                id="sync-to-algolia"
                color="warning"
                @click="syncToAlgolia()"
                block
                x-small
                depressed
                tile
                height="33"
                class="ml-3"
              >
                <span v-if="this.algolia.isSyncing" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  />
                </span>

                <span v-else>
                  {{ $t("button.sync_to_algolia") }}
                </span>
              </v-btn>
              <b-popover
                class="popover"
                target="sync-to-algolia"
                triggers="hover focus"
              >
                <template #title>
                  {{ $t("geolocalization_popover_queue") }}
                </template>
                <span class="publishing-item-popover-text">
                  {{ this.algolia.summary.new }}
                  {{ $t("courses_popover_new_courses") }}
                </span>
                <br />
                <span class="publishing-item-popover-text">
                  {{ this.algolia.summary.update }}
                  {{ $t("courses_popover_courses_to_be_updated") }}
                </span>
              </b-popover>
            </b-input-group-prepend>
          </v-col>
        </v-col>
        <v-col cols="12" sm="4" md="4">
          <b-form inline @submit.prevent="on_search_submit">
            <el-input
              prefix-icon="el-icon-search"
              v-model="filter"
              autocomplete="off"
              :disabled="showAdvanceSearch"
              :placeholder="$t('type_and_enter')"
              clearable
            >
              <el-button
                slot="append"
                :disabled="filter.length > 0 || isAdvanceSearch"
                type="primary"
                @click="on_advance_search"
              >
                <b-spinner v-if="isAdvanceSearch" small label="Small Spinner" />
                <i v-else class="el-icon-caret-bottom" />
              </el-button>
            </el-input>
          </b-form>
          <AdvanceSearchArticles
            v-if="showAdvanceSearch"
            :parent="this"
            @search-items="advance_search_item"
          />
        </v-col>
      </div>
      <div class="row">
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
              striped
              show-empty
              :empty-text="$t('no_record_found')"
              :fields="fields"
              :per-page="perPage"
              :busy="isLoading"
              :items="articles"
            >
              <template v-slot:table-busy>
                <div class="d-flex justify-content-center p-2">
                  <b-spinner label="Small Loading..."></b-spinner>
                </div>
              </template>

              <template v-slot:cell(article_title)="data">
                <a
                  :href="data.item.route_show"
                  class="mt-0 mb-1 font-weight-bold text-capitalize"
                  v-html="data.item.article_title"
                />
              </template>

              <template v-slot:cell(publishing_item_type_articles)="data">
                <div
                  v-for="item in data.item.publishing_item_type_articles"
                  :key="item.id"
                >
                  <span
                    class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                    style="font-size: 14px"
                    v-html="view_item_name(item, 'item_type')"
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
                        />
                      </span>
                      <span v-else>{{ $t("button.more_actions") }}</span>
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item
                      v-for="(action, index) in actions"
                      :key="index"
                      link
                      @click="on_action(data.item, action.value)"
                    >
                      <v-list-item-title>
                        <b-icon :icon="action.icon" :variant="action.variant" />
                        <span
                          :class="{
                            'text-primary': action.variant === 'primary',
                          }"
                          v-html="action.title"
                        />
                      </v-list-item-title>
                    </v-list-item>

                    <v-list-item
                      @click="on_link_course_term(data.item)"
                      v-if="data.item.has_course_types"
                      link
                    >
                      <v-list-item-title>
                        <b-icon icon="link45deg" variant="primary" />
                        <span
                          class="text-primary"
                          v-html="`${$t('link_course_terms')}`"
                        />
                      </v-list-item-title>
                    </v-list-item>
                    
                    <v-divider></v-divider>
                    <v-list-item @click="on_delete(data.item)" link>
                      <v-list-item-title>
                        <b-icon icon="trash-fill" variant="danger" />
                        <span
                          class="text-danger"
                          v-html="`${$t('button.delete')}`"
                        />
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
            {{ $t("to") }}
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

      <addNotesModal :parent="this"></addNotesModal>
      <CreateArticleModal
        v-if="isCreateModal"
        :parent="this"
        @done-success="create_success"
        @on-close="on_close"
      />

      <ContentEditorModal
        :parent="this"
        @done-create="create_success_publish_content"
      />

      <LinkArticleTerm
        v-if="itemSelected.id"
        :id="itemSelected.id"
        :parent="this"
        @done-success="link_success"
        @close-modal="close_modal"
      />

      <LinkBrand :parent="this" @done-sucess="loadArticles" />

      <CourseTermModal
        v-if="isLinkCourseTerm"
        :item="item"
        @on-update="loadAlgoliaSummary"
        @on-close="on_close"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import medicalMixin from "./mixins/medicalMixin";
import algoliaMixin from "./mixins/algoliaMixin";
import termMixin from "./mixins/termMixin";
import articleMixin from "./mixins/articleMixin";

import addNotesModal from "./modals/add-notes.modal.vue";
import CreateArticleModal from "./modals/create-article.modal";
import LinkArticleTerm from "./modals/link/link-articleTerm.modal";
import LinkBrand from "./modals/link/link-brand.modal";
import ContentEditorModal from "./modals/link/link-publish-content.modal";
import AdvanceSearchArticles from "./snippets/advance_search_articles";
import ArticleSearchItems from "./snippets/advance_search/article_search_items";
import { search_articles } from "./mixins/query";
import Form from "../../helpers/form";

import CourseTermModal from "./modals/link/link-course-term.modal";
export default {
  mixins: [medicalMixin, termMixin, articleMixin, algoliaMixin],

  components: {
    CreateArticleModal,
    LinkArticleTerm,
    LinkBrand,
    ContentEditorModal,
    addNotesModal,
    AdvanceSearchArticles,
    ArticleSearchItems,
    CourseTermModal,
  },

  data() {
    return {
      showAdvanceSearch: false,
      isAdvanceSearch: false,
      isCreateModal: false,
      name: "articles",
      isLinked: false,
      isLoading: true,
      isAddTypeDate: false,
      isAddAuthor: false,
      isAddImage: false,
      // medicalTerms: [],
      linkedBrands: [],
      filterMedicalTerm: "",
      htmlTagPriorityName: "",
      typedatesForm: [],
      linkedMedicalTerm: [],
      authorSlotForm: [],
      imageSlotForm: [],
      authors_array: [],
      items: [],
      articles: [],
      notes_array: [],
      articleTable: [],
      searchItems: {},
      item: {},
      article: "",

      //For Publication on Editor Modal
      contentEditorModalForm: new Form({
        articleID: "",
        id: "",
        articleTypeID: "",
        content: "",
        prefilledsectionOptions: null,
        meta_description: "",
        slug: "",
        alt_tag_image: "",
        image_description: "",
        language: this.$i18n.locale,
      }),

      modalPublishingTemplate: {
        on_content_editor: {
          name: "service_type_add_new_button",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "service_type_add_new_button",
            loading: false,
          },
        },
      },

      isShowLink: true,
      isShowHtmlTag: true,
      actors_list: "",
      author_type_list: "",
      article_id: "",
      linkedTermsId: [],
      is_term: false,
      formData: new FormData(),
      form: {},
      search_params: {},
    };
  },

  created() {
    this.loadBrands();
    this.loadAutorType();
    this.loadArticles();
    this.loadAlgoliaSummary();
    this.loadActors();
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isLinked = true;
        this.currentPage = 1;
        this.loadArticles();
      }
    },

    currentPage(value) {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.loadArticles();
    },

    perPage() {
      this.showAdvanceSearch = false;
      this.isLinked = true;
      this.loadArticles();
    },
  },

  computed: {
    ...mapGetters("actor", {
      actors: "actors",
    }),
    ...mapGetters("brand", {
      itemBrands: "brands",
    }),
    ...mapGetters("categ_terms", {
      article_list: "get_articles_items",
      terms: "get_terms_items",
      notes_terms_items: "get_notes_terms_items",
      type_authors: "get_type_author_items",
    }),
    ...mapGetters({
      specific_sections: "prefilledsection/get_specific_prefilledsections",
    }),

    isSearchItem() {
      if (_.isEmpty(this.searchItems)) {
        return false;
      }
      let k = false;

      Object.values(this.searchItems).forEach((ele) => {
        if (ele && Array.isArray(ele)) {
          k = true;
        } else if (ele && ele.type_dates) {
          if (ele.type_dates.name) {
            k = true;
          }
        }
      });
      return k;
    },
  },

  methods: {
    ...mapActions("actor", ["get_actors"]),

    ...mapActions("brand", ["get_brands"]),

    ...mapActions("categ_terms", [
      "get_articles",
      "delete_article",
      "get_terms",
      "get_notes_article",
      "get_type_authors",
      "get_advance_search_article",
      "reset_terms",
    ]),

    ...mapActions("prefilledsection", ["get_specific_prefilledsections"]),

    on_advance_search() {
      this.isAdvanceSearch = true;

      setTimeout(() => {
        this.$nextTick(() => {
          this.showAdvanceSearch = true;
        });
        this.isAdvanceSearch = false;
      }, 1000);
    },

    loadAll() {
      this.loadBrands();
      this.loadAutorType();
      this.loadArticles();
      this.loadAlgoliaSummary();
    },

    advance_search_success(items) {
      this.articles = items.data;
      this.total_rows = items.total;

      this.showing.to = items.to;
      this.showing.from = items.from;
      this.showing.total = items.total;
    },

    advance_search_item(searchItems, items, form, search_params) {
      this.isLinked = false;
      this.searchItems = searchItems;
      this.form = form;

      this.articles = items.data;
      this.total_rows = items.total;

      this.showing.to = items.to;
      this.showing.from = items.from;
      this.showing.total = items.total;

      this.search_params = search_params;

      this.filterAdvanceSearchType();
    },

    on_search_submit(value) {
      this.isLinked = true;
      this.currentPage = 1;
      this.loadArticles();
    },

    on_sort_language(value) {
      this.showAdvanceSearch = false;
      this.sortbyLang = value !== "all" ? value : "";

      this.isLinked = true;
      this.loadArticles();
    },

    loadArticles() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        sortbyLang: this.sortbyLang,
        perPage: this.perPage,
        page: this.currentPage,
        ...this.search_params,
      };

      axios
        .get("/api/articles/active-paginated", { params })
        .then((resp) => {
          let data = resp.data;
          this.articles = data.data;
          this.total_rows = data.total;
          // this.items = this.article_list;

          this.showing.to = data.to;
          this.showing.from = data.from;
          this.showing.total = data.total;
        })
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
    },

    loadAutorType() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.get_type_authors(params).then((_) => {
        this.author_type_list = this.type_authors;
      });
    },

    loadActors() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.$brand.id,
      };
      this.get_actors(params).then((_) => {
        this.actors_list = this.actors;
      });
    },

    getAuthorFullName(id) {
      var act = this.actors_list.find((x) => x.id === id);
      return act.id;
    },

    getAuthorName(data) {
      let article_id = [];
      if (data.length > 0) {
        data.forEach((ele) => {
          let id = [];
          let authors_ = jQuery.parseJSON(ele.authors);
          authors_.forEach((ele1) => {
            var act = this.actors_list.find((x) => x.id === ele1);
            id.push(act.fullname);
          });

          var type = this.author_type_list.find(
            (x) => x.id === ele.author_type
          );
          article_id.push({
            type: type.name,
            authors: id,
          });
        });
        return article_id;
      }
    },

    on_add() {
      // this.form.reset();
      this.btnloading = false;
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.articleForm.action = "Add";
      this.showAdvanceSearch = false;
      this.on_remove_advace_search();

      this.isLinked = true;
      this.isCreateModal = true;
      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("article-modal");
      }, 1000);
    },

    on_link_course_term(item) {
      this.isLinkCourseTerm = true;
      this.isLinked = true;
      this.item = item;

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("link-course-term-modal");
      }, 1000);
    },

    create_success(item) {
      let recordName = this.$t("label.articles");
      if (this.articleForm.action === "Add") {
        this.storeToast(item.article_title, recordName);
      } else {
        this.updateToast(item.id, recordName);
      }

      this.typedatesForm = [];
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.isAddTypeDate = false;
      this.isAddAuthor = false;
      this.isAddImage = false;
      this.isCreateModal = false;
      this.articleForm.reset();

      // this.search_params = {};
      if (Object.values(this.search_params).length > 0) {
        this.load_advance_search_articles();
      } else {
        this.loadArticles();
      }
      this.loadAlgoliaSummary();
    },

    load_advance_search_articles() {
      search_articles(this.search_params).then((data) => {
        this.articles = data.data;
        this.total_rows = data.total;
        this.showing.to = data.to;
        this.showing.from = data.from;
        this.showing.total = data.total;
      });
    },

    create_success_publish_content(item) {
      let recordName = "Pre Filled Section";
      // if (this.articleForm.action === "Add") {
      //   this.storeToast(item.article_title, recordName);
      // } else {
      //   this.updateToast(item.id, recordName);
      // }

      this.loadAll();
    },

    link_success(item) {
      this.loadArticles();
    },

    on_action(item, title) {
      switch (title) {
        case "edit":
          this.on_edit(item);
          break;
        case "brand":
          this.on_linked_brand(item);
          break;
        case "term":
          this.on_linked_terms(item);
          break;
        case "notes":
          this.addNotes(item);
          break;
        case "contenteditor":
          this.preload_content_editor(item);
          break;
        // case "delete":
        //   this.on_delete(item);
        //   break;
      }
    },

    on_linked_brand(item) {
      this.itemSelected = item;
      this.brandForm.linkedType = "article";
      this.mtForm.id = item.id;
      this.brandForm.brands = item.brands;

      this.$bvModal.show("link-brand-modal");
    },

    preload_content_editor(item) {
      let vm = this;

      this.article = item;
      this.contentEditorModalForm.reset();
      this.contentEditorModalForm.language = this.$i18n.locale;

      if (this.article.publishing_item_content != null) {
        this.contentEditorModalForm.id = this.article.publishing_item_content.id;
        this.contentEditorModalForm.content = this.article.publishing_item_content.content;
        this.contentEditorModalForm.meta_description = this.article.publishing_item_content.meta_description;
        this.contentEditorModalForm.slug = this.article.publishing_item_content.slug;
        this.contentEditorModalForm.alt_tag_image = this.article.publishing_item_content.alt_tag_image;
        this.contentEditorModalForm.image_description = this.article.publishing_item_content.img_description;
        this.contentEditorModalForm.originalData.content = this.article.publishing_item_content.content;
      }
      this.contentEditorModalForm.articleTypeID = vm.deepCopy(
        this.article.publishing_item_type_articles
      )[0].id;

      this.on_content_editor(this.contentEditorModalForm.articleTypeID);
    },

    async on_content_editor(id) {
      let params = {
        api_token: this.$user.api_token,
        articleTypeID: id,
        locale: this.$i18n.locale,
      };

      this.get_specific_prefilledsections(params).then((_) => {
        this.contentEditorModalForm.prefilledsectionOptions = this.specific_sections;
      });

      this.$bvModal.show("link-publish-content-modal");
    },

    addNotes(item) {
      let vm = this;
      let id = item.id;

      // item.is_loading = true;

      vm.notes = [];
      // vm.meta.page = "add-notes-modal";
      let params = {
        id: id,
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.form.notes = [];
      vm.get_notes_article(params).then(() => {
        vm.$nextTick(() => {
          vm.form.id = id;
          //     vm.form.profile_id = vm.profile.id;
          vm.notes_array = vm.deepCopy(vm.notes_terms_items);
          vm.addNote();
          vm.articleForm.title = item.article_title;

          //     vm.itemSelected = vm.profile;

          //     // item.is_loading = false;
          //     // open category modal
          //     vm.$bvModal.show("add-notes-modal");

          vm.$bvModal.show("add-notes-modal");
        });
      });
    },

    deepCopy(variable_to_copy) {
      return _.cloneDeep(variable_to_copy);
      return JSON.parse(JSON.stringify(variable_to_copy));
    },

    addedNotesSuccessfully(notes) {
      let vm = this;
      if (vm.form.id != undefined) vm.form.notes = notes;
    },

    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };

      // load brands
      this.get_brands(params).then((_) => {});
    },

    on_linked_terms(item) {
      this.reset_terms();

      this.itemSelected = item;
      this.isLinked = true;

      this.linkedTermsId = [];
      if (item.medical_terms.length > 0) {
        item.medical_terms.forEach((mt) => {
          this.linkedTermsId.push(mt.id);
        });
      }

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("link-to-terminology-modal");
      }, 1000);
    },

    loadMedicalTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterMedicalTerm,
        brand_id: this.$brand.id,
        articleLinked: "articleLinked",
      };

      this.get_terms(params).then((_) => {
        this.isLinked = false;
        this.$bvModal.show("link-to-terminology-modal");
      });
    },

    on_edit(item) {
      this.isCreateModal = true;
      this.isLinked = true;

      this.typedatesForm = [];
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.articleForm.reset();
      this.language = this.$i18n.locale;
      this.articleForm.id = item.id;

      this.articleForm.title = this.set_article_title(item);

      this.articleForm.type_dates = item.type_dates;
      this.articleForm.item_type = item.publishing_item_type_articles;

      this.articleForm.link = item.base_link !== "null" ? item.base_link : "";
      this.articleForm.publish_date = item.publish_date;

      // this.articleForm.actors = item.has_authors;
      this.articleForm.action = "Update";
      this.itemSelected = item;

      if (item.type_dates.length > 0) {
        this.typedatesForm = this.setTypeDates(item.type_dates);
        this.isAddTypeDate = true;
      }
      if (item.author_slot.length > 0) {
        this.authorSlotForm = this.setAuthorSlot(item.author_slot);
        this.isAddAuthor = true;
      }
      if (item.image_slot.length > 0) {
        this.imageSlotForm = this.setImageSlot(item.image_slot);
        this.isAddImage = true;
      }

      setTimeout(() => {
        this.isLinked = false;
        // Open Modal
        this.$bvModal.show("article-modal");
      }, 1000);
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = this.$t("publishing_items");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            brand_id: this.$brand.id,
            id: item.id,
          };
          this.delete_article(params)
            .then((_) => {
              this.deleteToast(baseName, records);
              this.articles.splice(
                this.getRemoveItemIndex(this.articles, item.id),
                1
              );
            })
            .catch((error) => {
              if (error.response) {
                this.inusedToast(baseName);
                item.is_loading = false;
              }
            });
        }
      });
    },

    on_close() {
      this.$bvModal.hide("article-modal");
      this.$bvModal.hide("link-course-term-modal");

      setTimeout(() => {
        this.item = "";
        this.itemSelected = "";
        this.isLinkCourseTerm = false;
        this.isCreateModal = false;
      }, 500);
    },

    setTypeDates(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          name: ele,
          date: ele.pivot.article_date,
        });
      });
      return items;
    },

    setItemType(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          name: ele,
          date: ele.pivot.article_date,
        });
      });
      return items;
    },

    setImageSlot(data) {
      let items = [];
      data.forEach((ele) => {
        items.push({
          image: null,
          imagePlaceholder:
            ele.image !== null
              ? JSON.parse(ele.image)[Object.keys(JSON.parse(ele.image))[0]]
              : "",
          htmlTags: ele.html_tag_items,
          id: ele.id,
        });
      });
      return items;
    },

    setAuthorSlot(data) {
      let items = [];
      data.forEach((ele) => {
        let id = [];
        let authors = jQuery.parseJSON(ele.authors);
        authors.forEach((ele1) => {
          var act = this.actors_list.find((x) => x.id === ele1);
          id.push({ id: ele1, full_name: act.fullname });
        });
        var type = this.author_type_list.find((x) => x.id === ele.author_type);
        items.push({
          actors: id,
          actor_type: { id: ele.author_type, name: type.name },
        });
      });
      return items;
    },

    on_remove_advace_search() {
      this.showAdvanceSearch = false;

      // this.$root.$emit("bv::hide::popover", "advanced-search-show-article");
    },

    close_modal() {
      this.linkedTermsId = [];
      this.itemSelected = "";
    },

    notif_created_prefilled(e) {
      this.storeToast(e, `Pre Filled Section`);
    },
  },
};
</script>

<style scoped>
/* .algolia-toast {
    position: absolute;
    top: 32px;
    left: 460px;
  } */

.publishing-item-popover-text {
  font-size: 0.825rem !important;
  color: #fff !important;
}

.popover-header {
  color: #fff;
  background-color: #fb8c00;
  border-bottom-color: #fb8c00;
}

.popover-body {
  color: #fb8c00;
}

.popover {
  background-color: #ff8e00;
  border-bottom-color: #ff9917;
}

.arrow::before {
  border-right-color: #fb8c00 !important;
}
.arrow::after {
  border-right-color: #fb8c00 !important;
}
</style>
 