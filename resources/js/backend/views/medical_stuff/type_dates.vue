<template>
  <div class="animated fadeIn mt-5 pl-3 pr-3">
    <v-app id="tags__container">
      <v-card tile flat>
        <div class="row">
          <div class="col-md-6">
            <h4 class="card-title">
              {{ $t("label.categorized_terms") }}
              <small class="text-muted text-capitalize">
                {{ $t("label.type_of_dates") }}
              </small>
            </h4>
          </div>
          <div class="col-md-6">
            <v-btn
              color="success"
              class="float-right"
              v-b-modal.typedate-modal
              tile
              @click="onAdd"
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new_type") }}
            </v-btn>
          </div>
        </div>
        <v-row>
          <v-col cols="12" sm="12" md="2">
            <v-row no-gutters>
              <v-col
                cols="3"
                class="caption font-weight-regular text--secondary text-right"
                style="display: flex; justify-content: center; padding-top: 5px"
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
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(index)="data">
                  {{ data.index + 1 }}
                </template>

                <template v-slot:cell(type_date_name)="data">
                  <span v-html="data.item.type_date_name"> </span>
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
          <div class="col-md-12 mt-0">
            <b-pagination
              v-model="currentPage"
              :total-rows="totalRows"
              :per-page="perPage"
              align="center"
            ></b-pagination>
          </div>
        </div>
        <!-- Modal Here -->
        <IndexCreateTypeDate :parent="this" @done-success="create_success" />
        <!-- <LinkBrand :parent="this"> </LinkBrand> -->
      </v-card>
    </v-app>
  </div>
</template>

<script>
// import CreateModal from "./modals/create-termtype.modal";

import { mapActions, mapGetters } from "vuex";

import LinkBrand from "./modals/link/link-brand.modal";
import medicalMixin from "./mixins/medicalMixin";

import IndexCreateTypeDate from "./modals/create-typedate.modal";

export default {
  mixins: [medicalMixin],

  components: {
    // CreateModal,
    IndexCreateTypeDate,
    LinkBrand,
  },
  data() {
    return {
      isLinked: false,
      btnloading: false,
      linkedBrands: [],
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
        // {
        //   value: "brand",
        //   title: this.$t("label.link_to_brand"),
        //   icon: "link45deg",
        //   variant: "primary",
        // },
      ],

      tableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: "#TD",
          thStyle: { width: "5%" },
        },
        {
          key: "type_date_name",
          label: this.$t("label.type_of_date"),
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
    };
  },

  mounted() {
    // console.log(this.$brand);
    this.loadITems();
    this.loadBrands();
  },

  watch: {
    filter(value) {
      this.loadITems();
    },
  },

  computed: {
    ...mapGetters("categ_terms", {
      items: "get_type_dates_items",
      status: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["get_type_dates", "delete_type_date"]),

    loadITems() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };

      this.get_type_dates(params).then((_) => {
        this.isLoading = false;
      });
    },

    create_success(item) {
      const records = this.$t("label.type_of_dates");
      if (this.typeDateForm.action === "Add") {
        this.storeToast(item.type_date_name, records);
      } else {
        this.updateToast(item.id, records);
      }

      this.typeDateForm.reset();
      this.loadITems();
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

    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };

      // axios.get("/api/brands", { params }).then((resp) => {
      //   this.brands = resp.data;
      // });
    },

    onAdd() {
      this.typeDateForm.reset();
      this.btnloading = false;
      this.typeDateForm.action = "Add";
    },

    onEdit(item) {
      this.typeForm.reset();
      this.language = item.base_language;
      this.typeDateForm.id = item.id;
      this.typeDateForm.name = item.base_name;
      this.typeDateForm.action = "Update";

      this.itemSelected = item;

      // Open Modal
      this.$bvModal.show("typedate-modal");
    },

    onDelete(item) {
      let baseName = item.base_name;
      let records = this.$t("label.type_of_dates");

      this.deleteConfirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            api_token: this.$user.api_token,
            locale: this.language,
            id: item.id,
          };
          this.delete_type_date(params)
            .then((resp) => {
              this.deleteToast(baseName, records);
              this.loadITems();
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
