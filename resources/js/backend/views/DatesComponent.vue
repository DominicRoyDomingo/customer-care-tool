<template>
  <div class="animated fadeIn">
    <v-app id="dates__container" light>
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
              {{ $t("date_list") }}
            </div>
            <div class="subheading font-weight-medium text--disabled">
              {{ $t("publishing_tools") }}
            </div>
          </v-col>
          <v-spacer></v-spacer>
        </v-row>
        <v-container fluid>
          <v-row v-for="(row, index) in statuses" :key="index">
            <v-col cols="12">
              <v-card color="blue-grey lighten-2" tile>
                <v-container>
                  <v-row>
                    <v-col>
                      <div class="text-subtitle-1 text--secondary">
                        <span style="color: white">
                          {{ $t(row.name) }}
                        </span>
                      </div>
                    </v-col>
                    <v-col>
                      <v-menu top left>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            light
                            v-on="on"
                            tile
                            depressed
                            small
                            class="float-right"
                            outlined
                            dark
                          >
                            {{ $t("button.actions") }}
                            <v-icon small right>
                              mdi-chevron-down
                            </v-icon>
                          </v-btn>
                        </template>
                        <v-list dense class="content__table-menu">
                          <v-list-item-group color="primary">
                            <v-list-item @click="onCreate(row.name)">
                              <v-list-item-icon>
                                <v-icon color="green darken-2">
                                  mdi-playlist-plus
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content>
                                <v-list-item-title>
                                  {{ $t(`date_add_new_button_${row.name}`) }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                            <v-list-item @click="onChangeOrder(row.name)">
                              <v-list-item-icon>
                                <v-icon color="yellow darken-2">
                                  mdi-playlist-star
                                </v-icon>
                                <v-icon
                                  small
                                  color="yellow darken-2"
                                  style="margin-left: -25%;"
                                >
                                  mdi-swap-vertical
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content style="margin-left: -9.5%;">
                                <v-list-item-title>
                                  {{ $t("change_order_tooltip") }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                            <v-list-item @click="onEdit(row.name)">
                              <v-list-item-icon>
                                <v-icon color="yellow darken-2">
                                  mdi-playlist-edit
                                </v-icon>
                              </v-list-item-icon>
                              <v-list-item-content>
                                <v-list-item-title>
                                  {{ $t("delete_edit_remove_tooltip") }}
                                </v-list-item-title>
                              </v-list-item-content>
                            </v-list-item>
                          </v-list-item-group>
                        </v-list>
                      </v-menu>
                    </v-col>
                  </v-row>
                </v-container>
                <v-card-text class="white" light>
                  <v-timeline dense>
                    <v-timeline-item
                      v-for="(statuses, index) in row.statuses"
                      :key="index"
                      :color="
                        statuses.isLast
                          ? 'green lighten-1'
                          : 'primary lighten-1'
                      "
                      :icon="`mdi-roman-numeral-` + statuses.sequence"
                    >
                      <v-card
                        :color="
                          statuses.isLast
                            ? 'green lighten-1'
                            : 'primary lighten-1'
                        "
                        dark
                      >
                        <div class="text-subtitle-2" dark text-color="white">
                          <v-container>
                            <v-row>
                              <v-col>
                                <span class="float-left">
                                  {{ statuses.status }}
                                </span>
                              </v-col>
                              <v-divider></v-divider>
                              <v-col>
                                <v-chip
                                  v-if="statuses.isLast"
                                  color="white"
                                  style="padding: 10px 20px"
                                  class="overline font-weight-bold text-center float-right"
                                  outlined
                                  dark
                                  small
                                >
                                  {{ $t("date_is_last") }}
                                </v-chip>
                              </v-col>
                            </v-row>
                          </v-container>
                        </div>
                      </v-card>
                    </v-timeline-item>
                  </v-timeline>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
      <Create :$this="this"></Create>
      <Update :$this="this"></Update>
      <Edit :$this="this"></Edit>
      <ChangeOrder :$this="this"></ChangeOrder>
    </v-app>
  </div>
</template>

<script>
// Components
import Create from "./includes/dates/CreateComponent.vue";

import Update from "./includes/dates/UpdateComponent.vue";

import Edit from "./includes/dates/EditComponent.vue";

import ChangeOrder from "./includes/dates/ChangeOrderComponent.vue";

import Loading from "vue-loading-overlay";

// Custom Script
import Form from "./../shared/form.js";

import toast from "./../helpers/toast.js";

import {
  fetchList,
  softDelete,
  getDateStatusName,
  fetchDateStatus,
} from "./../api/date.js";

export default {
  mixins: [toast],

  components: {
    Create,

    Update,

    Loading,

    Edit,

    ChangeOrder,
  },

  data: function() {
    let sortOrders = {};

    let columns = [
      { width: "5%", label: "ID", name: "id", type: "number" },

      { label: "test", name: "name" },

      { width: "20%", label: "table.created_at", name: "created_at" },

      { width: "20%", label: "table.updated_at", name: "updated_at" },

      { width: "20%", label: "table.action", name: "action" },
    ];

    columns.forEach((column) => {
      sortOrders[column.name] = 2;
    });

    return {
      isLoading: true,

      btnloading: false,

      filter: "",

      currentPage: 1,

      modalId: "",

      items: [],

      class: "",

      perPage: 10,

      statuses: {
        publishing: {
          name: "publishing",
          statuses: [],
        },
        item: {
          name: "item",
          statuses: [],
        },
        content: {
          name: "content",
          statuses: [],
        },
      },

      modal: {
        add: {
          name: "date_add_new_button_",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "date_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "date_add_edit_button",

          id: "update-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            new: "date_add_edit_button",

            loading: false,
          },
        },

        order: {
          name: "date_change_order_button",

          id: "change-order-modal",

          isVisible: false,

          button: {
            update: "update",

            cancel: "cancel",

            reset: "reset",

            new: "date_add_edit_button",

            loading: false,
          },
        },
      },

      form: new Form({
        id: "",

        status: "",

        sequence: "",

        class: "",

        isLast: 0,

        language: this.$i18n.locale,
      }),

      formData: new FormData(),

      // for data tables,
      columns: columns,

      sortKey: "name",

      sortOrders: sortOrders,

      length: 6,

      search: "",

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
    this.loadDate();
  },

  methods: {
    loadDate() {
      this.isLoading = true;

      fetchList().then((resp) => {
        this.statuses.publishing.statuses = resp.dateStatusesForPublishing;

        this.statuses.item.statuses = resp.dateStatusesForItem;

        this.statuses.content.statuses = resp.dateStatusesForContent;

        this.isLoading = false;
      });
    },

    onCreate(dateClass) {
      console.log(dateClass);
      this.form.class = dateClass;

      this.modal.add.isVisible = true;
    },

    onEdit(name) {
      this.class = name;

      fetchDateStatus(name).then((resp) => {
        this.items = resp.items;

        this.modalId = this.modal.edit.id;

        this.$bvModal.show("update-modal");
      });
    },

    onChangeOrder(name) {
      this.class = name;

      fetchDateStatus(name).then((resp) => {
        this.items = resp.items;

        this.modalId = this.modal.edit.id;

        this.$bvModal.show("change-order-modal");
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
      let data = this.statuses.publishing.statuses;

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

<style scoped>
.v-menu__content {
  margin-left: -16% !important;
  margin-top: 1%;
}
</style>

<style lang="scss">
th:nth-last-child(2) {
  text-align: center;
}

th:nth-last-child(3) {
  text-align: center;
}

.date-status-card {
  min-height: 350px;
}
</style>
