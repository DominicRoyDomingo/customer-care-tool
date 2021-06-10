<template>
  <div>
    <b-modal
      id="job-category-modal"
      @hide="parent.showProfileModal(3)"
      hide-header
      hide-footer
      size="md"
    >
      <v-app id="contact_type__container">
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
              @click="$bvModal.hide('job-category-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <v-container>
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
            </v-container>

            <div class="form-group">
              <v-container>
                <label for="category">
                  {{ $t("label.job_category") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="category"
                  type="text"
                  name="category"
                  v-model="parent.form.category"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="job"
                />
                <small
                  id="job"
                  v-if="parent.form.errors.has('category')"
                  v-text="parent.form.errors.get('category')"
                  class="text-danger"
                />
              </v-container>
            </div>
          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('job-category-modal')"
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
      profile: "profile/profile",
    }),
  },

  methods: {
    ...mapActions("jobs", ["post_job_category"]),
    hide() {
      this.$emit("hide");
    },
    onSave() {
      this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.sub_action,
        category: this.parent.form.category,
        brand_id: this.profile.brand_ids.length ? this.profile.brand_ids[0] : 0,
        locale: this.parent.language,
        type: "category",
      };

      this.post_job_category(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-category-modal");
          if (this.jobStatus) {
            let notification_message = this.$t("toasts.added_job_category");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.category
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

            // check when data is success save
            if (!this.parent.listAddShow) {
              this.parent.language = "en";
              this.parent.listAddShow = true;
              this.parent.form.category = "";
              this.parent.listcaterories();
              this.$bvModal.show("job-profession-modal");
            }

            this.parent.addedNewCategorySuccessfully();
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
