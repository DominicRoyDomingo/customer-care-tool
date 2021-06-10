<template>
  <div class="animated fadeIn">
    <v-app id="content__container" light>
      <v-fade-transition v-if="this.isLoading">
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
      <v-card v-else tile flat>
        <v-row style="margin: 20px 15px 0 10px">
          <v-col cols="6" sm="6" md="4">
            <div class="title font-weight-light text--secondary">
              {{ $t("publishing_list") }}
            </div>
            <div class="subheading font-weight-medium text--disabled">
              {{ $t("publishing_tools") }}
            </div>
          </v-col>
          <v-spacer></v-spacer>
          <v-col cols="6" sm="6" md="4">
            <v-btn color="success" @click="onCreate" class="float-right" tile>
              <v-icon dark>mdi-newspaper-plus</v-icon>&nbsp;
              {{ $t(modal.add.button.new) }}
            </v-btn>
          </v-col>
        </v-row>
        <v-row style="margin: 0 15px 0 10px">
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
                    {{ $t("table.filter_by_publishing_name") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('status')">
                    {{ $t("table.filter_by_status") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('author')">
                    {{ $t("table.filter_by_publishing_author") }}
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
              <vue-good-table
                mode="remote"
                @on-page-change="onPageChange"
                :group-options="{
                  enabled: true,
                }"
                :pagination-options="{
                  enabled: true,
                  mode: 'records',
                  perPage: 5,
                }"
                :sort-options="{
                  enabled: true,
                }"
                :totalRows="totalRecords"
                :rows="rows"
                :columns="columns"
                id="publishing__table"
                styleClass="vgt-table striped"
              >
                <template slot="table-header-row" slot-scope="props">
                  <span
                    class="publishing__table-header font-weight-bold text-subtitle-1"
                  >
                    {{ props.row.label }}
                  </span>
                </template>
                <template slot="table-row" slot-scope="props">
                  <span v-if="props.column.field == 'publishing_name'">
                    <v-row no-gutters>
                      <v-col cols="12" sm="12" md="6">
                        <v-btn
                          text
                          rounded
                          @click.prevent="showHistory(props.row.id)"
                          :style="[
                            props.row.tags == '' ? { color: 'red' } : '',
                          ]"
                          color="primary"
                        >
                          <v-icon left small>mdi-newspaper</v-icon>
                          {{ props.row.publishing_name }}
                        </v-btn>
                        <br />
                        <v-btn
                          text
                          rounded
                          small
                          @click.prevent="
                            showItemHistory(
                              props.row.item_id,
                              props.row.content_id
                            )
                          "
                          color="primary"
                        >
                          {{ props.row.item_name }}
                        </v-btn>
                        <v-container>
                          <div style="margin-top: 20px">
                            <div class="body-2 text--secondary">
                              Author:
                              <span class="body-2 text--disabled">
                                {{ props.row.author_name }}
                              </span>
                            </div>
                            <div class="body-2 text--secondary">
                              Publisher:
                              <span class="body-2 text--disabled">
                                {{ props.row.publisher_name }}
                              </span>
                            </div>
                          </div>
                          <v-chip
                            class="overline"
                            color="red lighten-2"
                            dark
                            small
                            style="margin-top: 30px"
                            @click.prevent="
                              props.row.to_be_linked == 0
                                ? undefined
                                : onPublishingRelation(props.row.id)
                            "
                          >
                            <v-icon left small>mdi-link-variant</v-icon>
                            <div style="letter-spacing: 0.05em;">
                              {{ props.row.to_be_linked }}
                              {{
                                props.row.to_be_linked == 1
                                  ? $t("publishing_to_be_linked_no_s")
                                  : $t("publishing_to_be_linked")
                              }}
                            </div>
                          </v-chip>
                        </v-container>
                      </v-col>
                      <v-col cols="12" sm="12" md="3">
                        <v-container>
                          <div
                            class="text-body-2 font-weight-bold text--secondary text-md-center"
                            style="margin: 50px auto 15px auto"
                          >
                            <span
                              class="d-inline-block text-truncate"
                              style="max-width: 290px; letter-spacing: normal; "
                            >
                              Publish to {{ props.row.platform_name }}
                            </span>
                          </div>
                          <div class="text-md-center">
                            <v-btn
                              class="overline font-weight-bold"
                              color="primary lighten-1"
                              dark
                              outlined
                              rounded
                            >
                              <v-container>
                                <div style="margin: 0">
                                  {{ props.row.status_name }}
                                </div>
                                <div class="caption" style="margin: 0">
                                  {{ props.row.date }}
                                </div>
                              </v-container>
                            </v-btn>
                          </div>
                        </v-container>
                      </v-col>
                      <v-col cols="12" sm="12" md="3">
                        <div
                          class="text-body-2 font-weight-bold text--secondary text-right"
                          style="margin: 50px auto 15px auto"
                        >
                          <v-container>
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
                                  <v-list-item @click="onStatus(props.row)">
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
                                  <v-list-item @click="onEdit(props.row)">
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
                                  <v-list-item @click="onDelete(props.row)">
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
                                  <v-divider></v-divider>
                                  <v-list-item @click.stop="onTags(props.row)">
                                    <v-list-item-icon>
                                      <v-icon color="green darken-2">
                                        mdi-tag-plus
                                      </v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                      <v-list-item-title>
                                        {{ $t("tags_item_tooltip") }}
                                      </v-list-item-title>
                                    </v-list-item-content>
                                  </v-list-item>
                                </v-list-item-group>
                              </v-list>
                            </v-menu>
                          </v-container>
                        </div>
                      </v-col>
                    </v-row>
                  </span>
                  <span v-else></span>
                </template>
              </vue-good-table>
            </v-container>
          </v-col>
        </v-row>
      </v-card>

      <Create :$this="this"></Create>

      <Edit :$this="this"></Edit>

      <Status :$this="this"></Status>

      <PlatformType :$this="this" @loadTable="loadPublishing"></PlatformType>

      <Tags :$this="this"></Tags>

      <PublishingRelation :$this="this"></PublishingRelation>
    </v-app>
  </div>
