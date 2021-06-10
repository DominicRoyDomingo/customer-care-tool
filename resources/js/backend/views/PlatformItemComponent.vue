<template>
  <div class="animated fadeIn">
    <v-app id="platform-item__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("platform_item_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="modal.addItem.isVisible = true"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-cellphone-link</v-icon>
                <v-icon small dark style="margin-top: 15px; margin-left: -2px;">
                  mdi-plus
                </v-icon>
                &nbsp;
                {{ $t(modal.addItem.button.new) }}
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
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :busy="isLoading"
                :items="items"
                v-model="platforms"
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

                <template v-slot:cell(index)="data">
                  <div class="caption text--disabled text-right">
                    {{ data.index + 1 }}
                  </div>
                </template>

                <template v-slot:cell(platform_type)="data">
                  <div class="text-subtitle-2 font-weight-bold text--secondary">
                    {{ data.item.brand }}
                  </div>
                  <div
                    class="text-subtitle-2 font-weight-regular text--disabled"
                  >
                    {{ data.item.platform_type }}
                  </div>
                </template>

                <template v-slot:cell(created_at)="data">
                  <div style="margin-top: 10px">
                    <span class="text-center text--disabled">
                      {{ data.item.created_at | moment("DD/MM/YYYY") }}
                    </span>
                  </div>
                </template>

                <template v-slot:cell(updated_at)="data">
                  <div style="margin-top: 10px">
                    <span class="text-center text--disabled">
                      {{ data.item.updated_at | moment("DD/MM/YYYY") }}
                    </span>
                  </div>
                </template>

                <template v-slot:cell(actions)="data">
                  <div style="margin-top: 10px">
                    <v-menu top left>
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
                          <v-list-item @click="onEdit(data.item)">
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-cellphone-link
                              </v-icon>
                              <v-icon
                                size="12"
                                color="yellow darken-3"
                                style="margin-top: 15px; margin-left: -2px;"
                              >
                                mdi-pencil
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-cellphone-link
                              </v-icon>
                              <v-icon
                                size="12"
                                color="red lighten-1"
                                style="margin-top: 15px; margin-left: -2px;"
                              >
                                mdi-close
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
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="platforms.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <b-pagination
                v-if="!this.isLoading"
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
              ></b-pagination>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <Create :$this="this"></Create>

      <Edit :$this="this"></Edit>

      <brandModal :parent="this" @hide="closeBrandModal"></brandModal>

      <CreatePlatformType
        :$this="this"
        @close="successfullySavedPlatformType"
      ></CreatePlatformType>
    </v-app>
  </div>
</template>

<script>
import Create from "./includes/platform-item/CreateComponent.vue";

import Edit from "./includes/platform-item/UpdateComponent.vue";

import brandModal from "./brands/modals/brand.modal.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import CreatePlatformType from "./includes/platform-type/CreateComponent.vue";

import {
  fetchList,
  softDelete,
  getPlatformName,
} from "./../api/platform_item.js";

export default {
  mixins: [toast],
  components: {
    Create,
    Edit,
    Loading,
    brandModal,
    CreatePlatformType,
  },

  data: function() {
    return {
      platforms: [],
      
      isLoading: true,

      btnloading: false,

      filter: "",

      currentPage: 1,

      perPage: 10,

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],

      tableHeaders: [
        { key: "index", label: "ID", thStyle: { width: "5%" } },

        {
          key: "platform_type",
          label: this.$t("table.platform_item_platform_type"),
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
          thClass: "text-centert",
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

      items: [],

      brands: [],

      platform_type: [],

      modal: {
        addItem: {
          name: "platform_item_add_new_button",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "platform_item_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "platform_item_add_edit_button",

          isVisible: false,

          button: {
            update: "button.save_change",

            cancel: "cancel",

            new: "platform_item_add_edit_button",

            loading: false,
          },
        },

        brand: {
          isVisible: false,
        },

        add: {
          name: "platform_type_add_new_button",

          isVisible: false,

          from: "item",

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "platform_type_add_new_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        brand: "",

        platform_type: "",

        name: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

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
    this.loadPlatform();
  },

  methods: {
    loadPlatform() {
      this.isLoading = true;

      fetchList().then((resp) => {
        this.items = resp.platform_item;

        this.brands = resp.brands;

        this.platform_type = resp.platform_type;

        this.isLoading = false;
      });
    },

    onEdit(platform) {
      this.form.id = platform.id;

      (this.form.brand = platform.brand_id),
        (this.form.platform_type = platform.platform_type_id),
        (this.modal.edit.isVisible = true);
    },

    onDelete(platform) {
      let vm = this;

      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content: vm.$t("delete_transaction_alert"),
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(resp) {
              platform.loading = true;

              softDelete(platform.id).then((resp) => {
                platform.loading = false;

                if (resp == false) {
                  vm.$notify({
                    title: vm.$t("data_used"),

                    message: vm.$t("used_data_alert"),

                    type: "error",
                  });

                  return false;
                }

                vm.$notify({
                  title: vm.$t("delete"),

                  message:
                    resp.message + " " + vm.$t("remove_platform_item_alert"),

                  type: resp.type,
                });

                vm.loadPlatform();
              });
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

    openBrandModal() {
      this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.addItem.isVisible = false;
      this.$bvModal.show("brand-modal");
    },

    openBrandModalEdit() {
      this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Edit";
      this.modal.edit.isVisible = false;
      this.$bvModal.show("brand-modal");
    },

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");

      if (this.form.actual_action == "Add") {
        this.modal.addItem.isVisible = true;
      } else {
        this.modal.edit.isVisible = true;
      }
    },

    successfullySavedBrand() {
      this.isLoading = true;
      fetchList().then((resp) => {
        this.brands = resp.brands;

        this.isLoading = false;
      });
    },

    openPlatformTypeModal() {
      this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.addItem.isVisible = false;
      this.modal.add.isVisible = true;
    },

    openPlatformTypeModalEdit() {
      this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Edit";
      this.modal.edit.isVisible = false;
      this.modal.add.isVisible = true;
    },

    successfullySavedPlatformType() {
      this.isLoading = true;
      fetchList().then((resp) => {
        this.platform_type = resp.platform_type;

        this.isLoading = false;

        this.modal.add.isVisible = false;

        if (this.form.actual_action == "Add") {
          this.modal.addItem.isVisible = true;
        } else {
          this.modal.edit.isVisible = true;
        }
      });
    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },
};
</script>

<style scoped>
/* .v-menu__content {
  margin-left: -16% !important;
  margin-top: 6.9%;
} */
</style>
