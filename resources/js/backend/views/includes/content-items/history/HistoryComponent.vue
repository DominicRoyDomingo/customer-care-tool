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
              <v-col cols="12" sm="6" md="9">
                <div class="headline text--secondary font-weight-medium">
                  {{ firstItem.item }}
                </div>
                <div
                  class="text-subtitle-2 text--disabled font-weight-medium"
                  style="margin-top: 10px"
                >
                  {{ $t("content") }}: {{ content.content_name }}
                </div>
                <div>
                  <v-btn
                    text
                    small
                    @click.prevent="backToContent"
                    class="caption text--secondary"
                  >
                    <i class="fas fa-angle-left"></i>&nbsp;
                    {{ $t("item_check_other_content") }}
                  </v-btn>
                </div>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-row class="float-right">
                  <v-btn
                    small
                    tile
                    outlined
                    color="grey darken-1"
                    @click="onStatus(currentItemStatus.date_sequence)"
                    style="margin-top: 10px"
                  >
                    {{ $t("button.status") }}&nbsp;
                    <i
                      class="fas fa-exchange-alt"
                      style="transform: rotateZ(90deg); stroke-width: 1px;"
                    ></i>
                  </v-btn>
                </v-row>
                <br />
                <br />
                <v-row class="float-right">
                  <v-chip
                    color="blue lighten-1"
                    small
                    class="text-overline font-weight-medium"
                    outlined
                    style="margin-top: 20px"
                  >
                    <i class="fas fa-sitemap"></i>&nbsp; {{ $t("item_type") }}:
                    {{ firstItem.item_type }}
                  </v-chip>
                </v-row>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
        <v-timeline dense align-top>
          <v-timeline-item
            v-for="(row, index) in items"
            :key="index"
            :color="
              row.status_name == currentItemStatus.status_name
                ? 'green lighten-1'
                : 'primary lighten-1'
            "
          >
            <v-card
              :color="
                row.status_name == currentItemStatus.status_name
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
                    v-if="row.status_name == currentItemStatus.status_name"
                    color="white"
                    dark
                    style="padding: 10px 20px"
                    class="overline font-weight-bold text-center float-right"
                    outlined
                  >
                    {{ $t("item_current_status") }}
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
                    <i
                      class="fas fa-info-circle"
                      style="stroke-width: 1px;"
                    ></i>
                    {{ $t("notes") + ": " + row.notes }}
                  </span>
                  <span v-else>No notes</span>
                </v-container>
              </v-card-text>
            </v-card>
          </v-timeline-item>
        </v-timeline>
      </v-container>
    </v-card>
    <HistoryStatus :$this="this"></HistoryStatus>
  </v-app>
</template>

<script>
import HistoryStatus from "./HistoryStatusComponent.vue";

import Form from "./../../../../shared/form.js";

import toast from "./../../../../helpers/toast.js";

import {
  fetchNextStatus,
  fetchHistory,
} from "./../../../../api/content_item.js";

export default {
  mixins: [toast],

  components: {
    HistoryStatus,
  },

  data: function() {
    return {
      isLoading: false,

      firstItem: [],

      items: [],

      content: [],

      users: [],

      nextStatus: "",

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
      },

      form: new Form({
        id: "",

        item_name: "",

        status: "",

        item_type: "",

        sequence: "",

        author_idea: "",

        notes: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadItem();
  },

  methods: {
    backToContent() {
      window.location.href = `/admin/publishing-tools/content/items?id=${this.content.id}`;
    },

    loadItem() {
      this.isLoading = true;
      let uri = window.location.search.substring(1);

      let params = new URLSearchParams(uri);

      fetchHistory(params.get("id"), params.get("content")).then((resp) => {
        this.items = resp.items;

        this.content = resp.content;

        this.users = resp.users;

        this.firstItem = resp.firstItem;

        this.isLoading = false;
      });
    },

    onStatus(sequence) {
      fetchNextStatus(sequence).then((resp) => {
        this.nextStatus = resp.status;

        this.form.sequence = this.nextStatus.status;

        this.form.status = this.nextStatus.id;
      });
      this.form.id = this.currentItemStatus.item_id;

      this.modal.status.isVisible = true;
    },
  },

  computed: {
    currentItemStatus() {
      return this.items[this.items.length - 1];
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
</style>
