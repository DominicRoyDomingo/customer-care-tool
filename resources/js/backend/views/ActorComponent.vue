<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
      <v-tabs
        show-arrows
        background-color="#F0F3F5"
        color="#2F353A"
        v-model="tab"
        center-active
        grow
      >
        <v-tab>
          <strong>{{ this.$brand.brand_name }}</strong>
          &nbsp;
          {{ $t("label.actors") }}
        </v-tab>
        <v-divider vertical inset v-if="tab_length > 0"></v-divider>
        <v-tab
          v-if="viewing_actor"
          style="background-color: #ebf4fe; color: #329ef4;"
        >
          {{ actorInfo.fullname }}'s Profile &nbsp;
          <v-btn
            icon
            @click="
              [
                (tab_length = 0),
                (tab = 0),
                (viewing_actor = false),
              ]
            "
            color="error"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-tab>
          <v-tab-item>
            <v-card tile flat>
              <v-card-title>
                <v-row>
                  <v-col>
                    <span class="title font-weight-light text--secondary">
                      {{ $t("actor_list") }}
                    </span>
                  </v-col>
                  <v-spacer> </v-spacer>
                  <v-col>
                    <v-btn
                      color="success"
                      @click="createActor()"
                      class="float-right"
                      tile
                    >
                      <v-icon dark>mdi-hand-heart</v-icon>
                      <v-icon small dark style="margin-top: 15px; margin-left: -3px">
                        mdi-plus
                      </v-icon>
                      &nbsp;
                      {{ $t(modal.add.button.new) }}
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card-title>
              <v-card-text>
                <v-row>
                  <v-col cols="12" sm="12" md="2">
                    <v-row no-gutters>
                      <v-col
                        cols="3"
                        class="caption font-weight-regular text--secondary text-right"
                        style="
                          display: flex;
                          justify-content: center;
                          padding-top: 5px;
                        "
                      >
                        {{ $t("button.show") }}
                      </v-col>
                      <v-col cols="6">
                        <b-form-select
                          :options="showEntriesOpt"
                          v-model="perPage"
                          style="border-radius: 0"
                        />
                      </v-col>
                      <v-col
                        cols="3"
                        class="caption font-weight-regular text--secondary"
                        style="
                          display: flex;
                          justify-content: center;
                          padding: 5px 0 0 5px;
                        "
                      >
                        {{ $t("label.entries") }}
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-spacer></v-spacer>
                  <v-col cols="12" sm="12" md="4">
                    <b-input-group class="float-right">
                      <template v-slot:prepend>
                        <b-input-group-text
                          style="
                            background-color: rgba(0, 0, 0, 0.05);
                            border-radius: 0;
                          "
                        >
                          <v-icon size="21">mdi-magnify</v-icon>
                        </b-input-group-text>
                      </template>
                      <b-form-input
                        v-model="filter"
                        :placeholder="$t('search_here')"
                        type="search"
                        style="border-radius: 0; border-left: 0"
                      ></b-form-input>
                    </b-input-group>
                  </v-col>
                </v-row>
                <v-row>
                  <v-container fluid>
                    <b-table
                      striped
                      show-empty
                      stacked="md"
                      ref="table"
                      :fields="tableHeaders"
                      :current-page="currentPage"
                      :per-page="0"
                      :busy="isLoading"
                      :items="actors.data"
                      :sort-by.sync="sortBy"
                      :sort-desc.sync="sortDesc"
                      @sort-changed="sortChanged"
                    >
                      <template v-slot:table-busy>
                        <v-fade-transition>
                          <v-overlay opacity="0.8" style="z-index: 999999 !important">
                            <v-progress-circular
                              indeterminate
                              size="80"
                              width="2"
                              color="white"
                              class="text-center"
                            ></v-progress-circular>
                          </v-overlay>
                        </v-fade-transition>
                      </template>

                      <template v-slot:cell(fullname)="data">
                        <div style="margin: 10px 0 0 25px">
                          <a href="#" class="font-weight-bold" @click.prevent="showActorInformation(data.item)">
                            {{ data.item.fullname }}
                          </a>
                          <!-- <span
                            class="text-subtitle-1 text--secondary font-weight-bold"
                          >
                            
                          </span> -->
                        </div>
                      </template>

                      <template v-slot:cell(identification_code)="data">
                        <div style="margin-top: 10px">
                          <span
                            class="text-left text--secondary"
                            v-for="physical_code in data.item.physical_code_item"
                            :key="physical_code.index"
                          >
                            {{ physical_code["physical_code_and_description"][0]
                            }}<br />
                          </span>
                        </div>
                      </template>

                      <template v-slot:cell(actions)="data">
                        <div style="margin-top: 10px">
                          <v-menu offset-y>
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                color="#c8ced3"
                                light
                                v-bind="attrs"
                                v-on="on"
                                tile
                                depressed
                                small
                              >
                                {{ $t("button.more_actions") }}
                                <v-icon small right> mdi-chevron-down </v-icon>
                              </v-btn>
                            </template>
                            <v-list dense class="profile__row-menu">
                              <v-list-item-group color="primary">
                                <v-list-item @click="onEdit(data.item, data.index)">
                                  <v-list-item-icon>
                                    <v-icon color="yellow darken-3">
                                      mdi-map-marker-check
                                    </v-icon>
                                  </v-list-item-icon>
                                  <v-list-item-content>
                                    <v-list-item-title>
                                      {{ $t("button.edit") }}
                                    </v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                                <v-list-item @click="onDelete(data.item, data.index)">
                                  <v-list-item-icon>
                                    <v-icon color="red lighten-1">
                                      mdi-map-marker-remove
                                    </v-icon>
                                  </v-list-item-icon>
                                  <v-list-item-content>
                                    <v-list-item-title>
                                      {{ $t("button.delete") }}
                                    </v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                                <v-list-item
                                  @click="onLinkBrand(data.item, data.index)"
                                >
                                  <v-list-item-icon>
                                    <v-icon color="blue lighten-1">
                                      mdi-briefcase-plus
                                    </v-icon>
                                  </v-list-item-icon>
                                  <v-list-item-content>
                                    <v-list-item-title>
                                      {{ $t("label.link_to_brand") }}
                                    </v-list-item-title>
                                  </v-list-item-content>
                                </v-list-item>
                              </v-list-item-group>
                            </v-list>
                          </v-menu>
                        </div>
                      </template>
                    </b-table>
                  </v-container>
                </v-row>
                <v-row>
                  <v-spacer></v-spacer>
                  <v-col>
                    <b-pagination
                      v-if="!this.isLoading"
                      v-model="currentPage"
                      :total-rows="tableTotalRows"
                      :per-page="perPage"
                    ></b-pagination>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <actorDiv :parent="this"></actorDiv>
          </v-tab-item>
        </v-tabs>

      <Create :parent="this" @loadTable="loadItems"></Create>
      <PhysicalCodeType
        :$this="this"
        @loadTable="loadPhysicalCodeTypes"
      ></PhysicalCodeType>
      <InformationType
        :$this="this"
        @loadTable="loadInformationTypes"
      ></InformationType>
      <CreateMedicalTermModal :parent="this" @done-success="loadTerms()" />
      <!-- <LinkTerminologies
          v-if="itemSelected"
          :items="items"
          :parent="this"
          @link-success="link_success"
        /> -->
      <JobDescription :parent="this"></JobDescription>
      <JobCategory :parent="this"></JobCategory>
      <JobProfession :parent="this"></JobProfession>
      <LinkBrandModal :$this="this"></LinkBrandModal>
      <CreateBrand :$this="this" :parent="this"></CreateBrand>
      <Edit :parent="this"></Edit>
      <LinkTermsModal :parent="this"></LinkTermsModal>
      <InformationListModal :parent="this"></InformationListModal>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./includes/actor/CreateComponent.vue";

