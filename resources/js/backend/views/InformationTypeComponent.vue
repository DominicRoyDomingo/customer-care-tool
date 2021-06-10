<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("information_type_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createInformationType()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-map-marker-plus</v-icon>
                &nbsp;
                {{ $t(modal.createInformationType.button.new) }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="12" md="8">
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
                <b-input-group-prepend class="mr-2">
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
                </b-input-group-prepend>
              </b-form>
            </v-col>
            <v-col cols="12" sm="12" md="4">
              <b-input-group class="float-right">
                <b-form-input
                  v-model="filter"
                  :providerholder="$t('search_here')"
                  type="search"
                  style="border-radius: 0;"
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
                      v-html="listDisplay(data.item.name)"
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

      <Create :$this="this" @loadTable="loadItems()"></Create>

      <Edit :$this="this"></Edit>
    </v-app>

 
  </div>
  

</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./includes/information-type/CreateComponent.vue";

import Edit from "./includes/information-type/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import text from "./../helpers/text.js";

import { fetchList, softDelete, getTagsName } from "./../api/tags.js";


export default {

  mixins: [toast, text],
  
  components: {
    
    Create,

    Loading,

    Edit,
  
  },
  
  data: function() {
    
    return {

      isLoading: true,

      btnloading: false,
      
      filter: "",

      nameTranslation: "",
      
      currentPage: 1,
      
      perPage: 10,

      currentLanguage: this.$i18n.locale,

      tableTotalRows: "",
      
      filter: "",

      sortBy: 'name',

      searched: "",

      sortDesc: false,

      showEntriesOpt: [

        { value: 10, text: "10" },
      
        { value: 30, text: "30" },
        
        { value: 50, text: "50" },
        
        { value: 100, text: "100" },
      
      ],

      tableHeaders: [
        
        { key: "name", label: this.$t('label.name'), thClass: "text-left", sortable: true },
        
        { key: "created_at", label: this.$t("table.created_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "updated_at", label: this.$t("table.updated_at"), thClass: "text-center", thStyle: { width: "15%" }, tdClass: "text-center", sortable: true },
        
        { key: "actions", label: this.$t("table.action"), thClass: "text-center", thStyle: { width: "20%" }, tdClass: "text-center" }
      
      ],

      translations: {
        en: {
          title: "Edit Information Type",
          name: "Name",
          button: "Update",
          cancel: "Cancel"
        },

        it: {
          title: "Modifica tipo di informazioni",
          name: "Nome",
          button: "Aggiornare",
          cancel: "Annulla"
        },

        de: {
          title: "Informationstyp bearbeiten",
          name: "Name",
          button: "Aktualisieren",
          cancel: "Stornieren"
        },

        ph_fil: {
          title: "I-edit ang Uri ng Impormasyon",
          name: "Pangalan",
          button: "Update",
          cancel: "Kanselahin"
        },

        ph_bis: {
          title: "Pag-edit sa Matang sa Impormasyon",
          name: "Ngalan",
          button: "Pagbag-o",
          cancel: "Kanselahon"
        }
      },

      modal: {
        
        createInformationType: {
          
          name: "information_type_new_button",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "information_type_new_button",
            
            loading: false
          
          }

        },
        
        edit: {
          
          name: "information_type_edit_button",
          
          isVisible: false,
          
          button: {
            
            update: "update",
            
            cancel: "cancel",
            
            new: "information_type_edit_button",
            
            loading: false
          
          }

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
        
        information_type_name: "",

        information_type_data: null,
        
        language: this.$i18n.locale
      
      }),

      formData: new FormData(),
    
    };
  
  },

  mounted() {
    
    this.loadItems();
  
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
  
  methods: {
    ...mapActions("information_type", ["get_information_types", "delete_information_type", "remove_from_information_type_array"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        locale: this.$i18n.locale,
        sort_desc: this.sortDesc,
        search: this.searched,
        sortbyLang: this.sortByLang
      };
      this.get_information_types(params).then(_ => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
      });
    },

    successfullySavedInformationType(){
      this.$refs.table.refresh();
    },

    createInformationType() {
      this.form.language = this.$i18n.locale;
      this.$bvModal.show("information-type-add-modal");

    },
    
    onEdit(item,index) {
      let name;
      this.editingIndex = index;
      name = this.checkIfHasTranslation(item.name);
      this.form.reset();
      if(name == "") {
        this.form.information_type_name = item.name;
      }
      this.nameTranslation = name;
      this.form.information_type_data = item.type_of_data;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      // Open Modal
      this.$bvModal.show("information-type-edit-modal");
    },

    onDelete(item,index) {
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toUpperCase(),
        content: vm.$t( "question_record_delete") + `${item.name}` + vm.$t( "from") +  vm.$t( "label.information_type") + vm.$t( "records")+"?",
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
                vm.delete_information_type(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if(resp.data == false) {
                      vm.makeToast(
                        "danger",
                        vm.$t("data_used"),
                        vm.$t("used_data_alert")
                      );
                      return false;
                    }
                    if (vm.informationTypesResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.information_type") +
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

    sortbyLang(language){
      this.sortByLang = language
      this.loadItems()
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
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    performSearch: _.debounce(function(query) {
        this.searched = query
    }, 1000)

  },

  computed: {
    ...mapGetters("information_type", {
      items: "information_types",
      // categories: "categories",
      informationTypesResponseStatus: "get_response_status"
    }),

    totalRows() {

      return this.items.length;

    },

    selectedLanguage() {

      return this.currentLanguage.replace("-", "_")

    }

  }

};

</script>