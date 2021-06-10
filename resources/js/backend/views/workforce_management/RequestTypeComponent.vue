<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ this.brand_session.name }}  {{ $t("request_type_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createRequestType()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-newspaper-plus</v-icon>
                &nbsp;
                {{ $t(modal.add.button.new) }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col  cols="12" sm="6" md="2"
                class="caption font-weight-regular text--secondary text-right"
                style="display:flex; padding-top: 15px"
              >
                {{ $t("button.show") }}
                <b-form-select
                  :options="showEntriesOpt"
                  v-model="perPage"
                  style="border-radius: 0; width: 40% !important; margin-top: -8px; margin-left: 5px; margin-right: 5px"
                />{{ $t("label.entries") }}
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <b-input-group-prepend>
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      style="height: 33px; min-width: 64px; padding: 0 16px; margin-right: 7px; margin-top: -5px;"
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
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="12" sm="6" md="6">
              <div class="float-right">
                <b-form inline style="margin-right:-8px">
                  <b-input-group class="mb-2 mr-sm-2">
                    <template #append>
                      <b-input-group-text>
                        <i class="fa fa-search" aria-hidden="true"></i>
                      </b-input-group-text>
                    </template>
                    <b-form-input
                      v-model="filter"
                      id="inline-form-input-username"
                      :placeholder="$t('search_here')"
                      style="width: 300px"
                      type="search"
                    />
                  </b-input-group>
                </b-form>
              </div>
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
                :per-page="perPage"       
                :filter="filter"
                :busy="isLoading"
                :items="items"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                @sort-changed="sortChanged"
                v-model="requestTypeTable"
              >

                <template v-slot:cell(name)="data">
                  <div style="margin-top: 10px">
                    <span
                      class="text-left text--secondary"
                    >
                      {{ data.item.name }}
                      <small
                        v-if="data.item.convertion == true"
                        style="color:red"
                      >
                        (No {{ data.item.language }} translation yet)</small
                      >
                    </span>
                  </div>
                </template>

                <!-- <template v-slot:cell(affect_limit)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left text--secondary">
                      {{ ( data.item.affect_limit === 1 ? 'Yes' : 'No' ) }}
                    </span>
                  </div>
                </template> -->

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

                <template slot="empty">
                  <section class="section">
                    <div class="content has-text-grey has-text-centered">
                      <center>{{ $t("table.empty") }}</center>
                    </div>
                  </section>
                </template>
              </b-table>
            </v-container>
          </v-row>
          <v-row>
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="requestTypeTable.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <v-spacer>
                <b-pagination
                  v-if="!this.isLoading"
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  style="float: right"
                ></b-pagination>
              </v-spacer>
            </v-col>
          </v-row>
        </v-card-text>

      <brandModal :parent="this"  @loadTable="loadBrand" @hide="closeBrandModal"></brandModal>
      <LinkBrandModal :$this="this"></LinkBrandModal>

      </v-card>

      
      <Create :$this="this" @loadTable="loadItems"></Create>

      <Edit :$this="this"></Edit>

      
    </v-app>
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import Create from "./Request-type/RequestTypeCreateComponent.vue";

import Edit from "./Request-type/RequestTypeUpdateComponent.vue";

import brandModal from "./modals/brand.modal.vue";

import LinkBrandModal from "./modals/LinkBrandComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../../shared/form.js";

import toast from "./../../helpers/toast.js";

export default {
  mixins: [toast],
  props: [ 'brand_session' ],
  components: {
    Create,

    Loading,

    Edit,

    brandModal,

    LinkBrandModal
    
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

      requestTypeTable: [],

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],

      tableHeaders: [
        {
          key: "name",
          label: this.$t("table.request_type"),
          thClass: "text-left",
          sortable: true,
        },

        // {
        //   key: "affect_limit",
        //   label: this.$t("table.is_affecting_workforce"),
        //   thClass: "text-center",
        //   tdClass: "text-center",
        //   sortable: true,
        // },

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
          name: "request_type_add_new_button",

          id: "request-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "request_type_add_new_button",

            loading: false,
          },
        },

        brand: {
          isVisible: false,
        },
        
        edit: {
          name: "request_type_edit_button",

          id: "request-type-edit-modal",

          isVisible: false,

          button: {
            update: "button.save_change",

            cancel: "cancel",

            new: "request_type_add_new_button",

            loading: false,
          },
        },

        createRequestType: {
          name: "request_type_add_new_button",

          id: "request-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "request_type_add_new_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        request_type: "",

        is_affect_limit: false,

        brand: [],

        default: '',

        required: false,

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

      link_option: '',

      convertion: '',

      language: '',

      sortbyLangId: '',

      noTranslationsOptions: [
        { value: 'all', text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],

    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("request_type", [
      "get_request_type",
      "delete_request_type",
      "remove_from_request_type_array",
    ]),
    ...mapActions("brand", ["get_brands"]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id,
        sortbyLang: this.sortbyLangId,
        brand: ( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null )
      };
      this.get_request_type(params).then((_) => {
        this.tableTotalRows = this.items.total
        this.isLoading = false;
      });
    },
    
    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },

    successfullySavedRequestType() {
      this.$refs.table.refresh();
    },

    createRequestType() {
      this.form.reset();
      this.modalId = this.modal.add.id;
      this.form.language = this.$i18n.locale;
      this.form.brand = JSON.parse(JSON.stringify( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null ) );
      
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      // this.get_request_type(params).then((_) => {
        this.$bvModal.show("request-type-add-modal");
      // });
    },

    openBrandModal() {
      // this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.brand.isVisible = false;
      this.$bvModal.show("brand-modal");
    },

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");
    },

    onLinkBrand(item, index) {
      this.form.reset();
      this.editingIndex = index;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.brand = JSON.parse( item.brands );
      this.modalId = this.modal.brand.id;
      this.link_option = 'post_link_brand_request_type';
      this.$bvModal.show("link-brand-modal");
    },

    loadBrand() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id
      };
      this.get_brands(params).then((_) => {
      });
    },

    loadRequestTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id,
        brand: ( this.brand_session.id !== undefined || this.brand_session.id !== '' ? this.brand_session.id : null )
      };
      this.get_request_type(params).then((_) => {
        this.$bvModal.hide("request-type-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    onEdit(item, index) {
      console.log( item );
      this.editingIndex = index;
      this.form.reset();
      this.form.language = this.$i18n.locale;
      this.loadRequestTypes();
      this.form.id = item.id;
      this.form.default = item.name;
      this.convertion = item.convertion;
      this.language = item.language;
      this.form.request_type = ( item.convertion === true ? '' : item.name );
      this.form.is_affect_limit = item.affect_limit;
      this.form.brand = JSON.parse( item.brands );
      this.modalId = this.modal.edit.id;
    },

    sortChanged(e) {
      console.log(e.sortBy)
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.loadItems()
    },

    onDelete(item, index) {
      let request_type = item;
      let vm = this;

      $.confirm({
        title:
          vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.name}` +
          vm.$t("from") +
          vm.$t("label.request_type") +
          vm.$t("records") + '?',
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
                  lang: vm.form.language
                };
                vm.delete_request_type(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if( resp.data.inUse == true ) {
                      vm.makeToast(
                        "danger",
                        vm.$t("failed_to_delete"),
                        vm.$t("unable_message1") + resp.data.name + vm.$t("unable_message2")
                      );
                      return false;
                    }
                    if (vm.requestTypeResponseStatus) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.request_type") +
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

    // currentPage: {
    //   handler: function(value) {
    //     this.loadItems()
    //   }
    // },

    // perPage: {
    //   handler: function(value) {
    //     this.loadItems()
    //   }
    // },

    searched: {
      handler: function(value) {
        this.loadItems()
      }
    },
  },

  computed: {
    ...mapGetters("request_type", {
      items: "request_type",
      // categories: "categories",
      requestTypeResponseStatus: "get_response_status",
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),

    totalRows() {
      return this.items.length;
    },
  },
};
</script>

<style lang="scss">
th:nth-last-child(2) {
  text-align: center;
}

th:nth-last-child(3) {
  text-align: center;
}
</style>
