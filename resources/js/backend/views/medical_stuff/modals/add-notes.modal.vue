<template>
  <b-modal
    id="add-notes-modal"
    @hide="$bvModal.hide('add-notes-modal')"
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
                v-html="`(` + parent.articleForm.title + `)`"
              >
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
          <v-card-text class="modal__content">
            <v-tabs
              show-arrows
              center-active
              grow
              background-color="blue-grey lighten-5"
              slider-color="blue-grey darken-2"
              color="blue-grey darken-2"
            >
              <v-tab class="caption font-weight-bold" @click="tabName('new')">
                {{ $t("label.new") }}
              </v-tab>
              <v-tab
                class="caption font-weight-bold"
                @click="tabName('existing')"
              >
                {{ $t("label.existing_notes") }}
              </v-tab>
              <v-tab-item>
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
              </v-tab-item>
              <v-tab-item>
                <!-- {{ parent.notes_array}} -->
                <!-- <div class="form-group">

                    <label for="date_requested">{{ $t("date_of_notes") }}</label>
                    <datepicker
                      input-class="form-control bg-white"
                      v-model="root_parent.form.notes[index].date_requested"
                      @input="changedDateRequested"
                    ></datepicker>
                  </div> -->

                <!-- <div class="form-group">
                    <label for="note">{{ $t("label.notes") }}</label>
                    <textarea
                      v-model="root_parent.form.notes[index].notes"
                      id="note"
                      class="form-control"
                      rows="5"
                    ></textarea>
                  </div>
                </div> -->
                <div
                  class="note-display"
                  v-for="(note, index) in parent.notes_array"
                >
                  <div class="note-div note-data">
                    <div class="row no-gutters">
                      <div class="col-md-11 text-left">
                        <hr />
                      </div>
                      <div class="col-md-1 text-right">
                        <v-btn
                          fab
                          dark
                          small
                          color="error"
                          @click.prevent="deleteNote(index)"
                        >
                          <v-icon dark>mdi-close</v-icon>
                        </v-btn>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <label
                          class="subheading font-weight-bold text--secondary"
                          style="position: absolute"
                        >
                          {{ $t("label.interaction_date") }}:
                          <!-- {{ note.notes_date }}: -->
                        </label>
                        <datepicker
                          :class="`date_picker_notes`"
                          style="width: 20% !important"
                          v-model="note.notes_date"
                          :format="`dd MMM yyyy`"
                        ></datepicker>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <!-- <div
                          class="subheading font-weight-regular text--primary"
                          v-html="note.notes"
                        ></div> -->
                        <textarea
                          v-model="note.notes"
                          id="note"
                          class="form-control"
                          rows="5"
                        ></textarea>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="caption text--disabled text-right">
                          {{ $t("label.added_by") }}:
                          <span class="font-italic">{{ note.created_by }}</span
                          >, {{ note.created_at }}
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
                <!--                 
                <notesDisplay
                  v-for="(note, index) in parent.notes_array"
                  v-bind:key="'note_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :display_array="parent.notes_array"
                  :index="note.index"
                  :is_editable="false"
                  ref="notes-display"
                >{{ parent.notes_array }}</notesDisplay> -->
              </v-tab-item>
            </v-tabs>
          </v-card-text>
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
                <div v-if="parent.notes_array.length > 0">
                  {{ $t("button.save_change") }}
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
import notesDiv from "./note_div.vue";
import notesDisplay from "./note_display.vue";

export default {
  name: "add_notes_modal",
  props: ["root_parent", "parent", "index", "is_deletable"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
      tab_name: "",
    };
  },

  components: {
    notesDiv,
    notesDisplay,
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
      if (this.parent.form.notes && this.parent.form.notes.length !== 0) {
        this.parent.form.notes.forEach((data, index) => {
          data.index = index;
          if (data.added_by == undefined) filtered.push(data);
        });
      }
      return filtered;
    },
  },

  methods: {
    ...mapActions("categ_terms", ["post_to_action_route"]),

    hide() {
      this.$emit("hide");
    },

    tabName(tab) {
      this.tab_name = tab;
    },

    deleteNote(index) {
      let vm = this;
      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_note_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function () {
              vm.parent.notes_array.splice(index, 1);
            },
          },
          cancel: function () {},
        },
      });
    },

    onSave() {
      this.parent.btnloading = true;
      let tab = this.new_notes;
      if (this.tab_name === "existing") {
        tab = this.parent.notes_array;
      }

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        action_route: "/add_notes",
        article_id: this.parent.form.id,
        notes: tab,
      };
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
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
<style>
.date_picker_notes {
  position: relative;
  left: 25%;
  border: 1px solid #e5e5e5;
  width: 20%;
  padding: 2px;
  margin-top: -4px;
  border-radius: 3px;
}
</style>