<template>

  <div class="create">

    <b-modal
      id="parameter-edit-modal"
      hide-footer
      size="md"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
    <v-app id="service-services__container">
        <v-container>

          <div>
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
                <!-- <span class="float-left">
                  <div class="form-check form-check-inline mr-1">
                    <input
                      class="form-check-input"
                      v-model="keep_open"
                      type="checkbox"
                      id="inline-checkbox3"
                    />

                    <label class="form-check-label" for="inline-checkbox3">
                      <small>Keep the form open?</small>
                    </label>
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
        </v-container>
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

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
    };

  },

  methods: {
    
    ...mapActions("parameter", [
      "post_parameter",
      "update_parameter",
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
        
        this.$this.$bvModal.hide('parameter-edit-modal');

        this.form.reset();
    
      this.keep_open = false;

    },
    
    onSubmit() {
      this.modal.button.loading = true;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.parameter_name);
      // formData.append("index", this.form.id);

      this.post_parameter(formData)
        .then(resp => {
          
          this.$this.btnloading = false;
          this.$bvModal.hide("parameter-edit-modal");

          if (this.$this.parametersResponseStatus) {

            let response = this.$this.parametersResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_parameter(response);
            let message = {
              update: response.name + this.$t( 'updated_message2' ),
              title: {
                update: this.$t( 'record_updated' )
              }
            };
            
            this.$this.makeToast(
              "success",
              message.title.update,
              message.update
            );
            this.modal.button.loading = false;
            this.$this.successfullySavedParameter();
          }
        })
        .catch(error => {
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
