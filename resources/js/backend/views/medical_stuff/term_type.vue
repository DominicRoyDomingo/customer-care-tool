<template>
  <div>
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <div class="col-md-10">
            <h4 class="card-title">
              {{ $t("label.categorized_terms") }}
              <small class="text-muted text-capitalize">
                {{ $t("label.type_of_terms") }}
              </small>
            </h4>
          </div>
          <div class="col-md-2">
            <v-btn
              color="success"
              class="float-right"
              v-b-modal.term-type-modal
              block
              tile
              @click="onAdd()"
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new_type") }}
            </v-btn>
          </div>
        </div>

        <div class="row mt-5">
          <v-col cols="12" sm="6" md="6">
            <v-col
                  class="caption font-weight-regular text--secondary text-right"
                  style="display: flex; position: absolute; margin-left: -10px;"
                >
                  {{ $t("button.show") }}
                  <b-form-select
                    :options="showEntriesOpt"
                    v-model="perPage"
                    style="
                      border-radius: 0;
                      width: 13% !important;
                      margin-top: -8px;
                      margin-left: 5px;
                      margin-right: 5px;
                    "
                  />{{ $t("label.entries") }}
                <b-input-group-prepend style="margin-left: 10px; margin-top: -10px;">
                  <v-menu offset-y :rounded="false">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="info"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        tile
                        depressed
                        style="
                          height: 33px;
                          min-width: 64px;
                          padding: 0 16px;
                          margin-right: 7px;
                          margin-top: 5px;
                        "
                      >
                        <v-icon :size="18"> mdi-filter-menu </v-icon>
                      </v-btn>
                    </template>
                    <v-list dense class="profile__row-menu">
                      <v-list-item-group color="primary">
                        <v-list-item
                          v-for="(option, index) in noTranslationsOptions"
                          :key="index"
                        >
                          <v-list-item-content
                            @click="sortbyLanguage(option.value)"
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
            </v-col>
          </v-col>
          <v-col cols="12" sm="6" md="6">
            <div class="float-right">
              <b-form inline style="margin-right: -8px">
                <b-input-group class="mb-2 mr-sm-2">
                  <template #append>
                    <b-input-group-text>
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </b-input-group-text>
                  </template>
                  <b-form-input
                    v-model="filter"
                    :placeholder="$t('search_here')"
                    style="width: 300px"
                    type="search"
                  />
                </b-input-group>
              </b-form>
            </div>
          </v-col>
        </div>

        <div class="row">
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
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
                v-model="termTypeTable"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  {{ data.index + 1 }}
                </template>

                <template v-slot:cell(term_type_name)="data">
                  <span v-html="data.item.term_type_name"> </span>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </template>

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="#c8ced3"
                        small
                        v-bind="attrs"
                        v-on="on"
                        :disabled="data.item.is_loading"
                      >
                        <span v-if="data.item.is_loading" class="text-center">
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="white"
                            indeterminate
                          >
                          </v-progress-circular>
                        </span>

                        <span v-else>{{ $t("button.more_actions") }}</span>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item
                        v-for="(action, index) in actions"
                        :key="index"
                        link
                        @click="onAction(data.item, action.value)"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                          />
                          <span
                            :class="{
                              'text-primary': action.variant === 'primary',
                            }"
                            v-html="action.title"
                          />
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>

          <div class="col-md-12 mb-0">
            <v-row>
              <v-spacer>
                <v-col cols="12" sm="6" md="4">
                  <span>
                    Showing
                    <strong v-text="termTypeTable.length" />
                    of
                    <strong v-text="totalRows" />
                    {{ $t("label.entries") }}
                  </span>
                </v-col>
              </v-spacer>
              <v-col>
                <v-spacer>
                  <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    style="float: right"
                  ></b-pagination>
                </v-spacer>
              </v-col>
            </v-row>
          </div>
        </div>
        <!-- Modal Here -->
        <CreateModal :parent="this" @done-success="create_success" />
        <LinkBrand :parent="this" @done-sucess="link_brand_success" />

        <CreateBrand :parent="this" @done-success="create_brand_success" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateModal from "./modals/create-termtype.modal";

