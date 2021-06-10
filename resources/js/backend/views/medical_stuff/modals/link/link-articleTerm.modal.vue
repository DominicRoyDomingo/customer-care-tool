<style scoped>
.div-scroll {
  position: relative;
  height: 500px;
  overflow-y: scroll;
}
.check-box {
  cursor: pointer !important;
}
</style>
<template>
  <b-modal
    id="link-to-terminology-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light">
        <div class="row">
          <div class="col-sm-11">
            <div
              class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
              style="max-width: 700px; letter-spacing: normal"
            >
              {{ $t("label.linking_to") }} {{ $t("label.terminilogies") }}
              <small v-html="`(${parent.itemSelected.article_title})`" />
            </div>
          </div>
          <div class="col-sm-1">
            <v-btn icon color="error lighten-2" @click="onCancel">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </div>
        </div>
      </v-card-title>

      <v-container>
        <div class="row mt-3 pl-3 pr-3">
          <div class="col-md-6">
            <b-form inline>
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("button.show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="parent.showEntriesOpt"
                v-model="perPage"
              />
              <label class="mb-2 mr-sm-2 mb-sm-0">
                {{ $t("label.entries") }}
              </label>
            </b-form>
          </div>
          <div class="col-md-6">
            <b-form @submit.prevent="on_search_submit">
              <b-input-group class="mb-0 float-right">
                <b-input-group-prepend v-show="filter">
                  <b-button variant="lignt" @click="filter = ''">
                    <b-icon icon="x" class="text-danger" />
                  </b-button>
                </b-input-group-prepend>

                <template #append>
                  <b-button variant="light" type="submit" :disabled="!filter">
                    <i
                      class="fa fa-search"
                      :class="{ 'text-primary': filter }"
                    />
                  </b-button>
                </template>
                <b-form-input
                  v-model="filter"
                  autocomplete="off"
                  :placeholder="$t('type_and_enter')"
                />
              </b-input-group>
            </b-form>
          </div>

          <div class="col-md-12" style="margin-top: -15px">
            <b-card no-body>
              <template #header>
                <h6 class="mb-0 font-weight-bold text-info">
                  {{ $t("label.terminilogies") }}
                </h6>
              </template>

              <b-card-body
                id="nav-scroller"
                ref="content"
                class="p-0"
                :class="{ 'div-scroll': terms.length > 7 }"
              >
                <b-overlay
                  id="overlay-background"
                  :show="isOverlay"
                  :variant="'light'"
                  opacity="0.85"
                  :blur="'1px'"
                  rounded="sm"
                >
                  <b-table
                    show-empty
                    class="mb-0"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :per-page="perPage"
                    :busy="isLoading"
                    :items="terms"
                    :tbody-tr-class="bgLightClass"
                  >
                    <template v-slot:table-busy>
                      <div class="d-flex justify-content-center p-2">
                        <b-spinner label="Small Loading..."></b-spinner>
                      </div>
                    </template>

                    <template v-slot:cell(index)="data">
                      <div class="row mb-0 mt-0">
                        <div class="col-md-12">
                          <span
                            :id="`term-${data.index}`"
                            class="d-inline-block"
                            tabindex="0"
                          >
                            <b-form-checkbox
                              @change="on_term_linked(data.item)"
                              v-model="parent.linkedTermsId"
                              :id="`medterm-${data.item.id}`"
                              :name="`medterm-${data.item.id}`"
                              :value="data.item.id"
                              :disabled="
                                data.item.is_loading ||
                                !data.item.has_term_types
                              "
                            >
                              <strong
                                :for="`medterm-${data.item.id}`"
                                class="text-capitalize check-box"
                                v-html="data.item.term_name"
                              />

                              <b-spinner
                                label="Loading..."
                                class="ml-2"
                                style="position: absolute; margin-top: -2px"
                                small
                                v-if="data.item.is_loading"
                              />

                              <span
                                class="badge badge-success h4 mr-2 text-white text-uppercase"
                                style="vertical-align: top; margin-top: -5px"
                                v-text="$t('linked')"
                                v-if="
                                  onLinkMessage(data.item.id) &&
                                  !data.item.is_loading
                                "
                              />
                            </b-form-checkbox>
                          </span>
                          <b-tooltip
                            v-if="!data.item.has_term_types"
                            variant="danger"
                            :target="`term-${data.index}`"
                          >
                            <p class="mt-2">
                              <strong> {{ $t("errors.error") }}! </strong>
                              {{ $t("label.linking_to") }}
                              <strong> {{ data.item.base_name }} </strong>
                              {{ $t("errors.linking_error") }}
                            </p>
                          </b-tooltip>
                        </div>
                      </div>
                    </template>
                  </b-table>
                </b-overlay>
              </b-card-body>
              <b-card-footer v-if="totalRows > 0">
                <b-pagination
                  class="mb-0"
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  align="center"
                  size="sm"
                />
              </b-card-footer>
            </b-card>
          </div>
        </div>
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent", "id"],

  data() {
    return {
      termName: "",
      filter: "",

      sortBy: "is_linked_to_article",
      sortDesc: true,
      sortByArticle: "article",

      isPaginate: false,
      isLoading: true,
      currentPage: 1,
      perPage: 10,

      isOverlay: false,

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.linking_to") + " ?",
        },
      ],

      terms: [],
      totalRows: 1,
    };
  },

  mounted() {
    this.load_terms().catch((error) => console.log(error));

    // console.log(this.terms);
    // this.items = this.terms.data;
    // this.totalRows = this.terms.total;
  },

  watch: {
    filter: function (value) {
      if (!value) {
        this.reset_terms();
        this.sortByArticle = "article";
        this.isOverlay = true;
        this.load_terms().catch((error) => console.log(error));
      }
    },

    currentPage(value) {
      this.reset_terms();
      this.sortByArticle = "article";
      this.isOverlay = true;
      this.load_terms().catch((error) => console.log(error));
    },

    perPage() {
      this.reset_terms();
      this.sortByArticle = "article";
      this.isOverlay = true;
      this.load_terms().catch((error) => console.log(error));
    },
  },

  methods: {
    ...mapActions("categ_terms", ["get_terms", "reset_terms"]),

    async load_terms() {
      let medical_terms = "";
      if (this.parent.linkedTermsId.length > 0) {
        medical_terms = JSON.stringify(this.parent.linkedTermsId);
      }

      let params = {
        ...this.setParamsDetails(),
        article_id: this.id,
        sortByArticle: this.sortByArticle,
        medical_terms: medical_terms,
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terms = items.data;
          this.totalRows = items.total;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
          this.isOverlay = false;
        });
    },

    on_search_submit() {
      this.reset_terms();
      this.isOverlay = true;
      this.sortByArticle = "";
      this.load_terms().catch((error) => console.log(error));
    },

    onCancel() {
      this.$bvModal.hide("link-to-terminology-modal");
      this.$emit("close-modal");
    },

    onLinkMessage(id) {
      let key = false;
      this.parent.linkedTermsId.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });

      return key;
    },

    on_term_linked(item) {
      item.is_loading = true;

      let checked = $(`#medterm-${item.id}`).prop("checked");

      let params = {
        medical_article_id: this.id,
        medical_term_id: item.id,
        api_token: this.$user.api_token,
        key: checked ? "link" : "unlink",
      };

      axios
        .post("/api/articles/link-article-term", params)
        .then((resp) => {
          if (checked) {
            this.parent.linkedToast(item.base_name, this.$t("label.article"));
          }

          this.$emit("done-success");
        })
        .catch((error) => {
          const index = this.parent.linkedTermsId.indexOf(item.id);
          if (index > -1) {
            this.parent.linkedTermsId.splice(index, 1);
          }
        })
        .finally(() => {
          item.is_loading = false;
        });
    },

    bgLightClass(item, type) {
      let key = false;
      if (item) {
        this.parent.linkedTermsId.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },

    setParamsDetails() {
      return {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        perPage: this.perPage,
        page: this.currentPage,
      };
    },
  },
};
</script>
