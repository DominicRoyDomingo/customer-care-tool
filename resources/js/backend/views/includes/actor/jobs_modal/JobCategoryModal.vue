<template>
  <div>
      <b-modal
      id="job-category-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card>
          <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('button.new_specialization_category')}}
                </span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="modalClose"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content">
                <v-container>
                  <v-row>
                    <v-col cols="9">
                    </v-col>
                    <v-col cols="3">
                    </v-col>
                  </v-row>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                        <v-text-field
                          v-model="parent.form.category"
                          :rules="[(v) => !!v || $t('label.specialization_category_required')]"
                          :label="$t('label.specialization_category')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                        >
                        </v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-card-actions class="modal__footer blue-grey lighten-5">
                <v-spacer></v-spacer>
                <v-btn color="error" text tile @click="modalClose">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn color="success" tile @click="onSave">
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
                    <div>
                      {{ $t('button.save') }}
                    </div>
                  </div>
                </v-btn>
              </v-card-actions>
          </v-form>
          <!-- <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <div class="form-group mb-4" v-show="parent.form.action === 'Update'">
              <div class="form-inline">
                <label class="mr-sm-2" for="inline-form-custom-select-pref">Language</label>
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
                {{$t('label.job_category')}}
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
            </div>

            <div class="form-group">
              <span class="float-right">
                <b-button
                  type="submit"
                  :variant="parent.form.action == 'Add' ? 'primary' : 'success'"
                  :disabled="parent.btnloading"
                  class="mb-2 mt-2 text-capitalize mt-1"
                >
                  <span v-if="!parent.btnloading">
                    <i class="fas fa-save"></i>
                    {{ parent.form.action == "Add" ? $t('button.save') : $t('button.save_change') }}
                  </span>
                  <b-spinner v-else small label="Small Spinner"></b-spinner>
                </b-button>
                <b-button
                  variant="secondary"
                  size="sm"
                  @click="$bvModal.hide('job-category-modal')"
                >{{$t('cancel')}}</b-button>
              </span>
            </div>
          </form> -->
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

      valid: true
    };
  },

  computed: {
    ...mapGetters({
      jobStatus: "jobs/get_job_status"
    })
  },

  methods: {
    ...mapActions("jobs", ["post_job_category"]),
    modalClose(done) {

        this.parent.$bvModal.hide("job-category-modal");
      
        this.parent.$bvModal.show(this.parent.modalId)

        this.parent.form.category = ""
        
    },
    onSave() {
      this.$refs.form.validate();
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        category: this.parent.form.category,
        locale: this.$user.locale,
        brand_id: this.$brand.id,
        type: "category"
      };
      this.post_job_category(params)
        .then(resp => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-category-modal");
          if (this.jobStatus) {

            let message = {
              create: `${this.parent.form.category}` + this.$t( 'created_message' ) + this.$t('label.category'),
              title: {
                create: this.$t( 'new_record_created' ),
              }
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.parent.form.category = "";
            this.parent.loadSpecializationCategories();
            this.parent.$bvModal.show(this.parent.modalId);
          }
        })
        .catch(error => {
          this.parent.btnloading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(key == "profession" || value[0].indexOf('is an existing') !== -1) this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    }
  }
};
</script>