</template>

<script>
// Components
import Create from "./includes/publishing/CreateComponent.vue";

import Edit from "./includes/publishing/UpdateComponent.vue";

import Status from "./includes/publishing/StatusComponent.vue";

import Tags from "./includes/publishing/TagsComponent.vue";

import PublishingRelation from "./includes/publishing/PublishingRelationComponent.vue";

import PlatformType from "./includes/modals/CreatePlatformComponent.vue";

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
  fetchTags,
  getUsersAndItems,
  getPublishingRelation,
  getTags,
} from "./../api/publishing.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Edit,

    datatable,

    pagination,

    Loading,

    Status,

    Tags,

    PlatformType,

    PublishingRelation,
  },

  data: function() {
    let sortOrders = {};

    let columns = [
      { name: "name" },

      { name: "created_at" },

      { name: "updated_at" },

      { name: "action" },
    ];

    columns.forEach((column) => {
      sortOrders[column.name] = 2;
    });

    return {
      isLoading: false,
      columns: [
        {
          field: "publishing_name",
        },
        {
          field: "platform_name",
        },
        {
          field: "after",
        },
      ],
      rows: [],
      totalRecords: 0,
      serverParams: {
        columnFilters: {},
        sort: {
          field: "",
          type: "",
        },
        page: 1,
        perPage: 5,
      },

      isLoading: false,

      isHovering: false,

      records_found: false,

      publishings: [],

      items: [],

      brands: [],

      statuses: [],

      platforms: [],

      tags: [],

      platform_type: [],

      params: null,

      nextStatus: "",

      modalId: "",

      publishing_name: "",

      publishing_id: "",

      page: "",

      search: "",

      searched: "",

      otherTags: [],

      publishing_relation: [],

      modalObj: {},

      users: [],

      cliparts: [],

      modal: {
        add: {
          name: "publishing_add_new_button",

          isVisible: false,

          id: "create-publishing",

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "publishing_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "publishing_add_edit_button",

          id: "edit-publishing",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "publishing_add_edit_button",

            loading: false,
          },
        },

        status: {
          name: "publishing_add_status_button",

          isVisible: false,

          button: {
            update: "save_button",

            cancel: "cancel",

            new: "publishing_add_status_button",

            loading: false,
          },
        },

        tags: {
          isVisible: false,

          id: "publishing-tags",

          button: {
            update: "save_button",

            cancel: "cancel",

            new: "publishing_add_status_button",

            loading: false,
          },
        },

        copy_tags: {
          isVisible: false,

          id: "publishing-tags",

          button: {
            update: "copy_button",

            cancel: "cancel",

            new: "publishing_add_status_button",

            loading: false,
          },
        },

        platformType: {
          name: "platform_item_add_new_button",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "publishing_add_edit_button",

            loading: false,
          },
        },

        publishing_relation: {
          name: "publishing_related",

          id: "publishing-relation",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "publishing_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        name: "",

        item: "",

        author: "",

        tags: [],

        status: "",

        clipart: "",

        sequence: "",

        platform: "",

        platform_type: "",

        statusId: "",

        platformId: "",

        notes: "",

        linked_publishing: "",

        publishing: "",

        status: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadPublishing();
  },

  methods: {
    showHistory(id) {
      window.location.href = `publishing/history?id=${id}`;
    },

    showItemHistory(item_id, content_id) {
      window.location.href = `/admin/publishing-tools/content/items/history?id=${item_id}&content=${content_id}`;
    },

    updateParams(newProps) {
      this.serverParams = Object.assign({}, this.serverParams, newProps);
    },

    onPageChange(params) {
      this.loadPublishing(params.currentPage);
    },

    filter(filter) {
      this.search = "";
      if (this.searched != "") {
        this.params = {
          filter: filter,
          filter_value: this.searched,
        };
      }
      this.loadPublishing(1, "", this.params);
    },

    onSearch() {
      this.params = null;
      this.search = this.searched;
      this.loadPublishing(1, this.search);
    },

    seeAll() {
      this.params = null;
      this.search = "";
      this.loadPublishing();
    },

    // onSearch: _.debounce(function (searchTerm) {
    //   this.loadPublishing(1, searchTerm);
    // }, 1000),

    loadPublishing(page, search, filter) {
      this.isLoading = true;

      fetchList(page, search, filter).then((resp) => {
        if (resp.statuses == null) {
          this.$bvModal
            .msgBoxOk(this.$t("publishing_status_not_existing"), {
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

          this.publishings = resp.publishings;

          this.rows = resp.publishings;

          this.totalRecords = resp.totalRecords;

          this.page = resp.page;

          // this.pagination.total = Object.keys(this.publishings).length;

          // if( this.pagination.total == 0 ) {

          //   this.records_found = true;

          // }
        }

        this.isLoading = false;
      });
    },

    loadPublishingRelation(id) {
      getPublishingRelation(id).then((resp) => {
        this.publishing_relation = resp.publishing_relation;
      });
    },

    onCreate() {
      getUsersAndItems().then((resp) => {
        this.users = resp.users;

        this.items = resp.items;

        this.platforms = resp.platforms;

        this.$bvModal.show("create-publishing");

        this.modalId = this.modal.add.id;
      });
    },

    onEdit(publishing) {
      getUsersAndItems().then((resp) => {
        this.users = resp.users;

        this.items = resp.items;

        this.platforms = resp.platforms;

        this.form.id = publishing.id;

        this.form.name = publishing.publishing_name;

        this.form.item = publishing.item_id;

        this.form.platform = publishing;

        this.form.author = publishing.author_id;

        this.modalId = this.modal.edit.id;

        this.$bvModal.show("edit-publishing");
      });
    },

    onStatus(publishing) {
      fetchNextStatus(publishing.date_sequence).then((resp) => {
        this.nextStatus = resp.status;

        this.users = resp.users;

        this.form.sequence = this.nextStatus.status;

        this.form.statusId = this.nextStatus.id;

        this.form.id = publishing.id;

        this.form.name = publishing.publishing_name;

        this.form.platformId = publishing.platform_id;

        this.modal.status.isVisible = true;
      });
    },

    loadTags() {
      getTags().then((resp) => {
        this.tags = resp.tags;
      });
    },

    onTags(publishing) {
      this.loadTags();

      this.form.id = publishing.id;
      // console.log(publishing.tags);
      this.form.tags = publishing.tags;

      this.publishing_name = publishing.publishing_name;

      this.publishing_id = publishing.id;

      this.modalId = this.modal.tags.id;

      this.$bvModal.show("publishing-tags");
    },

    onPublishingRelation(id) {
      this.loadPublishingRelation(id);

      this.modalId = this.modal.publishing_relation.id;

      this.$bvModal.show("publishing-relation");
    },

    onDelete(publishing) {
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
                    `${publishing.publishing_name}` +
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
            publishing.loading = true;

            softDelete(publishing.id).then((resp) => {
              publishing.loading = false;

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
                this.$t("delete_record"),
                resp.message + " " + this.$t("remove_publishing_alert")
              );

              this.loadPublishing(this.page);
            });
          }
        });
    },

    // for custom datatables
    paginate(array, length, pageNumber) {
      this.pagination.from = array.length ? (pageNumber - 1) * length + 1 : " ";

      this.pagination.to =
        pageNumber * length > array.length ? array.length : pageNumber * length;

      this.pagination.prevPage = pageNumber > 1 ? pageNumber : "";

      this.pagination.nextPage =
        array.length > this.pagination.to ? pageNumber + 1 : "";

      return array.slice((pageNumber - 1) * length, pageNumber * length);
    },

    resetPagination() {
      this.pagination.currentPage = 1;

      this.pagination.prevPage = "";

      this.pagination.nextPage = "";
    },

    sortBy(key) {
      this.resetPagination();

      this.sortKey = key;

      this.sortOrders[key] = this.sortOrders[key] * -1;
    },

    getIndex(array, key, value) {
      return array.findIndex((i) => i[key] == value);
    },
  },
  computed: {
    filterData() {
      let data = this.publishings;

      if (this.search) {
        data = data.filter((row) => {
          return Object.keys(row).some((key) => {
            return (
              String(row[key])
                .toLowerCase()
                .indexOf(this.search.toLowerCase()) > -1
            );
          });
        });
      }

      let sortKey = this.sortKey;

      let order = this.sortOrders[sortKey] || 1;

      if (data.length > 0) {
        data = data.slice().sort((a, b) => {
          let index = this.getIndex(this.columns, "name", sortKey);

          a = String(a[sortKey]).toLowerCase();

          b = String(b[sortKey]).toLowerCase();

          if (this.columns[index].type && this.columns[index].type === "date") {
            return (
              (a === b
                ? 0
                : new Date(a).getTime() > new Date(b).getTime()
                ? 1
                : -1) * order
            );
          } else if (
            this.columns[index].type &&
            this.columns[index].type === "number"
          ) {
            return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
          } else {
            return (a === b ? 0 : a > b ? 1 : -1) * order;
          }
        });
      }

      if (data.length == 0) {
        this.records_found = true;
      } else {
        this.records_found = false;
      }

      this.isLoading = false;

      return data;
    },

    paginated() {
      return this.paginate(
        this.filterData,

        this.length,

        this.pagination.currentPage
      );
    },
  },
};
</script>

