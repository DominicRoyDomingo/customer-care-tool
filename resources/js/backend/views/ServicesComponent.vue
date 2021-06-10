<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("service_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createService()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-hand-heart</v-icon>
                <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
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
                  style="display:flex; justify-content: center; padding-top: 5px"
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
                  style="display:flex; justify-content: center; padding: 5px 0 0 5px"
                >
                  {{ $t("label.entries") }}
                </v-col>
              </v-row>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="12" md="4">
              <b-input-group class="float-right">
                <template v-slot:prepend>
                  <v-menu offset-y :rounded="false">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="info"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        tile
                        depressed
                        style="height: 35px; margin-right: 1px;"
                      >
                        <v-icon :size="18">
                          mdi-filter-menu
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-list dense class="profile__row-menu">
                      <v-list-item-group color="primary">
                        <v-list-item v-for="(option, index) in noTranslationsOptions" :key="index">
                          <v-list-item-content @click="sortbyLang(option.value)">
                            <v-list-item-title>
                              {{ option.text }}
                            </v-list-item-title>
                          </v-list-item-content>
                        </v-list-item>
                      </v-list-item-group>
                    </v-list>
                  </v-menu>
                </template>
                <b-form-input
                  v-model="filter"
                  :placeholder="$t('search_here')"
                  type="search"
                  style="border-radius: 0; border-left: 0"
                ></b-form-input>
                
                <template v-slot:append>
                  <b-input-group-text
                    style="background-color: rgba(0,0,0,0.05); 
                      border-radius: 0;"
                  >
                    <v-icon size="21">mdi-magnify</v-icon>
                  </b-input-group-text>
                </template>
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
                :items="items.data"
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

                <template v-slot:cell(name)="data">
                  <div style="margin: 10px 0 0 25px;">
                    <span
                      class="text-subtitle-1 text--secondary font-weight-bold"
                       v-html="listDisplay(data.item.service_name)"
                    >
                    </span>
                  </div>
                </template>

                <template v-slot:cell(service_type)="data">
                  <div style="margin-top: 10px">
                    <span 
                      class="text-left text--secondary"
                      v-html="listDisplay(data.item.service_type_item.name)"
                    >
                    </span>
                  </div>
                </template>

                <template v-slot:cell(created_at)="data">
                  <div style="margin-top: 10px">
                    <span class="text--disabled">
                      {{ data.item.created_at | moment("DD/MM/YYYY") }}
                    </span>
                  </div>
                </template>

                <template v-slot:cell(updated_at)="data">
                  <div style="margin-top: 10px">
                    <span class="text--disabled">
                      {{ data.item.updated_at | moment("DD/MM/YYYY") }}
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
                          <v-icon small right>
                            mdi-chevron-down
                          </v-icon>
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
                          <v-list-item @click="onLinkBrand(data.item, data.index)">
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

      
      <Create :$this="this" @loadTable="loadItems"></Create>

      <Edit :$this="this"></Edit>

      <CreateBrand :$this="this" :parent="this"></CreateBrand>

      <LinkBrandModal :$this="this"></LinkBrandModal>

      
    </v-app>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./includes/services/CreateComponent.vue";

import Edit from "./includes/services/UpdateComponent.vue";

import LinkBrandModal from "./includes/services/LinkBrandComponent.vue";

import CreateBrand from "./includes/brands/CreateeComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import text from "./../helpers/text.js";

export default {
  mixins: [toast, text],

  components: {
    Create,

    Loading,

    LinkBrandModal,

    Edit,

    CreateBrand
  },

  data: function() {
    return {
      isLoading: true,

      btnloading: false,

      filter: "",

      tableTotalRows: "",
      
      nameTranslation: "",

      sortByLang: "",

      searched: "",

      currentPage: 1,

      perPage: 10,

      sortBy: 'name',

      files: [],

      newSpecializationCategories: [],

      sortDesc: false,

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],

      tableHeaders: [
        {
          key: "name",
          label: this.$t("label.name"),
          thClass: "text-left",
          sortable: true,
        },

        {
          key: "service_type",
          label: this.$t("service_type_category"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "updated_at",
          label: this.$t("table.updated_at"),
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
          name: "service_add_new_button",

          id: "service-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "service_add_edit_button",

          id: "service-edit-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "service_add_new_button",

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

        createService: {
          name: "service_type_add_new_button",

          id: "service-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_type_add_new_button",

            loading: false,
          },
        },
      },
      
      currentLanguage: this.$i18n.locale,

      translations: {
        en: {
          title: "Edit Service",
          name: "Name",
          button: "Save Changes",
          cancel: "Cancel",
          name_is_required: "Name is required.",
          service_type_category: "Service Type",
          new_service_type: "New Service Type",
          description: "Description",
          image: "Image",
        },

        it: {
          title: "Modifica servizio",
          name: "Nome",
          button: "Salva il resto",
          cancel: "Annulla",
          name_is_required: "Il nome è obbligatorio.",
          service_type_category: "Tipologia di servizio",
          new_service_type: "Nuovo tipo di servizio",
          description: "Descrizione",
          image: "Immagine",
        },

        de: {
          title: "Service bearbeiten",
          name: "Name",
          button: "Änderungen speichern",
          cancel: "Stornieren",
          name_is_required: "Name ist erforderlich.",
          service_type_category: "Servicetyp",
          new_service_type: "Neuer Servicetyp",
          description: "Beschreibung",
          image: "Bild",
        },

        ph_fil: {
          title: "I-edit ang Serbisyo",
          name: "Pangalan",
          button: "I-save ang mga pagbabago",
          cancel: "Kanselahin",
          name_is_required: "Pangalan ang kailangan.",
          service_type_category: "Uri ng Serbisyo",
          new_service_type: "Bagong Uri ng Serbisyo",
          description: "Paglalarawan",
          image: "Larawan",
        },

        ph_bis: {
          title: "Pag-edit sa Serbisyo",
          name: "Ngalan",
          button: "E-save ang kausaban",
          cancel: "Kanselahon",
          name_is_required: "Kinahanglan ang ngalan.",
          service_type_category: "Klase sa Serbisyo",
          new_service_type: "Bag-ong Tipo sa Serbisyo",
          description: "Paghulagway",
          image: "Litrato",
        },
      },

      noTranslationsOptions: [
        { value: 'all', text: "All" },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],


      form: new Form({
        id: "",

        name: "",

        service_type: "",

        service_type_name: "",

        brands: "",

        brand_name: "",

        website: "",
                
        logo: "",
                
        isDefault: 0,

        description: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("services", [
      "get_services",
      "delete_service",
      "remove_from_service_array",
      "check_if_has_link_or_article",
    ]),
    ...mapActions("service_type", ["get_service_type_all"]),
    ...mapActions("jobs", ["get_jobs", "get_job_categories", "delete_job_description", "get_filtered_job_professions"]),
    ...mapActions("brand", ["get_brands"]),

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
        sortbyLang: this.sortByLang
      };
      console.log(params)
      this.get_services(params).then((_) => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
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

    async loadBrands() {
        let params = {
            api_token: this.$user.api_token,
            org_id: this.$org_id,
        };
        this.get_brands(params).then((_) => {
        });
    },

    successfullySavedService() {
      this.$refs.table.refresh();
    },

    createService() {
      this.form.reset();
      this.modalId = this.modal.add.id;
      this.form.language = this.$i18n.locale;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_service_type_all(params).then((_) => {
        this.$bvModal.show("service-add-modal");
      });
    },

    loadServiceTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_service_type_all(params).then((_) => {
        this.$bvModal.hide("service-type-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    serviceTypePage(){
      window.location.href = `/admin/service-type`;
    },

    onFiltered(filteredItems) {
      
      this.tableTotalRows = filteredItems.length
      this.currentPage = 1
    },

    onEdit(item, index) {
      let name;
      this.editingIndex = index;
      name = this.checkIfHasTranslation(item.service_name);
      this.form.reset();
      if(name == "") {
        this.form.name = item.name;
      }
      this.nameTranslation = name;
      this.form.language = this.$i18n.locale;
      this.form.description = item.description == null ? "" : item.description;
      this.loadServiceTypes();
      this.form.id = item.id;
      this.modalId = this.modal.edit.id;
      this.form.service_type = item.service_type_item;
    },

    onLinkBrand(item, index) {
      this.form.reset();
      this.editingIndex = index;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.brands = item.brands;
      this.form.name = item.name;
      this.modalId = this.modal.brand.id;
      this.loadSpecializationCategories();
      this.$bvModal.show("link-brand-modal");
    },

    listDisplay(array) {
      let trans = '';
      let spliter = '';
        spliter = array.split('(')[0];
        trans = array.split('(')[1];
        if( trans !== undefined ){
          if( trans.length > 0 ) {
            spliter = spliter + '<small style="color:red">(' + trans + '</small>';
            array = spliter;
          }
        }
      return array;
    },

    checkIfHasTranslation(name) {
      let trans = '';
        trans = name.split('(')[1];
        if( trans !== undefined ){
          if( trans.length > 0 ) {
            return '(' +trans;
          }
        }
      return "";
    },

    sortChanged(e) {
      console.log(e.sortBy)
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    sortbyLang(language){
      this.sortByLang = language
      this.loadItems()
    },

    onDelete(item, index) {
      let services = item;
      let vm = this;
      let parameters = {
        api_token: vm.$user.api_token,
        id: item.id,
      }
      this.check_if_has_link_or_article(parameters).then((resp) => {
        if(resp) {
          $.confirm({
            title: vm.$t("warning").toUpperCase(),
            content:
              vm.$t("unable_message1") +
              vm.$t("unable_message2"),
            type: "red",
            typeAnimated: true,
            columnClass: "medium",
            buttons: {
              yes: {
                text: vm.$t("okay"),
                btnClass:
                  "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                action: function(value) {
                },
              },
            },
          });
        } else {
          $.confirm({
            title:
              vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
              vm
                .$t("confirmation_record_delete")
                .slice(1)
                .toUpperCase(),
            content:
              vm.$t("question_record_delete") +
              `${item.name}` +
              vm.$t("from") +
              vm.$t("label.services") +
              vm.$t("records") +"?",
            type: "red",
            typeAnimated: true,
            columnClass: "medium",
            buttons: {
              yes: {
                text: vm.$t("yes"),
                btnClass:
                  "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                action: function(value) {
                  if (value) {
                    item.is_loading = true;
                    let params = {
                      api_token: vm.$user.api_token,
                      id: item.id,
                    };
                    vm.delete_service(params)
                      .then((resp) => {
                        console.log(resp.data);
                        item.is_loading = false;
                        if(resp.data == false) {
                          vm.makeToast(
                            "danger",
                            vm.$t("data_used"),
                            vm.$t("used_data_alert")
                          );
                          return false;
                        }
                        if (vm.servicesResponseStatus) {
                          vm.loadItems();
                          vm.makeToast(
                            "danger",
                            vm.$t("delete_record"),
                            `${item.name}` +
                              vm.$t("delete_record_message") +
                              vm.$t("label.services") +
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
                action: function() {},
              },
            },
          });
        }
      });
      return;
      
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000)

  },

  watch: {

    filter(query) {
      this.performSearch(query)
    },

    currentPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    perPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    searched: {
      handler: function(value) {
        this.loadItems()
      }
    },
  },

  computed: {
    ...mapGetters("services", {
      items: "services",
      // categories: "categories",
      servicesResponseStatus: "get_response_status",
    }),

    ...mapGetters("service_type", {
      service_types: "service_type_all",
      responseStatus: "get_response_status",
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters({
      specializations: "jobs/get_job_items",
      categories: "jobs/get_job_categories",
      jobStatus: "jobs/get_job_status"
    }),

    totalRows() {
      return this.items.length;
    },

    selectedLanguage() {
      return this.currentLanguage.replace("-", "_")
    }

  },
};
</script>
