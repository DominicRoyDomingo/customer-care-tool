<template>
  <div>
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
              @click="onAdd"
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new") }} {{ $t("title") }}
            </v-btn>
          </div>
        </div>
        <div class="row mt-2" v-if="isSearchItem">
          <div class="col-md-12">
            <div style="">
              <h6 class="font-weight-bold">Filter By :</h6>
              <!-- START BUTTON FOR AUTHOR -->
              <b-button
                variant="light"
                pill
                class="mb-1"
                v-if="searchItems.authors && searchItems.authors.length > 0"
              >
                {{ $t("authors") }} :
                <span
                  class="text-info text-capitalize"
                  v-for="(author, index) in searchItems.authors"
                  :key="author.id"
                >
                  {{ author.firstname }} {{ author.surname }}
                  <span v-if="searchItems.authors.length > index + 1">,</span>
                </span>

                <b-badge
                  variant="danger"
                  @click="onCloseSearchItem('author')"
                  class="badge-close float-right"
                  title="Close"
                >
                  <i class="fa fa-times" aria-hidden="true"></i>
                </b-badge>
              </b-button>
              <!-- END BUTTON FOR AUTHOR -->

              <!-- START BUTTON FOR TERM TYPE -->
              <b-button
                variant="light"
                pill
                class="mb-1"
                v-if="searchItems.terms && searchItems.terms.length > 0"
              >
                {{ $t("label.terminilogies") }} :
                <span
                  class="text-info text-capitalize"
                  v-for="(term, index) in searchItems.terms"
                  :key="term.id"
                  v-html="term.term_name"
                >
                  <!-- {{ term.term_name }} -->
                  <span v-if="searchItems.terms.length > index + 1">,</span>
                </span>

                <b-badge
                  variant="danger"
                  @click="onCloseSearchItem('terms')"
                  class="badge-close float-right"
                  title="Close"
                >
                  <i class="fa fa-times" aria-hidden="true"></i>
                </b-badge>
              </b-button>
              <!-- END BUTTON FOR TERM TYPE -->

              <!-- START BUTTON FOR TYPE OF DATE -->
              <b-button
                variant="light"
                pill
                class="mb-1"
                v-if="searchItems.type_dates && searchItems.type_dates !== ''"
              >
                {{ $t("label.type_of_date") }} :
                <span
                  class="text-info text-capitalize"
                  v-html="searchItems.type_dates.type_date_name"
                />

                <b-badge
                  variant="danger"
                  @click="onCloseSearchItem('type_of_date')"
                  class="badge-close float-right"
                  title="Close"
                >
                  <i class="fa fa-times" aria-hidden="true"></i>
                </b-badge>
              </b-button>
              <!-- END BUTTON FOR TYPE OF DATE -->

              <!-- START BUTTON FOR START -->
              <b-button
                variant="light"
                pill
                class="mb-1"
                v-if="searchItems.from && searchItems.from !== ''"
              >
                Date Start :
                <span class="text-info text-capitalize">
                  {{ searchItems.from | moment("YYYY/MM/D") }}
                </span>

                <b-badge
                  variant="danger"
                  @click="onCloseSearchItem('from')"
                  class="badge-close float-right"
                  title="Close"
                >
                  <i class="fa fa-times" aria-hidden="true"></i>
                </b-badge>
              </b-button>
              <!-- END BUTTON FOR START -->

              <!-- START BUTTON FOR END -->
              <b-button
                variant="light"
                pill
                class="mb-1"
                v-if="searchItems.to && searchItems.to !== ''"
              >
                Date End:
                <span class="text-info text-capitalize">
                  {{ searchItems.to | moment("YYYY/MM/D") }}
                </span>

                <b-badge
                  variant="danger"
                  @click="onCloseSearchItem('to')"
                  class="badge-close float-right"
                  title="Close"
                >
                  <i class="fa fa-times" aria-hidden="true"></i>
                </b-badge>
              </b-button>
              <!-- END BUTTON FOR END -->
            </div>
          </div>
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
              <b-input-group-prepend
                style="margin-left: 10px; margin-top: -10px"
              >
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      @click="onRemovePopover"
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
                          @click="sortbyLanguage(option.value)"
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
            </v-col>
          </v-col>
          <v-col cols="12" sm="4" md="4">
            <b-form inline @submit.prevent="on_search_submit">
              <el-input
                prefix-icon="el-icon-search"
                v-model="filter"
                autocomplete="off"
                @keyup="on_search_key"
                :disabled="showAdvanceSearch"
                :placeholder="$t('search_here')"
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
            <AdvanceSearchArticles
              v-if="showAdvanceSearch"
              :parent="this"
              @search-items="advance_search_item"
            />

            <!-- <div class="float-right">
              <b-form inline style="margin-right: -8px">
                <b-input-group class="mb-2 mr-sm-2">
                  <template #append>
                    <b-button
                      :disabled="filter.length > 0"
                      variant="primary text-white mr-1"
                      id="advanced-search-show-article"
                    >
                      <b-icon icon="caret-down-fill"></b-icon>
                    </b-button>
                  </template>
                  <b-form-input
                    v-model="filter"
                    autocomplete="off"
                    :placeholder="$t('search_here')"
                    style="width: 300px"
                    type="search"
                  />
                </b-input-group>
              </b-form>
            </div> -->
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
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
                :filter="filter"
                @filtered="onFiltered"
                v-model="articleTable"
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

                <!-- <template v-slot:cell(publish_date)="data">
                  <span>{{ data.item.publish_date | toLocaleString }}</span>
                </template> -->

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="#c8ced3"
                        small
                        v-bind="attrs"
                        v-on="on"
                        :disabled="data.item.is_loading"
                        @click="onRemovePopover"
                      >
                        <span v-if="data.item.is_loading" class="text-center">
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
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
                        @click="onAction(data.item, action.value)"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                          />
                          <span
                            :class="{
                              'text-primary': action.variant === 'primary',
                            }"
                            v-html="action.title"
                          />
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>

          <div class="col-md-12 mb-0">
            <v-row>
              <v-spacer>
                <v-col cols="12" sm="6" md="4">
                  <!-- <span>
                    Page
                    <strong v-text="perPage" />
                    of
                    <strong v-text="totalRows" />
                    {{ $t("label.entries") }}
                  </span> -->
                </v-col>
              </v-spacer>
              <v-col>
                <v-spacer>
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    style="float: right"
                  ></b-pagination>
                </v-spacer>
              </v-col>
            </v-row>
          </div>

          <!-- <div class="col-md-12 mt-0">
            <v-col
              cols="2"
              class="caption font-weight-regular text--secondary"
              style="float:left; margin-top: -10px; margin-left: 0px;"
            >
              {{ $t("button.show") }} {{ this.perPage }} {{ $t("label.entries") }}
            </v-col>
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              align="center"
            />
          </div> -->
        </div>
        <addNotesModal :parent="this"></addNotesModal>
        <CreateArticleModal :parent="this" @done-success="create_success" />
        <LinkArticleTerm
          v-if="Object.values(terms).length > 0"
          :id="itemSelected.id"
          :parent="this"
          :terms="terms"
          @done-success="link_success"
        />
        <LinkBrand :parent="this" @done-sucess="loadArticles" />
        <!-- <AdvanceSearchArticle
          :parent="this"
          @search-items="advance_search_item"
        /> -->
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import medicalMixin from "./mixins/medicalMixin";
import termMixin from "./mixins/termMixin";

