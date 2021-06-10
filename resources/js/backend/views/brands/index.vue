<template>
  <div class="animated fadeIn">
    <div class="animated fadeIn">
      <v-app id="brands__container">
        <v-card tile flat>
          <v-card-title>
            <v-row>
              <v-col>
                <span class="title font-weight-light text--secondary">
                  {{ $t("label.brands") }}
                </span>
              </v-col>
              <v-spacer> </v-spacer>
              <v-col>
                <v-btn color="success" @click="onAdd" class="float-right" tile>
                  <v-icon dark>mdi-briefcase-plus</v-icon>&nbsp;
                  {{ $t("button.new") }}
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
              <v-col cols="12" sm="6" md="6">
                <div class="float-right">
                  <b-form inline style="margin-right:-8px">
                    <!-- <b-input-group-prepend>
                      <v-menu offset-y :rounded="false">
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            color="info"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            tile
                            depressed
                            style="height: 33px; min-width: 64px; padding: 0 16px; margin-right: 7px; margin-top: -9px;"
                          >
                            <v-icon :size="18">
                              mdi-filter-menu
                            </v-icon>
                          </v-btn>
                        </template>
                        <v-list dense class="profile__row-menu">
                          <v-list-item-group color="primary">
                            <v-list-item v-for="(option, index) in noTranslationsOptions" :key="index">
                              <v-list-item-content @click="filterbyLang(option.value)">
                                <v-list-item-title>
                                  {{ option.text }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                          </v-list-item-group>
                        </v-list>
                      </v-menu>
                    </b-input-group-prepend> -->
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
                  ref="table"
                  show-empty
                  stacked="md"
                  :fields="tableHeaders"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :filter="filter"
                  :busy="isLoading"
                  :items="items"
                >
                  <template v-slot:table-busy>
                    <v-fade-transition>
                      <v-overlay
                        opacity="0.8"
                        style="z-index: 999999 !important"
                      >
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

                  <template v-slot:cell(logo)="data">
                    <div style="margin-left: 25%">
                      <v-avatar
                        v-if="data.item.logo"
                        color="grey"
                        size="48"
                        class="template__table-preview"
                      >
                        <v-img :src="data.item.logo"></v-img>
                      </v-avatar>
                      <v-avatar
                        v-else
                        color="primary"
                        size="48"
                        class="template__table-preview"
                      >
                        <span class="white--text title">
                          {{ data.item.brand_name[0] }}
                        </span>
                      </v-avatar>
                    </div>
                  </template>

                  <template v-slot:cell(brand_name)="data">
                    <div style="margin-top: 10px">
                      <span
                        class="text-subtitle-2 text--secondary font-weight-medium"
                      >
                        {{ data.item.brand_name }}
                      </span>
                    </div>
                  </template>

                  <template v-slot:cell(website)="data">
                    <div style="margin-top: 10px">
                      <span
                        v-if="
                          data.item.website == null || data.item.website == ''
                        "
                      >
                        N/A
                      </span>
                      <a
                        :href="data.item.website"
                        v-html="data.item.website"
                        target="_blank"
                        v-if="data.item.website != ''"
                      ></a>
                    </div>
                  </template>

                  <template v-slot:cell(created_at)="data">
                    <div style="margin-top: 10px">
                      <span class="text--disabled">
                        {{ data.item.created_at | toLocaleString }}
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
                            <v-list-item @click="onEdit(data.item)">
                              <v-list-item-icon>
                                <v-icon color="yellow darken-3">
                                  mdi-briefcase-edit
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
                                  mdi-briefcase-remove
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content>
                                <v-list-item-title>
                                  {{ $t("button.delete") }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                            <v-divider></v-divider>
                            <v-list-item @click="onCategory(data.item)">
                              <v-list-item-icon>
                                <v-icon color="green lighten-1">
                                  mdi-tag-multiple
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content>
                                <v-list-item-title>
                                  {{ $t("button.add_categories") }}
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
                  :total-rows="totalRows"
                  :per-page="perPage"
                ></b-pagination>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <!-- Modal Here -->
        <createBrand :parent="this"></createBrand>
        <updateBrand :parent="this"></updateBrand>
        <categoryModal :parent="this"></categoryModal>
        <Category :$this="this" @loadTable="loadCategory"></Category>
      </v-app>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import jobMixins from "./mixins/brandMixins.js";
import createBrand from "../includes/brands/CreateBrandComponent.vue";
import updateBrand from "../includes/brands/UpdateBrandComponent.vue";
import categoryModal from "./modals/category.modal.vue";
import Category from "./../includes/category/CreateComponent.vue";
import Form from "../../shared/form.js";

export default {
  name: "brands-index",
  components: {
    createBrand,
    updateBrand,
    categoryModal,
    Category,
  },
  mixins: [jobMixins],
  data() {
    return {
      newSpecializationCategories: [],
      modal: {
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

        editBrand: {
          name: "brands_add_edit_button",
          isVisible: false,
          button: {
            update: "update",
            cancel: "cancel",
            new: "content_add_new_button",
            loading: false,
          },
        },
      },
      tableHeaders: [
        {
          key: "logo",
          label: this.$t("label.logo"),
          thClass: "text-center",
          thStyle: { width: "8%" },
          sortable: false,
        },
        {
          key: "brand_name",
          label: this.$t("label.brand_name"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "website",
          label: this.$t("label.website"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-center",
          thStyle: { width: "23%" },
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
      form: new Form({
        id: "",
        brand_name: "",
        brandCategories: [],
        website: "",
        logo: "",
        isDefault: 0,
        action: ""
      }),
    };
  },

  created() {
    this.loadItems().catch((errors) => {
      console.log(errors);
    });
  },

  computed: {
    ...mapGetters("brand", {
      items: "brands",
      categories: "categories",
      responseStatus: "get_response_status",
    }),

    ...mapGetters({
        specializations: "jobs/get_job_items",
        categories: "jobs/get_job_categories",
        jobStatus: "jobs/get_job_status"
    }),
  },

  methods: {
    ...mapActions("brand", [
      "get_brands",
      "delete_brand",
      "remove_from_brands_array",
      "get_categories",
    ]),

    ...mapActions("jobs", [
      "get_jobs", 
      "get_job_categories", 
      "delete_job_description", 
      "get_filtered_job_professions"
    ]),

    async loadItems() {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        org_id: this.$org_id,
        sortbyLang: this.sortbyLang,
      };
      this.get_brands(params).then((_) => {
        this.isLoading = false;
      });
    },

    filterbyLang(value) {
      this.sortbyLang = value;
      this.isLoading = true;
      this.loadItems();
    },

    modalAdd() {
      this.form.reset();
      this.form.action = "Add";
      this.$bvModal.show("brand-modal");
    },

    onAdd() {
      this.form.reset();
      this.btnloading = false;
      this.loadSpecializationCategories();
      this.modal.createBrand.isVisible = true;
    },

    loadCategory(params) {
      this.get_categories(params).then((_) => {
        this.$bvModal.show("category-brand-modal");
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

    onCategory(item) {
      let params = {
        api_token: this.$user.api_token,
      };
      this.form.id = item.id;
      this.form.brand_name = item.name;
      this.form.categories = item.categories;
      this.loadCategory(params);
    },

    onEdit(item) {
      let brandCategories = []
      this.editingIndex = item.brand_index;
      this.form.reset();
      this.language = this.$i18n.locale;
      this.form.id = item.id;
      this.form.logo = item.logo;
      this.form.brand_name = item.name;
      this.form.website = item.website;
      this.form.isDefault = item.isDefault
      this.itemSelected = item;
      if(item.brand_categories.length != 0) {
        item.brand_categories.map(function(brandCategory) {
          brandCategories.push(brandCategory.category)
        })

        this.form.brandCategories = brandCategories
      };
      this.loadSpecializationCategories();
      // Open Modal
      this.modal.editBrand.isVisible = true;
    },

    successfullySavedBrand() {
      this.$refs.table.refresh();
    },

    onDelete(item) {
      let brand = item;
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
          vm.$t("label.brands") +
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
                vm.delete_brand(params)
                  .then((resp) => {
                    item.is_loading = false;
                    if (vm.responseStatus) {
                      vm.remove_from_brands_array(brand);
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("from") +
                          vm.$t("label.brands") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    vm.form.errors.record(error.response.data.errors);
                    vm.makeToast(
                      `danger`,
                      vm.$t("inused_alert"),
                      vm.$t("unable_message1") +
                        `${item.name}` +
                        vm.$t("unable_message2"),
                      `${item.name} ` +
                        vm.$t("delete_record_message") +
                        vm.$t("from") +
                        vm.$t("label.brands") +
                        vm.$t("records")
                    );
                    item.is_loading = false;
                  });
              } else {
                item.is_loading = false;
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
  },
};
</script>
