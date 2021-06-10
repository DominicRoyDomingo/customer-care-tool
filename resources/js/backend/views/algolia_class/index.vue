<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("algolia_class") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createAlgoliaClass()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-plus</v-icon>
                &nbsp;
                {{ $t(modal.createAlgoliaClass.button.new) }}
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
                <!-- <template v-slot:prepend>
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
                </template> -->
                <b-form-input
                  v-model="filter"
                  :placeholder="$t('search_here')"
                  type="search"
                  style="border-radius: 0"
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
                ref="algoliaClassTable"
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
                  <div style="">
                    <!-- <span
                      class="text-subtitle-1 text--secondary font-weight-bold"
                       v-html="listDisplay(data.item.name)"
                    >
                    </span> -->
                    <span class="">
                      {{ data.item.name }}
                    </span>
                  </div>
                </template>

                <template v-slot:cell(created_at)="data">
                  <div style="">
                    <span class="">
                      {{ data.item.created_at}}
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
                          <!-- <v-list-item @click="onAddProvider(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-plus-circle-outline
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("provider_group_add_provider_button") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item> -->
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

      
      <Create :parent="this" @loadTable="loadItems"></Create>
      <Edit :parent="this"></Edit> 
      
    </v-app>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Create from "./modals/createAlgoliaClass.vue";
import Edit from "./modals/editAlgoliaClass";
// import AddProvider from "./includes/provider-group/AddProviderModal.vue";
// import ListOfProviders from "./includes/provider-group/ListOfProvidersModal.vue";
import Loading from "vue-loading-overlay";
import Form from "../../shared/form.js";
import toast from "../../helpers/toast.js";
import text from "../../helpers/text.js";

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
      tableTotalRows: "",
      sortByLang: "",
      searched: "",
      providerGroupID: "",
      providerGroupName: "",
      className: "",
      currentPage: 1,
      perPage: 10,
      sortBy: 'class',
      algoliaClasses: [],
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
          label: this.$t("name"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "created_at",
          label: this.$t("table.created_at"),
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
          
        createAlgoliaClass: {
          name: "algolia_class_add_new_button",
          id: "algolia-class-add-modal",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "algolia_class_add_new_button",
            loading: false,
          },
        },

        editAlgoliaClass: {
          name: "algolia_class_add_edit_button",
          id: "algolia-class-edit-modal",
          isVisible: false,
          button: {
            update: "button.save_change",
            cancel: "cancel",
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
      },
      
      currentLanguage: this.$i18n.locale,

      algoliaClassForm: new Form({
        id: "",
        name: "",
      }),

      form: new Form({
        id: "",
        name: "",
        brand_name: "",
        website: "",
        logo: "",
        isDefault: 0,
        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("algolia_class", [
        "get_algolia_classes",
        "delete_algolia_class",
    ]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        sortbyLang: this.sortByLang
      };
      this.get_algolia_classes(params).then((_) => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
      });
    },

    successfullySavedAlgoliaClass() {
      this.$refs.algoliaClassTable.refresh();
    },

    createAlgoliaClass() {
      this.algoliaClassForm.reset();
      this.modalId = this.modal.createAlgoliaClass.id;
      this.$bvModal.show(this.modalId);
    },

    onFiltered(filteredItems) {
      
      this.tableTotalRows = filteredItems.length
      this.currentPage = 1
    },

    sortbyLang(language){
      this.sortByLang = language
      this.loadItems()
    },

    onEdit(item, index) {
			this.algoliaClassForm.reset();
      this.editingIndex = item.algolia_class_index;
			console.log(item);
      this.algoliaClassForm.id = item.id;
      this.algoliaClassForm.name = item.name;
      this.modalId = this.modal.editAlgoliaClass.id;
      this.$bvModal.show(this.modalId);
    },

    onAddProvider(item, index) {
      this.addedProviders = [];
      this.providerGroupID = item.id;
      this.providerGroupName = item.name;
      this.editingIndex = index;
      item.providers.forEach((provider) => {
        this.addedProviders.push(provider.id);
      });
      this.$bvModal.show('add-provider-modal');
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

    showProvidersList( item, index ) {
      console.log(item);
      this.providers = item.providers;
      this.$bvModal.show("show-providers-list");
    },

    sortChanged(e) {
      console.log(e.sortBy)
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    onDelete(item, index) {
        let services = item;
        let vm = this;
        let parameters = {
            api_token: vm.$user.api_token,
            id: item.id,
        }
            $.confirm({
            title:
                vm.$t("confirmation_record_delete").charAt(0).toUpperCase() +
                vm
                .$t("confirmation_record_delete")
                .slice(1)
                .toUpperCase(),
            content:
							vm.$t("question_record_delete") +
							`${item.name.bold()}` +
							vm.$t("from") +
							vm.$t("algolia_class") +
							vm.$t("records") + "?",
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
                    vm.delete_algolia_class(params)
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
                        if (vm.algoliaClassResponseStatus) {
                            vm.loadItems();
                            vm.makeToast(
                            "danger",
                            vm.$t("delete_record"),
                            `${item.name}` +
                                vm.$t("delete_record_message") +
                                vm.$t("algolia_class") +
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
    ...mapGetters("algolia_class", {
      items: "algolia_classes",
      // categories: "categories",
      algoliaClassResponseStatus: "get_response_status",
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
