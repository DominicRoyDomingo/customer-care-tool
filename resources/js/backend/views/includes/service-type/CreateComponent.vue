<template>
  <div class="create">
    <b-modal
      id="service-type-add-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
      <v-app id="service-type__container">
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <span>
              {{ $t(modal.name) }}
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
                    <!-- <label for="name">
                      {{ $t("label.name") }}
                    </label>
                    
                    <input
                      id="name"
                      type="text"
                      name="name"
                      v-model="form.service_type_name"
                      class="form-control"
                      :placeholder="$t('label.name')"
                      autocomplete="off"
                      aria-describedby="name"
                    /> -->
                    <v-text-field
                      v-model="form.service_type_name"
                      :rules="[(v) => !!v || $t('label.name_required')]"
                      :label="$t('label.name')"
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
                    <label for="image">
                      {{ $t("brands_image") }}
                    </label>

                    <b-form-file
                      accept=".jpg, .png"
                      v-model="$this.files"
                      :state="Boolean($this.files)"
                      multiple 
                      placeholder="Image/s"
                      drop-placeholder="Drop image/s here..."
                    ></b-form-file>
                  </v-col>
                </v-row>
              </v-container>
            </v-container>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
            <v-card-actions class="modal__footer blue-grey lighten-5">
              <div v-if="this.modal.id != undefined">
                <v-btn
                  color="error"
                  text
                  tile
                  link
                  @click.stop="$this.serviceTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_service_type_page") }}
                </v-btn>
              </div>
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
          <!-- <br />
          <div class="p-2 margin-top">
            <form
              @submit.prevent="onSubmit"
              method="post"
              enctype="multipart/form-data"
              @keydown="form.errors.clear($event.target.name)"
            >
              <b-form-group>
                <label for="name">
                  {{ $t("label.name") }}
                </label>
                
                <input
                  id="service_type_name"
                  type="text"
                  name="name"
                  v-model="form.service_type_name"
                  class="form-control"
                  :placeholder="$t('label.name')"
                  autocomplete="off"
                  aria-describedby="name"
                />
              </b-form-group>

              <b-form-group>
                <label for="image">
                  {{ $t("brands_image") }}
                </label>

                <b-form-file
                  accept=".jpg, .png"
                  v-model="$this.files"
                  :state="Boolean($this.files)"
                  multiple 
                  placeholder="Image/s"
                  drop-placeholder="Drop image/s here..."
                ></b-form-file>
              </b-form-group>

              <b-form-group>
                <span class="float-left">
                  <div v-if="this.modal.id != undefined">
                    <b-link href="#" @click.stop="$this.serviceTypePage">Go To Service Type Page</b-link>
                  </div>
                </span>

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
                    >{{ $t(modal.button.save) }}</el-button
                  >
                </span>
              </b-form-group>
            </form>
          </div> -->
      </v-app>
    </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],
  
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modalServiceType.createService,

      submitted: false,

      keep_open: false,

      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
    };
  },

  methods: {
    ...mapActions("service_type", [
      "post_service_type",
      "add_service_type",
      "update_brand",
    ]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    modalClose(done) {
      $(".error-provider").remove();

      $("#service_type_name").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("service-type-add-modal");

      this.$this.modalServiceType.add != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();
        
      this.keep_open = false;
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();
      for( var i = 0; i < this.$this.files.length; i++ ){
        let file = this.$this.files[i];

        formData.append('images[' + i + ']', file);
        
      }
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("service_type_name", this.form.service_type_name);

      this.post_service_type(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("service-type-add-modal");

          if (this.$this.responseStatus) {
            console.log(this.$this.responseStatus);
            this.add_service_type(this.$this.responseStatus);

            let message = {
              create:
                `${this.form.service_type_name}` +
                this.$t("created_message") +
                this.$t("successfull_service_type"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );
            this.modal.button.loading = false;
            this.$emit("loadTable");
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(value[0].indexOf('is an existing') !== -1) this.$this.makeToast("danger", vm.$t('errors.error'), value);
      }
    },
  },
};
</script>

<style></style>
