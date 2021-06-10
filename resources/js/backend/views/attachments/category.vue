<template>
  <div class="animated fadeIn">
    <v-app id="category__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <div class="title font-weight-light text--secondary">
                {{ $t("label.category_of_document") }}
              </div>
              <div class="text-subtitle-1 font-weight-medium text--disabled">
                {{ $t("label.attachments") }}
              </div>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="openFormModalAsAdd"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-file-compare</v-icon>
                <v-icon size="13" style="margin: 10px 0 0 -5px">
                  mdi-plus
                </v-icon>
                &nbsp;
                {{ $t("label.new_category") }}
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
                ref="table"
                show-empty
                stacked="md"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :busy="isLoading"
                :items="items"
                v-model="attachementTable"
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

                <template v-slot:cell(document_category_name)="data">
                  <div style="margin: 10px 0 0 25px;">
                    <span
                      class="text-left text--secondary"
                    >
                      {{ data.item.document_category_name }}
                      <small
                        v-if="data.item.convertion == true"
                        style="color:red"
                      >
                        (No {{ data.item.language }} translation yet)</small
                      >
                    </span>

                    <!-- <span
                      class="text-subtitle-1 text--secondary"
                      v-html="data.item.document_category_name"
                    >
                    </span> -->
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
                          <v-list-item
                            @click="openFormModalAsEdit(data.item, data.index)"
                          >
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-file-compare
                              </v-icon>
                              <v-icon
                                color="yellow darken-3"
                                size="13"
                                style="margin: 10px 0 0 -4px"
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
                          <v-list-item @click="onDelete(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-file-compare
                              </v-icon>
                              <v-icon
                                color="red lighten-1"
                                size="13"
                                style="margin: 10px 0 0 -4px"
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
                  <strong v-text="attachementTable.length" />
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
      </v-card>

      <formModal :parent="this"></formModal>
    </v-app>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import attachmentsMixins from "./mixins/attachmentsMixins";
import formModal from "./modals/category/formModal";

export default {
  mixins: [attachmentsMixins],
  components: {
    formModal,
  },
  data() {
    return {
      tableHeaders: [
        {
          key: "document_category_name",
          label: this.$t("label.category_name"),
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
    };
  },
  created() {
    this.loadCategories();
  },
  computed: {
    ...mapGetters("attachments", {
      items: "categories",
      response_status: "get_response_status",
    }),
  },
  methods: {
    ...mapActions("attachments", ["get_categories", "delete_category"]),
    async loadCategories() {
      this.isLoading = true;
      this.categoryReferrer = null;

      let params = {
        api_token: this.$user.api_token,
        locale: this.language,
        sortbyLang: this.sortbyLangId,
      };

      await this.get_categories(params).then((_) => {
        this.isLoading = false;
      });
    },
    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadCategories();
    },
    openFormModalAsAdd() {
      this.resetForm();
      this.form.action = "Add";
      this.btnloading = false;
      this.isCategoryFormModalOpen = true;
    },
    openFormModalAsEdit(item, index) {
      this.resetForm();
      this.form.action = "Edit";
      this.btnloading = false;
      this.language = this.$i18n.locale;
      this.isCategoryFormModalOpen = true;

      // set values that will be updated
      this.form.category_name = item.document_category_name.includes("<small")
        ? null
        : item.document_category_name;
      this.form.id = item.id;
    },
    successfullySavedDocCategory() {
      this.isCategoryFormModalOpen = false;
      this.language = this.$i18n.locale;
      this.loadCategories();
    },
    onDelete(item, index) {
      let vm = this;

      let confirmation_message = vm.$t("label.delete_category_confirmation");
      confirmation_message = confirmation_message.replace(
        /%variable%/g,
        "<strong>" + item.document_category_name + "</strong>"
      );

      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content: confirmation_message,
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

                let notification_message = vm.$t(
                  "toasts.deleted_document_category"
                );
                notification_message = notification_message.replace(
                  /%variable%/g,
                  item.document_category_name
                );

                vm.delete_category(params)
                  .then((resp) => {
                    if (vm.response_status) {
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        notification_message
                      );

                      vm.loadCategories();
                    }
                  })
                  .catch((error) => {
                    vm.makeToast(
                      "danger",
                      vm.$t("failed_to_delete"),
                      error.response.data.errors.documentCategory[0]
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
  watch: {
    isCategoryFormModalOpen: function(isShown) {
      document.querySelector("html").style.overflow = isShown ? "hidden" : null;
    },
  },
};
</script>
