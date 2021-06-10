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
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span>
                {{$t('button.new')}} {{ $t('label.profession') }}
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
                          v-model="parent.form.profession2"
                          :label="$t('label.job_profession')"
                          :rules="[(v) => !!v || $t('label.job_profession_required')]"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                        >
                        </v-text-field>
                      </v-col>
                    </v-row>
                     <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                        <label for="category">
                          {{$t('label.job_category')}}
                          <strong class="text-danger">*</strong>
                        </label>
                        <v-select
                          name="category"
                          multiple
                          label="category_name"
                          v-model="parent.form.category"
                          :options="parent.newSpecializationCategories"
                        >
                          <template #list-header>
                            <li style="margin-left: 20px" class="mb-2">
                              <b-link href="#" @click="openJobCategoryModal">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("label.new_category") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
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

      valid: true,
    };
  },

  computed: {
    ...mapGetters({
      jobStatus: "jobs/get_job_status"
    })
  },

  methods: {
    ...mapActions("jobs", ["post_job_category", "post_job_profession"]),

    modalClose(done) {

        this.parent.$bvModal.hide("job-profession-modal");

        this.parent.modalId = "job-description-modal"
        
        this.parent.$bvModal.show(this.parent.modalId)

        

    },

    openJobCategoryModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide("job-profession-modal");
      this.parent.modalId = "job-profession-modal"
      this.parent.$bvModal.show("job-category-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
    
    onSave() {
      this.$refs.form.validate();
      this.parent.btnloading = true;
      let categoryId = [];
      let categories = this.parent.form.category == undefined ? "" : this.parent.setObjectToArray(this.parent.form.category)

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        profession: this.parent.form.profession2,
        category: categories,
        locale: this.$i18n.locale,
        type: "profession"
      };

      this.post_job_profession(params)
        .then(resp => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-profession-modal");

          if (this.jobStatus) {

            let message = {
              create: `${this.parent.form.profession2}` + this.$t( 'created_message' ) + this.$t('label.profession'),
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
              this.parent.form.profession2 = "";
              this.parent.loadProfessions()
              this.$bvModal.show("job-description-modal");

            // if (!this.parent.listAddShow) {
            //   this.$bvModal.show("job-description-modal");
            // }
          }
        })
        .catch(error => {
          let errors = error.response.data.errors;
          this.parent.btnloading = false;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(key == "category" || key == "profession") this.parent.makeToast("danger", vm.$t('errors.error'), value);
        
      }
    }
  }
};
</script>
