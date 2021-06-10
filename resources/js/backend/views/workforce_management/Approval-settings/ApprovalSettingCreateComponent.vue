<template>
  <div class="create">
      <b-modal
        id="approval-settings-add-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t('approval_settings_add_new_button')"
      >
        <v-app id="create__container">
          <v-card>
            <form ref="form" @submit.prevent="onSubmit" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{$t('approval_settings_add_new_button')}}
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
              <v-card-text class="modal__content" style="">
                <v-container>
                  <v-container>
                    <v-row>
                      <v-col cols="4" md="4" class="modal__input-container">
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
                          <v-checkbox
                              v-model="form.admin_approval"
                              :label="$t('table.admin_approval') + '?'"
                              @click="onChangeCheckbox($event)"
                              ref="rolesSelected"
                            >
                          </v-checkbox>
                          <b-form-group>
                            <label for="name">
                              {{ $t("table.overbooking") }}
                            </label>
                            <v-select
                              label="name"
                              v-model="form.overbooking"
                              :options="overbooking_option"
                              chips
                              outlined
                              required
                            >
                            </v-select>
                            <small style="color:red" v-if="overbooking_required">{{ $t('table.overbooking') + $t('is_required') }}</small>
                        </b-form-group>
                      </v-col>

                      <v-col cols="8" md="8" class="modal__input-container">
                        <v-container fluid>
                          <b-table
                            striped
                            show-empty
                            stacked="md"
                            ref="table"
                            :fields="tableHeaders"
                            :current-page="currentPage"
                            :per-page="0"
                            :items="request_type_items"
                          >
                            <template v-slot:cell(name)="data">
                              <div style="margin-top: 10px">
                                <span
                                  class="text-left text--secondary"
                                >
                                  <input type="hidden" name="request_type" :value="data.item.id">
                                  {{ data.item.name }}
                                  <small
                                    v-if="data.item.convertion == true"
                                    style="color:red"
                                  >
                                    (No {{ data.item.language }} translation yet)</small
                                  >
                                </span>
                              </div>
                            </template>
                            <template v-slot:cell(approval)="data">
                              <div style="margin-top: 10px">
                                  <select :id="data.item.id" class="form-control approval" disabled>
                                    <option selected> Off </option>
                                    <option> On </option>
                                    <option> If Overbook Only </option>
                                  </select>
                                  
                                  <!-- <v-select
                                    label="name"
                                    :class="`approval`"
                                    :id="`approval_id_`+ data.item.id"
                                    :options="approval_option"
                                    chips
                                    outlined
                                  >
                                  </v-select> -->
                              </div>
                            </template>
                          </b-table>
                        </v-container>
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

      isLoading: true,

      btnloading: false,

      filter: "",

      tableTotalRows: "",

      searched: "",

      currentPage: 1,

      perPage: 10,

      sortBy: 'name',

      sortDesc: false,

      modal: this.$this.modal.add,

      submitted: false,

      valid: true,

      modalId: "approval-settings-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      overbooking_option: ['Automatic', 'Manual', 'Off'],
      
      approval_option: ['Off', 'On', 'If Overbook Only'],

      tableHeaders: [
        {
          key: "name",
          label: this.$t("table.request_type"),
          thClass: "text-left",
          sortable: true,
        },
        {
          key: "approval",
          label: this.$t("table.approval"),
          thClass: "text-left",
          sortable: true,
        },
      ],

      name_required: false,
      overbooking_required: false

    };
  },
  methods: {
    ...mapActions("approval_settings", ["post_approval_settings", "add_approval_settings", "update_approval_settings"]),
    
    modalClose(done) {

      $(".error-provider").remove();

      this.$this.$bvModal.hide("approval-settings-add-modal");

      this.keep_open = false;
    },

    chooseColor() {
      this.$bvModal.show("create-select-color-modal");
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
    
    onChangeCheckbox(event){
      if( this.form.admin_approval === true ) {
        $(".approval").removeAttr( 'disabled' );
        this.form.admin_approval = 1;
      } else {
        $(".approval").attr( 'disabled', 'disabled' );
        this.form.admin_approval = 0;
      }
    },
    onSubmit() {
      this.name_required = false;
      this.overbooking_required = false;
      if( this.form.name == '' ) {
        this.name_required = true;
        return false;
      }
      if( this.form.overbooking == '' ) {
        this.overbooking_required = true;
        return false;
      }

      let formData = new FormData();

      var array = [];
      $('.approval').map(function() {
        array.push({
          'id' : $(this).attr('id'),
          'value': $(this).val()
        })
      }); 

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.name);
      formData.append("admin_approval", this.form.admin_approval);
      formData.append("overbooking", this.form.overbooking);
      formData.append("approval_setting", JSON.stringify(array));
      formData.append("brands", ( this.$this.brand_session.id !== undefined || this.$this.brand_session.id !== '' ? this.$this.brand_session.id : null ));

      this.modal.button.loading = true;
      this.post_approval_settings(formData)
        .then((resp) => {
          this.modal.button.loading = false;
          if( resp.data.duplicate == true ) {
            this.$this.makeToast(
              "danger",
              "DUPLICATE",
              resp.data.name + this.$t("approval_settings_exist")
            );
            return false;
          }
          this.$bvModal.hide("approval-settings-add-modal");

            let message = {
              create:
                `${this.form.name}` +
                this.$t("created_message") +
                this.$t("label.approval_settings"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );
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
    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      // this.loadItems()
    },
  },

  computed: {
    ...mapGetters("approval_settings", {
      approval_settings: "approval_settings",
      responseStatus: "get_response_status",
    }),
    ...mapGetters("request_type", {
        request_type_items: "request_type"
    }),
  },
}
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
