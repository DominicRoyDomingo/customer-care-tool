<template>
  <div>
    <b-modal
      id="job-description-modal"
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
                <small
                      v-if="parent.form.convertion == true"
                      style="color:red"
                    >
                      (No {{ parent.form.language }} translation yet)</small
                    >
              </span>
              <span v-else>
                {{$t('button.new')}} {{ $t("label.specilazation") }}
              </span>
              <v-spacer></v-spacer>
              <v-btn
                icon
                color="error lighten-2"
                @click="$bvModal.hide('job-description-modal')"
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
                        <b-form-group>
                          <label for="category">
                            {{ $t("label.job_profession") }}
                            <strong class="text-danger">*</strong>
                          </label>
                          <v-select
                            id="profession"
                            name="profession"
                            label="profession_name"
                            @change="parent.form.errors.clear('profession')"
                            v-model="parent.form.profession"
                            :options="parent.professionOption"
                          >
                            <template #list-header>
                              <li style="margin-left: 20px" class="mb-2">
                                <b-link href="#" @click="parent.modalAdd">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("label.new_profession") }}
                                </b-link>
                              </li>
                            </template>
                          </v-select>
                          <small style="color:red; position:absolute" v-if="job_profession_required"> {{ $t("label.job_profession") }} {{ $t('is_required')}}</small>
                        </b-form-group>
                      </v-col>
                     </v-row>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                        <v-text-field
                          v-model="parent.form.description"
                          :label="$t('label.specilazation')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top: -15px; position:absolute" v-if="job_description_required"> {{ $t("label.specilazation") }} {{ $t('is_required')}}</small>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-card-actions class="modal__footer blue-grey lighten-5">
                <v-spacer></v-spacer>
                <v-btn color="error" text tile @click="$bvModal.hide('job-description-modal')">
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
                    <div>
                      {{ parent.form.action == "Add" ? $t('button.save') : $t('button.save_change') }}
                    </div>
                  </div>
                </v-btn>
              </v-card-actions>
            <!-- <div class="form-group mb-4" v-show="parent.form.action === 'Update'">
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
                :options="parent.professionOption"
              >
                <template #list-header v-if="parent.form.action === 'Add'">
                  <li style="margin-left: 20px" class="mb-2">
                    <b-link href="#" @click="parent.modalAdd">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ $t("label.new_profession") }}
                    </b-link>
                  </li>
                </template>
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
                    {{
                      parent.form.action == "Add"
                        ? $t("button.save")
                        : $t("button.save_change")
                    }}
                  </span>
                  <b-spinner v-else small label="Small Spinner"></b-spinner>
                </b-button>
                <b-button
                  variant="secondary"
                  size="sm"
                  @click="$bvModal.hide('job-description-modal')"
                  >{{ $t("cancel") }}</b-button
                >
              </span>
            </div> -->
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
      job_description_required: false
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
      
      this.job_profession_required = false;
      this.job_description_required = false;
      if( this.parent.form.profession == '' ) {
        this.job_profession_required = true;
        return false;
      }
      if( this.parent.form.description == '' ) {
         this.job_description_required = true;
         return false;
       }
      this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        profession: this.parent.form.profession.id,
        description: this.parent.form.description,
        id: this.parent.form.id,
        locale: this.parent.language,
        type: "description",
      };
      this.post_job_description(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("job-description-modal");

          if (this.jobStatus) {
            this.parent.loadItems();
            this.parent.language = "en";

            let message = {
              create:
                `${this.parent.form.description}` +
                this.$t("created_message") +
                this.$t("label.specializations"),
              update:
                this.$t("updated_message1") +
                this.$t("label.specializations") +
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
          }
        })
        .catch((error) => {
          this.parent.makeToast(
            "danger",
            "DUPLICATE",
            error.response.data.errors.description[0]
          );
          return false;
          // this.parent.form.errors.record(error.response.data.errors);
          // this.parent.btnloading = false;
        });
    },
  },
};
</script>
