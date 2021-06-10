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
              <v-col>
                <div class="headline text--secondary font-weight-medium">
                  {{ firstPublishing.publishing_name }}
                </div>
                <div>
                  <v-btn
                    text
                    small
                    @click.prevent="backToPublishing"
                    class="caption text--secondary"
                  >
                    <v-icon size="15">
                      mdi-chevron-left
                    </v-icon>
                    &nbsp;
                    {{ $t("publishing_check_other_publishing") }}
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
                  @click="onStatus(currentPublishingStatus.date_sequence)"
                >
                  {{ $t("button.status") }}
                  <v-icon small right>
                    mdi-swap-vertical
                  </v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
          <v-card color="blue-grey lighten-5" tile>
            <v-container>
              <v-row>
                <v-col>
                  <div class="text-body-2 text--secondary font-weight-bold">
                    <span>
                      {{ $t("publishing_attached_tags") }}
                    </span>
                  </div>
                </v-col>
                <v-col>
                  <v-btn
                    small
                    tile
                    outlined
                    class="float-right"
                    @click="onTags(firstPublishing)"
                    color="teal lighten-1"
                  >
                    {{ $t("tags_item_tooltip") }}
                    <v-icon small right>
                      mdi-tag-plus
                    </v-icon>
                  </v-btn>
                </v-col>
              </v-row>
              <v-row>
                <span
                  v-for="(row, index) in firstPublishing.tags_name"
                  :key="index"
                  style="margin: 5px"
                >
                  <v-chip
                    class="overline"
                    color="teal lighten-4"
                    style="margin: 5px; padding: 10px 20px"
                  >
                    {{ row.tags_name }}
                    <v-icon small right @click="removeTag(row)">
                      mdi-close
                    </v-icon>
                  </v-chip>
                </span>
              </v-row>
            </v-container>
          </v-card>
        </v-card>
        <v-timeline dense align-top>
          <v-timeline-item
            v-for="(row, index) in publishings"
            :key="index"
            :color="
              row.status_name == currentPublishingStatus.status_name
                ? 'green lighten-1'
                : 'primary lighten-1'
            "
            :icon="`mdi-roman-numeral-` + (index + 1)"
          >
            <v-card
              :color="
                row.status_name == currentPublishingStatus.status_name
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
                      {{ index > 0 ? "Person in-charge: " : "Author: " }}
                      {{ row.author }}
                    </div>
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-chip
                    v-if="
                      row.status_name == currentPublishingStatus.status_name
                    "
                    color="white"
                    dark
                    style="padding: 10px 20px"
                    class="overline font-weight-bold text-center float-right"
                    outlined
                  >
                    {{ $t("publishing_current_status") }}
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
                  <span style="color: white">{{
                    $t("publishing_related")
                  }}</span>
                </div>
              </v-col>
            </v-row>
          </v-container>
          <v-card-text class="white" light>
            <v-list-item
              v-for="(row, index) in firstPublishing.publishing_relations"
              :key="index"
              color="primary"
            >
              <v-list-item-content>
                <v-list-item-title
                  color="primary"
                  class="text--secondary"
                  style="font-size: 0.8125rem !important"
                >
                  <v-row>
                    <v-col>
                      <v-icon left>mdi-newspaper</v-icon>
                      {{ row.linked_publishing_relation_name }}
                    </v-col>
                    <v-col>
                      <v-hover v-slot:default="{ hover }">
                        <v-fade-transition>
                          <v-btn
                            class="overline float-right"
                            @click="
                              row.status == 'To Be Linked'
                                ? createLink(row)
                                : unlink(row)
                            "
                            :color="
                              row.status == 'To Be Linked'
                                ? hover
                                  ? 'yellow darken-1'
                                  : 'red lighten-2'
                                : hover
                                ? 'red lighten-2'
                                : 'yellow darken-1'
                            "
                            style="margin: 5px; padding: 10px 20px"
                            :dark="
                              row.status == 'To Be Linked'
                                ? hover
                                  ? false
                                  : true
                                : hover
                                ? true
                                : false
                            "
                            rounded
                          >
                            <v-icon left>{{
                              row.status == "To Be Linked"
                                ? hover
                                  ? "mdi-link-variant"
                                  : "mdi-link-variant-off"
                                : hover
                                ? "mdi-link-variant-off"
                                : "mdi-link-variant"
                            }}</v-icon>
                            {{
                              hover
                                ? row.status == "To Be Linked"
                                  ? $t("button.create_link")
                                  : $t("button.unlink")
                                : row.status
                            }}
                          </v-btn>
                        </v-fade-transition>
                      </v-hover>
                    </v-col>
                  </v-row>
                </v-list-item-title>
                <v-divider></v-divider>
              </v-list-item-content>
            </v-list-item>
          </v-card-text>
        </v-card>
      </v-container>
    </v-card>
    <HistoryStatus :$this="this"></HistoryStatus>

    <Tags :$this="this"></Tags>
  </v-app>
