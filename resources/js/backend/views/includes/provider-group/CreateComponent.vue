<template>
  <div class="create">
    <b-modal
      id="provider-group-add-modal"
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
                {{$t(modal.name)}}
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
                      <v-text-field
                        v-model="form.provider_group_name"
                        :rules="[(v) => !!v || $t('label.name_required')]"
                        :label="$t('label.name')"
                        suffix="*"
                        class="modal__input"
                        autocomplete="off"
                        required
                      >
                      </v-text-field>
                      <v-file-input
                        v-model="image"
                        accept="image/png, image/jpeg, image/bmp"
                        prepend-icon="mdi-camera"
                        :label="$t('brands_image')"
                      ></v-file-input>
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
                  {{ $t("cancel") }}
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
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>
<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this", "parent"],

  data: function() {
    return {
      modal: this.$this.modal.createProviderGroup,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      valid: true,

      formData: this.$this.formData,

      country_id: "",

      image: undefined,

      formGroups: [],
    };
  },

  methods: {
    ...mapActions("provider_group", ["post_provider_group", "add_provider_group"]),
    
    modalClose(done) {
      $(".error-provider").remove();

      $("#name, #provider_type").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("provider-group-add-modal");

      this.$this.modal.add != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();
      let image = ""
      if(this.image != undefined) image = this.image
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("provider_group_name", this.form.provider_group_name);
      formData.append("image", image);
      this.post_provider_group(formData)
        .then((resp) => {
          
          this.$bvModal.hide("provider-group-add-modal");

          // if (this.$this.providersResponseStatus) {

            let message = {
              create:
                `${this.form.provider_group_name}` +
                this.$t("created_message") +
                this.$t("successfull_provider"),
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
          // }
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
