<template>
 
 
      <b-modal
        id="parameter-add-modal"
        hide-footer
        hide-header
        size="md"
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
                      <v-text-field
                        v-model="form.parameter_name"
                        :rules="[(v) => !!v || $t('label.name_required')]"
                        :label="$t('label.name')"
                        suffix="*"
                        class="modal__input"
                        autocomplete="off"
                        required
                      >
                      </v-text-field>
                      <!-- <label for="name">
                        {{ $t("label.name") }}
                      </label>
                      
                      <input
                        id="name"
                        type="text"
                        name="name"
                        v-model="form.information_type_name"
                        class="form-control"
                        :placeholder="$t('label.name')"
                        autocomplete="off"
                        aria-describedby="name"
                      /> -->
                    </v-col>
                  </v-row>
                </v-container>
              </v-container>
            </v-card-text>
            <v-divider style="margin-bottom: 0"></v-divider>
              <v-card-actions class="modal__footer blue-grey lighten-5">
                <!-- <div v-if="this.modal.id != undefined">
                  <v-btn
                    color="error"
                    text
                    tile
                    link
                    @click.stop="$this.informationTypePage"
                  >
                    <v-icon>
                      mdi-open-in-new
                    </v-icon>
                    {{ $t("label.go_to_information_type_page") }}
                  </v-btn>
                </div> -->
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
       </v-app>
        <!-- <div>
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("label.name") }}
                </label>

                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="form.parameter_name"
                  class="form-control"
                  :placeholder="$t('label.name')"
                  autocomplete="off"
                  aria-describedby="name"
                />
              </v-container>
    
            </b-form-group>

            <b-form-group>
              <v-container>
                <span class="float-left">
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
                  >
                    {{ $t(modal.button.save) }}
                  </el-button>
                </span>
              </v-container>
            </b-form-group>
          </form>
        </div> -->
      </b-modal>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.parameter,

      submitted: false,

      keep_open: false,

      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      modalId: "parameter-add-modal",

      formGroups: [],
    };
  },

  methods: {
    ...mapActions("parameter", ["post_parameter", "add_parameter"]),
    
    modalClose(done) {
      $(".error-provider").remove();

      $("#name, #provider_type").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("parameter-add-modal");

      this.$this.modal.createProviderServices != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.parameter_name);
      this.post_parameter(formData)
        .then((resp) => {
          
          this.$bvModal.hide("parameter-add-modal");

          if (this.$this.parametersResponseStatus) {

            let message = {
              create:
                `${this.form.parameter_name}` +
                this.$t("created_message") +
                this.$t("successfull_parameter"),
              title: {
                create: this.$t("new_record_created"),
              },
            };
            console.log(this.form.parameter_name)
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
          console.log(error)
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          $(".error-parameter").remove();

          $("#name").removeAttr(
            "style"
          );
          for (let field of Object.keys(errors)) {
            
            let el = $(`#${field}`);
            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error-parameter">' +
                  errors[field][0] +
                  "</span>"
              )
            );
          }
        });
    },
 
  },
};
</script>

<style></style>
