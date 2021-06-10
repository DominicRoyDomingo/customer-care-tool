<template>
  <div class="create">
    <b-modal
      id="physical-code-type-add-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
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
                      v-model="form.physical_code_type_name"
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
                      v-model="form.physical_code_type_name"
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
              <div v-if="this.modal.id != undefined">
                <v-btn
                  color="error"
                  text
                  tile
                  link
                  @click.stop="$this.physicalCodeTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_physical_code_type_page") }}
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
        <!-- <v-container>
          <div class="p-2">
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
                  id="name"
                  type="text"
                  name="name"
                  v-model="form.physical_code_type_name"
                  class="form-control"
                  :placeholder="$t('label.name')"
                  autocomplete="off"
                  aria-describedby="name"
                />
              </b-form-group>

              <b-form-group>
                <span class="float-left">
                  <div v-if="this.modal.id != undefined">
                    <b-link href="#" @click.stop="$this.physicalCodeTypePage">Go To Physical Code List</b-link>
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
                  >
                    {{ $t(modal.button.save) }}
                  </el-button>
                </span>
              </b-form-group>
            </form>
          </div>
        </v-container> -->
      </v-app>
    </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.createPhysicalCodeType,

      form: this.$this.form,

      valid: true,

      formData: this.$this.formData,
    };
  },

  methods: {
    ...mapActions("physical_code_type", [
      "post_physical_code_type",
      "add_physical_code_type"
    ]),

    modalClose(done) {

      this.$this.$bvModal.hide("physical-code-type-add-modal");

      this.$this.modal.add != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();

      this.form.physical_code_type_name = "";
      console.log('test')
    },

    capitalizeFirstLetter(str, lower = false) {
      return (lower ? str.toLowerCase() : str).replace(/(?:^|\s|["'([{])+\S/g, match => match.toUpperCase());
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();
      let physicalCodeTypeName = this.form.physical_code_type_name == undefined ? "" : this.capitalizeFirstLetter(this.form.physical_code_type_name)
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("physical_code_type_name", physicalCodeTypeName);

      this.post_physical_code_type(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("physical-code-type-add-modal");
          this.form.physical_code_type_name = "";

          if (this.$this.physicalCodeTypesResponseStatus) {
            this.add_physical_code_type(this.$this.physicalCodeTypesResponseStatus);

            let message = {
              create:
                `${this.capitalizeFirstLetter(this.form.physical_code_type_name)}` +
                this.$t("created_message") +
                this.$t("successfull_physical_code_type_list"),
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
