<template>
  <b-modal
    id="add-author-type-modal"
    @hide="$bvModal.hide('add-author-type-modal')"
    hide-header
    hide-footer
    size="lg"
  >
    <v-app id="contacts__modal">
      <v-card>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              <span v-if="parent.typeAuthor.action == 'Add'">
                {{ $t("label.new") }} {{ $t("new_type_of_author") }}
              </span>

              <span
                class="d-inline-block text-truncate"
                style="max-width: 700px; letter-spacing: normal"
                v-else
              >
                {{ $t("button.edit") }} "{{ parent.typeAuthor.default }}"
                <small
                  v-if="parent.typeAuthor.convertion == true"
                  style="color: red"
                >
                  (No {{ parent.typeAuthor.language }} translation yet)</small
                >
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn icon color="error lighten-2" @click="closeModal">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text class="modal__content">
            <v-container>
              <v-container>
                <v-row>
                  <v-col class="modal__input-container">
                    <div
                      class="form-group mb-4"
                      v-show="parent.typeAuthor.action === 'Update'"
                    >
                      <v-row>
                        <v-col cols="9"> </v-col>
                        <v-col cols="3">
                          <div>
                            <select
                              class="form-control"
                              @change.prevent="selectLang($event)"
                            >
                              <option
                                :value="language.value"
                                :selected="language.value === parent.language"
                                v-for="(
                                  language, langInd
                                ) in parent.languageOptions"
                                :key="langInd"
                              >
                                {{ language.text }}
                              </option>
                            </select>
                          </div>
                        </v-col>
                      </v-row>

                      <hr />
                    </div>

                    <div class="form-group">
                      <v-text-field
                        :label="$t('name').toUpperCase()"
                        id="title"
                        type="text"
                        name="title"
                        v-model="parent.typeAuthor.name"
                        hide-details="auto"
                      />
                      <small
                        style="color: red; position: absolute"
                        v-if="name_required"
                      >
                        {{ $t("label.name") }} {{ $t("is_required") }}</small
                      >
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-container>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <a
              :href="`/admin/categorized-terms/type-of-authors`"
              v-if="parent.isShowLink"
              class="view-type-notes"
              style="float: left; color: red"
              >{{ $t("label.goto_page") }}</a
            >
            <v-spacer></v-spacer>
            <v-btn color="error" text tile @click="closeModal">
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
                <div v-if="parent.typeAuthor.action == 'Add'">
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  {{ $t("button.save_change") }}
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
  props: ["parent"],

  data() {
    return {
      loading: true,
      vm: this,
      valid: true,
      name_required: false,
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
    ...mapActions("categ_terms", [
      "post_to_type_author_route",
      "get_type_author_name",
    ]),

    closeModal() {
      this.$bvModal.hide("add-author-type-modal");
      this.parent.typeAuthor.reset();
      this.parent.typeAuthor.action = "Add";
    },

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.name_required = false;
      if (this.parent.typeAuthor.name === "") {
        this.name_required = true;
        return false;
      }
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        locale: this.parent.typeAuthor.language,
        id: this.parent.typeAuthor.id,
        name: this.parent.typeAuthor.name,
      };

      this.post_to_type_author_route(params).then((resp) => {
        this.parent.btnloading = false;
        resp = resp.data;
        if (resp.responseStatus) {
          //Add callback function here if necessary

          let message = {
            create:
              `${this.parent.typeAuthor.name}` +
              this.$t("created_message") +
              this.$t("type_of_dates"),
            update:
              this.$t("updated_message1") +
              this.$t("label.client_profile") +
              ` ID: ${this.parent.typeAuthor.id}` +
              this.$t("updated_message2"),
            title: {
              create: this.$t("new_record_created"),
              update: this.$t("record_updated"),
            },
          };
          let act = message.title.create;
          let header = message.create;
          if (this.parent.typeAuthor.action === "Update") {
            act = message.title.update;
            header = message.update;
          }
          this.parent.makeToast("success", act, header);
          this.cleanForm();
          this.$emit("loadTable");
          this.$bvModal.hide("add-author-type-modal");
        } else {
          if (JSON.parse(JSON.stringify(resp.duplicate)) == true) {
            this.parent.makeToast(
              "danger",
              "DUPLICATE",
              JSON.parse(JSON.stringify(resp.name)) +
                this.$t("type_of_author_exist")
            );
            return false;
          }
        }
      });
      // .catch((error) => {
      //   console.log(error);
      //   this.parent.form.errors.record(error.response.data.errors);
      //   this.parent.btnloading = false;
      // });
    },

    cleanForm() {
      this.parent.typeAuthor.id = "";
      this.parent.typeAuthor.name = "";
      this.parent.typeAuthor.action = "Add";
      this.parent.typeAuthor.default = "";
      this.parent.typeAuthor.convertion = "";
      this.parent.typeAuthor.language = this.$i18n.locale;
    },

    selectLang(event) {
      let formData = new FormData();

      this.parent.typeAuthor.language = event.target.value;
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.parent.typeAuthor.id);
      formData.append("lang", event.target.value);
      // this.parent.typeAuthor.language = event.target.value;
      this.get_type_author_name(formData).then((resp) => {
        if (resp) {
          this.parent.typeAuthor.name = resp;
        } else {
          this.parent.typeAuthor.name = "";
        }
      });
    },
  },
};
</script>
