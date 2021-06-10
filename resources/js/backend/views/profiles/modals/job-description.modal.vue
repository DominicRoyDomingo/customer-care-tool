<template>
  <div>
    <b-modal
      id="job-description-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="md"
    >
      <v-app id="job-description__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.sub_action == "Add"
                  ? $t("label.add")
                  : $t("button.update")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('job-description-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <br />
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <div
                class="form-group mb-4"
                v-show="parent.form.sub_action === 'Update'"
              >
                <div class="form-inline">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref"
                    >Language</label
                  >
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </div>
                <hr />
              </div>
              <div class="form-group">
                <label for="profession">
                  {{ $t("label.job_profession") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-select
                  id="profession"
                  name="profession"
                  label="profession_name"
                  @change="parent.form.errors.clear('profession')"
                  v-model="parent.form.profession"
                  :options="parent.professions"
                >
                </v-select>
                <small
                  id="job"
                  v-if="parent.form.errors.has('profession')"
                  v-text="parent.form.errors.get('profession')"
                  class="text-danger"
                />
              </div>
              <div class="form-group mt-4">
                <label for="description">
                  {{ $t("label.specilazation") }}
                  <strong class="text-danger">*</strong>
                </label>
                <b-form-input
                  id="description"
                  v-model="parent.form.description"
                  :placeholder="$t('label.required')"
                  type="text"
                />
                <small
                  id="job"
                  v-if="parent.form.errors.has('description')"
                  v-text="parent.form.errors.get('description')"
                  class="text-danger"
                />
              </div>
            </form>
          </v-card-text>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('job-description-modal')"
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
                <div v-if="parent.form.sub_action == 'Add'">
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
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
    };
  },

  computed: {
    ...mapGetters({
      jobStatus: "jobs/get_job_status",
    }),
  },

  methods: {
    ...mapActions("jobs", ["post_job_description"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.sub_action,
        profession: this.parent.form.profession.id,
        description: this.parent.form.description,
        locale: this.parent.language,
        type: "description",
      };
      this.post_job_description(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-description-modal");

          if (this.jobStatus) {
            this.parent.language = "en";

            let notification_message = this.$t("toasts.added_job_description");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.description
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
              "success",
              message.title.create,
              message.create
            );

            this.parent.addedNewDescriptionSuccessfully();
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