<style lang="scss">
#publishing__table {
  thead {
    display: none;
  }
  .vgt-row-header {
    background-color: #f2f3f6 !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    cursor: default !important;
  }
}
</style>

<style scoped>
.v-menu__content {
  margin-left: -16% !important;
  margin-top: 1%;
}
</style>

<style>
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

.publishing-item {
  margin-bottom: 1.5rem;
}

.image-loading {
  padding-top: 100%;
}

.color-red {
  color: red;
}

.color-white {
  color: white;
}

.image-buttons {
  position: absolute;
  width: 100%;
  left: 0;
  bottom: 0;
  padding: 0 10px 10px 10px;
}

.attach-tags-button {
  position: absolute;
  width: 100%;
  top: 0;
  right: 0;
  padding: 10px 10px 0px 10px;
}

.platform-name {
  height: 100px;
  width: 100px;
  position: absolute;
  left: 50%;
  margin-left: -50px;
  top: 50%;
  margin-top: -50px;
}

.platform-wrapper {
  position: relative;
  border-bottom: 1px solid #c8ced3;
  height: 200px;
  width: 100%;
}

.history-card {
  cursor: pointer;
  position: relative;
}

.hovering {
  background-color: rgb(130, 205, 231);
}

.current-status {
  background: #ccc;
  border: 1px solid #ccc;
  border-radius: 3px;
  display: inline-block;
  font-size: 12px;
  font-weight: 700;
  line-height: 99%;
  background-color: #fff;
  border-color: #a5b3c2;
  color: #4a6785;
  margin: 0;
  padding: 2px 5px;
  text-align: center;
  text-decoration: none;
  text-transform: uppercase;
}
</style>
