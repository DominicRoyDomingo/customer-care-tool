<style   scoped>
.nav-scroller {
  position: relative;
  height: 560px !important;
  overflow-y: scroll;
}
.cursor-pointer {
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
        {{ $t("label.linking_to") }} {{ $t("label.articles") }}
        <small
          class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
          style="max-width: 600px; letter-spacing: normal"
          v-html="`(${parent.itemSelected.term_name})`"
        />
        <v-spacer></v-spacer>

        <v-btn icon color="error lighten-2" @click="on_cancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <div class="row pl-3 pr-3 mt-2">
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
                  {{ $t("label.articles") }}
                </h6>
              </template>

              <b-overlay
                id="overlay-background"
                :show="isOverlay"
                :variant="'light'"
                opacity="0.85"
                :blur="'1px'"
                rounded="sm"
              >
                <b-card-body
                  class="p-0"
                  :class="{ 'nav-scroller': articles.length > 7 }"
                >
                  <b-table
                    class="mb-0"
                    show-empty
                    ref="linkedTable"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :per-page="perPage"
                    :busy="isLoading"
                    :items="articles"
                    :tbody-tr-class="table_row_active"
                  >
                    <template v-slot:table-busy>
                      <div class="d-flex justify-content-center p-2">
                        <b-spinner label="Small Loading..."></b-spinner>
                      </div>
                    </template>

                    <template v-slot:cell(index)="data">
                      <div class="row">
                        <div class="col-sm-10 p-4">
                          <b-form-checkbox
                            class="cursor-pointer"
                            :disabled="data.item.is_loading"
                            v-b-tooltip.hover
                            @change="on_link_article(data.item)"
                            v-model="parent.linkedMedicalArticles"
                            :id="`article-${data.item.id}`"
                            :name="`article-${data.item.id}`"
                            :value="data.item.id"
                          >
                            <strong
                              class="text-capitalize"
                              v-html="data.item.article_title"
                            />

                            <b-spinner
                              label="Loading..."
                              small
                              v-if="data.item.is_loading"
                            />
                          </b-form-checkbox>
                        </div>
                        <div class="col-sm-2">
                          <span
                            class="badge badge-success p-2 float-right mt-2"
                            v-if="
                              is_linked_article(data.item.id) &&
                              !data.item.is_loading
                            "
                          >
                            <i class="fa fa-check text-white" />
                            <span
                              class="text-white text-uppercase"
                              v-text="$t('linked')"
                            />
                          </span>
                        </div>
                      </div>
                    </template>
                  </b-table>
                </b-card-body>
              </b-overlay>
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
      sortBy: "is_linked_to_term",
      sortDesc: true,
      isLoading: true,
      isOverlay: false,

      filter: "",
      currentPage: 1,
      perPage: 10,
      totalRows: 1,

      articles: [],

      linked_articles: "",

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.linking_to") + " ?",
        },
      ],
    };
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isOverlay = true;
        this.currentPage = 1;
        this.load_articles().catch((error) => {
          console.log(error);
        });
      }
    },

    currentPage(value) {
      this.isOverlay = true;
      this.load_articles().catch((error) => {
        console.log(error);
      });
    },

    perPage() {
      this.isOverlay = true;
      this.load_articles().catch((error) => {
        console.log(error);
      });
    },
  },

  mounted() {
    this.load_articles().catch((error) => {
      console.log(error);
    });
  },

  methods: {
    ...mapActions("categ_terms", ["get_articles"]),

    async load_articles() {
      let article_ids = "";

      if (this.parent.linkedMedicalArticles.length > 0) {
        article_ids = JSON.stringify(this.parent.linkedMedicalArticles);
      }
      this.linked_articles = article_ids;

      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        term_id: this.id,
        perPage: this.perPage,
        page: this.currentPage,
        medical_article_id: this.linked_articles,
        sortByTerm: "term",
      };

      axios
        .get("/api/articles/active-paginated", { params })
        .then((resp) => {
          let data = resp.data;
          this.articles = data.data;
          this.totalRows = data.total;
        })
        .finally(() => {
          this.isOverlay = false;
          this.isLoading = false;
        });
    },

    on_search_submit() {
      if (this.parent.linkedMedicalArticles.length > 0) {
        this.linked_articles = JSON.stringify(
          this.parent.linkedMedicalArticles
        );
      }

      this.isOverlay = true;
      this.load_articles().catch((error) => {
        console.log(error);
      });
    },

    table_row_active(item, type) {
      let key = false;
      if (item) {
        this.parent.linkedMedicalArticles.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },

    on_cancel() {
      this.$bvModal.hide("link-to-terminology-modal");
      this.$emit("close-modal");
    },

    is_linked_article(id) {
      let key = false;
      this.parent.linkedMedicalArticles.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });

      return key;
    },

    on_link_article(item) {
      let data = this.parent.itemSelected;
      item.is_loading = true;
      let checked = $(`#article-${item.id}`).prop("checked");

      let params = {
        medical_term_id: this.id,
        medical_article_id: item.id,
        api_token: this.$user.api_token,
        key: checked ? "link" : "unlink",
      };

      axios.post("/api/articles/link-article-term", params).then((resp) => {
        item.is_loading = false;
        if (checked) {
          this.parent.linkedToast(data.base_name, item.base_name);
        } else {
          this.parent.unlinkedToast(data.base_name, item.base_name);
        }

        this.$emit("link-success", item, checked);
      });
    },
  },
};
</script>
