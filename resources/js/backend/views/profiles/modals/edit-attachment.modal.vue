<template>
  <b-modal
    id="edit-attachment-modal"
    @hide="$bvModal.hide('edit-attachment-modal')"
    hide-header
    hide-footer
    size="md"
    scrollable
  >
    <v-app id="edit-attachment__modal">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <span>
            {{ $t("label.edit_attachment") }}
          </span>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="$bvModal.hide('edit-attachment-modal')"
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
              <attachmentDiv
                v-bind:key="'attachment_' + parent.editingIndex"
                :root_parent="parent"
                :parent="vm"
                :index="parent.editingIndex"
                :is_deletable="false"
                ref="attachment"
              ></attachmentDiv>
              <div class="row">
                <div class="col-md-6">
                  <b-link
                    href="#"
                    class="text-danger"
                    @click.prevent="onDelete"
                  >
                    {{ $t("label.remove_this_attachment") }}
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
            @click="$bvModal.hide('edit-attachment-modal')"
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
import attachmentDiv from "./../components/attachment_div.vue";

export default {
  name: "edit_attachment_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
    };
  },

  components: {
    attachmentDiv,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("profile", ["post_to_action_route", "post_form_data_to_action_route"]),

    hide() {
      this.$emit("hide");
    },

    onDelete() {
      let vm = this;
      
      let attachment_name = this.parent.form.attachments_info[
        this.parent.editingIndex
      ].filename;

      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content: vm.$t("toasts.confirm_delete_attachment") + " " + attachment_name + "?" ,
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
                  action_route: "/delete_attachment",
                  attachment_info: vm.parent.form.attachments_info[vm.parent.editingIndex],
                };

                vm.post_to_action_route(params)
                  .then((resp) => {
                    vm.parent.btnloading = false;
                    vm.$bvModal.hide("edit-attachment-modal");

                    resp = resp.data;
                    if (resp.responseStatus) {
                      //Add callback function here if necessary
                      vm.parent.deletedAttachmentSuccessfully(resp.attachments);

                      let message = {
                        delete:
                          `${attachment_name} ` + vm.$t("has_been_deleted"),
                        title: {
                          delete: vm.$t("delete_record"),
                        },
                      };

                      vm.parent.makeToast(
                        "danger",
                        message.title.delete,
                        message.delete
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

      let params = {
        api_token: this.$user.api_token,
        action: "Edit",
        action_route: "/update_attachment",
        id: this.parent.form.attachments_info[this.parent.editingIndex].id,
        attachment: this.parent.form.attachments[this.parent.editingIndex],
      };
      
      console.log(params);

      let formData = new FormData();
      
      formData.append("api_token", this.$user.api_token);
      formData.append("action", "Add");
      formData.append("action_route", "/update_attachment");
      formData.append("id", this.parent.form.attachments_info[this.parent.editingIndex].id);
      formData.append("attachment", this.parent.form.attachments[this.parent.editingIndex]);
      formData.append("attachments_info", JSON.stringify(this.parent.form.attachments_info[this.parent.editingIndex]));
      
      this.post_form_data_to_action_route(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("edit-attachment-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            let notification_message = this.$t("toasts.updated_attachment");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.attachments_info[this.parent.editingIndex].id
            );

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
            this.parent.updatedAttachmentSuccessfully(resp.attachments);
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
