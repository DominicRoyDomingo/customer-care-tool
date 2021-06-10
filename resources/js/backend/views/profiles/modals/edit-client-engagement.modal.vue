<template>
  <b-modal
    id="edit-client-engagement-modal"
    @hide="$bvModal.hide('edit-client-engagement-modal')"
    hide-header
    hide-footer
    size="md"
    scrollable
  >
    <v-app id="edit-client-engagement__modal">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <span>
            {{ $t("label.edit_client_engagement") }}
          </span>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="$bvModal.hide('edit-client-engagement-modal')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text class="modal__content">
          <v-container>
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <clientEngagementsDiv
                v-bind:key="'engagement_' + parent.editingIndex"
                :root_parent="parent"
                :parent="vm"
                :index="parent.editingIndex"
                :is_deletable="false"
                ref="client-engagement"
              ></clientEngagementsDiv>
              <div class="row">
                <div class="col-md-6">
                  <b-link
                    href="#"
                    class="text-danger"
                    @click.prevent="onDelete"
                  >
                    {{ $t("label.remove_this_engagement") }}
                  </b-link>
                </div>
              </div>
            </form>
          </v-container>
        </v-card-text>
        <v-divider style="margin-bottom: 0"></v-divider>
        <v-card-actions class="modal__footer blue-grey lighten-5">
          <v-spacer></v-spacer>
          <v-btn
            color="error"
            text
            tile
            @click="$bvModal.hide('edit-client-engagement-modal')"
          >
            {{ $t("cancel") }}
          </v-btn>
          <v-btn color="success" tile dark @click="onSave">
            <div v-if="parent.btnloading">
              <v-progress-circular
                size="20"
                width="1"
                color="white"
                indeterminate
              >
              </v-progress-circular>
            </div>
            <div v-else>
              <div v-if="parent.form.action == 'Add'">
                <v-icon left>mdi-account-plus</v-icon>
                {{ $t("button.save") }}
              </div>
              <div v-else>
                <v-icon left>mdi-account-edit</v-icon>
                {{ $t("button.save_change") }}
              </div>
            </div>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import clientEngagementsDiv from "./../components/client_engagement_div.vue";
import clientEngagementsDisplay from "./../components/client_engagement_display.vue";

export default {
  name: "client_engagement_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
    };
  },

  components: {
    clientEngagementsDiv,
    clientEngagementsDisplay,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_client_engagements() {
      let filtered = [];
      this.parent.form.client_engagements.forEach((data, index) => {
        data.index = index;
        if (data.id == undefined) filtered.push(data);
      });

      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", [
      "post_client_engagement",
      "delete_client_engagement",
    ]),

    hide() {
      this.$emit("hide");
    },

    onDelete() {
      let vm = this;
      let client_engagement_id = this.parent.form.client_engagements[
        this.parent.editingIndex
      ].id;
      let client_engagement_description =
        this.parent.form.client_engagements[this.parent.editingIndex]
        .engagement.client_engagement_with_brand_names +
        " " +
        this.parent.form.client_engagements[this.parent.editingIndex]
          .platform_name;

      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${client_engagement_description}` +
          vm.$t("from") +
          vm.$t("label.client_engagements") +
          vm.$t("records"),
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
                let params = {
                  api_token: vm.$user.api_token,
                  action_route: "/delete_client_engagement",
                  id: client_engagement_id,
                };

                vm.delete_client_engagement(params)
                  .then((resp) => {
                    vm.parent.btnloading = false;
                    vm.$bvModal.hide("edit-client-engagement-modal");

                    resp = resp.data;
                    if (resp.responseStatus) {
                      let message = {
                        delete:
                          `${client_engagement_description}` +
                          vm.$t("delete_record_message") +
                          vm.$t("from") +
                          vm.$t("label.client_engagements") +
                          vm.$t("records"),
                        title: {
                          delete: vm.$t("delete_record"),
                        },
                      };

                      vm.parent.makeToast(
                        "danger",
                        message.title.delete,
                        message.delete
                      );

                      //Add callback function here if necessary
                      vm.parent.deletedClientEngagementSuccessfully(
                        resp.client_engagements
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                    vm.parent.form.errors.record(error.response.data.errors);
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

    onSave() {
      this.parent.btnloading = true;
      let notification_message = this.$t("toasts.updated_client_engagement");
      notification_message = notification_message.replace(
        /%variable%/g,
        this.parent.form.client_engagements[this.parent.editingIndex].id
      );

      let params = {
        api_token: this.$user.api_token,
        action: "Edit",
        action_route: "/update_client_engagement",
        id: this.parent.form.client_engagements[this.parent.editingIndex].id,
        client_engagement: this.parent.form.client_engagements[
          this.parent.editingIndex
        ],
      };

      this.post_client_engagement(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("edit-client-engagement-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            let message = {
              create: notification_message,
              update: notification_message,
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "warning",
              message.title.update,
              message.update
            );

            //Add callback function here if necessary
            this.parent.updatedClientEngagementSuccessfully(
              resp.client_engagements
            );
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
