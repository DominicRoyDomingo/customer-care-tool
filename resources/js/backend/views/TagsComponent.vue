<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("tags_list") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createTag"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-tag-plus</v-icon>&nbsp;
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
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :busy="isLoading"
                :items="items"
                v-model="tags"
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

                <template v-slot:cell(name)="data">
                  <span
                    class="overline font-weight-bold text--secondary"
                    style="font-size: 0.8rem !important"
                  >
                    {{ data.item.name }}
                    <small
                      v-if="data.item.convertion == true"
                      style="color:red"
                    >
                      (No {{ data.item.language }} translation yet)
                    </small>
                  </span>
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
                                mdi-tag-text
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
                                mdi-tag-remove
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
                  <strong v-text="tags.length" />
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

      <Create :$this="this" @loadTable="loadTags"></Create>
      <Edit :$this="this"></Edit>
    </v-app>
  </div>
</template>

<script>
import Create from "./includes/tags/CreateComponent.vue";

import Edit from "./includes/tags/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import { fetchList, softDelete, getTagsName } from "./../api/tags.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Edit,

    Loading,
  },

  data: function() {
    return {
      tags: [],
      
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
        { key: "index", label: "ID", thStyle: { width: "1%" } },

        {
          key: "name",
          label: this.$t("table.tags_name"),
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

      modal: {
        add: {
          name: "tags_add_new_button",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "tags_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "tags_add_edit_button",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "tags_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        name: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

      default: '',
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
    this.loadTags();
  },

  methods: {
    loadTags() {
      this.isLoading = true;

      fetchList().then((resp) => {
        this.items = resp;

        this.isLoading = false;
      });
    },

    createTag() {
      this.$bvModal.show("tags-modal");
    },

    onEdit(tags) {
      console.log( tags );
      this.form.id = tags.id;
      this.default = tags.name;
      this.convertion = tags.convertion;
      this.language = tags.language;

      getTagsName(this.form.id, this.$i18n.locale).then((resp) => {
        if (resp) {
          this.form.name = resp.name;
        } else {
          this.form.name = "";
        }
      });

      this.modal.edit.isVisible = true;
    },

    onDelete(tags) {
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
          `${tags.name}` +
          vm.$t("from") +
          vm.$t("tags_name") +
          vm.$t("records"),
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(resp) {
              if (resp) {
                tags.loading = true;

                softDelete(tags.id).then((resp) => {
                  tags.loading = false;

                  if (resp == false) {
                    vm.makeToast(
                      "danger",

                      vm.$t("data_used"),

                      vm.$t("used_data_alert")
                    );

                    return false;
                  }

                  vm.makeToast(
                    resp.type,

                    vm.$t("delete_record"),

                    resp.message + " " + vm.$t("remove_tags_alert")
                  );

                  vm.loadTags();
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
  margin-left: -17% !important;
  margin-top: 6.8% !important;
} */
</style>