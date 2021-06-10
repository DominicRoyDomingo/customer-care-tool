<template>
  <b-modal
    id="edit-note-modal"
    @hide="$bvModal.hide('edit-note-modal')"
    hide-header
    hide-footer
    size="md"
    scrollable
  >
    <v-app id="edit-note__modal">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <span>
            {{ $t("label.edit_note") }}
          </span>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="$bvModal.hide('edit-note-modal')"
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
              <div class="note-div note-data" v-if="parent.isNoteExists">

              <div class="form-group">
                <label for="date_requested">{{ $t("label.date_requested") }}</label>
                <datepicker
                  input-class="form-control bg-white"
                  v-model="parent.form.note[0].date_requested"
                  @input="changedDateRequested"
                ></datepicker>
              </div>

              <div class="form-group">
                <label for="note">{{ $t("label.notes") }}</label>
                <textarea
                  v-model="parent.form.note[0].notes"
                  id="note"
                  class="form-control"
                  rows="5"
                ></textarea>
              </div>
            </div>
              <div class="row">
                <div class="col-md-6">
                  <b-link
                    href="#"
                    class="text-danger"
                    @click.prevent="onDelete"
                  >
                    {{ $t("label.remove_this_note") }}
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
            @click="closeModal"
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
                {{ $t("button.save") }}
              </div>
              <div v-else>
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

export default {
  name: "edit_note_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
    };
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),
  },

  mounted() {
  },

  methods: {
    ...mapActions("profile", ["post_to_action_route"]),

    hide() {
      this.$emit("hide");
    },

    onDelete() {
      let vm = this;
      
      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content: vm.$t("toasts.delete_note"),
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
                  action_route: "/delete_note",
                  id: vm.parent.profileViewed.id,
                  note: vm.parent.form.note[0],
                };
                
                vm.post_to_action_route(params)
                  .then((resp) => {
                    vm.parent.btnloading = false;
                    vm.$bvModal.hide("edit-note-modal");

                    resp = resp.data;
                    if (resp.responseStatus) {
                      //Add callback function here if necessary
                      
                      vm.parent.provider.provider_profiles.provider_profile.notes = resp.notes
                      let message = {
                        delete: vm.$t("toasts.deleted_note"),
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

    closeModal() {
      this.$bvModal.hide('edit-note-modal')
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Edit",
        action_route: "/update_note",
        id: this.parent.profileViewed.id,
        note: this.parent.form.note[0]
      };

      this.post_to_action_route(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("edit-note-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            let notification_message = this.$t("toasts.updated_note");
            this.parent.provider.provider_profiles.provider_profile.notes = resp.notes
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
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },

    changedDateRequested: function() {
      let vm = this;
      let date_requested = vm.parent.form.note[0].date_requested;
      var date_object = new Date(date_requested);
      vm.parent.form.note[0].date_requested = formatDateToYMD(
        date_object
      );
    },
  },
};
</script>
