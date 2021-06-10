<template>
  <div class="create">
      <b-modal
        id="provider-services-edit-modal"
        hide-footer
        hide-header
        size="md"
        no-close-on-backdrop
      >
        <v-app id="service-services__container">
          <v-card>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span>
                {{$t('button.edit')}} {{form.services.base_name}}
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
                  <!-- <b-form-group>
                    <label for="name">
                      {{ $t("service_category") }}
                    </label>

                    <el-row>
                      <el-col :span="24">
                        <v-select
                          name="service"
                          label="name"
                          v-model="form.services"
                          :options="$this.terms"
                          id="services"
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link href="#" @click="openServiceModal">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("service_add_new_button") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
                      </el-col>
                    </el-row>
                  </b-form-group> -->

                  <b-form-group>
                    <label for="day start">
                      {{ $t("label.start_date") }}
                    </label>

                    <b-form-datepicker id="day_start" v-model="form.day_start" :date-disabled-fn="dateDisabledStart" class="mb-2"></b-form-datepicker>
                  </b-form-group>

                  <b-form-group>
                    <b-form inline>
                      <label for="day end">
                      {{ $t("label.end_date") }}
                      <b-form-checkbox
                        id="checkbox-1"
                        v-model="$this.editEndDateCheckBoxStatus"
                        name="checkbox-1"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="ml-3"
                      >
                        <span v-if="$this.editEndDateCheckBoxStatus == 'not_accepted'">
                          {{$t("label.ask_end_date")}}
                        </span>
                        <span v-else>
                          {{$t("label.uncheck_end_date")}}
                        </span>
                      </b-form-checkbox>
                    </label>
                    </b-form>

                    <b-form-datepicker id="day_end" v-model="form.day_end" class="mb-2" :date-disabled-fn="dateDisabled" v-if="$this.editEndDateCheckBoxStatus == 'accepted'"></b-form-datepicker>
                    
                  </b-form-group>
          
                  <b-form-group>

                    <label for="description">
                      {{ $t("label.description") }}
                    </label>

                    <input
                      id="description"
                      type="text"
                      name="description"
                      v-model="form.description"
                      class="form-control"
                      :placeholder="$t('label.description')"
                      autocomplete="off"
                      aria-describedby="description"
                    />

                  </b-form-group>

                  <b-form-group>
                    <label for="name">
                      {{ $t("parameter_label") }}
                    </label>

                    <el-row>
                      <el-col :span="24">
                        <v-select
                          name="parameter"
                          label="name"
                          v-model="form.parameter"
                          :options="$this.parameters"
                          id="parameter"
                        >
                          <template #list-header>
                            <li style="margin-left:20px;" class="mb-2">
                              <b-link href="#" @click="openParameterModal">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ $t("parameter_add_new_button") }}
                              </b-link>
                            </li>
                          </template>
                        </v-select>
                      </el-col>
                    </el-row>
                  </b-form-group>
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
                      {{ $t(modal.button.update) }}
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
    
    let weekdays = [
          { value: 'Sunday', text: 'Sunday' },
          { value: 'Monday', text: 'Monday' },
          { value: 'Tuesday', text: 'Tuesday' },
          { value: 'Wednesday', text: 'Wednesday' },
          { value: 'Thursday', text: 'Thursday' },
          { value: 'Friday', text: 'Friday' },
          { value: 'Saturday', text: 'Saturday' },
        ]
    return {
      modal: this.$this.modal.editProviderServices,

      submitted: false,

      keep_open: false,
       
      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      weekdays: weekdays,

      weekdays2: weekdays,

      formGroups: [],
    };

  },

  methods: {
    
    ...mapActions("provider_service", [
      "post_provider_service",
      "update_provider_service",
    ]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    modalClose(done) {
      
     
        $(".error-provider").remove();

        $("#provider_type_name").removeAttr(
          "style"
        );
        this.file = "";
        
        this.$this.$bvModal.hide('provider-services-edit-modal');

        this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
      
      
    
      this.keep_open = false;

    },

    dateDisabled(ymd, date) {
      return ymd <= this.form.day_start;
    },

    dateDisabledStart(ymd, date) {
      return ymd < this.form.day_start;
    },
    
    onSubmit() {
      this.modal.button.loading = true;
      let formData = new FormData();
      let endDate = null
      let parameter = ""

      if (this.form.parameter != null) parameter = this.form.parameter.id;
      if(this.$this.editEndDateCheckBoxStatus == 'accepted') {
        endDate = this.form.day_end
      }
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("providers", this.$this.providers_iden);
      formData.append("services", this.form.services.id);
      formData.append("parameter", parameter);
      formData.append("description", this.form.description);
      formData.append("day_start", this.form.day_start);
      formData.append("day_end", endDate);
      // formData.append("index", this.form.id);

      this.post_provider_service(formData)
        .then(resp => {
          
          this.$this.btnloading = false;
          this.$bvModal.hide("provider-services-edit-modal");

          if (this.$this.providerServiceResponseStatus) {
            let response = this.$this.providerServiceResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_provider_service(response)
            let message = {
              update: this.form.services.name + this.$t( 'updated_message2' ),
              title: {
                update: this.$t( 'record_updated' )
              }
            };
            
            this.$this.makeToast(
              "success",
              message.title.update,
              message.update
            );
            // console.log('test')
            this.modal.button.loading = false;
            this.$this.$refs.tableService.refresh();
          }
        })
        .catch(error => {
          console.log(error);
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#provider_type_name").removeAttr( 
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

    openParameterModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("parameter-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    openServiceModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);
      this.$this.mtForm.action = "Add";
      this.$this.$bvModal.show("term-modal");
      // this.$this.modal.itemType.isVisible = true;
    },

    selectLang(event){
      // this.form.language = event.target.value;
      let formData = new FormData();
     
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", event.target.value);
      this.form.language = event.target.value; 
      this.get_provider_type_name(formData).then( resp => {
      
        if( resp ) {
          
          this.form.provider_type_name = resp;
        
        } else {
          
          this.form.provider_type_name = "";
        
        }

      });
        
    }
  
  }

};

</script>

<style>

</style>
