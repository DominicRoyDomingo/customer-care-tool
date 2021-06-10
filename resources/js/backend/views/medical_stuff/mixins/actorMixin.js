import { mapActions, mapGetters } from "vuex";
import Form from "../../../helpers/form.js";
import toast from "../../../helpers/toast.js";

import { fetchList, softDelete, getTagsName } from "./../../../api/tags";


// import Edit from "./includes/actor/UpdateComponent.vue";

// import JobDescription from "./includes/actor/jobs_modal/JobDescriptionModal.vue";
import JobDescription from "./../../includes/actor/jobs_modal/JobDescriptionModal";

import PhysicalCodeType from "./../..//includes/physical-code-type/CreateComponent.vue";

import InformationType from "./../..//includes/information-type/CreateComponent.vue";

// import LinkBrandModal from "./../..//includes/actor/LinkBrandComponent.vue";

// import Loading from "vue-loading-overlay";

// import Form from "./../shared/form.js";

// import toast from "./../helpers/toast.js";

export default {

   mixins: [toast],
   components: {
      JobDescription,
      // LinkBrandModal,
      PhysicalCodeType,
      InformationType,
   },

   data: function () {
      return {
         isLoading: true,
         btnloading: false,
         termsFilter: "",
         filter: "",
         itemSelected: "",
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
         tableTotalRows: "",
         searched: "",
         lastPCTID: null,
         termsCurrentPage: 1,
         termsPerPage: 10,
         termsTotalRow: 1,
         tableTermLoading: true,
         isTermOverlay: false,
         currentPage: 1,
         isSpecializationFocused: false,
         perPage: 10,
         sortBy: 'name',
         language: this.$i18n.locale,
         files: [],
         sortDesc: false,

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
               key: "specialization",
               label: this.$t("table.specialization"),
               thClass: "text-center",
               thStyle: { width: "15%" },
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
            term_type: '',
            action: "",
            term_types: '',
            file: ""
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
            type_name: "",
            physical_codes: [],
            other_infos: [],
            specializations: [],
            language: this.$i18n.locale,
         }),

         formData: new FormData(),
      };
   },

   mounted() {
      // this.loadActors();
      this.loadSpecializationCategories();
      // this.loadPhysicalCodeTypes();
   },

   methods: {
      ...mapActions("actor", [
         "get_actors",
         "delete_actor",
         "remove_from_actor_array",
      ]),
      ...mapActions("physical_code_type", ["get_physical_code_types", "get_physical_code_type_all"]),
      ...mapActions("information_type", ["get_information_types", "get_information_type_all"]),
      ...mapActions("jobs", ["get_jobs", "get_job_categories", "delete_job_description", "get_filtered_job_professions"]),
      ...mapActions("brand", ["get_brands"]),

      // loadActors() {
      //    this.isLoading = true;
      //    let params = {
      //       api_token: this.$user.api_token,
      //       lang: this.form.language,
      //       brand_id: this.$brand.id,
      //       entries: this.perPage,
      //       page: this.currentPage,
      //       sort_name: this.sortBy,
      //       sort_desc: this.sortDesc,
      //       search: this.searched,
      //    };
      //    this.get_actors(params).then((_) => {
      //       this.tableTotalRows = this.actors.total
      //       this.isLoading = false;
      //    });
      // },

      successfullySavedService() {
         this.$refs.table.refresh();
      },

      createActor() {
         this.loadTerms();
         this.form.reset();
         this.form.physical_codes = [];
         this.form.specializations = [];
         this.form.other_infos = [];
         this.addPhysicalCode()
         this.addOtherInfoDiv()
         this.addSpecializationDiv()
         this.modalId = this.modal.add.id;
         this.actorModal = this.modal.add.id;
         this.form.language = this.$i18n.locale;
         let params = {
            api_token: this.$user.api_token,
            lang: this.form.language,
         };
         this.get_physical_code_type_all(params).then((_) => {
            this.get_information_type_all(params).then((_) => {
               this.newPhysicalCodeTypes = this.physical_code_types
               this.newInformationTypes = this.information_types
               this.$bvModal.show("actor-add-modal");
            });
         });


      },

      async loadTerms() {
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

      loadSpecializations() {
         let params = {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            type: "description",
            filter: this.form.specializationCategories
         };

         this.get_jobs(params).then(_ => {
            let mapSpecializations = this.specializations.map(s => ({
               id: s.id,
               description: s.description,
               description_name: s.description_name,
               profession_description: s.profession_description,
               created_at: s.created_at,
               is_english: s.is_english,
               is_german: s.is_german,
               is_italian: s.is_italian,
               is_loading: s.is_loading,
               job_category_id: s.job_profession.job_category_profession[0].job_category_id,
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

         this.get_job_categories(params).then(_ => {
            let mapSpecializationCategories = this.categories.map(s => ({
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

      successfullySavedActor() {
         this.$refs.table.refresh();
      },

      loadPhysicalCodeTypes() {
         let params = {
            api_token: this.$user.api_token,
            lang: this.form.language,
         };
         this.get_physical_code_types(params).then((_) => {
            this.newPhysicalCodeTypes = this.physical_code_types;
         });
      },

      loadInformationTypes() {
         let params = {
            api_token: this.$user.api_token,
            lang: this.form.language,
         };
         this.get_information_types(params).then((_) => {
            this.removeDuplicateOtherInfos()
            this.$bvModal.hide("information-type-add-modal");
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
         this.filterPhysicalCodeDiv()
      },

      deletePhysicalCode(index) {
         this.form.physical_codes.splice(index, 1);
         this.removeDuplicatePCT()
         this.filterPhysicalCodeDiv()
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
         this.filterSpecializationDiv()
      },

      deleteSpecializationDiv(index) {
         this.form.specializations.splice(index, 1);
         this.removeDuplicateSpecializations()
         this.filterSpecializationDiv()
      },

      filterSpecializationDiv() {
         let filtered = [];
         this.form.specializations.forEach((data, index) => {
            data.index = index;
            filtered.push(data);
         });
         this.form.specializations = filtered;
      },

      addOtherInfoDiv() {
         this.form.other_infos.push({
            type: "",
            value: "",
         });
         this.filterOtherInfoDiv()
      },

      deleteOtherInfoDiv(index) {
         this.form.other_infos.splice(index, 1);
         this.removeDuplicateOtherInfos()
         this.filterOtherInfoDiv()
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

         this.tableTotalRows = filteredItems.length
         this.currentPage = 1
      },

      loadProfessions() {
         let params = {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            filter: this.form.specializationCategories
         };

         this.get_filtered_job_professions(params).then(resp => {
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
            this.newPhysicalCodeTypes = this.physical_code_types
            this.form.physical_codes.forEach(physicalCode => {
               if (physicalCode.physical_code_type == null) return;
               let data = $.grep(this.newPhysicalCodeTypes, function (e) {
                  return e.id != physicalCode.physical_code_type.id;
               });
               this.newPhysicalCodeTypes = data
            });
         });

         // this.get_physical_code_types(params).then((_) => {
         //    this.newPhysicalCodeTypes = this.physical_code_types
         //    this.form.physical_codes.forEach(physicalCode => {
         //       if (physicalCode.physical_code_type == null) return;
         //       let data = $.grep(this.newPhysicalCodeTypes, function (e) {
         //          return e.id != physicalCode.physical_code_type.id;
         //       });
         //       this.newPhysicalCodeTypes = data
         //    });
         // });
      },

      removeDuplicateOtherInfos() {
         let params = {
            api_token: this.$user.api_token,
            lang: this.form.language,
         };
         this.get_information_types(params).then((_) => {
            this.newInformationTypes = this.information_types
            this.form.other_infos.forEach(otherInfo => {
               if (otherInfo.type == null) return;
               let data = $.grep(this.newInformationTypes, function (e) {
                  return e.id != otherInfo.type.id;
               });
               this.newInformationTypes = data
            });
         });
      },

      removeAllDuplicates() {
         this.removeDuplicateSpecializations()
         this.removeDuplicatePCT()
         this.removeDuplicateOtherInfos()
      },

      removeDuplicateSpecializations() {
         let params = {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            type: "description",
            filter: this.form.specializationCategories
         };

         this.get_jobs(params).then(_ => {
            let mapSpecializations = this.specializations.map(s => ({

               id: s.id,
               description: s.description,
               description_name: s.description_name,
               profession_description: s.profession_description,
               created_at: s.created_at,
               is_english: s.is_english,
               is_german: s.is_german,
               is_italian: s.is_italian,
               is_loading: s.is_loading,
               job_category_id: s.job_profession.job_category_profession[0].job_category_id,
               job_profession_id: s.job_profession_id,
               updated_at: s.updated_at,
            }));
            this.newSpecializations = mapSpecializations;

            this.form.specializations.forEach(s => {
               if (s.profession == null) return;
               let data = $.grep(this.newSpecializations, function (e) {
                  return e.id != s.profession.id;
               });
               this.newSpecializations = data
            });
         });
      },

      hasSpecializationCategory() {

         if (this.form.specializationCategories.length == 0) {
            this.newSpecializations = []
            this.form.specializations = [];
            this.addSpecializationDiv()
            return;
         }
         //  console.log(this.form.specializationCategories)

         this.loadSpecializations();
         this.removeDuplicateSpecializations()
      },

      hasSpecializationCategoryUpdate() {
         if (this.form.specializationCategories.length == 0) {
            this.newSpecializations = []
            this.form.specializations = [];
            this.addSpecializationDiv()
            return;
         }
         this.loadSpecializations();
         this.removeDuplicateSpecializations()
      },

      onLinkBrand(item, index) {
         this.form.reset();
         this.editingIndex = index;
         this.form.language = this.$i18n.locale;
         this.form.id = item.id;
         this.form.brands = item.brands;
         this.modalId = this.modal.brand.id;
         this.$bvModal.show("link-brand-modal");
      },

      onEdit(item, index) {
         let physicalCodes = [];
         let otherInfos = [];
         let specializations = [];

         if (item.physical_code_item != null) {
            item.physical_code_item.forEach(function (data) {
               physicalCodes.push({
                  index: data.index,
                  physical_code_type: data.physical_code_type[0],
                  code: data.code,
               });
            })
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
            })
         } else {
            otherInfos.push({
               type: "",
               value: "",
            });
         }

         if (item.specialization_item != null) {
            item.specialization_item.forEach(function (data) {
               specializations.push({
                  profession: data.profession,
                  specialization: data.specialization,
                  category: data.category,
               });
            })
         } else {
            specializations.push({
               profession: "",
               specialization: "",
               category: "",
            });
         }

         this.editingIndex = index;
         this.form.reset();
         this.form.language = this.$i18n.locale;
         this.form.id = item.id;
         this.form.surname = item.surname;
         this.form.firstname = item.firstname;
         this.form.middlename = item.middlename;
         this.form.specializationCategories = item.specialization_category == null ? "" : item.specialization_category;
         this.form.physical_codes = physicalCodes;
         this.form.specializations = specializations;
         this.form.other_infos = otherInfos;
         // this.$bvModal.show("actor-edit-modal");
         // return;
         this.modalId = this.modal.edit.id;
         let params = {
            api_token: this.$user.api_token,
            lang: this.form.language,
         };
         this.filterOtherInfoDiv()
         this.filterPhysicalCodeDiv()
         this.filterSpecializationDiv()
         this.loadSpecializations();
         this.get_physical_code_types(params).then((_) => {
            this.get_information_types(params).then((_) => {
               this.removeAllDuplicates()
               this.$bvModal.show("actor-edit-modal");
            });
         });
      },

      sortChanged(e) {
         this.sortBy = e.sortBy
         this.sortDesc = e.sortDesc
         this.loadItems()
      },

      onDelete(item, index) {
         let vm = this;
         $.confirm({
            title:
               vm.$t("confirmation_record_delete").charAt(0) +
               vm
                  .$t("confirmation_record_delete")
                  .slice(1)
                  .toLowerCase(),
            content:
               vm.$t("question_record_delete") +
               `${item.fullname}` +
               vm.$t("from") +
               vm.$t("label.actors") +
               vm.$t("records") + "?",
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
                  action: function () { },
               },
            },
         });
      },

      performSearch: _.debounce(function (query) {
         this.searched = query
      }, 1000)

   },

   watch: {

      filter(query) {
         this.performSearch(query)
      },

      currentPage: {
         handler: function (value) {
            this.loadItems()
         }
      },

      perPage: {
         handler: function (value) {
            this.loadItems()
         }
      },

      searched: {
         handler: function (value) {
            this.loadItems()
         }
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
         jobStatus: "jobs/get_job_status"
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

      termDefaultParams() {
         return {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            brand_id: this.$brand.id,
         }
      },

      ...mapGetters("brand", {
         brands: "brands",
      }),

      totalRows() {
         return this.actors.length;
      },
   },


}