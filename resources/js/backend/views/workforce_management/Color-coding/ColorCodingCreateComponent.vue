<template>
  <div class="create">
      <b-modal
        id="color-coding-add-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t('color_coding_add_new_button')"
      >
        <v-app id="create__container">
          <v-card>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('color_coding_add_new_button')}}
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
                          v-model="form.color_name"
                          :label="$t('label.color_name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="color_name_required"> {{ $t("label.color_name") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                        <div class="div_back" @click="chooseColor" v-bind:style="{ backgroundColor: color_background}"></div>
                       <b-form-group style="margin-bottom:1%">
                        <v-text-field
                          v-model="form.hexcode"
                          :label="$t('label.hexcode')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          @click="chooseColor"
                          style="width: 65%"
                        >
                        </v-text-field>
                         <small style="color:red; margin-top:-15px;position:absolute" v-if="hexcode_required"> {{ $t("label.hexcode") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                        <v-text-field
                          v-model="form.description"
                          :label="$t('label.color_coding_description')"
                          class="modal__input"
                          autocomplete="off"
                        >
                        </v-text-field>
                        <b-form-group>
                            <label for="name">
                              {{ $t("label.slot_limit") }}
                            </label>
                          <v-select
                            v-model="form.slot_limit"
                            :options="$this.slot_limit_option"
                            id="slot_item"
                            label="Standard"
                          >
                          </v-select>
                         <small style="color:red; position:absolute" v-if="slot_limit_required"> {{ $t("label.slot_limit") }} {{ $t('is_required')}}</small>
                        </b-form-group>
                        <!-- <v-text-field
                          v-model="form.slot_limit"
                          :rules="[(v) => !!v || $t('label.slot_limit_empty')]"
                          :label="$t('label.slot_limit')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                          @keypress="isNumber($event)"
                        >
                        </v-text-field> -->
                          <!-- <label for="name">
                            {{ $t("label.is_affecting_workforce") }}
                          </label> -->
                          <!-- <v-checkbox
                              v-model="form.is_affect_limit"
                              :label="$t('label.is_affecting_workforce')"
                            ></v-checkbox> -->
                          <b-form-group>
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
                         <small style="color:red; position:absolute" v-if="brand_required"> {{ $t("brands_name") }} {{ $t('is_required')}}</small>
                        </b-form-group>
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

      <b-modal
        id="create-select-color-modal"
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

      modalId: "color-coding-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      color: '#673AB7',

      color_background: '#673AB7',

      color_name_required: false,
      
      hexcode_required: false,
      
      slot_limit_required: false,
      
      brand_required: false

    };
  },
  watch : {
    color:function(val) {
      this.form.hexcode = val;
      this.color_background = val;
    },

    // 'form.slot_limit': function(val) {
    //   console.log( val );
    //   let vm = this;
    //   if( val === 'Not Available' ) {
    //     let params = {
    //       api_token: this.$user.api_token,
    //       brand: ( this.$this.brand_session.id !== undefined || this.$this.brand_session.id !== '' ? this.$this.brand_session.id : null ),
    //       id: '',
    //       slot: val
    //     }
    //         axios.get(`/api/color-coding/check_slot_item`, { params }).then((res) => {
    //             console.log( res.data );
    //             let vm = this;
    //             if( res.data === 'true' ){
    //               $.confirm({
    //                 title: 'NOT AVAILABLE SLOT ITIM',
    //                 content: 'This slot limit is already selected by other color. Do you wish to override?',
    //                 type: "red",
    //                 typeAnimated: true,
    //                 columnClass: "medium",
    //                 buttons: {
    //                   yes: {
    //                     text: vm.$t("yes"),
    //                     btnClass:
    //                       "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
    //                     action: function(value) {
    //                     },
    //                   },
    //                   no: {
    //                     text: vm.$t("no"),
    //                     btnClass:
    //                       "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
    //                     action: function() {
    //                       vm.form.slot_limit = '';
    //                     },
    //                   },
    //                 },
    //               });
    //             }
    //         })
    //         .catch((err) => {
    //             console.log(err);
    //         });
    //   }
    // }
  },
  methods: {
    ...mapActions("color_coding", ["post_color_coding", "add_color_coding", "update_color_coding"]),
    
    modalClose(done) {

      $(".error-provider").remove();

      this.$this.$bvModal.hide("color-coding-add-modal");

      // this.$this.modal.createRequestType != undefined
      //   ? this.$this.$bvModal.show(this.$this.modalId)
      //   : this.form.reset();

      // this.form.reset();

      this.keep_open = false;
    },

    checkslotItem( val ){
      
    },


    chooseColor() {
      this.$bvModal.show("create-select-color-modal");
    },

    modalSelectColorClose() {
      // this.color = this.form.hexcode;
      this.$bvModal.hide("create-select-color-modal");
    },

    modalSelectColorCancel() {
      this.form.hexcode = "";
      this.$bvModal.hide("create-select-color-modal");
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

    closeBrandModal() {
      this.$bvModal.hide("brand-modal");

      if (this.form.actual_action == "Add") {
        this.modal.addItem.isVisible = true;
      } else {
        this.modal.edit.isVisible = true;
      }
    },

    onSubmit() {
      this.color_name_required = false;
      this.hexcode_required = false;
      this.slot_limit_required = false;
      this.brand_required = false;

      if( this.form.color_name == '' ) {
        this.color_name_required = true;
        return false;
      }
      if( this.form.hexcode == '' ) {
        this.hexcode_required = true;
        return false;
      }
      if( this.form.slot_limit == '' ) {
        this.slot_limit_required = true;
        return false;
      }
      if( this.form.brand.length == '' ) {
        this.brand_required = true;
        return false;
      }

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("color_name", this.form.color_name);
      formData.append("hexcode", this.form.hexcode);
      formData.append("description", this.form.description);
      formData.append("slot_limit", this.form.slot_limit);
      formData.append("org_id", this.$org_id);
      formData.append("brand", JSON.stringify( this.form.brand ) );
      this.modal.button.loading = true;
      this.post_color_coding(formData)
        .then((resp) => {
          this.modal.button.loading = false;
          if( resp.data.duplicate == true ) {
            this.$this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("color_coding_exist")
            );
            return false;
          }
          if( resp.data.not_available == true ) {
            this.$this.makeToast(
              "danger",
              this.$t("errors.slot_limit"),
              this.$t("errors.duplicate_slot_limit")
            );
            return false;
          }

          this.$bvModal.hide("color-coding-add-modal");

            let message = {
              create:
                `${this.form.color_name}` +
                this.$t("created_message") +
                this.$t("label.color_coding"),
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
    ...mapGetters("color_coding", {
      color_coding: "color_coding",
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
.div_back{
  float:right; 
  width: 30%; 
  height: 13%;
}
</style>
