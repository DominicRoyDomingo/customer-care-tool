<template>
  <div>
    <b-modal
      id="job-profession-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span v-if="parent.form.action === 'Update'">
                {{ $t("button.edit") }} {{ parent.form.default }}
                {{ parent.form.convertion }}
                <small v-if="parent.form.convertion == true" style="color: red">
                  (No {{ parent.form.language }} translation yet)</small
                >
              </span>
              <span v-else>
                {{ $t("button.new") }} {{ $t("label.profession") }}
              </span>
              <v-spacer></v-spacer>
              <v-btn
                icon
                color="error lighten-2"
                @click="$bvModal.hide('job-profession-modal')"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text class="modal__content">
              <v-container>
                <v-row>
                  <v-col cols="9"> </v-col>
                  <v-col cols="3">
                    <!-- <div>
                          <label class="mr-sm-2" for="inline-form-custom-select-pref">Language</label> -->
                    <b-form-select
                      v-show="parent.form.action === 'Update'"
                      id="inline-form-custom-select-pref"
                      v-model="parent.language"
                      :options="parent.languageOptions"
                    />
                    <!-- </div> -->
                  </v-col>
                </v-row>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <v-text-field
                        v-model="parent.form.profession"
                        :label="$t('label.job_profession')"
                        suffix="*"
                        class="modal__input"
                        autocomplete="off"
                      >
                      </v-text-field>
                      <small
                        style="
                          color: red;
                          margin-top: -15px;
                          position: absolute;
                        "
                        v-if="job_profession_required"
                      >
                        {{ $t("label.job_profession") }}
                        {{ $t("is_required") }}</small
                      >
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <label for="category">
                        {{ $t("label.job_category") }}
                        <strong class="text-danger">*</strong>
                      </label>
                      <v-select
                        name="category"
                        multiple
                        label="category_name"
                        v-model="parent.form.category"
                        :options="parent.categoryOption"
                      >
                        <template #list-header>
                          <li style="margin-left: 20px" class="mb-2">
                            <!-- @click="parent.modalAdd" -->
                            <b-link
                              href="javascript:;"
                              @click="parent.modalAdd"
                            >
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("label.new_category") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
                      <small
                        style="color: red; position: absolute"
                        v-if="job_category_required"
                      >
                        {{ $t("label.job_category") }}
                        {{ $t("is_required") }}</small
                      >
                      <!-- 
                      <small
                        id="job"
                        v-if="parent.form.errors.has('category')"
                        v-text="parent.form.errors.get('category')"
                        class="text-danger"
                      /> -->
                    </v-col>
                  </v-row>
                </v-container>
              </v-container>
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
              <v-btn color="success" tile type="submit">
                <div v-if="parent.btnloading" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>
                <div v-else>
                  <span v-if="parent.form.action == 'Add'">
                    {{ $t("button.save") }}
                  </span>
                  <span v-else>
                    {{ $t("button.save_change") }}
                  </span>
                </div>
              </v-btn>
            </v-card-actions>
          </form>
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
      job_profession_required: false,
      job_category_required: false,
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
    onSave() {
      this.job_profession_required = false;
      this.job_category_required = false;
      if (this.parent.form.profession == "") {
        this.job_profession_required = true;
        return false;
      }
      if (this.parent.form.category.length === 0) {
        this.job_category_required = true;
        return false;
      }
      this.parent.btnloading = true;
      let categoryId = [];

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        profession: this.parent.form.profession,
        category: this.parent.setObjectToArray(this.parent.form.category),
        id: this.parent.form.id,
        locale: this.parent.language,
        type: "profession",
      };

      this.post_job_profession(params)
        .then((resp) => {
          this.parent.btnloading = false;

          if (this.jobStatus) {
            this.$emit("done-success");

            this.parent.language = "en";

            let message = {
              create:
                `${this.parent.form.profession}` +
                this.$t("created_message") +
                this.$t("label.profession"),
              update:
                this.$t("updated_message1") +
                this.$t("label.profession") +
                ` ID: ${this.parent.form.id}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              this.parent.form.action === "Add" ? "success" : "warning",
              this.parent.form.action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.action === "Add"
                ? message.create
                : message.update
            );

            // check when data is success save
            if (!this.parent.listAddShow) {
              this.parent.listAddShow = true;
              this.parent.form.profession = "";
              this.parent.listProfession();
              this.$bvModal.show("job-description-modal");
            }

            this.$bvModal.hide("job-profession-modal");
            // this.parent.loadItems();

            // if (!this.parent.listAddShow) {
            //   this.$bvModal.show("job-description-modal");
            // }
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
