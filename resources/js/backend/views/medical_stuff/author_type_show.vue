<template>
  <div class="animated fadeIn mt-5 pl-3 pr-3">
    <v-app id="tags__container">
      <v-card tile flat>
        <div class="row">
          <div class="col-md-6">
            <h4 class="card-title">
              {{ $t("new_type_of_author") }}
            </h4>
          </div>
          <div class="col-md-6">
            <v-btn
              color="success"
              class="float-right"
              v-b-modal.add-author-type-modal
              tile
              @click="onAdd()"
            >
              <i class="fas fa-notes-medical"></i>
              &nbsp; {{ $t("button.new") }} {{ $t("new_type_of_author") }}
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

                <template v-slot:cell(name)="data">
                  <span>
                    {{ data.item.name }}
                    <small
                      v-if="data.item.convertion == true"
                      style="color: red"
                    >
                      (No {{ data.item.language }} translation yet)</small
                    >
                  </span>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at }}</span>
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
        <CreateModal :parent="this" @loadTable="loadItems" />
      </v-card>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateModal from "./modals/create-type-author.modal";

import medicalMixin from "./mixins/medicalMixin";
export default {
  mixins: [medicalMixin],

  components: {
    CreateModal,
  },
  data() {
    return {
      // items: [],
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
          label: "#T",
          thStyle: { width: "5%" },
        },
        {
          key: "name",
          label: this.$t("label.name"),
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
      isShowLink: false,
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
      items: "get_type_author_items",
      status: "get_response_status",
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),
  },

  methods: {
    ...mapActions("categ_terms", [
      "get_type_authors",
      "delete_term_type",
      "delete_type_author",
    ]),

    ...mapActions("brand", ["get_brands"]),

    loadItems() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.get_type_authors(params).then((_) => {
        this.isLoading = false;
      });
    },

    onAction(item, title) {
      switch (title) {
        case "edit":
          this.onEdit(item);
          break;
        case "delete":
          this.onDelete(item);
          break;
        // case "brand":
        //   this.onLinkBrand(item);
        //   break;
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
      this.typeAuthor.language = this.$i18n.locale;
    },
    onEdit(item) {
      this.form.reset();
      this.typeAuthor.language = this.$i18n.locale;
      this.typeAuthor.default = item.name;
      this.typeAuthor.convertion = item.convertion;
      this.typeAuthor.id = item.id;
      this.typeAuthor.name = item.convertion === true ? "" : item.name;
      this.typeAuthor.action = "Update";
      // Open Modal
      this.$bvModal.show("add-author-type-modal");
    },
    onDelete(item, index) {
      let request_type = item;
      let vm = this;

      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.name}` +
          vm.$t("from") +
          vm.$t("type_of_dates") +
          vm.$t("records") +
          "?",
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
                  lang: vm.$i18n.locale,
                };
                vm.delete_type_author(params)
                  .then((resp) => {
                    if (resp.data.inUse == true) {
                      vm.makeToast(
                        "danger",
                        vm.$t("failed_to_delete"),
                        vm.$t("unable_message1") +
                          resp.data.name +
                          vm.$t("unable_message2")
                      );
                      return false;
                    }
                    if (resp.data == true) {
                      vm.loadItems();
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("type_of_dates") +
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
            action: function () {},
          },
        },
      });
    },
  },
};
</script>
