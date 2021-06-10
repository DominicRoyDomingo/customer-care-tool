<template>
  <b-modal
    id="specialization-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <div>
          <span v-if="parent.form.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.specialization") }}
          </span>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("button.update") }} "{{ parent.itemSelected.base_name }}"
          </span>
        </div>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="on_cancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <v-row class="p-1">
          <v-col sm="12" md="12" cols="12">
            <form
              class="form"
              @submit.prevent="on_submit_form"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <div class="form-group" v-show="parent.form.action === 'Update'">
                <div class="form-inline">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    Language
                  </label>
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
                  <small>({{ $t("label.required") }})</small>
                </label>
                <v-select
                  id="profession"
                  name="profession"
                  label="base_name"
                  @change="parent.form.errors.clear('profession')"
                  v-model="parent.form.profession"
                  :options="professionOption"
                >
                  <template #list-header>
                    <li style="margin-left: 20px" class="mb-2">
                      <b-link
                        v-b-tooltip.hover
                        :title="`${$t('label.new_profession')}`"
                        href="javascript:;"
                        @click="on_add_profession"
                      >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("label.new_profession") }}
                      </b-link>
                    </li>
                  </template>

                  <template #option="{ on_select_profession_name }">
                    <span v-html="on_select_profession_name" />
                  </template>
                </v-select>
                <small
                  id="job"
                  v-if="
                    !parent.form.profession &&
                    parent.form.errors.has('profession')
                  "
                  v-text="parent.form.errors.get('profession')"
                  class="text-danger"
                />
              </div>

              <div class="form-group">
                <v-text-field
                  v-model="parent.form.description"
                  :label="$t('label.specilazation')"
                  type="text"
                  name="description"
                  hide-details="auto"
                />
                <small
                  v-if="
                    !parent.form.description &&
                    parent.form.errors.has('description')
                  "
                  v-text="parent.form.errors.get('description')"
                  class="text-danger"
                />
              </div>
              <v-card-actions class="float-right mb-0">
                <v-btn color="error" text tile @click="on_cancel">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                >
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
          </v-col>
        </v-row>

        <jobProfession :parent="this" @done-success="load_job_profession" />
        <jobCategory :parent="this" @done-success="load_job_category" />
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import jobProfession from "./../../jobs/modals/job-profession.modal";
import jobCategory from "./../../jobs/modals/job-category.modal";
import jobMixin from "./../../jobs/mixins/jobMixins";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  components: {
    jobProfession,
    jobCategory,
  },

  mixins: [jobMixin],

  data() {
    return {
      professionOption: [],
    };
  },

  computed: {
    ...mapGetters({
      jobStatus: "jobs/get_job_status",
    }),
  },

  mounted() {
    this.load_job_profession();
  },

  methods: {
    ...mapActions("jobs", ["post_job_description"]),

    on_submit_form() {
      this.parent.btnloading = true;
      let action = this.parent.form.action;

      let params = {
        api_token: this.$user.api_token,
        action: action,
        profession: this.parent.form.profession.id,
        description: this.parent.form.description,
        id: this.parent.form.id,
        locale: this.parent.language,
        type: "description",
      };

      this.post_job_description(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data, action);

          this.$bvModal.hide("job-description-modal");
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);

          if (this.parent.form.description) {
            this.parent.errorToast(
              `${this.$t("errors.duplicate")}!`,
              this.parent.form.errors.get("description")
            );
          }
          if (this.parent.form.profession) {
            this.parent.errorToast(
              `${this.$t("errors.duplicate")}!`,
              this.parent.form.errors.get("profession")
            );
          }

          this.parent.btnloading = false;
        });
    },

    on_cancel() {
      this.$emit("on-close");
      // this.parent.on_reset();
      this.$bvModal.hide("specialization-modal");
    },

    modalAdd() {
      this.form.reset();
      this.form.action = "Add";
      this.listAddShow = false;

      // open category modal
      this.$bvModal.show("job-category-modal");
    },

    on_add_profession() {
      this.form.action = "Add";
      this.$bvModal.show("job-profession-modal");
      this.listcaterories();
    },

    load_job_profession() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        // filterHealth: "health",
        type: "profession",
        brand_id: this.$brand.id,
      };
      axios.get("/api/jobs", { params }).then((resp) => {
        this.professionOption = resp.data;
        this.form.reset();
      });
    },

    load_job_category() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        type: "category",
        brand_id: this.$brand.id,
      };
      axios.get("/api/jobs", { params }).then((resp) => {
        this.categoryOption = resp.data;
        this.form.reset();
        this.form.action = "Add";
      });
    },
  },
};
</script>

 