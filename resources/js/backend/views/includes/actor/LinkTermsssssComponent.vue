<style scoped>
.div-scroll {
  position: relative;
  height: 700px;
  overflow-y: scroll;
}
.check-box {
  cursor: pointer !important;
}
</style>

<template>
  <b-modal
    id="link-term-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light">
        {{ $t("label.linking_to") }} {{ $t("label.terminilogies") }}
        <small
          class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
          style="max-width: 600px; letter-spacing: normal"
          v-html="`(${parent.itemSelected.term_name})`"
        />
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onCancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <div class="row mt-3 pl-3 pr-3">
        <div class="col-sm-6">
          <b-form inline>
            <label class="mr-sm-2" for="inline-form-custom-select-pref">
              {{ $t("button.show") }}
            </label>
            <b-form-select
              id="inline-form-custom-select-pref"
              class="mb-2 mr-sm-2 mb-sm-0"
              :options="showEntriesOpt"
              v-model="perPage"
            />
            <label class="mb-2 mr-sm-2 mb-sm-0">
              {{ $t("label.entries") }}
            </label>
          </b-form>
        </div>
        <div class="col-sm-6">
          <b-form @submit.prevent="onSearchSubmit">
            <b-input-group class="mb-0 float-right">
              <b-input-group-prepend v-show="filter">
                <b-button variant="lignt" @click="filter = ''">
                  <b-icon icon="x" class="text-danger" />
                </b-button>
              </b-input-group-prepend>

              <template #append>
                <b-button variant="light" type="submit" :disabled="!filter">
                  <i class="fa fa-search" :class="{ 'text-primary': filter }" />
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
        <div class="col-md-12 mt-0" style="margin-top: -15px !important">
          <b-overlay
            id="overlay-background"
            :variant="'light'"
            opacity="0.85"
            :blur="'1px'"
            rounded="sm"
          >
            <b-card no-body>
              <template #header>
                <h6 class="mb-0 font-weight-bold text-info">
                  {{ $t("label.terminilogies") }}
                </h6>
              </template>

              <!-- <div v-if="tableTermLoading" class="p-4">
                <b-spinner small label="Small Spinner"></b-spinner>
                Fetching data...
              </div> -->

              <b-card-body
                id="nav-scroller"
                ref="content"
                class="p-0"
                :class="{ 'div-scroll': totalRow > 7 }"
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
                    class="mb-0"
                    show-empty
                    ref="linkedTable"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :current-page="currentPage"
                    :per-page="perPage"
                    :busy="tableTermLoading"
                    :items="terms"
                    :tbody-tr-class="bgLightClass"
                    @filtered="onFiltered"
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
                              class="check-box"
                              v-model="parent.linkedMedicalTerm"
                              :disabled="
                                data.item.is_loading ||
                                !data.item.has_term_types
                              "
                              @change="onLinked(data.item, data.index)"
                              :id="`medterm-${data.item.id}`"
                              :name="`medterm-${data.item.id}`"
                              :value="data.item.id"
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
                                class="font-weight-bold badge badge-success ml-2 text-uppercase"
                                style="
                                  position: absolute;
                                  margin-top: -2px;
                                  padding: 4px;
                                "
                                v-if="
                                  onLinkMessage(data.item.id) &&
                                  !data.item.is_loading
                                "
                              >
                                {{ $t("linked") }}
                              </span>
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

                          <template v-if="onLinkMessage(data.item.id)">
                            <TermDescriptionIndex
                              v-if="data.item.is_rendered"
                              :parent_id="
                                parent_id ? parent_id : this.parent.id
                              "
                              :term="data.item"
                              :descriptions="descriptions"
                              :term_descriptions="data.item.has_descriptions"
                              @link-success="create_term_connection_success"
                              @delete-connection-desc-success="
                                delete_description_success
                              "
                              @show-modal="show_modal"
                            />
                          </template>
                        </div>
                      </div>
                    </template>
                  </b-table>
                </b-overlay>
              </b-card-body>

              <b-card-footer v-if="totalRow > 0">
                <b-pagination
                  class="mb-0"
                  v-model="currentPage"
                  :total-rows="totalRow"
                  :per-page="perPage"
                  align="center"
                  size="sm"
                />
              </b-card-footer>
            </b-card>
          </b-overlay>
        </div>
      </div>
    </v-card>

    <CreateTermDescription
      :parent="this"
      @done-success="create_description_success"
    />
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// Snippets
import TermDescriptionIndex from "../../medical_stuff/snippets/term_descriptions/index";

// Modals
import CreateTermDescription from "../../medical_stuff/modals/create-termDescription.modal";

import medicalMixin from "../../medical_stuff/mixins/medicalMixin";
import termMixin from "../../medical_stuff/mixins/termMixin";

