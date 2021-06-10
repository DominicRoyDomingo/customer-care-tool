<template>
  <div class="animated fadeIn">
    <v-app id="content__container" light>
      <v-card tile flat>
        <v-row style="margin: 20px 15px 0 10px">
          <v-col cols="6" sm="6" md="4">
            <div class="title font-weight-light text--secondary">
              {{ $t("content_list") }}
            </div>
            <div class="subheading font-weight-medium text--disabled">
              {{ $t("publishing_tools") }}
            </div>
          </v-col>
          <v-spacer></v-spacer>
          <v-col cols="6" sm="6" md="4">
            <v-btn color="success" @click="onCreate" class="float-right" tile>
              <v-icon dark>mdi-book-plus-multiple</v-icon>&nbsp;
              {{ $t(modal.add.button.new) }}
            </v-btn>
          </v-col>
        </v-row>
        <v-row style="margin: 0 15px 0 10px">
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
            <b-input-group>
              <template v-slot:append>
                <b-dropdown right class="dropdown--filter">
                  <template slot="button-content">
                    <i class="fa fa-filter" aria-hidden="true"> </i>
                  </template>
                  <b-dropdown-item @click="onSearch()">
                    {{ $t("table.search") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('name')">
                    {{ $t("table.filter_by_content_name") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('status')">
                    {{ $t("table.filter_by_status") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('author')">
                    {{ $t("table.filter_by_content_item_author") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="seeAll()">
                    {{ $t("table.filter_by_see_all") }}
                  </b-dropdown-item>
                </b-dropdown>
              </template>
              <b-form-input
                v-model="searched"
                type="search"
                :placeholder="$t('search_here')"
                style="border-radius: 0; height: 36px"
              ></b-form-input>
            </b-input-group>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-container fluid>
              <b-table
                ref="table"
                striped
                stacked="md"
                show-empty
                primary-key="id"
                :current-page="currentPage"
                :per-page="perPage"
                :busy="isLoading"
                :items="rows"
                :fields="columns"
                thead-class="content__table-header"
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
                <template v-slot:cell(content_name)="data">
                  <v-btn
                    text
                    rounded
                    large
                    @click.prevent="showHistory(data.item.id)"
                    color="primary"
                    class="text-subtitle-1 font-text-bold"
                  >
                    <v-icon left>mdi-book</v-icon>
                    {{ data.item.name }}
                  </v-btn>
                  <br />
                  <v-btn
                    @click.prevent="showItems(data.item, $i18n.locale)"
                    color="primary lighten-1"
                    text
                    rounded
                    style="margin: 10px"
                    small
                  >
                    <v-icon left small>mdi-information-outline</v-icon>
                    Item: {{ data.item.items }}
                  </v-btn>
                </template>
                <template v-slot:cell(status)="data">
                  <div style="display: flex; justify-content: center;">
                    <v-menu top left>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          v-on="on"
                          class="overline font-weight-bold content__row-details"
                          color="primary lighten-1"
                          style="padding: 10px 20px"
                          dark
                          outlined
                          rounded
                        >
                          <v-container>
                            <div style="margin: 0">
                              {{ data.item.status_name }}
                            </div>
                            <div class="caption" style="margin: 0">
                              {{ data.item.date }}
                            </div>
                          </v-container>
                        </v-btn>
                      </template>
                      <v-card max-width="400" class="content__menu">
                        <v-list color="primary lighten-1" dark>
                          <v-list-item>
                            <v-list-item-avatar>
                              <v-avatar left color="white">
                                <span
                                  style="color: #1976d2"
                                  class="caption font-weight-bold"
                                >
                                  {{
                                    data.item.author_name
                                      .split(/\s/)
                                      .reduce(
                                        (response, word) =>
                                          (response += word.slice(0, 1)),
                                        ""
                                      )
                                  }}
                                </span>
                              </v-avatar>
                            </v-list-item-avatar>
                            <v-list-item-content>
                              <v-list-item-title class="title">
                                {{ data.item.name }}
                              </v-list-item-title>
                              <v-list-item-subtitle>
                                {{ data.item.author_name }}
                              </v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                              <v-btn icon @click="menu = false">
                                <v-icon>mdi-close-circle</v-icon>
                              </v-btn>
                            </v-list-item-action>
                          </v-list-item>
                        </v-list>
                        <v-list>
                          <v-list-item @click="() => {}">
                            <v-list-item-action>
                              <v-icon>mdi-information</v-icon>
                            </v-list-item-action>
                            <v-list-item-subtitle>
                              <span v-if="data.item.notes != null">
                                {{ data.item.notes }}
                              </span>
                              <span v-else>
                                No Notes
                              </span>
                            </v-list-item-subtitle>
                          </v-list-item>
                        </v-list>
                      </v-card>
                    </v-menu>
                  </div>
                </template>
                <template v-slot:cell(author_name)="data">
                  <div class="content__row-details">
                    <div class="body-2 text--secondary">
                      Author:
                      <span class="body-2 text--disabled">
                        {{ data.item.author_name }}
                      </span>
                    </div>
                    <div class="body-2 text--secondary">
                      Current Person in-charge:
                      <span class="body-2 text--disabled">
                        {{ data.item.person_in_charge_name }}
                      </span>
                    </div>
                  </div>
                </template>
                <template v-slot:cell(after)="data">
                  <v-menu top left>
                    <template v-slot:activator="{ on }">
                      <v-btn
                        color="#c8ced3"
                        light
                        v-on="on"
                        tile
                        depressed
                        small
                        class="content__row-details"
                      >
                        {{ $t("button.more_actions") }}
                        <v-icon small right>
                          mdi-chevron-down
                        </v-icon>
                      </v-btn>
                    </template>
                    <v-list dense class="content__table-menu">
                      <v-list-item-group color="primary">
                        <v-list-item @click="onStatus(data.item)">
                          <v-list-item-icon>
                            <v-icon color="green darken-2">
                              mdi-swap-vertical
                            </v-icon>
                          </v-list-item-icon>
                          <v-list-item-content>
                            <v-list-item-title>
                              {{ $t("button.status") }}
                            </v-list-item-title>
                          </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="onEdit(data.item)">
                          <v-list-item-icon>
                            <v-icon color="yellow darken-2">
                              mdi-file-edit
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
                              mdi-book-remove
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
                </template>
              </b-table>
            </v-container>
          </v-col>
        </v-row>
        <v-row class="float-right">
          <v-container>
            <v-col>
              <v-pagination
                v-if="!isLoading"
                v-model="currentPage"
                :length="Math.ceil(totalRecords / perPage)"
                :total-visible="5"
                color="secondary"
                circle
                class="profile__table-pagination"
              >
              </v-pagination>
            </v-col>
          </v-container>
        </v-row>
      </v-card>
      <Create :$this="this"></Create>
      <Edit :$this="this"></Edit>
      <Status :$this="this"></Status>
    </v-app>
  </div>
</template>

<script>
// Components
import Create from "./includes/content/CreateComponent.vue";

import Edit from "./includes/content/UpdateComponent.vue";

import Status from "./includes/content/StatusComponent.vue";

import Loading from "vue-loading-overlay";

import datatable from "./../components/DataTable.vue";

import pagination from "./../components/Pagination.vue";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import {
  fetchList,
  softDelete,
  fetchNextStatus,
  getLastStatus,
  fetchUsers,
} from "./../api/content.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Edit,

    datatable,

    pagination,

    Loading,

    Status,
  },

  data: function() {
    let sortOrders = {};

    let columns = [
      { width: "5%", name: "id", type: "number" },
      { name: "name" },
      { width: "20%", name: "created_at" },
      { width: "20%", name: "updated_at" },
      { width: "20%", name: "action" },
    ];

    columns.forEach((column) => {
      sortOrders[column.name] = 2;
    });

    return {
      records_found: false,

      isLoading: false,
      currentPage: 1,
      perPage: 10,
      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],
      columns: [
        {
          key: "content_name",
        },
        {
          key: "status",
        },
        {
          key: "author_name",
        },
        {
          key: "after",
        },
      ],
      rows: [],
      totalRecords: 0,

      contents: [],

      brands: [],

      statuses: [],

      nextStatus: "",

      page: "",

      searched: "",

      search: "",

      params: null,

      users: [],

      cliparts: [],

      modal: {
        add: {
          name: "content_add_new_button",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "content_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "content_add_edit_button",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "content_add_edit_button",

            loading: false,
          },
        },

        status: {
          name: "content_add_status_button",

          isVisible: false,

          button: {
            update: "save_button",

            cancel: "cancel",

            new: "content_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        name: "",

        brand: "",

        author_idea: "",

        author_task: "",

        status: "",

        clipart: "",

        sequence: "",

        statusId: "",

        notes: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadContent();
  },

  methods: {
    showItems(content, lang) {
      const h = this.$createElement;

      getLastStatus().then((resp) => {
        if (resp.lastStatus != content.status_name) {
          this.$bvModal.msgBoxOk(
            h("v-app", [
              h("v-container", [
                h("p", {
                  class: ["text--secondary text-subtitle-2"],
                  domProps: {
                    innerHTML: this.$t("content_not_complete_message"),
                  },
                }),
              ]),
            ]),
            {
              title: h("v-app", [
                h("v-container", [
                  h("div", {
                    class: ["text--secondary text-h6"],
                    domProps: {
                      innerHTML:
                        this.$t("content_not_complete_title")
                          .charAt(0)
                          .toUpperCase() +
                        this.$t("content_not_complete_title")
                          .toLowerCase()
                          .slice(1),
                    },
                  }),
                  h("v-divider"),
                ]),
              ]),
              okVariant: "success",
              hideHeaderClose: false,
              centered: true,
              headerClass: "p-2 border-bottom-0",
              footerClass: "p-2 border-top-0",
            }
          );
          return;
        }

        window.location.href = `content/items?id=${content.id}`;
      });
    },

    onPageChange(params) {
      this.loadContent(params.currentPage);
    },

    filter(filter) {
      this.search = "";
      this.params = {
        filter: filter,
        filter_value: this.searched,
      };
      this.loadContent(1, "", this.params);
    },

    onSearch() {
      this.params = null;
      this.search = this.searched;
      this.loadContent(1, this.search);
    },

    seeAll() {
      this.searched = null;
      this.params = null;
      this.loadContent();
    },

    showHistory(id) {
      window.location.href = `content/history?id=${id}`;
    },

    loadContent(page, search, filter) {
      this.isLoading = true;

      fetchList(page, search, filter).then((resp) => {
        if (resp.statuses == null) {
          this.$bvModal
            .msgBoxOk(this.$t("content_status_not_existing"), {
              title: this.$t("confirmation_date_status"),
              okVariant: "success",
              hideHeaderClose: false,
              centered: true,
            })
            .then((value) => {
              window.location.href = `/admin/publishing-tools/date-status`;
            });
        } else {
          this.statuses = resp.statuses;

          this.users = resp.users;

          this.rows = resp.contents;

          this.totalRecords = resp.totalRecords;
          this.page = resp.page;
        }

        this.isLoading = false;
      });
    },

    onCreate() {
      fetchUsers().then((resp) => {
        this.users = resp.users;
        this.modal.add.isVisible = true;
      });
    },

    onEdit(content) {
      fetchUsers().then((resp) => {
        this.users = resp.users;
        this.form.id = content.id;
        this.form.name = content.name;
        this.form.status = content.status;
        this.form.author_idea = content.author_id;
        this.modal.edit.isVisible = true;
      });
    },

    onStatus(content) {
      fetchNextStatus(content.date_sequence).then((resp) => {
        this.nextStatus = resp.status;
        this.users = resp.users;
        this.form.sequence = this.nextStatus.status;
        this.form.statusId = this.nextStatus.id;
        this.form.id = content.id;
        this.form.name = content.name;
        this.modal.status.isVisible = true;
      });
    },

    onDelete(content) {
      const h = this.$createElement;

      this.$bvModal
        .msgBoxConfirm(
          h("v-app", [
            h("v-container", [
              h("p", {
                class: ["text--secondary text-subtitle-2"],
                domProps: {
                  innerHTML:
                    this.$t("question_record_delete") +
                    `${content.name}` +
                    this.$t("from") +
                    this.$t("content") +
                    this.$t("records"),
                },
              }),
            ]),
          ]),
          {
            title: h("v-app", [
              h("v-container", [
                h("div", {
                  class: ["text--secondary text-h6"],
                  domProps: {
                    innerHTML:
                      this.$t("confirmation_record_delete")
                        .charAt(0)
                        .toUpperCase() +
                      this.$t("confirmation_record_delete")
                        .toLowerCase()
                        .slice(1),
                  },
                }),
                h("v-divider"),
              ]),
            ]),
            okVariant: "danger",
            okTitle: this.$t("yes"),
            cancelTitle: this.$t("no"),
            hideHeaderClose: false,
            centered: true,
            headerClass: "p-2 border-bottom-0",
            footerClass: "p-2 border-top-0",
          }
        )
        .then((resp) => {
          if (resp) {
            content.loading = true;

            softDelete(content.id).then((resp) => {
              content.loading = false;
              if (resp.name != undefined) {
                this.makeToast(
                  "danger",
                  this.$t("failed_to_delete"),
                  `${this.$t("unable_message1")} ${resp.name} ${this.$t(
                    "unable_message2"
                  )}`
                );
                return;
              }

              if (resp == false) {
                this.$notify({
                  title: this.$t("data_used"),

                  message: this.$t("used_data_alert"),

                  type: "error",
                });

                return false;
              }

              this.makeToast(
                resp.type,
                this.$t("content_deleted"),
                resp.message + " " + this.$t("remove_content_alert")
              );

              this.loadContent(this.page);
            });
          }
        });
    },
  },
};
</script>

<style scoped>
.v-menu__content {
  margin-left: -16% !important;
  margin-top: 1%;
}
</style>

<style lang="scss">
.dropdown--filter {
  transition: all 0.2s ease;

  button {
    color: white !important;
    border-radius: 0;
    background-color: #2196f3 !important;
    transition: all 0.5s ease-out;
    min-width: 64px;
    z-index: auto !important;

    &:active {
      opacity: 0.75;
    }
  }
  a {
    color: black !important;
    &:hover {
      background-color: rgba(100, 100, 100, 0.05);
    }
    &:active {
      background-color: rgba(100, 100, 100, 0.1);
    }
  }
  ul {
    margin-top: 0;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0px 5px 5px -3px rgba(0, 0, 0, 0.2),
      0px 8px 10px 1px rgba(0, 0, 0, 0.14), 0px 3px 14px 2px rgba(0, 0, 0, 0.12);
  }
  li {
    font-size: 0.8125rem;
  }
}
.content__table-header th {
  display: none;
}
.content__row-title {
  padding-bottom: 4px;
  border-bottom: 3px solid rgba(85, 77, 207, 0.1);
  transition: all 0.2s ease-in-out;

  &:hover {
    border-bottom: 4px solid rgba(85, 77, 207, 0.5);
    text-decoration: none;
  }
  a:hover {
    text-decoration: none;
  }
}
.content__row-details {
  margin: 40px 0 20px 0;
}
.clipart-item {
  position: absolute;

  left: 40px;

  top: 75%;

  left: 50%;

  transform: translate(-50%, -50%);

  color: white;
}

.clipart-wrapper {
  background: rgb(22, 116, 149);

  padding-top: 32% !important;
}

.clipart-wrapper:hover,
.history-wrapper:hover {
  background-color: rgb(130, 205, 231);
}

.content-card {
  cursor: pointer;
}

th:nth-last-child(2) {
  text-align: center;
}

th:nth-last-child(3) {
  text-align: center;
}

.card {
  margin-bottom: 0px !important;
}

.content-item {
  margin-bottom: 1.5rem;
}

.image-loading {
  padding-top: 100%;
}
</style>