import CreateBrand from "./../includes/brands/CreateBrandComponent";

import LinkBrand from "./modals/link/link-brand.modal";
import medicalMixin from "./mixins/medicalMixin";

import Form from "./../../helpers/form";

export default {
  mixins: [medicalMixin],

  components: {
    CreateModal,
    LinkBrand,
    CreateBrand,
  },
  data() {
    return {
      // items: [],
      isLinked: false,
      btnloading: false,
      linkedBrands: [],
      termTypeTable:[],
      actions: [
        {
          value: "edit",
          title: this.$t("button.edit"),
          icon: "pencil-square",
          variant: "success",
        },
        {
          value: "delete",
          title: this.$t("button.delete"),
          icon: "trash-fill",
          variant: "danger",
        },
        {
          value: "brand",
          title: this.$t("label.link_to_brand"),
          icon: "link45deg",
          variant: "primary",
        },
      ],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: "#SN",
          thStyle: { width: "5%" },
        },
        {
          key: "term_type_name",
          label: this.$t("label.type_of_term"),
          thClass: "text-left",
          thStyle: { width: "50%" },
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-right",
          thStyle: { width: "20%" },
          tdClass: "text-right",
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      newSpecializationCategories: [],

      form: new Form({
        id: "",
        brand_name: "",
        brandCategories: [],
        website: "",
        logo: "",
        isDefault: 0,
        action: "",
      }),

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
      },

      formData: new FormData(),
    };
  },

  mounted() {
    // console.log(this.$brand);
    this.loadItems();
    this.loadBrands();
  },

  watch: {
    filter(value) {
      this.loadItems();
    },
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_type_terms_items",
      status: "get_response_status",
    }),

    ...mapGetters("brand", {
      itemBrands: "brands",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["get_type_terms", "delete_term_type"]),

    ...mapActions("brand", ["get_brands"]),

    loadItems() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        sortbyLang: this.sortbyLang,
      };
      this.get_type_terms(params).then((_) => {
        this.isLoading = false;
      });
    },

    sortbyLanguage(value) {
      this.sortbyLang = value;
      this.isLoading = true;
      this.loadItems();
    },

    onAction(item, title) {
      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
        case "brand":
          this.onLinkBrand(item);
          break;
      }
    },

    onLinkBrand(item) {
      this.itemSelected = item;
      this.brandForm.linkedType = "term_type";
      this.mtForm.id = item.id;
      this.brandForm.brands = item.brands;

      this.$bvModal.show("link-brand-modal");
    },

    create_brand_success() {
      this.loadBrands();
      this.$bvModal.show("link-brand-modal");
    },

    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {});
    },

    create_success(item) {
      const recordName = this.$t("label.type_of_terms");

      if (this.typeForm.action === "Add") {
        this.storeToast(item.term_type_name, recordName);
      } else {
        this.updateToast(item.id, recordName);
      }

      this.loadItems();
      this.typeForm.reset();
    },

    link_brand_success() {
      this.loadItems();
    },

    onAdd() {
      this.typeForm.reset();
      this.btnloading = false;
      this.typeForm.action = "Add";
    },
    onEdit(item) {
      this.typeForm.reset();
      this.language = this.$i18n.locale;
      // this.language = item.base_language;
      this.typeForm.id = item.id;
      this.typeForm.term_type = item.has_translation == true ? item.base_name : "";
      this.typeForm.action = "Update";

      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("term-type-modal");
    },
    onDelete(item) {
      let baseName = item.base_name;
      let records = this.$t("label.type_of_terms");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          this.delete_term_type(params)
            .then((resp) => {
              this.deleteToast(baseName, records);
              this.items.splice(
                this.getRemoveItemIndex(this.items, item.id),
                1
              );
            })
            .catch((error) => {
              if (error.response) {
                this.inusedToast(baseName);
                item.is_loading = false;
              }
            });
        }
      });
    },
  },
};
</script>
