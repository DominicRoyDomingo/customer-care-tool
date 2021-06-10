<template>
  <div class="create">
    <v-app id="create__container">
      <b-modal
        id="service-exclusive-edit-modal"
        hide-footer
        size="md"
        no-close-on-backdrop
        :title="$t(modal.name)"
      >
        <div class="p-2 margin-top">
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <br />

            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("service_type_category") }}
                </label>

                <el-row>
                  <el-col :span="24">
                    <v-select
                      name="service"
                      label="name"
                      v-model="form.service"
                      :options="$this.serviceTypes"
                      id="services"
                    >
                      <!-- <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                          <b-link href="#" @click="openServiceTypeModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("service_type_add_new_button") }}
                          </b-link>
                        </li>
                      </template> -->
                    </v-select>
                  </el-col>
                </el-row>
              </v-container>

              <v-container>
                <label for="text_display">
                  {{ $t("label.text_display") }}
                </label>
                
                <input
                  id="text_display"
                  type="text"
                  name="text_display"
                  v-model="form.text_display"
                  class="form-control"
                  :placeholder="$t('label.text_display')"
                  autocomplete="off"
                  aria-describedby="text_display"
                />
              </v-container>

            </b-form-group>

            <b-form-group>
              <v-container>
                <!-- <span class="float-left">
                  <div class="form-check form-check-inline mr-1">
                    <input
                      class="form-check-input"
                      v-model="keep_open"
                      type="checkbox"
                      id="inline-checkbox3"
                    />

                    <label class="form-check-label" for="inline-checkbox3"
                      ><small>Keep the form open?</small></label
                    >
                  </div>
                </span> -->

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
                    {{ $t(modal.button.update) }}
                  </el-button>
                </span>
              </v-container>
            </b-form-group>
          </form>
        </div>
      </b-modal>
    </v-app>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },
      ],
    };
  },

  methods: {
    ...mapActions("services_exclusive", [
      "post_services_exclusive",
      "update_services_exclusive",
    ]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    
    modalClose(done) {

      $(".error-provider").remove();

      $("#name, #service_type").removeAttr(
        "style"
      );
      
      this.file = "";

      this.$this.$bvModal.hide("service-exclusive-edit-modal");

      this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
      let formData = new FormData();

      let service = "";

      if (this.form.service.id != undefined)
        service = this.form.service.id;
      formData.append("id", this.form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("text_display", this.form.text_display);
      formData.append("service", service);

      this.post_services_exclusive(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("service-exclusive-edit-modal");
          if (this.$this.servicesExclusivesResponseStatus) {
            let response = this.$this.servicesExclusivesResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_services_exclusive(response);

            let message = {
              update:
                this.$t("updated_message1") +
                this.$t("label.services") +
                ` ID: ${this.$this.form.id} ` +
                this.$t("updated_message2"),
              title: {
                update: this.$t("record_updated"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.update,
              message.update
            );

            this.$this.successfullySavedService();
          }
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#name, #service_type").removeAttr(
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
                '<span style="color: #ff3b3b;" class="error-provider">' +
                  errors[field][0] +
                  "</span>"
              )
            );
          }
        });
    },

    openServiceTypeModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("service-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    selectLang(event) {
      // this.form.language = event.target.value;
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", event.target.value);
      this.form.language = event.target.value;
      this.get_service_name(formData).then((resp) => {
        if (resp) {
          this.form.name = resp;
        } else {
          this.form.name = "";
        }
      });
    },
  },
};
</script>

<style></style>
