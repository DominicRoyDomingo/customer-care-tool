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
        
    },
    onSave() {
      this.$refs.form.validate();
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        category: this.parent.form.category,
        locale: this.$user.locale,
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
            console.log(this.parent.modalId)
            this.parent.$bvModal.show(this.parent.modalId);
          }
        })
        .catch(error => {
          this.parent.btnloading = false;
          let errors = error.response.data.errors;
        });
    },
  }
};
</script>
