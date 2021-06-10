<template>
  <div class="create">
      <b-modal
        id="reasons-add-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t(modal.name)"
      >
        <v-app id="create__container">
          <v-card>
            <form ref="form" v-model="valid" @submit.prevent="onSubmit" lazy-validation>
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
                         <b-form-group style="margin-bottom:1%">
                          <v-text-field
                            v-model="form.reason_name"
                            :label="$t('label.reasons')"
                            suffix="*"
                            ref="myField"
                            class="modal__input"
                            autocomplete="off"
                          >
                          </v-text-field>
                          <small style="color:red; margin-top:-15px;position:absolute" v-if="reason_name_required">{{ $t('label.reasons') + $t('is_required') }}</small>
                         </b-form-group>
                        <v-text-field
                          v-model="form.abbreviation"
                          :label="$t('label.abbreviation')"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <v-textarea
                          v-model="form.description"
                          class="mx-2"
                          label="Description"
                        ></v-textarea>
                        <label for="name">
                          {{ $t("label.request_type") }}
                           <strong class="text-danger">*</strong>
                        </label>
                        <v-select
                          id="request_type"
                          name="request_type"
                          label="name"
                          v-model="form.request_type_id"
                          :options="$this.request_type"
                          :reduce="(request_type) => request_type.id"
                          chips
                          outlined
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link href="#"  @click="$this.openRequestTypeModal">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("request_type_add_new_button") }}
                              </b-link>
                            </li>
                          </template>
                          <template v-slot:option="option">
                              {{ option.name }}
                              <small
                                v-if="option.convertion == true"
                                style="color:red"
                              >
                                (No {{ option.language }} translation yet)</small
                              >
                            </template>
                        </v-select>
                        <small style="color:red" v-if="request_type_required">{{ $t('label.request_type') + $t('is_required') }}</small>
                        <br>
                        <label for="name">
                          {{ $t("brands_name") }}
                        </label>
                        <v-select
                          id="brands_name"
                          name="brands_name"
                          label="name"
                          v-model="form.brand"
                          :options="$this.brands"
                          chips
                          :reduce="(brand) => brand.id"
                          multiple
                          outlined
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link href="#" @click="$this.openBrandModal">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("label.new_brand") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
                        <small style="color:red" v-if="brand_name_required">{{ $t('brands_name') + $t('is_required') }}</small>
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
                    type="submit"
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
            </form>
          </v-card>
          
        </v-app>
        
      </b-modal>
    
  </div>
  
</template>

<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],

  props: ["$this"],


  data: function() {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      valid: true,

      modalId: "reasons-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      reason_name_required: false,

      request_type_required: false,

      brand_name_required: false

    };
  },
  
  methods: {
    ...mapActions("reasons", ["post_reasons", "add_reasons", "update_reasons"]),
    
    focusInput() {
      this.$refs.myField.focus();
    },

    modalClose(done) {

      $(".error-provider").remove();

      this.$this.$bvModal.hide("reasons-add-modal");

      // this.$this.modal.createRequestType != undefined
      //   ? this.$this.$bvModal.show(this.$this.modalId)
      //   : this.form.reset();

      this.form.reset();

      this.keep_open = false;
    },

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");

      if (this.form.actual_action == "Add") {
        this.modal.addItem.isVisible = true;
      } else {
        this.modal.edit.isVisible = true;
      }
    },

    closeRequestTypeModal() {
      this.$bvModal.hide("request-type-add-modal");

      if (this.form.actual_action == "Add") {
        this.modal.addItem.isVisible = true;
      } else {
        this.modal.edit.isVisible = true;
      }
    },

    onSubmit() {
      this.request_type_required = false;
      this.reason_name_required = false;
      this.brand_name_required = false;
      
      if( this.form.reason_name == '' ) {
        this.focusInput();
        this.reason_name_required = true;
        return false;
      }
      if( this.form.request_type_id == '' || this.form.request_type_id == null ) {
        this.request_type_required = true;
        return false;
      }
      if( this.form.brand.length == '' ) {
        this.brand_name_required = true;
        return false;
      }

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.reason_name);
      formData.append("abbreviation", this.form.abbreviation);
      formData.append("description", this.form.description);
      formData.append("request_type", this.form.request_type_id);
      formData.append("org_id", this.$org_id);
      formData.append("brand", JSON.stringify( this.form.brand ) );
      this.modal.button.loading = true;
      this.post_reasons(formData)
        .then((resp) => {
          
          this.modal.button.loading = false;
          if( resp.data.duplicate == true ) {
            this.$this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("reason_exist")
            );
            return false;
          }
          
          this.$bvModal.hide("reasons-add-modal");

            let message = {
              create:
                `${this.form.reason_name}` +
                this.$t("created_message") +
                this.$t("label.reasons"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.form.reset();
            this.$emit("loadTable");
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          $(".error-provider").remove();

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
  },

  computed: {
    ...mapGetters("reasons", {
      reasons: "reasons",
      responseStatus: "get_response_status",
    }),
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