</template>

<script>
import HistoryStatus from "./HistoryStatusComponent.vue";

import Tags from "./../TagsComponent.vue";

import Form from "./../../../../shared/form.js";

import toast from "./../../../../helpers/toast.js";

import {
  fetchNextStatus,
  fetchHistory,
  attachTags,
  removeTags,
  getTags,
  createLink,
} from "./../../../../api/publishing.js";

export default {
  mixins: [toast],

  components: {
    HistoryStatus,

    Tags,
  },

  data: function() {
    return {
      isLoading: false,

      publishings: [],

      users: [],

      firstPublishing: [],

      tags: [],

      modalId: "",

      publishing_id: "",

      publishing_name: "",

      nextStatus: "",

      otherTags: [],

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

        tags: {
          isVisible: false,

          id: "publishing-tags",

          button: {
            update: "save_button",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },

        copy_tags: {
          isVisible: false,

          id: "publishing-copy-tags",

          button: {
            update: "copy_button",

            cancel: "cancel",

            new: "item_add_edit_button",

            loading: false,
          },
        },
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
      },

      form: new Form({
        id: "",

        item_name: "",

        status: "",

        item_type: "",

        sequence: "",

        author: "",

        tags: [],

        notes: "",

        name: "",

        platformId: "",

        linked_publishing: "",

        publishing: "",

        language: this.$i18n.locale,
      }),

      formData: new FormData(),
    };
  },

  mounted() {
    this.loadPublishing();
  },

  methods: {
    backToPublishing() {
      window.location.href = `/admin/publishing-tools/publishing`;
    },

    loadPublishing() {
      this.isLoading = true;
      let uri = window.location.search.substring(1);

      let params = new URLSearchParams(uri);

      fetchHistory(params.get("id")).then((resp) => {
        this.publishings = resp.publishings;

        this.firstPublishing = this.publishings[0];

        this.isLoading = false;
      });
    },

    onStatus(sequence) {
      fetchNextStatus(sequence).then((resp) => {
        this.nextStatus = resp.status;

        this.form.sequence = this.nextStatus.status;

        this.form.status = this.nextStatus.id;

        this.users = resp.users;

        this.form.id = this.currentPublishingStatus.publishing_id;

        this.form.platformId = this.currentPublishingStatus.platform_id;

        this.modal.status.isVisible = true;
      });
    },

    loadTags() {
      getTags().then((resp) => {
        this.tags = resp.tags;
      });
    },

    onTags(firstPublishing) {
      this.loadTags()

      this.publishing_name = this.firstPublishing.publishing_name;

      this.form.tags = this.firstPublishing.tags;

      this.publishing_id = this.firstPublishing.publishing_id;

      this.modalId = this.modal.tags.id;

      this.$bvModal.show("publishing-tags");
    },

    removeTag(tag) {
      this.$bvModal
        .msgBoxConfirm(
          this.$t("question_record_delete") +
            `${tag.name}` +
            this.$t("from") +
            this.$t("tags_name") +
            this.$t("records"),
          {
            title: this.$t("confirmation_record_delete"),
            okVariant: "danger",
            okTitle: this.$t("yes"),
            cancelTitle: this.$t("no"),
            hideHeaderClose: false,
            centered: true,
          }
        )
        .then((resp) => {
          if (resp) {
            let formData = new FormData();

            formData.append(
              "publishing_id",
              this.firstPublishing.publishing_id
            );
            formData.append("tag", tag.tag);

            removeTags(formData).then((resp) => {
              this.makeToast(
                "danger",
                this.$t("delete_record"),
                `${resp.data.message} ${this.$t("remove_tags_alert")} ${
                  this.firstPublishing.publishing_name
                }`
              );

              this.form.reset();

              this.loadPublishing();

              this.form.language = this.$i18n.locale;
            });
          }
        });
    },

    createLink(publishingRelation) {
      this.$bvModal
        .msgBoxConfirm(
          this.$t("question_create_link") +
            `${publishingRelation.linked_publishing_relation_name}`,
          {
            title: this.$t("confirmation_create_link"),
            okVariant: "warning",
            okTitle: this.$t("yes"),
            cancelTitle: this.$t("no"),
            hideHeaderClose: false,
            centered: true,
          }
        )
        .then((resp) => {
          if (resp) {
            let formData = new FormData();

            formData.append(
              "linked_publishing",
              publishingRelation.linked_publishing
            );
            formData.append("publishing", publishingRelation.publishing);
            formData.append("status", publishingRelation.status);

            createLink(formData).then((resp) => {
              this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${
                  publishingRelation.linked_publishing_relation_name
                } ${this.$t("create_publishing_link_alert")}`
              );

              this.form.reset();

              this.loadPublishing();

              this.form.language = this.$i18n.locale;
            });
          }
        });
    },

    unlink(publishingRelation) {
      this.$bvModal
        .msgBoxConfirm(
          this.$t("question_unlink") +
            `${publishingRelation.linked_publishing_relation_name}`,
          {
            title: this.$t("confirmation_unlink"),
            okVariant: "warning",
            okTitle: this.$t("yes"),
            cancelTitle: this.$t("no"),
            hideHeaderClose: false,
            centered: true,
          }
        )
        .then((resp) => {
          if (resp) {
            let formData = new FormData();

            formData.append(
              "linked_publishing",
              publishingRelation.linked_publishing
            );
            formData.append("publishing", publishingRelation.publishing);
            formData.append("status", publishingRelation.status);

            createLink(formData).then((resp) => {
              this.makeToast(
                "success",
                this.$t("unlink"),
                `${
                  publishingRelation.linked_publishing_relation_name
                } ${this.$t("unlink_alert")}`
              );

              this.form.reset();

              this.loadPublishing();

              this.form.language = this.$i18n.locale;
            });
          }
        });
    },
  },

  computed: {
    currentPublishingStatus() {
      return this.publishings[this.publishings.length - 1];
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

.chip {
  display: inline-block;
  height: 32px;
  padding: 0 12px;
  margin-right: 1rem;
  margin-bottom: 1rem;
  font-size: 13px;
  font-weight: 500;
  line-height: 32px;
  color: rgba(0, 0, 0, 0.6);
  background-color: #eceff1;
  border-radius: 16px;
  -webkit-transition: all 0.3s linear;
  transition: all 0.3s linear;
}

.chip .close {
  float: right;
  padding-left: 8px;
  font-size: 16px;
  line-height: 32px;
  cursor: pointer;
  -webkit-transition: all 0.1s linear;
  transition: all 0.1s linear;
}

.publishing-relation-list:not(:last-child) {
  margin-bottom: 10px;
}

.text-red {
  color: red;
}

.text-blue {
  color: blue;
}
</style>
