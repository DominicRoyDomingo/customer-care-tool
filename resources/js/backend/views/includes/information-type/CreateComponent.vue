<template>
  <div class="create">
    <b-modal
      id="information-type-add-modal"
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
                    <v-text-field
                      v-model="form.information_type_name"
                      :rules="[(v) => !!v || $t('label.name_required')]"
                      :label="$t('label.name')"
                      suffix="*"
                      class="modal__input"
                      autocomplete="off"
                      required
                    >
                    </v-text-field>
                    <span @click.stop>
                      <v-autocomplete
                        v-model="form.information_type_data"
                        :rules="[(v) => !!v || $t('label.type_of_data_required')]"
                        :label="$t('label.type_of_data')"
                        :items="items"
                        ref="select"
                      ></v-autocomplete>
                    </span>
                    
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
                  @click.stop="$this.informationTypePage"
                >
                  <v-icon>
                    mdi-open-in-new
                  </v-icon>
                  {{ $t("label.go_to_information_type_page") }}
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
      </v-app>
    </b-modal>
  </div>
</template>

<script>

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.createInformationType,

      submitted: false,

      keep_open: false,

      items: ['Long Text', 'Short Text', 'Date', 'Email Format', 'Numeric'],

      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
    };
  },

  created() {              
    window.addEventListener("click",() => {
      if(this.$refs.select != undefined) this.$refs.select.blur();
    }); 
  },

  methods: {
    ...mapActions("information_type", [
      "post_information_type",
      "add_information_type"
    ]),

    modalClose(done) {

      this.$this.$bvModal.hide("information-type-add-modal");

      this.$this.modal.add != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();
      this.form.information_type_name = "";
      this.keep_open = false;
    },

    capitalizeFirstLetter(str, lower = false) {
      return (lower ? str.toLowerCase() : str).replace(/(?:^|\s|["'([{])+\S/g, match => match.toUpperCase());
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();
      let informationTypeName = this.form.information_type_name == undefined ? "" : this.capitalizeFirstLetter(this.form.information_type_name)
      let informationTypeData = this.form.information_type_data == null ? "" : this.form.information_type_data
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("information_type_name", informationTypeName);
      formData.append("information_type_data", informationTypeData);

      this.post_information_type(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("information-type-add-modal");

          if (this.$this.informationTypesResponseStatus) {
            this.add_information_type(this.$this.informationTypesResponseStatus);

            let message = {
              create:
                `${this.form.information_type_name}` +
                this.$t("created_message") +
                this.$t("successfull_information_type_list"),
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
            this.form.information_type_name = "";
            this.form.information_type_data = null;
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