import addNotesModal from "./modals/add-notes.modal.vue";
import CreateArticleModal from "./modals/create-article.modal";
import LinkArticleTerm from "./modals/link/link-articleTerm.modal";
import LinkBrand from "./modals/link/link-brand.modal";
import AdvanceSearchArticle from "./snippets/advance_search_article";
import AdvanceSearchArticles from "./snippets/advance_search_articles";

import Form from "./../../helpers/form";

export default {
  mixins: [medicalMixin, termMixin],

  components: {
    CreateArticleModal,
    LinkArticleTerm,
    LinkBrand,
    addNotesModal,
    AdvanceSearchArticle,
    AdvanceSearchArticles,
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
      notes_array: [],
      articleTable: [],
      searchItems: {},

      actions: [
        {
          value: "edit",
          title: this.$t("button.edit"),
          icon: "pencil-square",
          variant: "success",
        },
        {
          value: "delete",
          title: this.$t("button.delete"),
          icon: "trash-fill",
          variant: "danger",
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
          value: "notes",
          title: this.$t("label.add_notes"),
          icon: "file-earmark-plus",
          variant: "primary",
        },
      ],

      tableHeaders: [
        {
          key: "article_title",
          label: this.$t("title"),
          thClass: "text-left text-capitalize",
          thStyle: { width: "40%" },
          sortable: true,
        },

        {
          key: "publishing_item_type_articles",
          label: this.$t("type_of_publising_item"),
          thClass: "text-left text-capitalize",
          thStyle: { width: "40%" },
          sortable: true,
        },
        // {
        //   key: "has_authors",
        //   label: this.$t("item_author_idea"),
        //   thClass: "text-left",
        //   thStyle: { width: "50%" },
        // },
        // {
        //   key: "publish_date",
        //   label: `${this.$t("button.publish")} ${this.$t("date")}`,
        //   thClass: "text-center",
        //   tdClass: "text-center",
        // },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "10%" },
          tdClass: "text-center",
        },
      ],
      isShowLink: true,
      isShowHtmlTag: true,
      actors_list: "",
      author_type_list: "",
      article_id: "",
      linkedTermsId: [],
      is_term: false,
      formData: new FormData(),
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

  created() {
    this.loadActors();
    this.loadBrands();
    this.loadAutorType();
    this.loadArticles();
  },

  watch: {
    filter(value) {
      this.loadArticles();
    },

    filterMedicalTerm(value) {
      this.loadMedicalTerms();
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

    isSearchItem() {
      if (_.isEmpty(this.searchItems)) {
        return false;
      }
      let k = false;

      // console.log( this.searchItems );
      Object.values(this.searchItems).forEach((ele) => {
        if (ele && ele.length > 0) {
          k = true;
        }
        // console.log( this.searchItems.type_dates );
      });
      if (
        this.searchItems.type_dates !== "" &&
        this.searchItems.type_dates !== null
      ) {
        k = true;
      }

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

    on_search_submit() {},
    on_search_key() {},
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
      this.loadActors();
      this.loadBrands();
      this.loadAutorType();
      this.loadArticles();
    },

    advance_search_item(form, data) {
      this.isLinked = false;
      this.searchItems = form;

      this.total_rows = data.length;
      this.items = data;

      // this.searchItems = items;
    },

    onSearchSubmit() {
      this.isLinked = true;
      // this.loadItems();
    },

    onSearchKeyup() {
      this.searchItems = {};
      this.onRemovePopover();
    },

    onCloseSearchItem(type) {
      switch (type) {
        case "from":
          this.searchItems.from = "";
          break;

        case "from":
          this.searchItems.to = "";
          break;

        case "type_of_date":
          this.searchItems.type_dates = "";
          break;

        case "terms":
          this.searchItems.terms = "";
          break;

        case "author":
          this.searchItems.authors = "";
          break;
      }

      this.isLinked = true;
      if (!this.isSearchItem) {
        this.searchItems = {};
        this.loadAll();
        return;
      }

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        type_dates: this.searchItems.type_dates,
        authors: this.set_item_search(this.searchItems.authors),
        terms: this.set_item_search(this.searchItems.terms),
        from: this.searchItems.from,
        to: this.searchItems.to,
      };

      this.get_advance_search_article(params).then((_) => {
        this.isLinked = false;
        this.total_rows = this.items.length;
        this.isLoading = false;
      });
    },

    set_item_search(data) {
      if (!data) {
        return "";
      }
      return data.map((x) => parseInt(x.id));
    },

    onResetSearch() {
      this.filter = "";
      this.onRemovePopover();
    },

    onAdvanceSearch() {},

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

    onFiltered(items) {
      this.total_rows = items.length;
      this.currentPage = 1;
      this.onRemovePopover();
    },

    sortbyLanguage(value) {
      this.sortbyLang = value;
      this.isLoading = true;
      this.loadArticles();
      this.onRemovePopover();
    },

    loadArticles() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        sortbyLang: this.sortbyLang,
      };

      this.get_articles(params).then((_) => {
        this.isLinked = false;
        this.isLoading = false;
        this.items = this.article_list;
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

    onAdd() {
      // this.isCreateModal = true;
      this.form.reset();
      this.btnloading = false;
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.articleForm.action = "Add";
      this.onRemovePopover();

      this.$bvModal.show("article-modal");
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
      this.articleForm.reset();
      this.loadArticles();
    },

    link_success(item) {
      // this.linkedToast(item.base_name, this.$t("label.terminilogies"));
      this.loadArticles();
    },

    onAction(item, title) {
      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
        case "brand":
          this.onLinkBrand(item);
          break;
        case "term":
          this.on_linked_terms(item);
          break;
        case "notes":
          this.addNotes(item);
          break;
      }
    },

    onLinkBrand(item) {
      this.itemSelected = item;
      this.brandForm.linkedType = "article";
      this.mtForm.id = item.id;
      this.brandForm.brands = item.brands;

      this.$bvModal.show("link-brand-modal");
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

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        perPage: this.perPage,
        page: this.currentPage,
        article_id: item.id,
        sortbyTerm: "most_la",
      };

      this.get_terms(params).then((_) => {
        this.isLinked = false;
        this.$bvModal.show("link-to-terminology-modal");
      });
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

    onEdit(item) {
      // this.isCreateModal = true;
      this.typedatesForm = [];
      this.authorSlotForm = [];
      this.imageSlotForm = [];
      this.articleForm.reset();
      this.language = this.$i18n.locale;
      this.articleForm.id = item.id;
      this.articleForm.title = item.base_name;
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

      // Open Modal
      this.$bvModal.show("article-modal");
    },

    onDelete(item) {
      let baseName = item.base_name;
      let records = this.$t("label.articles");

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
            .then((resp) => {
              this.deleteToast(baseName, records);
              this.items.splice(
                this.getRemoveItemIndex(this.items, item.id),
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

    onRemovePopover() {
      this.$root.$emit("bv::hide::popover", "advanced-search-show-article");
    },
  },
};
</script>

 