import Edit from "./includes/actor/UpdateComponent.vue";

import CreateMedicalTermModal from "./includes/actor/CreateLinkTermComponent.vue";

import LinkTermsModal from "./includes/actor/LinkTermsComponent.vue";

import JobDescription from "./includes/actor/jobs_modal/JobDescriptionModal.vue";

import JobCategory from "./includes/actor/jobs_modal/JobCategoryModal.vue";

import JobProfession from "./includes/actor/jobs_modal/JobProfessionModal.vue";

import PhysicalCodeType from "./includes/physical-code-type/CreateComponent.vue";

import InformationType from "./includes/information-type/CreateComponent.vue";

import InformationListModal from "./includes/actor/InformationListModal.vue";

import actorDiv from "./includes/actor/components/actor_div.vue";

import CreateBrand from "./includes/brands/CreateeComponent.vue";

import LinkBrandModal from "./includes/actor/LinkBrandComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import text from "./../helpers/text.js";

export default {
  mixins: [toast, text],

  components: {
    Create,

    Loading,

    Edit,

    actorDiv,

    JobDescription,

    JobCategory,

    JobProfession,

    LinkBrandModal,

    PhysicalCodeType,

    InformationType,

    CreateBrand,

    CreateMedicalTermModal,

    LinkTermsModal,

    InformationListModal,
  },

  data: function () {
    return {
      language: this.$i18n.locale,
      isLoading: true,
      tableTermLoading: true,
      btnloading: false,
      isSpecializationFocused: false,
      viewing_actor: false,
      isTermOverlay: false,
      sortDesc: false,
      sortBy: "name",
      filter: "",
      termsFilter: "",
      tableTotalRows: "",
      itemSelected: "",
      searched: "",
      lastPCTID: null,
      tab: null,
      newSpecializations: [],
      newPhysicalCodeTypes: [],
      newSpecializationCategories: [],
      newInformationTypes: [],
      professionOption: [],
      new_other_infos: [],
      new_physical_codes: [],
      new_specializations: [],
      linkedMedicalTerm: [],
      terminologies: [],
      files: [],
      actorInfo: [],
      currentPage: 1,
      perPage: 10,
      termsCurrentPage: 1,
      termsPerPage: 10,
      termsTotalRow: 1,
      tab_length: 0,
      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

      languageOptions: [
        { value: "en", text: "English" },
        { value: "de", text: "German" },
        { value: "it", text: "Italian" },
        { value: "ph-fil", text: "Filipino" },
        { value: "ph-bis", text: "Visayan" },
      ],

      tableHeaders: [
        {
          key: "fullname",
          label: this.$t("table.full_name"),
          thClass: "text-left",
          sortable: true,
        },

        {
          key: "identification_code",
          label: this.$t("table.identification_code"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      modal: {
        add: {
          name: "actor_add_new_button",

          id: "actor-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "actor_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "actor_add_edit_button",

          id: "actor-edit-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "actor_add_edit_button",

            loading: false,
          },
        },

        brand: {
          name: "label.link_to_brand",

          id: "link-brand-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_add_new_button",

            loading: false,
          },
        },

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

        createPhysicalCodeType: {
          name: "physical_code_type_new_button",

          id: "physical-code-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "physical_code_type_new_button",

            loading: false,
          },
        },

        createInformationType: {
          name: "information_type_new_button",

          id: "information-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "information_type_new_button",

            loading: false,
          },
        },

        createJobDescription: {
          name: "job_description_new_button",

          id: "job-description-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "job_description_new_button",

            loading: false,
          },
        },
      },

      rules: {
        required: (value) => !!value || "Required.",
        counter: (value) => value.length <= 20 || "Max 20 characters",
        email: (value) => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Invalid email.";
        },
        number: (value) =>
          Number.isInteger(Number(value)) || "The value must be numeric",
      },

      mtForm: new Form({
        id: "",
        name: "",
        term_types: "",
        specializations: "",
        action: "",
        file: "",
      }),

      typeForm: new Form({
        id: "",
        name: "",
        term_type: "",
        action: "",
        term_types: "",
        file: "",
      }),

      form: new Form({
        id: "",
        category: "",
        surname: "",
        firstname: "",
        middlename: "",
        profession: "",
        description: "",
        brand_name: "",
        website: "",
        logo: "",
        isDefault: 0,
        code: "",
        brands: "",
        specialization: "",
        specializationCategories: "",
        fieldOfProfessions: "",
        medical_terms: [],
        physical_codes: [],
        other_infos: [],
        specializations: [],
        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("actor", [
      "get_actors",
      "delete_actor",
      "remove_from_actor_array",
    ]),
    ...mapActions("physical_code_type", ["get_physical_code_type_all"]),
    ...mapActions("information_type", ["get_information_type_all"]),
    ...mapActions("jobs", [
      "get_jobs",
      "get_job_categories",
      "delete_job_description",
      "get_filtered_job_professions",
    ]),
    ...mapActions("brand", ["get_brands"]),
    ...mapActions("categ_terms", ["get_professional_terms"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
      };
      this.get_actors(params).then((_) => {
        this.tableTotalRows = this.actors.total;
        this.isLoading = false;
      });
    },

    successfullySavedService() {
      this.$refs.table.refresh();
    },

    onReset() {},

    createActor() {
      this.form.reset();
      this.loadTerms();
      this.form.physical_codes = [];
      this.form.other_infos = [];
      this.form.fieldOfProfessions = [];
      this.linkedMedicalTerm = [];
      this.addPhysicalCode();
      this.addOtherInfoDiv();
      this.modalId = this.modal.add.id;
      this.actorModal = this.modal.add.id;
      this.form.language = this.$i18n.locale;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
      };
      this.get_physical_code_type_all(params).then((_) => {
        this.get_information_type_all(params).then((_) => {
          this.newPhysicalCodeTypes = this.physical_code_types;
          this.newInformationTypes = this.information_types;
          this.$bvModal.show("actor-add-modal");
        });
      });
    },
    
    loadSpecializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        type: "description",
        filter: this.form.specializationCategories,
      };

      this.get_jobs(params).then((_) => {
        let mapSpecializations = this.specializations.map((s) => ({
          id: s.id,
          description: s.description,
          description_name: s.description_name,
          profession_description: s.profession_description,
          created_at: s.created_at,
          is_english: s.is_english,
          is_german: s.is_german,
          is_italian: s.is_italian,
          is_loading: s.is_loading,
          job_category_id:
            s.job_profession.job_category_profession[0].job_category_id,
          job_profession_id: s.job_profession_id,
          updated_at: s.updated_at,
        }));
        this.newSpecializations = mapSpecializations;
      });
    },

    loadSpecializationCategories() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_job_categories(params).then((_) => {
        let mapSpecializationCategories = this.categories.map((s) => ({
          category: s.category,
          category_name: s.category_name,
          created_at: s.created_at,
          id: s.id,
          is_english: s.is_english,
          is_german: s.is_german,
          is_italian: s.is_italian,
          is_loading: s.is_loading,
          updated_at: s.updated_at,
        }));
        this.newSpecializationCategories = mapSpecializationCategories;
      });
    },

    loadProfessionalTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
      this.get_professional_terms(params).then((_) => {
      });
    },

    successfullySavedActor() {
      this.termsFilter = "";
      this.$refs.table.refresh();
    },

    async loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {});
    },

    loadPhysicalCodeTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_physical_code_type_all(params).then((_) => {
        this.removeDuplicatePCT();
        this.$bvModal.hide("physical-code-type-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    loadInformationTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_information_type_all(params).then((_) => {
        this.$bvModal.hide("information-type-add-modal");
        this.newInformationTypes = this.information_types;
        this.$bvModal.show(this.modalId);
      });
    },

    serviceTypePage() {
      window.location.href = `/admin/service-type`;
    },

    physicalCodeTypePage() {
      window.location.href = `/admin/physical-code-type`;
    },

    informationTypePage() {
      window.location.href = `/admin/information-type`;
    },

    addPhysicalCode() {
      this.form.physical_codes.push({
        physical_code_type: "",
        code: "",
      });
      this.filterPhysicalCodeDiv();
    },

    deletePhysicalCode(index) {
      this.form.physical_codes.splice(index, 1);
      this.removeDuplicatePCT();
      this.filterPhysicalCodeDiv();
    },

    filterPhysicalCodeDiv() {
      let filtered = [];
      this.form.physical_codes.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.physical_codes = filtered;
    },

    addSpecializationDiv() {
      this.form.specializations.push({
        profession: "",
        specialization: "",
        category: "",
      });
      this.filterSpecializationDiv();
    },

    deleteSpecializationDiv(index) {
      this.form.specializations.splice(index, 1);
      this.removeDuplicateSpecializations();
      this.filterSpecializationDiv();
    },

    filterSpecializationDiv() {
      let filtered = [];
      this.form.specializations.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.specializations = filtered;
    },

    async loadTerms() {
      this.loadProfessionalTerms();
      let params = {
        ...this.termDefaultParams,
        perPage: this.termsPerPage,
        page: this.termsCurrentPage,
        filter: this.termsFilter,
      };

      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terminologies = items.data;
          this.termsTotalRow = this.filter ? items.data.length : items.total;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.tableTermLoading = false;
          this.isTermOverlay = false;
        });
    },

    addOtherInfoDiv() {
      this.form.other_infos.push({
        type: null,
        value: "",
      });
      this.filterOtherInfoDiv();
    },

    deleteOtherInfoDiv(index) {
      this.form.other_infos.splice(index, 1);
      this.filterOtherInfoDiv();
    },

    filterOtherInfoDiv() {
      let filtered = [];
      this.form.other_infos.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.other_infos = filtered;
    },

    onFiltered(filteredItems) {
      this.tableTotalRows = filteredItems.length;
      this.currentPage = 1;
    },

    loadProfessions() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.form.specializationCategories,
      };

      this.get_filtered_job_professions(params).then((resp) => {
        this.professionOption = resp;
        this.$bvModal.show("job-description-modal");
      });
    },

    removeDuplicatePCT() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
      };
      this.get_physical_code_type_all(params).then((_) => {
        this.newPhysicalCodeTypes = this.physical_code_types;
        this.form.physical_codes.forEach((physicalCode) => {
          if (physicalCode.physical_code_type == null) return;
          let data = $.grep(this.newPhysicalCodeTypes, function (e) {
            return e.id != physicalCode.physical_code_type.id;
          });
          this.newPhysicalCodeTypes = data;
        });
      });
    },

    removeAllDuplicates() {
      this.removeDuplicatePCT();
    },

    removeDuplicateSpecializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        type: "description",
        filter: this.form.specializationCategories,
      };

      this.get_jobs(params).then((_) => {
        let mapSpecializations = this.specializations.map((s) => ({
          id: s.id,
          description: s.description,
          description_name: s.description_name,
          profession_description: s.profession_description,
          created_at: s.created_at,
          is_english: s.is_english,
          is_german: s.is_german,
          is_italian: s.is_italian,
          is_loading: s.is_loading,
          job_category_id:
            s.job_profession.job_category_profession[0].job_category_id,
          job_profession_id: s.job_profession_id,
          updated_at: s.updated_at,
        }));
        this.newSpecializations = mapSpecializations;

        this.form.specializations.forEach((s) => {
          if (s.profession == null) return;
          let data = $.grep(this.newSpecializations, function (e) {
            return e.id != s.profession.id;
          });
          this.newSpecializations = data;
        });
      });
    },

    hasSpecializationCategory() {
      if (this.form.specializationCategories.length == 0) {
        this.newSpecializations = [];
        this.form.specializations = [];
        this.addSpecializationDiv();
        return;
      }
      //  console.log(this.form.specializationCategories)

      this.loadSpecializations();
      this.removeDuplicateSpecializations();
    },

    hasSpecializationCategoryUpdate() {
      if (this.form.specializationCategories.length == 0) {
        this.newSpecializations = [];
        this.form.specializations = [];
        this.addSpecializationDiv();
        return;
      }
      this.loadSpecializations();
      this.removeDuplicateSpecializations();
    },

    onLinkBrand(item, index) {
      this.form.reset();
      this.editingIndex = index;
      this.modalId = this.modal.brand.id;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.name = item.fullname;
      this.form.brands = item.brands;
      this.modalId = this.modal.brand.id;
      this.$bvModal.show("link-brand-modal");
    },

    setObjectToArray(array) {
      if (array.length < 1 && !array) {
        return [];
      }
      let items = [];
      array.forEach((ele) => {
        items.push(ele.id);
      });
      return items;
    },

    searchProfession(termTypes) {
        let professionKeywords = [
          'Professional', 
          'Professionals', 
          'Professionista',
        ]

        for (var i=0; i < termTypes.length; i++) {
            for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
              if (professionKeywords.includes(value)) {
                return termTypes[i];
              }
            }
        }
    },

    searchTitle(otherInfos) {
      let title = [
        'Title',
      ]

      for (var i=0; i < otherInfos.length; i++) {
          for (const [key, value] of Object.entries(JSON.parse(otherInfos[i].type[0].name))) {
            if (title.includes(value)) {
              return otherInfos[i].value;
            }
          }
      }

      return null;
    },

    removeTitle(otherInfos) {
      let title = [
        'Title',
      ]

      for (var i=0; i < otherInfos.length; i++) {
        for (const [key, value] of Object.entries(JSON.parse(otherInfos[i].type[0].name))) {
          if (title.includes(value)) {
            otherInfos.splice(i, 1);
            return otherInfos;
          }
        }
      }

      return otherInfos;
    },

    showActorInformation(item) {
      this.viewing_actor = false;
      this.loadTerms()
      this.loadActorInformation(item)
      
      this.tab_length = 1;
      this.tab = 1;
      this.viewing_actor = true;
    },

    loadActorInformation(item) {
      let title = null
      let newOtherInfoArr = null;
      this.editingIndex = item.actor_index;
      this.actorInfo = item
      if(item.other_info_item != null) {
        title = this.searchTitle(item.other_info_item);
        newOtherInfoArr = this.removeTitle(item.other_info_item);
      }
      this.actorInfo['title'] = title;
      this.actorInfo['new_other_info_item'] = newOtherInfoArr;
      this.linkedMedicalTerm = item.medical_terms;
    },

    onEdit(item, index) {
      this.loadTerms();
      let physicalCodes = [];
      let otherInfos = [];

      if (item.physical_code_item != null) {
        item.physical_code_item.forEach(function (data) {
          physicalCodes.push({
            index: data.index,
            physical_code_type: data.physical_code_type[0],
            code: data.code,
          });
        });
      } else {
        physicalCodes.push({
          physical_code_type: "",
          code: "",
        });
      }
      if (item.other_info_item != null) {
        item.other_info_item.forEach(function (data) {
          otherInfos.push({
            type: data.type[0],
            value: data.value,
          });
        });
      } else {
        otherInfos.push({
          type: "",
          value: "",
        });
      }
      // console.log(item)
      // return;
      this.editingIndex = index;
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.surname = item.surname;
      this.form.firstname = item.firstname;
      this.form.middlename = item.middlename;
      this.form.specializationCategories =
        item.specialization_category == null
          ? ""
          : item.specialization_category;
      this.form.fieldOfProfessions = item.actor_field_of_professions_items
      this.form.physical_codes = physicalCodes;
      this.form.other_infos = otherInfos;
      this.linkedMedicalTerm = item.medical_terms;
      this.modalId = this.modal.edit.id;
      this.actorModal = this.modal.edit.id;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
      };
      this.filterOtherInfoDiv();
      this.filterPhysicalCodeDiv();
      this.removeAllDuplicates();
      this.get_information_type_all(params).then((_) => {
        this.newInformationTypes = this.information_types;
        this.$bvModal.show("actor-edit-modal");
      });
    },

    sortChanged(e) {
      this.sortBy = e.sortBy;
      this.sortDesc = e.sortDesc;
      this.loadItems();
    },

    onDelete(item, index) {
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
          vm.$t("confirmation_record_delete").slice(1).toUpperCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.fullname}` +
          vm.$t("from") +
          vm.$t("label.actors") +
          vm.$t("records") +
          "?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function (value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.delete_actor(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (resp.data == false) {
                      vm.makeToast(
                        "danger",
                        vm.$t("data_used"),
                        vm.$t("used_data_alert")
                      );
                      return false;
                    }
                    if (vm.actorsResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.fullname}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.actors") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function () {},
          },
        },
      });
    },

    performSearch: _.debounce(function (query) {
      this.searched = query;
    }, 1000),
  },

  watch: {
    filter(query) {
      this.performSearch(query);
    },

    currentPage: {
      handler: function (value) {
        this.loadItems();
      },
    },

    perPage: {
      handler: function (value) {
        this.loadItems();
      },
    },

    searched: {
      handler: function (value) {
        this.loadItems();
      },
    },

    termsCurrentPage(value) {
      this.isTermOverlay = true;
      this.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    termsPerPage() {
      this.isTermOverlay = true;
      this.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    termsFilter(value) {
      if (!value) {
        this.tableTermLoading = true;
        this.loadTerms().catch((error) => {
          console.log(error);
        });
      }
    },
  },

  computed: {
    ...mapGetters("actor", {
      actors: "actors",
      // categories: "categories",
      actorsResponseStatus: "get_response_status",
    }),

    ...mapGetters({
      specializations: "jobs/get_job_items",
      categories: "jobs/get_job_categories",
      jobStatus: "jobs/get_job_status",
    }),

    ...mapGetters("physical_code_type", {
      physical_code_types: "physical_code_type_all",
      // categories: "categories",
      physicalCodeTypesResponseStatus: "get_response_status",
    }),

    ...mapGetters("information_type", {
      information_types: "information_type_all",
      // categories: "categories",
      informationTypesResponseStatus: "get_response_status",
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters("categ_terms", {
      professionalTerms: "get_professional_terms_items",
    }),

    termDefaultParams() {
      return {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
    },

    totalRows() {
      return this.actors.length;
    },
  },
};
</script>
