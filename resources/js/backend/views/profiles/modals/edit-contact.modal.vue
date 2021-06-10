<template>
  <b-modal
    id="edit-contact-modal"
    @show="onShow"
    @hide="onHide"
    @ok="handleOk"
    hide-header
    hide-footer
    size="md"
    scrollable
  >
    <v-app id="edit-contact__modal">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <span>
            {{ $t("label.edit_contact") }}
          </span>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="onHideClick"
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
              <contactDiv
                v-bind:key="'engagement_' + parent.editingIndex"
                :root_parent="parent"
                :parent="vm"
                :index="parent.editingIndex"
                :is_deletable="false"
                ref="contact"
              ></contactDiv>
              <div class="row">
                <div class="col-md-6">
                  <b-link
                    href="#"
                    class="text-danger"
                    @click.prevent="onDelete"
                  >
                    {{ $t("label.remove_this_contact") }}
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
            @click="onHideClick"
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
import contactDiv from "./../components/contact_div.vue";

export default {
  name: "edit_contact_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      isHideBtn : false
    };
  },

  components: {
    contactDiv,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("profile", ["post_to_action_route"]),
    handleOk(bvModalEvt){
        bvModalEvt.preventDefault()
    },
    onHide(bvModalEvt){
        if(!this.isHideBtn){
            bvModalEvt.preventDefault()  
        }
    },
    onShow(){
        this.isHideBtn = false
    },
    onHideClick(){
        this.isHideBtn = true
        this.$bvModal.hide('edit-contact-modal')
    },
    hide() {
      this.$emit("hide");
    },

    onDelete() {
      let vm = this;
      let contact_id = this.parent.form.contacts[this.parent.editingIndex].id;
      let contact_description = this.parent.form.contacts[
        this.parent.editingIndex
      ].contact_type.contact_type_name;

      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content: vm.$t("toasts.delete_contact"),
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
                  action_route: "/delete_contact",
                  id: contact_id,
                };

                vm.post_to_action_route(params)
                  .then((resp) => {
                    vm.parent.btnloading = false;
                    vm.onHideClick()

                    resp = resp.data;
                    if (resp.responseStatus) {
                      //Add callback function here if necessary
                      vm.parent.deletedContactSuccessfully(resp.contacts);

                      let message = {
                        delete:
                          `${contact_description} ` + vm.$t("toasts.has_been_deleted"),
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
        action_route: "/update_contact",
        id: this.parent.form.contacts[this.parent.editingIndex].id,
        contact: this.parent.form.contacts[this.parent.editingIndex],
      };

      this.post_to_action_route(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("edit-contact-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            let notification_message = this.$t("toasts.updated_contact");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.contacts[this.parent.editingIndex].id
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
            this.parent.updatedContactSuccessfully(resp.contacts);
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