export default {
  props: ["parent"],

  components: {
    CreateTermDescription,
    TermDescriptionIndex,
  },

  mixins: [medicalMixin, termMixin],

  data() {
    return {
      sortBy: "is_linked_term",
      sortDesc: true,

      isPaginate: false,
      tableTermLoading: true,
      currentPage: 1,
      perPage: 10,
      totalRow: 1,
      filter: null,

      parent_id: this.parent.form.id,
      linked_medical_terms: this.parent.itemSelected.medical_terms,

      selectedTerm: "",
      term: "",
      isOverlay: false,

      description: "",
      medtermId: [],
      termName: "",
      selected: "",
      withValue: "",
      terms: [],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.linking_to") + " ?",
          filterByFormatted: true,
        },
      ],
    };
  },

  watch: {
    filter(value) {
      if (!value) {
        this.tableTermLoading = true;
        this.loadTerms().catch((error) => {
          console.log(error);
        });
      }
    },
  },

  mounted() {
    this.loadTermDescriptions().catch((error) => {
      console.log(error);
    });

    this.terms = [];
    this.loadTerms().catch((error) => {
      console.log(error);
    });
  },

  computed: {
    ...mapGetters("categ_terms", {
      descriptions: "get_term_descriptions_items",
      termItems: "get_terms_items",
    }),

    terms_types_id() {
      let items = [];
      this.terms.forEach((ele) => {
        if (ele.has_term_types) {
          ele.has_term_types.forEach((type) => {
            items.push(type.id);
          });
        }
      });

      return items;
    },
  },

  methods: {
    ...mapActions("categ_terms", ["get_terms_descriptions", "get_terms"]),

    async loadTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        parent_id: this.parent_id ? this.parent_id : this.parent.id,
        medical_terms: this.linked_medical_terms,
        type: "term_to_term",
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terms = items;
          this.totalRow = items.length;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.tableTermLoading = false;
          this.isOverlay = false;
        });
    },

    async loadTermDescriptions() {
      let params = {
        ...this.parent.defaultParams(),
        filter: "",
      };

      this.get_terms_descriptions(params).then((_) => {
        if (this.term) {
          this.$nextTick(() => {
            this.term.is_rendered = true;
          });
        }
      });
    },

    onFiltered(items) {
      this.totalRow = items.length;
      this.currentPage = 1;
    },

    bgLightClass(item, type) {
      let key = false;
      if (item) {
        this.parent.linkedMedicalTerm.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },

    onSearchSubmit() {
      this.isOverlay = true;
      this.linked_medical_terms = JSON.stringify(this.parent.linkedMedicalTerm);
      this.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    create_description_success(item) {
      const records = this.$t("label.descriptions");

      if (this.term) {
        this.term.is_rendered = false;
      }

      if (this.termDescForm.action === "Add") {
        this.storeToast(item.description_name, records);
      }

      this.loadTermDescriptions().catch((error) => {
        console.log(error);
      });
    },

    delete_description_success(item) {
      this.$emit("link-success");
    },

    create_term_connection_success(item) {
      this.$emit("link-success");
    },

    onAddDescription() {
      this.termConform.reset();
      this.termConform.action = "Add";
      this.$root.$emit("bv::hide::popover");
    },

    onCancel() {
      this.parent.mtForm.reset();
      this.parent.articleForm.reset();
      this.parent.itemSelected = "";
      // this.parent.filter = "";
      this.$bvModal.hide("link-term-modal");
    },

    onLinkMessage(id) {
      let key = false;
      this.parent.linkedMedicalTerm.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });
      return key;
    },

    onLinked(term, index) {
      let checked = $(`#medterm-${term.id}`).prop("checked");
      this.termName = term.base_name;
      term.is_loading = true;

      if (!checked) {
        this.unLinkConfirm()
          .then((resp) => {
            term.is_rendered = false;
            this.postLink(term, checked);
          })
          .catch((error) => {
            term.is_loading = false;
            this.parent.linkedMedicalTerm.push(term.id);
          });

        return false;
      }

      term.is_rendered = false;
      this.postLink(term, checked);
    },

    linkQuery(data) {
      let params = {
        id: this.parent_id ? this.parent_id : this.parent.id,
        child_id: data.term.id,
        term_types: data.term.has_term_types,
        api_token: this.$user.api_token,
        key: data.key ? "link" : "unlink",
      };

      return new Promise((resolve, reject) => {
        axios
          .post("/api/links/term", params)
          .then((response) => resolve(response))
          .catch((error) => reject(error));
      });
    },

    postLink(term, checked) {
      this.linkQuery({ term: term, key: checked })
        .then((resp) => {
          if (checked) {
            // term.is_linked_term = true;
            this.parent.linkedToast(
              term.base_name,
              this.$t("label.terminilogies")
            );
          } else {
            this.parent.unlinkedToast(
              term.base_name,
              this.$t("label.terminilogies")
            );
            term.is_linked_term = false;
            term.has_descriptions = [];
          }
          this.$emit("link-success");
        })
        .catch((error) => {
          const index = this.parent.linkedMedicalTerm.indexOf(term.id);
          if (index > -1) {
            this.parent.linkedMedicalTerm.splice(index, 1);
          }
          this.$bvToast.show("link-error-toast");
        })
        .finally(() => {
          term.is_loading = false;
          this.$nextTick(() => {
            term.is_rendered = true;
          });
        });
    },

    show_modal(term, modalname) {
      this.term = term;
      this.termDescForm.reset();
      this.termDescForm.action = "Add";
      this.$bvModal.show(modalname);
    },
  },
};
</script>
