<template>
  <div>
    <b-modal
      id="job-profession-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="md"
    >
      <v-app id="job-profession__container">
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
              @click="$bvModal.hide('job-profession-modal')"
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
                <label for="category">
                  {{ $t("label.job_profession") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="profession"
                  type="text"
                  name="profession"
                  v-model="parent.form.profession"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="job"
                />

                <small
                  id="job"
                  v-if="parent.form.errors.has('profession')"
                  v-text="parent.form.errors.get('profession')"
                  class="text-danger"
                />
              </div>
              <div class="form-group mt-4">
                <label for="category">
                  {{ $t("label.job_category") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-select
                  name="category"
                  multiple
                  label="category_name"
                  v-model="parent.form.category"
                  :options="parent.categories"
                >
                 <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="openJobCat">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ $t("job_category_new_button") }}
                    </b-link>
                  </li>
                </template>
                </v-select>
                <small
                  id="job"
                  v-if="parent.form.errors.has('category')"
                  v-text="parent.form.errors.get('category')"
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
              @click="$bvModal.hide('job-profession-modal')"
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
    ...mapActions("jobs", ["post_job_category", "post_job_profession"]),

    hide() {
      this.$emit("hide");
    },
    openJobCat : function(){
        this.$bvModal.show("job-category-modal");
    },
    onSave() {
      this.parent.btnloading = true;
      let categoryId = [];

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.sub_action,
        profession: this.parent.form.profession,
        category: this.parent.setObjectToArray(this.parent.form.category),
        locale: this.parent.language,
        type: "profession",
      };

      this.post_job_profession(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-profession-modal");

          if (this.jobStatus) {
            this.parent.language = "en";

            let notification_message = this.$t("toasts.added_job_profession");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.profession
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
              this.parent.listAddShow = true;
              this.parent.form.profession = "";
              this.parent.listProfession();
              this.$bvModal.show("job-description-modal");
            }

            this.parent.addedNewProfessionSuccessfully();
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
