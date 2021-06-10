<template>
  <div>
    <b-modal
      id="job-description-modal"
      hide-footer
      hide-header
      size="md"
    >
        <v-app id="service-type__container">
          <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <span>
              {{ $t('button.new_specialization') }}
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
              <v-container>
                <v-row>
                  <v-col cols="12" md="12" class="modal__input-container">
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
                      <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openJobProfessionModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("label.new_profession") }}
                            </b-link>
                        </li>
                      </template>
                    </v-select>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" md="12" class="modal__input-container">
                    <v-text-field
                      v-model="parent.form.description"
                      :rules="[(v) => !!v || $t('label.specialization_required')]"
                      :label="$t('label.specialization')"
                      suffix="*"
                      class="modal__input"
                      autocomplete="off"
                      required
                    >
                    </v-text-field>
                    <!-- <label for="description">
                      {{ $t("label.specialization") }}
                      <strong class="text-danger">*</strong>
                    </label>
                    <b-form-input
                      id="description"
                      v-model="parent.form.description"
                      :placeholder="$t('label.required')"
                      type="text"
                    /> -->
                  </v-col>
                </v-row>
              </v-container>
            </v-container>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <v-spacer></v-spacer>
              <v-btn
                color="error"
                text
                tile
                @click="modalClose"
              >
                {{ $t(modal.button.cancel) }}
              </v-btn>
              <v-btn
                color="success"
                tile
                @click="onSubmit"
              >
                <div v-if="this.modal.button.loading" class="text-center">
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
                    {{ $t(modal.button.save) }}
                  </div>
                </div>
              </v-btn>
            </v-card-actions>
        </v-form>
            <!-- <v-container>
                <div class="p-2">
                    <form
                    @submit.prevent="onSubmit"
                    method="post"
                    enctype="multipart/form-data"
                    >
                    <b-form-group>
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
                            <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="parent.modalAdd">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("label.new_profession") }}
                                </b-link>
                            </li>
                            </template>
                        </v-select>
                    </b-form-group>

                    <b-form-group>
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
                    </b-form-group>

                    <b-form-group>
                        <span class="float-right">
                        <el-button
                            size="small"
                            @click="modalClose"
                            type="danger"
                            plain
                        >
                            {{ $t(modal.button.cancel) }}
                        </el-button>

                        <el-button
                            size="small"
                            native-type="submit"
                            type="success"
                            :loading="modal.button.loading"
                            style="color: #fff !important"
                        >
                            {{ $t(modal.button.save) }}
                        </el-button>
                        </span>
                    </b-form-group>
                    </form>
                </div>
            </v-container> -->
        </v-app>
      <!-- <form
        class="form"
        @submit.prevent="onSave"
        @keyup="parent.form.errors.clear($event.target.name)"
      >
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
              <li style="margin-left:20px;" class="mb-2">
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
                {{ parent.form.action == "Add" ? $t("button.save") : $t("button.save_change") }}
              </span>
              <b-spinner v-else small label="Small Spinner"></b-spinner>
            </b-button>
            <b-button
              variant="secondary"
              size="sm"
              @click="$bvModal.hide('job-description-modal')"
            >{{ $t("cancel") }}</b-button>
          </span>
        </div>
      </form> -->
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
      modal: this.parent.modal.createJobDescription,
    };
  },

  computed: {
    ...mapGetters({
      jobStatus: "jobs/get_job_status"
    })
  },

  methods: {
    ...mapActions("jobs", ["post_job_description"]),

    modalClose(done) {
        // console.log('test')
        this.parent.$bvModal.hide("job-description-modal");
        this.parent.modalId = this.parent.actorModal
        this.parent.$bvModal.show(this.parent.actorModal)

        this.keep_open = false;
    },

    openJobProfessionModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide('job-description-modal');
      this.parent.modalId = "job-description-modal"
      this.parent.$bvModal.show('job-profession-modal')
      // this.$this.modal.itemType.isVisible = true;
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let params = {
        api_token: this.$user.api_token,
        profession: this.parent.form.profession.id,
        description: this.parent.form.description,
        locale: this.parent.form.language,
        type: "description"
      };
      this.post_job_description(params)
        .then(resp => {
            this.modal.button.loading = false;
            this.$bvModal.hide("job-description-modal");
            this.parent.removeDuplicateSpecializations()
            if (this.jobStatus) {

                let message = {
                create: `${this.parent.form.description}` + this.$t( 'created_message' ) + this.$t('table.specialization'),
                update: this.$t( 'updated_message1' ) + this.$t('label.description') + ` ID: ${this.parent.form.id}` + this.$t( 'updated_message2' ),
                title: {
                    create: this.$t( 'new_record_created' ),
                    update: this.$t( 'record_updated' )
                }
                };

                this.parent.makeToast(
                "success",
                message.title.create,
                message.create
                );
            }
            this.parent.removeDuplicateSpecializations()
            this.parent.modalId = this.parent.actorModal
            this.parent.$bvModal.show(this.parent.modalId);

        })
        .catch(error => {
          this.modal.button.loading = false;
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
