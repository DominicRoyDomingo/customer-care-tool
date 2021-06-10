<template>
  <div class="create">
      <b-modal
        id="calendar-notes-type-add-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t('calendar_notes_type_add_new_button')"
      >
        <v-app id="create__container">
          <v-card>
            <form ref="form" v-model="valid" @submit.prevent="onSubmit" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('calendar_notes_type_add_new_button')}}
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
                          v-model="form.name"
                          :label="$t('label.name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="name_required"> {{ $t("label.name") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                        <div class="div_background" @click="chooseColor" v-bind:style="{ backgroundColor: color_background }"></div>
                      <b-form-group>
                        <v-text-field
                          v-model="form.color_hexcode"
                          :label="$t('label.hexcode')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          @click="chooseColor"
                          style="width: 65%"
                        >
                        </v-text-field>
                        <small style="color:red" v-if="color_hexcode_required"> {{ $t("label.hexcode") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                          <!-- <b-form-group>
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
                        </b-form-group> -->
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

      <b-modal
        id="create-select-color-calendar-modal"
        hide-footer
        hide-header
        size="sm"
        no-close-on-backdrop
      >
        <v-app id="create__container">
          <v-card>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  Choose Color
                </span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="modalSelectColorCancel"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content">
                <v-container>
                    <v-color-picker
                      dot-size="11"
                      hide-inputs
                      hide-mode-switch
                      mode="hexa"
                      swatches-max-height="157"
                      v-model="color"
                    ></v-color-picker>  
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <!-- <a :href="`calendar-notes-type`" class="view-type-notes" style="float:left">{{ $t("view_calendar_notes_type_link") }}</a> -->
                  <v-spacer></v-spacer>
                  <v-btn
                    color="error"
                    text
                    tile
                    @click="modalSelectColorCancel"
                  >
                    {{ $t("cancel") }}
                  </v-btn>
                  <v-btn
                    color="success"
                    tile
                    @click="modalSelectColorClose"
                  >
                    <div>
                      Okay
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

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],

  props: ["$this"],

  data: function() {
    return {
      
      modal: this.$this.modal.add,

      submitted: false,

      valid: true,

      modalId: "calendar-notes-type-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      color: '#673AB7',

      color_background: '#673AB7',

      color_hexcode_required: false,

      name_required: false
    };
  },
  watch : {
    color:function(val) {
      this.form.color_hexcode = val;
      this.color_background = val;
    },

  },
  methods: {
    ...mapActions("calendar_notes_type", ["post_calendar_notes_type", "add_calendar_notes_type", "update_calendar_notes_type"]),
    
    modalClose(done) {

      $(".error-provider").remove();

      this.$this.$bvModal.hide("calendar-notes-type-add-modal");

      this.keep_open = false;
    },

    checkslotItem( val ){
      
    },


    chooseColor() {
      this.form.color_hexcode = this.color;
      this.$bvModal.show("create-select-color-calendar-modal");
    },

    modalSelectColorClose() {
      this.$bvModal.hide("create-select-color-calendar-modal");
    },

    modalSelectColorCancel() {
      this.form.hexcode = "";
      this.$bvModal.hide("create-select-color-calendar-modal");
    },

    isNumber: function(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
        evt.preventDefault();;
      } else {
        return true;
      }
    },

    // closeBrandModal() {
    //   this.$bvModal.hide("brand-modal");

    //   if (this.form.actual_action == "Add") {
    //     this.modal.addItem.isVisible = true;
    //   } else {
    //     this.modal.edit.isVisible = true;
    //   }
    // },

    onSubmit() {
       this.name_required = false;
       this.color_hexcode_required = false;
      if( this.form.name == '' ) {
        this.name_required = true;
        return false;
      }
      if( this.form.color_hexcode == '' ) {
        this.color_hexcode_required = true;
        return false;
      }
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.name);
      formData.append("color_hexcode", this.form.color_hexcode);
      // formData.append("org_id", this.$org_id);
      // formData.append("brand", JSON.stringify( this.form.brand ) );
      this.modal.button.loading = true;
      this.post_calendar_notes_type(formData)
        .then((resp) => {
          this.modal.button.loading = false;
          if( resp.data.duplicate == true ) {
            this.$this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("calendar_notes_type_exist")
            );
            return false;
          }
  
          this.$bvModal.hide("calendar-notes-type-add-modal");

            let message = {
              create:
                `${this.form.name}` +
                this.$t("created_message") +
                this.$t("calendar_type_list"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            // this.form.reset();
            this.$emit("loadTable");
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#request_type, #affect_limit").removeAttr(
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
  },

  computed: {
    ...mapGetters("calendar_notes_type", {
      calendar_notes_type: "calendar_notes_type",
      responseStatus: "get_response_status",
    }),
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}

.div_color{
  cursor: pointer;
}
.div_background{
  float:right; 
  width: 30%; 
  height: 35%;
}
</style>
