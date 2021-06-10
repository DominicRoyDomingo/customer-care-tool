<template>
  <div class="create">
      <b-modal
        id="service-edit-modal"
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
                  {{$this.translations[$this.selectedLanguage].title}}
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
                    <v-col cols="3" md="3" class="modal__input-container ml-auto">
              
                      <select class="form-control" @change.prevent="selectLang($event)">
                          
                          <option
                          :value="language.id"
                          :selected="language.id === form.language"
                          v-for="(language, langInd) in langs"
                          :key="langInd"
                          >
                          {{language.value}}
                          </option>

                      </select>
                    </v-col>
                  </v-row>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                        <v-text-field
                          v-model="form.name"
                          :rules="[(v) => !!v || $this.translations[$this.selectedLanguage].name_is_required]"
                          :label="$this.translations[$this.selectedLanguage].name + ` ${$this.nameTranslation != '' ? $this.nameTranslation : ''}`"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                        >
                        </v-text-field>
                          <label for="name">
                            {{ $this.translations[$this.selectedLanguage].service_type_category }}
                          </label>

                          <v-select
                            name="service_type"
                            label="name"
                            v-model="form.service_type"
                            :options="$this.service_types"
                            id="service_type"
                          >
                            <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="openServiceTypeModal">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $this.translations[$this.selectedLanguage].new_service_type }}
                                </b-link>
                              </li>
                            </template>
                          </v-select>
                      </v-col>
                    </v-row>
                  </v-container>
                  <v-card color="gray lighten-3" outlined tile>
                    <v-tabs
                      v-model="tabIndex"
                      show-arrows
                      center-active
                      grow
                      background-color="blue-grey lighten-5"
                      slider-color="blue-grey darken-2"
                      color="blue-grey darken-2"
                    >
                      <v-tab class="caption font-weight-bold">
                        {{ $this.translations[$this.selectedLanguage].description }}
                      </v-tab>
                      <v-tab class="caption font-weight-bold">
                        {{ $this.translations[$this.selectedLanguage].image }}
                      </v-tab>
                      <v-tab-item eager>
                        <v-container>
                          <label for="description">
                            {{ $this.translations[$this.selectedLanguage].description }}
                          </label>
                          
                          <input
                            id="description"
                            type="text"
                            name="description"
                            v-model="form.description"
                            class="form-control"
                            :placeholder="$this.translations[$this.selectedLanguage].image"
                            autocomplete="off"
                            aria-describedby="description"
                          />
                        </v-container>
                      </v-tab-item>
                      <v-tab-item eager>
                        <v-container>
                          <label for="image">
                            {{ $this.translations[$this.selectedLanguage].image }}
                          </label>

                          <b-form-file
                            accept=".jpg, .png"
                            v-model="$this.files"
                            :state="Boolean($this.files)"
                            multiple 
                            placeholder="Image/s"
                            drop-placeholder="Drop image/s here..."
                          ></b-form-file>
                        </v-container>
                      </v-tab-item>
                    </v-tabs>
                  </v-card>
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
                    {{ $this.translations[$this.selectedLanguage].cancel }}
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
                        {{ $this.translations[$this.selectedLanguage].button }}
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

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      submitted: false,

      keep_open: false,

      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      currentLanguage: this.$i18n.locale,

      tabIndex: 0,

      formGroups: [],

      files: [],

      modalServiceType: {
        add: {

        },
        
        createService: {
          name: "service_type_add_new_button",

          id: "service-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_type_add_new_button",

            loading: false,
          },
        },
      },

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },

        { id: "ph-fil", value: "Filipino" },

        { id: "ph-bis", value: "Visayan" },
      ],
    };
  },

  methods: {
    ...mapActions("services", [
      "post_service",
      "update_service",
      "get_service_name",
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

      this.$this.$bvModal.hide("service-edit-modal");

      this.form.reset();

      this.$this.currentLanguage = this.$i18n.locale;

      this.keep_open = false;
    },

    onSubmit() {
      let formData = new FormData();

      let serviceType = "";

      for( var i = 0; i < this.$this.files.length; i++ ){
        let file = this.$this.files[i];

        formData.append('images[' + i + ']', file);
        
      }

      if (this.form.service_type != null)
        serviceType = this.form.service_type.id;

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("description", this.form.description);
      formData.append("service_type", serviceType);
      formData.append("brand_id", this.$brand.id);
      formData.append("locale", this.$i18n.locale);
      formData.append("name", this.$this.capitalizeFirstLetter(this.form.name));

      this.post_service(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("service-edit-modal");
          if (this.$this.servicesResponseStatus) {
            let response = this.$this.servicesResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_service(response);

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
          console.log(error)
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    openServiceTypeModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("service-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(key == "service_type" || value[0].indexOf('is an existing') !== -1) this.$this.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    selectLang(event) {
      // this.form.language = event.target.value;
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", event.target.value);
      this.$this.currentLanguage = event.target.value;
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
