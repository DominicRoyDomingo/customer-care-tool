<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("services_exclusive_list") }}
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
                  <b-input-group-text
                    style="background-color: rgba(0,0,0,0.05); 
                      border-radius: 0;"
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

                <template v-slot:cell(service_type)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ data.item.service_type_item.name }}
                    </span>
                  </div>
                </template>

                
                <template v-slot:cell(text_display)="data">
                  <div style="margin: 10px 0 0 25px;">
                    <span
                      class="text-subtitle-1 text--secondary font-weight-bold"
                    >
                      {{ data.item.text_display }}
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

      
      <Create :$this="this" @loadTable="loadItems"></Create>

      <Edit :$this="this"></Edit>

      
    </v-app>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./includes/services-exclusive/CreateComponent.vue";

import Edit from "./includes/services-exclusive/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

export default {
  mixins: [toast],

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

      searched: "",

      currentPage: 1,

      perPage: 10,

      sortBy: 'name',

      files: [],

      sortDesc: false,

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],

      tableHeaders: [

        {
          key: "service_type",
          label: this.$t("service_type_category"),
          thClass: "text-center",
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "text_display",
          label: this.$t("label.text_display"),
          thClass: "text-left",
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
          name: "services_exclusive_add_new_button",

          id: "service-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "services_exclusive_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "services_exclusive_add_edit_button",

          id: "service-exclusive-edit-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "service_add_new_button",

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

      form: new Form({
        id: "",

        service_type: "",

        text_display: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("services_exclusive", [
      "get_services_exclusives",
      "delete_services_exclusive",
      "remove_from_services_exclusive_array",
    ]),
    ...mapActions("service_type", ["get_service_types"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
      };
      this.get_services_exclusives(params).then((_) => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
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
      this.get_service_types(params).then((_) => {
        this.$bvModal.show("service-exclusive-add-modal");
      });
    },

    loadServiceTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_service_types(params).then((_) => {
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
      this.editingIndex = index;
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.modalId = this.modal.edit.id;
      this.loadServiceTypes();
      this.form.id = item.id;
      this.form.text_display = item.text_display;
      this.form.service_type = item.service_type_item;
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

      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.name}` +
          vm.$t("from") +
          vm.$t("label.services") +
          vm.$t("records"),
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
                vm.delete_services_exclusive(params)
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
                    if (vm.servicesExclusivesResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.text_display}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.services_exclusive") +
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
    ...mapGetters("services_exclusive", {
      items: "services_exclusives",
      // categories: "categories",
      servicesExclusivesResponseStatus: "get_response_status",
    }),

    ...mapGetters("service_type", {
      serviceTypes: "service_types",
      responseStatus: "get_response_status",
    }),

    totalRows() {
      return this.items.length;
    },
  },
};
</script>
