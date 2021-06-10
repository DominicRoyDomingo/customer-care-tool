<template>
  <b-modal
    id="add-notes-modal"
    hide-header
    hide-footer
    size="lg"
  >
    <v-app id="contacts__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("label.add_notes") }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
                ({{ parent.form.profile_name }})
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('add-notes-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-container>
            <v-card-text class="modal__content">
              <notesDiv
                  v-for="(note, index) in new_notes"
                  v-bind:key="'note_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :index="note.index"
                  :is_deletable="true"
                  ref="notes"
              ></notesDiv>
              <div class="row">
                  <div class="col-md-12">
                  <v-btn
                      tile
                      block
                      color="success lighten-1"
                      @click.prevent="parent.addNote"
                  >
                      <v-icon>mdi-sticker-plus</v-icon>&nbsp;
                      {{ $t("label.add_note") }}
                  </v-btn>
                  </div>
              </div>
            </v-card-text>
          </v-container>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('add-notes-modal')"
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
                  {{ $t("button.save") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import notesDiv from "./../../profiles/components/note_div";

export default {
  name: "add_notes_modal",
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
    };
  },

  components: {
    notesDiv,
  },

  computed: {
    ...mapGetters("profile", {
      responseStatus: "get_response_status",
    }),

    old_notes() {
      let filtered = [];
      this.parent.form.notes.forEach((data, index) => {
        data.index = index;
        if (data.added_by != undefined) filtered.push(data);
      });

      return filtered;
    },

    new_notes() {
      let filtered = [];
      this.parent.form.notes.forEach((data, index) => {
        data.index = index;
        if (data.added_by == undefined) filtered.push(data);
      });

      return filtered;
    },
  },

  methods: {
    ...mapActions("profile", ["post_to_action_route"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_notes",
        profile_id: this.parent.form.profile_id,
        notes: this.new_notes,
      };
      // return;
      this.post_to_action_route(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("add-notes-modal");
          resp = resp.data;
          if (resp.responseStatus) {
            //Add callback function here if necessary

            let notification_message = this.$t("toasts.added_note");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.itemSelected.profile_name
            );

            let message = {
              create: notification_message,
              update:
                this.$t("updated_message1") +
                this.$t("label.client_profile") +
                ` ID: ${this.parent.form.profile_id}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.parent.addedNotesSuccessfully(resp.notes);

          }
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          this.toastError(errors);
          this.parent.btnloading = false;
        });
    },
    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    }
  },
};
</script>
