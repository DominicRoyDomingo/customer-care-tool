<template>
  <div class="animated fadeIn">
    <v-app id="items__container" light>
      <v-fade-transition v-if="isLoading">
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
      <v-card tile flat v-else>
        <v-row style="margin: 20px 15px 0 10px">
          <v-col cols="6" sm="6" md="4">
            <div class="title font-weight-light text--secondary">
              {{ $t("content_items") }}
            </div>
            <div class="subheading font-weight-medium text--disabled">
              {{ $t("publishing_tools") }}
            </div>
          </v-col>
          <v-spacer></v-spacer>
          <v-col cols="6" sm="6" md="4">
            <v-btn color="success" @click="onCreate" class="float-right" tile>
              <v-icon dark>mdi-comment-plus</v-icon>&nbsp;
              {{ $t(modal.add.button.new) }}
            </v-btn>
          </v-col>
        </v-row>
        <v-row style="margin: 0 15px 0 10px">
          <v-col cols="12" sm="12" md="2">
            <el-select
              filterable
              v-model="form.content_filter"
              clearable
              :placeholder="$t('label.select_content')"
              id="content"
              style="display: block"
              @change="filterContent($event, filterName)"
              @clear="this.loadItem"
            >
              <el-option
                :value="content.id"
                v-for="(content, index) in this.contents"
                :key="index"
                :label="content.content_name"
              ></el-option>
            </el-select>
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
                    {{ $t("table.filter_by_item_name") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('status')">
                    {{ $t("table.filter_by_status") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('content')">
                    {{ $t("table.filter_by_content") }}
                  </b-dropdown-item>
                  <b-dropdown-item @click="filter('item_type')">
                    {{ $t("table.filter_by_item_type") }}
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
          <v-container fluid>
            <v-col>
              <datatable
                :columns="columns"
                :sortKey="sortKey"
                :sortOrders="sortOrders"
                :tableClass="'table table-striped'"
                :headerClass="'table-active'"
                @sort="sortBy"
              >
                <tbody>
                  <tr v-for="(row, index) in paginated" :key="index">
                    <td>
                      <v-btn
                        text
                        rounded
                        @click.prevent="showHistory(row.id, row.content_id)"
                        color="primary"
                      >
                        <v-icon left small>mdi-comment</v-icon>
                        {{ row.item_name }}
                      </v-btn>
                      <small v-if="row.convertion == true" style="color:red">
                        (No {{ row.language }} translation yet)
                      </small>
                    </td>
                    <td class="text-center">
                      <v-btn
                        class="overline font-weight-bold"
                        color="primary lighten-1"
                        style="padding: 10px 20px"
                        dark
                        outlined
                        rounded
                      >
                        <v-container>
                          <div style="margin: 0">
                            {{ row.status_name }}
                          </div>
                          <div class="caption" style="margin: 0">
                            {{ row.updated_at }}
                          </div>
                        </v-container>
                      </v-btn>
                    </td>
                    <td class="text-center">{{ row.content_name }}</td>
                    <td class="text-center">{{ row.type_name }}</td>
                    <td class="text-center">
                      <v-menu top right>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            color="#c8ced3"
                            light
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
                        <v-list dense>
                          <v-list-item-group color="primary">
                            <v-list-item @click="onStatus(row)">
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
                            <v-list-item @click="onEdit(row)">
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
                            <v-list-item @click="onDelete(row)">
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
                            <v-list-item @click="onPublish(row)">
                              <v-list-item-icon>
                                <v-icon color="blue lighten-1">
                                  mdi-newspaper
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content>
                                <v-list-item-title>
                                  {{ $t("button.publish") }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                          </v-list-item-group>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                  <tr v-if="records_found">
                    <td colspan="7" class="text-center">
                      <i> {{ $t("no_record_found") }} </i>
                    </td>
                  </tr>
                </tbody>
              </datatable>
            </v-col>
          </v-container>
        </v-row>
        <v-row>
          <v-col cols="12" sm="12" md="3">
            <v-container>
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
            </v-container>
          </v-col>
          <v-spacer></v-spacer>
          <v-col>
            <pagination
              :client="true"
              :filtered="filterData"
              :pagination="pagination"
              @next="++pagination.currentPage"
              @prev="--pagination.currentPage"
            ></pagination>
          </v-col>
        </v-row>
      </v-card>
      <Create :$this="this" @loadTable="loadItem(search, params)"></Create>
      <Edit :$this="this"></Edit>
      <Status :$this="this"></Status>
      <ItemType
        :$this="this"
        @loadTable="loadContentusersAndItemType"
      ></ItemType>
      <Publish :$this="this"></Publish>
      <Platform
        :$this="this"
        @loadTable="loadUsersAndPlatforms(item)"
      ></Platform>
    </v-app>
  </div>
</template>

<script>
// Components
import Create from "./includes/items/CreateComponent.vue";

import Edit from "./includes/content-items/UpdateComponent.vue";

import Status from "./includes/content-items/StatusComponent.vue";

import Publish from "./includes/content-items/PublishComponent.vue";

import ItemType from "./includes/content-items/CreateItemTypeComponent.vue";

import Platform from "./includes/modals/CreatePlatformComponent.vue";

import Loading from "vue-loading-overlay";

import datatable from "./../components/DataTable.vue";

import pagination from "./../components/Pagination.vue";

import toast from "./../helpers/toast.js";

// Custom Script
import Form from "./../shared/form.js";

import {
  fetchList,
  softDelete,
  fetchNextStatus,
  getLastStatus,
  fetchUsersAndItemTypes,
  getContentUsersAndItemType,
} from "./../api/item.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Edit,

    datatable,

    pagination,

    Loading,

    Status,

    Publish,

    ItemType,

    Platform,
  },

  data: function() {
    let sortOrders = {};

    let columns = [
      { label: "table.item_name", name: "name" },

      { label: "table.item_status", name: "status" },

      { label: "table.item_content", name: "content" },

      { label: "table.item_type", name: "item_type" },

      { label: "table.action", name: "action" },
    ];

    columns.forEach((column) => {
      sortOrders[column.name] = 2;
    });

    return {
      isLoading: false,
      currentPage: 1,
      perPage: 10,
      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

      fullPage: true,

      records_found: false,

      items: [],

      item: "",

      status: [],

      contents: [],

      content_id: "",

      filterName: "",

      search: "",

      searched: "",

      params: null,

      itemTypes: [],

      item_type: [],

      platforms: [],

      users: [],

      brands: [],

      contents: [],

      platform_type: [],

      modalId: "",

      nextStatus: "",

      publishStatusId: "",

      modal: {
        add: {
          name: "item_add_new_button",

          isVisible: false,

          id: "create-item",

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "item_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "item_add_edit_button",

          isVisible: false,

          id: "edit-item",

          button: {
            update: "update",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },

        status: {
          name: "item_add_status_button",

          isVisible: false,

          button: {
            update: "save_button",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },

        publish: {
          name: "item_add_publish_button",

          isVisible: false,

          id: "publish-item",

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "item_add_publish_button",

            loading: false,
          },
        },

        itemType: {
          name: "label.new_itemType",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },

        platformType: {
          name: "label.new_platformType",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        item_name: "",

        status: "",

        item_type: "",

        item_publishing_name: "",

        item_publishing_author: "",

        sequence: "",

        author_idea: "",

        notes: "",

        type_name: "",

        content: "",

        content_filter: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

      // for data tables,
      columns: columns,

      sortKey: "name",

      sortOrders: sortOrders,

      length: 10,

      tableData: {
        client: true,
      },
      pagination: {
        currentPage: 1,

        total: "",

        nextPage: "",

        prevPage: "",

        from: "",

        to: "",
      },
    };
  },

  mounted() {
    this.loadItem();
  },

  methods: {
    showHistory(id, content_id) {
      window.location.href = `/admin/publishing-tools/content/items/history?id=${id}&content=${content_id}`;
    },

    checkOtherContent() {
      window.location.href = `/admin/publishing-tools/content`;
    },

    loadItem(search, filter, content_id) {
      this.isLoading = true;

      fetchList(search, filter, content_id).then((resp) => {
        // console.log(resp.date_status)
        if (resp.status == null) {
          this.$bvModal
            .msgBoxOk(this.$t("item_status_not_existing"), {
              title: this.$t("confirmation_date_status"),
              okVariant: "success",
              hideHeaderClose: false,
              centered: true,
            })
            .then((value) => {
              window.location.href = `/admin/publishing-tools/date-status`;
            });
        } else {
          this.items = resp.items;

          this.status = resp.status;

          this.contents = resp.contents;

          this.publishStatusId = resp.publishStatusId;

          this.pagination.total = Object.keys(this.items).length;

          if (this.pagination.total == 0) {
            this.records_found = true;
          }
        }
        this.isLoading = false;
      });
    },

    loadContentusersAndItemType() {
      getContentUsersAndItemType().then((resp) => {
        this.contents = resp.contents;

        this.users = resp.users;

        this.itemTypes = resp.item_types;
      });
    },

    loadUsersAndPlatforms(item) {
      getLastStatus(item).then((resp) => {
        if (resp.lastStatus != item.status_name) {
          this.$bvModal.msgBoxOk(this.$t("item_not_complete_message"), {
            title: this.$t("item_not_complete_title"),
            okVariant: "success",
            hideHeaderClose: false,
            centered: true,
          });
          return;
        }
        this.users = resp.users;

        this.platforms = resp.platforms;

        this.form.id = item.id;

        this.modalId = this.modal.publish.id;

        this.$bvModal.show("publish-item");
      });
    },

    filterContent(content, filter) {
      if (content == "") return;
      this.loadItem(this.search, this.params, content);
    },
    filter(filter) {
      if (this.searched == "") return;
      this.search = "";
      this.params = {
        filter: filter,
        filter_value: this.searched,
      };
      this.loadItem(this.search, this.params);
    },

    onSearch() {
      this.params = null;
      this.search = this.searched;
      this.loadItem(this.search);
    },

    seeAll() {
      this.params = null;
      this.search = "";
      this.loadItem();
    },

    onCreate() {
      this.loadContentusersAndItemType();

      this.$bvModal.show("create-item");

      this.modalId = this.modal.add.id;
    },

    onEdit(item) {
      fetchUsersAndItemTypes().then((resp) => {
        this.itemTypes = resp.item_types;

        this.users = resp.users;

        this.form.id = item.id;

        this.form.item_name = item.item_name;

        this.form.status = item.status;

        this.form.item_type = item;

        this.form.author_idea = item.author_id;

        this.modalId = this.modal.edit.id;

        this.$bvModal.show("edit-item");
      });
    },

    onStatus(item) {
      fetchNextStatus(item.date_sequence).then((resp) => {
        this.nextStatus = resp.status;

        this.users = resp.users;
        console.log(this.users);
        this.form.sequence = this.nextStatus.status;

        this.form.status = this.nextStatus.id;

        this.form.id = item.id;

        this.form.item_name = item.item_name;

        this.modal.status.isVisible = true;
      });
    },

    onPublish(item) {
      this.item = item;

      this.loadUsersAndPlatforms(this.item);
    },

    onDelete(item) {
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
                    `${item.item_name}` +
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
            item.loading = true;

            softDelete(item.id).then((resp) => {
              item.loading = false;
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
              this.makeToast(
                resp.type,
                this.$t("content_deleted"),
                resp.message + " " + this.$t("remove_item_alert")
              );

              this.loadItem(this.content_id);
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
      let data = this.items;

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

<style scoped>
.v-menu__content {
  margin-left: -16% !important;
  margin-top: 1%;
}
</style>

<style lang="scss" scoped>
th {
  text-align: center;
  padding: 15px 10px;
  font-weight: 500;
  color: rgba(0, 0, 0, 0.6);
  font-size: 15px;
  background-color: white !important;
  border: 0 !important;
}
td {
  padding: 30px 5px !important;
}
.content-button {
  position: absolute;
  left: 46px;
  top: 28px;
}

.dropdown-menu.show {
  padding-left: 0px !important;
}
</style>
