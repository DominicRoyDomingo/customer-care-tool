<style  scoped>
.borderless td,
.borderless th {
  border: none;
}
.dropdown-toggle::after {
  display: none;
}
</style>
<template>
  <div class="animated fadeIn" v-if="medicalTerm">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <h4 class="card-title">
              <a :href="index_route">
                {{ $t("label.terminilogies") }}
              </a>
              <small
                class="text-muted text-capitalize"
                v-html="medicalTerm.term_name"
              />
            </h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7">
            <div class="card shadow mb-4">
              <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
              >
                <h6 class="m-0 font-weight-bold text-muted text-capitalize">
                  {{ $t("label.terminilogy") }} {{ $t("details") }}
                </h6>
                <a
                  href="javascript:;"
                  @click="on_edit"
                  class="h5 mb-0 float-right text-success"
                  v-b-tooltip.hover
                  :title="`${$t('button.edit')} ${medicalTerm.base_name}`"
                >
                  <i class="fas fa-edit"></i>
                </a>
              </div>
              <!-- Card Body -->
              <div class="p-4" v-if="linkLoading">
                <label>
                  <b-spinner small label="Small Spinner"></b-spinner>
                  <span class="mr-1">Fetching data...</span>
                </label>
              </div>

              <b-overlay
                v-else
                id="overlay-background"
                :show="isLoadingEdit"
                :variant="'light'"
                opacity="0.85"
                :blur="'1px'"
                rounded="sm"
              >
                <div class="card-body p-4">
                  <b-media>
                    <template #aside>
                      <b-img
                        class="img-thumbnail"
                        :src="
                          medicalTerm.img_url ? medicalTerm.img_url : no_photo
                        "
                        width="200"
                        :alt="medicalTerm.term_name"
                      />
                    </template>

                    <h4
                      class="mt-0 text-capitalize"
                      v-html="medicalTerm.term_name"
                    />

                    <div class="mt-1" v-if="medicalTerm.has_term_types">
                      <hr />
                      <p class="mb-0 text-muted">
                        {{ $t("label.type_of_terms") }}
                      </p>
                      <b-button
                        v-for="termType in medicalTerm.has_term_types"
                        :key="termType.id"
                        pill
                        variant="secondary"
                        class="text-white text-uppercase mr-2 mb-1 mt-1"
                        style="cursor: not-allowed"
                        size="sm"
                        v-html="termType.term_type_name"
                      />
                    </div>

                    <div class="mt-1" v-if="medicalTerm.has_specializations">
                      <hr />
                      <p class="mb-0 text-muted">
                        {{ $t("label.specializations") }}
                      </p>
                      <b-button
                        v-for="spec in medicalTerm.has_specializations"
                        :key="spec.id"
                        pill
                        variant="secondary"
                        class="text-white text-uppercase mr-2 mb-1 mt-1"
                        style="cursor: not-allowed"
                        size="sm"
                        v-html="spec.description_name"
                      />
                    </div>
                  </b-media>
                </div>
              </b-overlay>
            </div>
          </div>
          <div class="col-md-5">
            <div class="card shadow mb-4">
              <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
              >
                <h6 class="m-0 font-weight-bold text-muted">
                  {{ $t("label.articles") }} Connections
                </h6>
                <div
                  class="dropdown no-arrow"
                  v-if="medicalTerm && medicalTerm.has_term_types"
                >
                  <a
                    class="dropdown-toggle font-weight-bold"
                    href="javascript:;"
                    role="button"
                    id="dropdownMenuLink"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                  </a>
                  <div
                    class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink"
                  >
                    <div class="dropdown-header"></div>
                    <a
                      class="dropdown-item"
                      href="javascript:;"
                      @click="on_linked_articles"
                    >
                      <i class="fas fa-link text-primary"></i>
                      <span
                        v-text="
                          `${$t('label.linking_to')} ${$t('label.articles')}`
                        "
                      />
                    </a>
                  </div>
                </div>
              </div>
              <!-- Card Body -->
              <div class="p-4" v-if="linkLoading">
                <label>
                  <b-spinner small label="Small Spinner"></b-spinner>
                  <span class="mr-1">Fetching data...</span>
                </label>
              </div>

              <b-overlay
                v-else
                id="overlay-background"
                :show="isLinked"
                :variant="'light'"
                opacity="0.85"
                :blur="'1px'"
                rounded="sm"
              >
                <div class="card-body p-0">
                  <div
                    class="col-md-12"
                    v-if="
                      medicalTerm.medical_articles &&
                      medicalTerm.medical_articles.length > 0
                    "
                  >
                    <b-button
                      v-for="article in medicalTerm.medical_articles"
                      :key="article.id"
                      pill
                      variant="light"
                      class="text-capitalize text-primary mb-2 mr-1"
                      size="sm"
                      @click="on_article_show(article.route_show)"
                      v-html="`${article.article_title}`"
                    />
                  </div>

                  <div class="col-md-12" v-else>
                    <div class="text-center">
                      <h1 class="fa fa-link"></h1>
                      <p>
                        {{ $t("alerts.link_to_articles") }}
                      </p>
                    </div>
                  </div>
                </div>
              </b-overlay>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <h4 class="mb-0">
              {{ $t("label.terminilogies") }} Connections
              <a
                href="javascript:;"
                @click="on_linked_terms_id"
                v-if="medicalTerm && medicalTerm.has_term_types"
                class="font-sm ml-2"
                style="margin-top: -0px; position: absolute"
                v-b-tooltip.hover
                :title="`${$t('label.linking_to')} ${$t(
                  'label.terminilogies'
                )}`"
              >
                <i class="fa fa-link" aria-hidden="true"></i>
              </a>
            </h4>
            <b-overlay
              id="overlay-background"
              :show="isLoadingTerm"
              :variant="'light'"
              opacity="0.85"
              :blur="'1px'"
              rounded="sm"
            >
              <TermConnections
                v-if="isRendered && medicalTerm"
                :parent="this"
                :term="medicalTerm"
                :id="id"
              />
            </b-overlay>
          </div>
        </div>

        <LinkTerminologies
          v-if="itemSelected"
          :parent="this"
          :items="items"
          @link-success="link_success"
        />
        <LinkArticles
          v-if="itemSelected"
          :id="itemSelected.id"
          :parent="this"
          @link-success="link_article_success"
          @close-modal="close_modal"
        />
        <TerminologyDetails :name="termTypeName" :parent="this" />
        <EditTermModal
          v-if="itemSelected"
          :parent="this"
          @done-success="update_success"
          @close-modal="close_modal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import MedicalMixin from "./mixins/medicalMixin";
import termMixin from "./mixins/termMixin";

import LinkTerminologies from "./modals/link/link-terms.modal";
import EditTermModal from "./modals/create-term.modal";
import LinkArticles from "./modals/link/link-articles.modal";
import TerminologyDetails from "./modals/terminology_details_show.modal";

import TermConnections from "./snippets/term_connections_list";

export default {
  props: {
    id: {
      type: Number,
      default: () => {},
    },
  },

  mixins: [MedicalMixin, termMixin],

  components: {
    LinkTerminologies,
    TerminologyDetails,
    EditTermModal,
    LinkArticles,
    TermConnections,
  },

  data() {
    return {
      index_route: "/admin/categorized-terms/terminologies",
      isLinked: false,
      isRendered: true,
      isLoadingArticle: false,
      isLoadingTerm: false,
      linkLoading: true,
      isLoadingEdit: false,
      linkTermShow: false,
      linkArticleShow: false,
      filter: "",
      filterArticle: "",
      termTypeName: "",
      linkedMedicalTerm: [],
      linkedMedicalArticles: [],
    };
  },

  mounted() {
    this.loadItems();
  },

  watch: {
    filterArticle(value) {
      this.loadArticles();
    },
  },

  computed: {
    ...mapGetters("categ_terms", {
      linkedMedicalTems: "get_linked_terms_items",
      linkedMedicalDetailsItem: "get_linked_terms_details_item",
      items: "get_terms_items",
      medicalTerm: "get_response_item",
      articles: "get_articles_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", [
      "get_linked_terms",
      "get_linked_terms_details",
      "get_linked_term_id",
      "get_terms",
      "show_term",
      "get_articles",
    ]),

    loadItems() {
      let params = {
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
        locale: this.$i18n.locale,
        id: this.id,
      };

      this.show_term(params).then((_) => {
        //reditect to index terms
        console.log(this.medicalTerm);
        if (!this.medicalTerm) {
          window.location.href = this.index_route;
          return;
        }

        this.linkLoading = false;
        this.isLoading = false;
        this.$nextTick(() => {
          this.isRendered = true;
        });
      });
    },

    on_edit() {
      this.mtForm.reset();

      if (this.medicalTerm) {
        this.isLoadingEdit = true;

        this.language = this.medicalTerm.base_language;
        this.mtForm.id = this.medicalTerm.id;
        this.mtForm.name = this.medicalTerm.base_name;

        this.mtForm.action = "Update";

        this.mtForm.term_types = this.medicalTerm.has_term_types;
        this.mtForm.specializations = this.medicalTerm.has_specializations;
        this.mtForm.file = this.medicalTerm.img_url;

        this.itemSelected = this.medicalTerm;

        // Open Edit Modal
        setTimeout(() => {
          this.isLoadingEdit = false;
          this.$bvModal.show("term-modal");
        }, 1000);
      }
    },

    update_success(item) {
      const records = this.$t("label.terminilogies");
      this.updateToast(item.id, records);

      this.mtForm.reset();
      this.loadItems();
    },

    link_success() {
      this.isRendered = false;
      this.loadItems();
    },

    link_article_success(item, isLinked) {
      if (isLinked) {
        this.medicalTerm.medical_articles.push(item);
      } else {
        this.medicalTerm.medical_articles.splice(
          this.getRemoveItemIndex(this.medicalTerm.medical_articles, item.id),
          1
        );
      }
    },

    on_linked_terms_id() {
      this.itemSelected = this.medicalTerm;
      this.mtForm.id = this.medicalTerm.id;
      this.isLoadingTerm = true;
      this.linkedMedicalTerm = [];

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        medical_terms: this.medicalTerm.medical_terms,
      };

      this.get_linked_term_id(params).then((resp) => {
        let data = resp.data;

        if (data.length > 0) {
          data.forEach((id) => {
            this.linkedMedicalTerm.push(id);
          });
        }

        this.isLinked = false;
        this.isLoadingTerm = false;

        this.$bvModal.show("link-term-modal");
      });
    },

    on_linked_articles() {
      const term = this.medicalTerm;

      this.itemSelected = term;
      this.mtForm.id = term.id;

      this.linkedMedicalArticles = [];
      if (term.medical_articles.length > 0) {
        term.medical_articles.forEach((ma) => {
          this.linkedMedicalArticles.push(ma.id);
        });
      }
      // this.linkLoading = true;

      this.isLinked = true;

      setTimeout(() => {
        this.isLinked = false;
        // this.linkLoading = false;
        this.$bvModal.show("link-to-terminology-modal");
      }, 1000);
    },

    loadArticles() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterArticle,
        brand_id: this.$brand.id,
        term_id: this.id,
      };
      this.get_articles(params).then((_) => {
        this.isLinked = false;
        this.isLoadingArticle = false;
        this.$bvModal.show("link-to-terminology-modal");
      });
    },

    showTermsDetails(termName, termtypeName) {
      let termIds = undefined;
      termIds = termName.map(function (term) {
        return term.id;
      });
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        parent_id: this.id,
        term_ids: termIds,
        term_type_id: termName[0].term_type_id,
        brand_id: this.$brand.id,
      };

      this.get_linked_terms_details(params).then((_) => {
        this.termTypeName = termtypeName;
        this.$bvModal.show("terminology-details-modal");
        this.isLoading = false;
      });
    },

    on_term_show(route) {
      window.location.href = route;
    },

    on_article_show(route) {
      window.location.href = route;
    },

    close_modal() {
      this.itemSelected = "";
    },
  },
};
</script>
 