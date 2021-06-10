<template>
  <b-modal
    id="add-provider-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light">
        {{ $t("provider_group_add_provider_button") }}
        <small
          class="text-dark text-capitalize d-inline-block text-truncate mt-1 ml-1"
          style="max-width: 600px; letter-spacing: normal"
        />
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="on_cancel">
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
          <b-form @submit.prevent="on_search_submit">
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
                  {{ $t("label.providers") }}
                </h6>
              </template>

              <b-card-body
                id="nav-scroller"
                ref="content"
                class="p-0"
                :class="{ 'div-scroll': items.length > 7 }"
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
                    ref="providerTable"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :per-page="perPage"
                    :busy="tableProviderLoading"
                    :items="items.data"
                    :tbody-tr-class="bg_light_class"
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
                              v-model="parent.addedProviders"
                              @change="on_added(data.item, data.index)"
                              :id="`provider-${data.item.id}`"
                              :name="`provider-${data.item.id}`"
                              :value="data.item.id"
                            >
                              <strong
                                :for="`provider-${data.item.id}`"
                                class="text-capitalize check-box"
                                v-html="data.item.provider_name"
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
                                v-if="isAdded(data.item.id)"
                              >
                                {{ $t("added") }}
                              </span>
                            </b-form-checkbox>
                          </span>
<!-- 
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
                          </b-tooltip> -->

                          <!-- <template v-if="on_isLinked(data.item.id)">
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
                            <div v-else class="ml-3">
                              <b-spinner
                                small
                                label="Small Spinner"
                                class="text-success"
                              />
                            </div>
                          </template> -->
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
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// // Snippets
// import TermDescriptionIndex from "./../../snippets/term_descriptions/index";

// // Modals
// import CreateTermDescription from "./../create-termDescription.modal";

// import medicalMixin from "../../mixins/medicalMixin";
// import termMixin from "../../mixins/termMixin";

export default {
  props: ["parent"],

  data() {
    return {
			showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

			sortBy: "is_linked_term",
      sortDesc: true,

      isPaginate: false,
      tableProviderLoading: true,
      currentPage: 1,
      perPage: 10,
      totalRow: 1,
      filter: null,

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
          label: this.$t("label.adding_to") + " ?",
          filterByFormatted: true,
        },
      ],
    };
  },

	mounted() {
    this.loadProviders().catch((error) => {
      console.log(error);
    });
  },

  methods: {
    ...mapActions("providers", ["get_providers"]),
		...mapActions("provider_group", ["update_provider_group"]),

    async loadProviders(isSearched = false) {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.perPage,
        page: this.currentPage,
        search: this.filter,
				isSearched: isSearched,
				hasGroup: true,
      };

      this.get_providers(params).then((_) => {
        // this.tableTotalRows = this.items.total;
        // this.lastPage = this.items.last_page;
        // if(this.items.data.length == 0) {
        //   this.isLoading = false
        //   return
        // };
        // this.isLoading = false
				this.totalRow = this.items.total;
      })
			.catch((error) => {
				console.log(error);
			})
			.finally(() => {
				this.tableProviderLoading = false;
				this.isOverlay = false;
			});
    },

		isAdded(id) {
      let key = false;
      this.parent.addedProviders.forEach((ele) => {
        if (ele === id) {
          key = true;
        }
      });
      return key;
    },

		on_added(provider, index) {
      let checked = $(`#provider-${provider.id}`).prop("checked");
      // term.is_loading = true;

      if (!checked) {
        this.postLink(provider, checked);

        return false;
      }
      this.postLink(provider, checked);
    },

		bg_light_class(item, type) {
      let key = false;
      if (item) {
        this.parent.addedProviders.forEach((id) => {
          if (id === item.id) {
            key = true;
          }
        });
      }
      return key ? "bg-light" : "";
    },

		on_cancel() {
      // this.parent.filter = "";
      this.$bvModal.hide("add-provider-modal");
    },

		on_search_submit() {
      this.isOverlay = true;
      this.loadProviders(true).catch((error) => {
        console.log(error);
      });
    },

		linkQuery(data) {
      let params = {
        id: this.parent.providerGroupID,
        child_id: data.provider.id,
        api_token: this.$user.api_token,
        key: data.key ? "link" : "unlink",
      };

      return new Promise((resolve, reject) => {
        axios
          .post("/api/provider_group/add-to-provider", params)
          .then((response) => resolve(response))
          .catch((error) => reject(error));
      });
    },

		postLink(provider, checked) {
      this.linkQuery({ provider: provider, key: checked })
        .then((resp) => {
          if (checked) {
            this.parent.makeToast(
              "success",
              this.$t("added"),
							`${provider.provider_name} ` +
							this.$t("toasts.added") +
							` ${this.parent.providerGroupName}`,
            );
          } else {
            this.parent.makeToast(
              "danger",
              this.$t("removed"),
							`${provider.provider_name} ` +
							this.$t("toasts.removed") +
							` ${this.parent.providerGroupName}`,
            );
          }
					let response = resp.data;
					response.index = this.parent.editingIndex;
					this.update_provider_group(response);
        })
        .catch((error) => {
          const index = this.parent.addedProviders.indexOf(provider.id);
          if (index > -1) {
            this.parent.addedProviders.splice(index, 1);
          }
          this.$bvToast.show("link-error-toast");
        })
        .finally(() => {
					this.parent.successfullySavedProviderGroup();
					this.$refs.providerTable.refresh();
        });
    },

		async load_terms() {
      let params = {
        ...this.parent.termDefaultParams,
        perPage: this.perPage,
        page: this.currentPage,
        filter: this.filter,
        parent_id: this.parent_id ? this.parent_id : this.parent.id,
        medical_terms: this.linked_medical_terms,
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terms = items.data;
          this.totalRow = items.total;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.tableProviderLoading = false;
          this.isOverlay = false;
        });
    },
  },

	computed: {
    ...mapGetters("providers", {
      items: "providers",
      providersResponseStatus: "get_response_status",
    }),

    termDefaultParams() {
        return {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          brand_id: this.$brand.id,
        }
    },

    totalRows() {
      return this.items.length;
    },
    
  },

	watch: {

		filter(value) {
      if (!value) {
        this.tableTermLoading = true;
        this.loadProviders().catch((error) => {
          console.log(error);
        });
      }
    },

    perPage: {
      handler: function(value) {
        this.loadProviders()
      }
    },

    currentPage: {
      handler: function(value) {
        this.loadProviders()
      }
    },
  },
};
</script>
