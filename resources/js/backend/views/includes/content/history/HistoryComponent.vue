<template>
  <v-app id="history__container">
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
    <v-card v-else color="grey lighten-2" outlined>
      <v-container fluid>
        <v-card>
          <v-container>
            <v-row>
              <v-col v-if="!!firstContent.content_name">
                <div class="headline text--secondary font-weight-medium">
                  {{ firstContent.content_name }}
                </div>
                <div>
                  <v-btn
                    text
                    small
                    @click.prevent="backToContent"
                    class="caption text--secondary"
                  >
                    <v-icon size="15">
                      mdi-chevron-left
                    </v-icon>
                    &nbsp;
                    {{ $t("item_check_other_content") }}
                  </v-btn>
                </div>
              </v-col>
              <v-col>
                <v-btn
                  small
                  tile
                  outlined
                  color="grey darken-1"
                  class="float-right"
                  style="margin-top: 15px"
                  @click="onStatus(currentContentStatus.date_sequence)"
                >
                  {{ $t("button.status") }}
                  <v-icon small right>
                    mdi-swap-vertical
                  </v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
        <v-timeline dense align-top>
          <v-timeline-item
            v-for="(row, index) in contents"
            :key="index"
            :color="
              row.status_name == currentContentStatus.status_name
                ? 'green lighten-1'
                : 'primary lighten-1'
            "
            :icon="`mdi-roman-numeral-` + (index + 1)"
          >
            <v-card
              :color="
                row.status_name == currentContentStatus.status_name
                  ? 'green lighten-1'
                  : 'primary lighten-1'
              "
              dark
            >
              <v-card-title class="subheading" dark text-color="white">
                <v-col cols="12" sm="6" md="9">
                  <v-row>
                    <div>
                      {{ row.status_name }}
                    </div>
                  </v-row>
                  <v-row>
                    <div class="body-2" style="color: rgba(255,255,255,0.8)">
                      {{
                        index > 0
                          ? `${$t("content_person_in_charge")}: `
                          : `${$t("content_author_idea")}: `
                      }}
                      {{ row.author_idea }}
                    </div>
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-chip
                    v-if="row.status_name == currentContentStatus.status_name"
                    color="white"
                    dark
                    style="padding: 10px 20px"
                    class="overline font-weight-bold text-center float-right"
                    outlined
                  >
                    {{ $t("content_current_status") }}
                  </v-chip>
                  <div
                    class="body-2 text-right float-right"
                    style="margin-top: 10px; color: rgba(255,255,255,0.8)"
                  >
                    {{ $t("date") }}: {{ row.date_finished }}
                  </div>
                </v-col>
              </v-card-title>
              <v-card-text class="white text--secondary" light>
                <v-container>
                  <span v-if="row.notes != null">
                    <v-icon color="grey" size="16">mdi-information</v-icon>
                    {{ $t("notes") + ": " + row.notes }}
                  </span>
                  <span v-else>No notes</span>
                </v-container>
              </v-card-text>
            </v-card>
          </v-timeline-item>
        </v-timeline>
        <v-card color="blue-grey lighten-2">
          <v-container>
            <v-row>
              <v-col>
                <div class="text-subtitle-1 text--secondary font-weight-medium">
                  <span style="color: white">{{ $t("content_items") }}</span>
                </div>
              </v-col>
              <v-col>
                <v-btn
                  small
                  tile
                  outlined
                  class="float-right"
                  @click="onItem()"
                  dark
                >
                  {{ $t("add_item_tooltip") }}
                  <v-icon small right>
                    mdi-comment-plus
                  </v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
          <v-card-text class="white" light>
            <v-list dense rounded>
              <v-list-item-group>
                <v-list-item
                  v-for="(row, index) in firstContent.items"
                  :key="index"
                  color="primary"
                >
                  <v-list-item-content>
                    <v-list-item-title
                      @click.prevent="showItemHistory(row)"
                      color="primary"
                      class="text--secondary"
                    >
                      <v-icon size="20" left>mdi-comment</v-icon>
                      {{ row.item_name }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card-text>
        </v-card>
      </v-container>
    </v-card>
    <HistoryStatus :$this="this"></HistoryStatus>
    <CreateItem :$this="this" @loadTable="loadContent"></CreateItem>
    <ItemType :$this="this" @loadTable="loadContent"></ItemType>
  </v-app>
</template>

<script>
import HistoryStatus from "./HistoryStatusComponent.vue";

import Form from "./../../../../shared/form.js";

import CreateItem from "./../../content-items/CreateComponent.vue";

import ItemType from "./../../content-items/CreateItemTypeComponent.vue";

import toast from "./../../../../helpers/toast.js";

import {
  fetchNextStatus,
  fetchHistory,
  getItemStatus,
} from "./../../../../api/content.js";

export default {
  mixins: [toast],

  components: {
    HistoryStatus,

    CreateItem,

    ItemType,
  },

  data: function() {
    return {
      isLoading: false,
      contents: [],

      users: [],

      firstContent: [],

      itemTypes: [],

      status: [],

      content_id: "",

      nextStatus: "",

      modalId: "",

      modal: {
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
      },

      form: new Form({
        id: "",

        item_name: "",

        status: "",

        item_type: "",

        sequence: "",

        author_idea: "",

        item_type: "",

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
    backToContent() {
      window.location.href = `/admin/publishing-tools/content`;
    },

    showItemHistory(item) {
      window.location.href = `/admin/publishing-tools/content/items/history?id=${item.id}&content=${item.content}`;
    },

    openItemTypeModal() {
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("create-item-type");
    },

    loadContent() {
      this.isLoading = true;
      let uri = window.location.search.substring(1);

      let params = new URLSearchParams(uri);

      fetchHistory(params.get("id")).then((resp) => {
        this.contents = resp.contents;

        this.content_id = this.contents[0].content_id;

        this.firstContent = resp.firstContent;

        this.isLoading = false;
      });
    },

    onStatus(sequence) {
      fetchNextStatus(sequence).then((resp) => {
        this.nextStatus = resp.status;

        this.users = resp.users;

        this.form.sequence = this.nextStatus.status;

        this.form.status = this.nextStatus.id;

        this.form.id = this.currentContentStatus.content_id;

        this.modal.status.isVisible = true;
      });
    },

    onItem() {
      getItemStatus().then((resp) => {
        if (resp.date_status == null) {
          this.$bvModal.msgBoxOk(this.$t("item_status_not_existing"), {
            title: this.$t("confirmation_date_status"),
            okVariant: "success",
            hideHeaderClose: false,
            centered: true,
          });
          return;
        }

        if (
          resp.lastStatus != this.contents[this.contents.length - 1].status_name
        ) {
          this.$bvModal.msgBoxOk(this.$t("content_not_complete_message"), {
            title: this.$t("content_not_complete_title"),
            okVariant: "success",
            hideHeaderClose: false,
            centered: true,
          });
          return;
        }

        this.status = resp.date_status_item;

        this.itemTypes = resp.item_types;

        this.users = resp.users;

        this.$bvModal.show("create-item");

        this.modalId = this.modal.add.id;
      });
    },
  },

  computed: {
    currentContentStatus() {
      return this.contents[this.contents.length - 1];
    },
  },
};
</script>

<style>
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

.status-header {
  color: #505f79;
  font-weight: bold;
}

.item-list {
  list-style-type: none;
}
</style>
