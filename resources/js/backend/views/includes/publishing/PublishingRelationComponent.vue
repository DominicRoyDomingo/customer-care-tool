<template>
  <div>
    <v-app id="relation__container">
      <b-modal
        id="publishing-relation"
        centered
        hide-footer
        :title="$t(modal.name)"
      >
        <v-list-item
          v-for="(row, index) in $this.publishing_relation"
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
      </b-modal>
    </v-app>
  </div>
</template>

<script>
import { attachTags, createLink } from "./../../../api/publishing.js";

import publishingMixins from "./mixins/publishingMixins.js";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.publishing_relation,

      form: this.$this.form,

      formData: this.$this.formData,

      modalId: this.$this.modalId,
    };
  },

  methods: {
    createLink(publishingRelation) {
      console.log(publishingRelation);
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
              this.$this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${
                  publishingRelation.linked_publishing_relation_name
                } ${this.$t("create_publishing_link_alert")}`
              );

              this.form.reset();

              this.$this.loadPublishingRelation(publishingRelation.publishing);

              if (this.$this.search != "")
                this.$this.loadPublishing(this.$this.page, this.$this.search);
              this.$this.params != null
                ? this.$this.loadPublishing(this.$this.page, "", this.$this.params)
                : this.$this.loadPublishing(this.$this.page, "");

              this.form.language = this.$i18n.locale;
            });
          }
        });
    },
  },
};
</script>
