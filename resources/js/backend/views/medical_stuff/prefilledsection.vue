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
              {{$t('label.prefilled_section')}}
            </h4>
          </v-col>
          <v-col cols="12" sm="2" md="2">
            <v-btn color="success" block tile @click="onAddPreFilledSection">
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{$t('label.new_section')}}
            </v-btn>
          </v-col>
          
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

            </b-form>
          </div>
          <div class="col-md-4">
            <div class="">
              <b-form inline @submit.prevent="on_search_submit">
                <el-input
                  prefix-icon="el-icon-search"
                  v-model="filter"
                  autocomplete="off"
                  @input="on_search_submit"
                  clearable
                >
                </el-input>
              </b-form>
            </div>

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
                <template v-slot:cell(name)="data">
                  <span>
                    <ul class="list-unstyled pl-0">
                      <b-media tag="li">
                        <span
                          class="mt-0 mb-1 font-weight-bold"
                          v-html="data.item.section_name"
                        />
                      </b-media>
                    </ul>
                  </span>
                </template>

                <template v-slot:cell(publishing_item_type_articles)="data">
                  <div >
                    <span
                      class="badge badge-info text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="view_item_name(data.item.item_type, 'item_type')"
                    />
                  </div>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </template>

                <template v-slot:cell(actions)="data">
                  <v-btn
                        color="#c8ced3"
                        small
                        :disabled="data.item.is_loading"
                        @click="onEditPreFilledSection(data.item)"
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
                        <span v-else>
                          <b-icon
                            icon="pencil-square"
                            variant="success"
                          />
                          {{ $t("button.edit") }}
                        </span>
                  </v-btn>

                  <v-btn
                        color="#c8ced3"
                        small
                        :disabled="data.item.is_loading"
                        @click="on_delete(data.item)"
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
                        
                        <span v-else>
                          <b-icon
                            icon="trash-fill"
                            variant="danger"
                          />
                           {{ $t("button.delete") }}
                        </span>
                  </v-btn>
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

        <PreFilledSectionModal 
          v-if="this.modal_create.isVisible" 
          :parent="this" 
          @loadData="load_items"
          @updatePrefilledSectionList="notfcation_show_update"
          @createdData="notfcation_create"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import MedicalMixin from "./mixins/medicalMixin";
import termMixin from "./mixins/termMixin";
import PrefilledSectionMixin from "./mixins/prefilledSectionMixin";

// Modals
import AdvanceSearchTerms from "./snippets/advance_search_terms";
import PreFilledSectionModal from "./modals/create-prefilledsection.modal.vue";


export default {
  mixins: [MedicalMixin,termMixin, PrefilledSectionMixin],

  components: {
    PreFilledSectionModal,
    AdvanceSearchTerms,    
  },

  data() {
    return {
      isCreatedTerm: false,
      isCreatedPrefilled: false,
      isLoading: true,
      btnloading: false,
      isLinked: false,
      searchItems: {},
      items: [],
      form: {},
      formData: new FormData(),
      form: {},
      show_rows_total: 10,

      html_tags:[
      ],
      
      tableHeaders: [
        {
          key: "name",
          label: this.$t("label.prefilled_section").toUpperCase(),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "publishing_item_type_articles",
          label: this.$t("type_of_publising_item").toUpperCase(),
          thClass: "text-left",
          tdClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at").toUpperCase(),
          thStyle: { width: "25%" },
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },
        {
          key: "actions",
          label: this.$t("table.action").toUpperCase(),
          thStyle: { width: "25%" },
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
      ],

      modal_create: {
        isVisible: false,
        type: "create",
        button: {
          name: "SAVE",
          save: "save_button",
          cancel: "cancel",
          loading: false,
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

  mounted() {
    this.loadAll();
  },

  computed: {
    ...mapGetters("categ_terms", {
      status: "get_response_status",
      linkedTerms: "get_linked_terms_items",
      providerTypeTerms: "get_linked_provider_terms_items",
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

    ...mapActions("prefilledsection", [
      "post_delete_prefilledsection",
    ]),

    load_items() {
      let params = {
        ...this.SectionDefaultParams,
        ...this.set_params_detail(),
      };

      axios
        .get("/api/prefilledsection/", { params })
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

    loadAll() {   
      this.load_items();
    },

    on_search_submit(value) {
      this.isLinked = true;
      this.currentPage = 1;
      this.load_items();
    },

    on_search_key() {
      this.searchItems = {};
    },

    close_modal() {
      this.isCreatedTerm = false;
      this.mtForm.reset();
      this.articleForm.reset();
      this.itemSelected = "";
    },

    on_reset_search() {
      this.filter = "";
    },

    sort_by_language(value) {
      this.isLinked = true;
      this.sortbyLang = value;
      this.currentPage = 1;
      this.searchItems = {};
      this.load_items();
    },

    set_params_detail() {
      return {
        filter: this.filter,
        sortbyLang: this.sortbyLang,
        perPage: this.perPage,
        page: this.currentPage,
      };
    },

    onFiltered(items) {
      this.total_rows = items.length;
      this.currentPage = 1;
    },

    filterbyLang(value) {
      this.isLoading = true;
      this.sortbyLang = value;
      this.sortbyTerm = null;
      this.load_items();
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = "Pre Filled Section";

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };

          this.post_delete_prefilledsection(params)
            .then((_) => {
              this.deleteToast(baseName, records);
              this.items.splice(
                this.getRemoveItemIndex(this.items, item.id),
                1
              );
              this.load_items();
              
            })
            .catch((error) => {
              console.log(error);
            });
          }
        });
    },

    onAddPreFilledSection() {
      this.isCreatedPrefilled = true;
      this.modal_create.isVisible = true;
      this.modal_create.title = "New Pre-Filled Section";
      this.modal_create.type = "create";
      this.modal_create.button.name = "Save";
      this.isLinked = true;

      setTimeout(() => {
        this.isLinked = false;
        this.btnloading = false;
        this.$bvModal.show("create-prefilledsection");
      }, 1000);
    },

    onEditPreFilledSection(item) {
      let baseName = item.base_name;

      this.isLinked = true;
      this.isCreatedPrefilled = true;
      this.modal_create.isVisible =true;

      this.modal_create.title = "Edit " + baseName;
      this.modal_create.button.name = "Save Changes";
      this.modal_create.type = "edit";
      this.modal_create.form = item; 
      
      setTimeout(() => {
        this.isLinked = false;
        this.btnloading = false;
        this.$bvModal.show("create-prefilledsection");
      }, 1000);
    },

    notfcation_show_update(e){
      this.updateToast(e, `Pre Filled Section`);
    },

    notfcation_create(e){
      this.storeToast(e, `Pre Filled Section`);
    }

  },
};
</script>
