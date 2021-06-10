<template>

  <div class="create">

    <b-modal
      id="provider-type-edit-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >

      <!-- <div class="p-2 margin-top">

        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >

          <b-form-group>

            <div class="col-lg-3 col-md-3 float-right">

                <div class="row">
              
                    <select class="form-control" @change.prevent="selectLang($event)" style="margin-bottom:-35px">
                        
                        <option
                        :value="language.id"
                        :selected="language.id === form.language"
                        v-for="(language, langInd) in langs"
                        :key="langInd"
                        >
                        {{language.value}}
                        </option>

                    </select>

                </div>

            </div>

          </b-form-group>

          <b-form-group>

            <label for="name">
                {{ $t('label.name') }}
            </label>
            
            <input
              id="name"
              type="text"
              name="name"
              v-model="form.provider_type_name"
              class="form-control"
              :placeholder="$t('label.name')"
              autocomplete="off"
              aria-describedby="name"
            />

          </b-form-group>

          <b-form-group>

            <span class="float-left">

              <div class="form-check form-check-inline mr-1">

                <input class="form-check-input" v-model="keep_open" type="checkbox">

                <label class="form-check-label" for="inline-checkbox3"><small>Keep the form open?</small></label>

              </div>

            </span>

            <span class="float-right">

              <el-button size="small" @click="modalClose">{{ $t(modal.button.cancel)}}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="primary"
                :loading="modal.button.loading"
              >{{ $t(modal.button.update) }}</el-button>

            </span>

          </b-form-group>

        </form>

      </div> -->

      <v-app id="create__container">
        <v-card>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span>
                {{ $this.translations[$this.selectedLanguage].title }}
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
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <v-text-field
                        v-model="form.provider_type_name"
                        :rules="[(v) => !!v || $t('label.name_required')]"
                        :label="$this.translations[$this.selectedLanguage].name + ` ${$this.nameTranslation != '' ? $this.nameTranslation : ''}`"
                        suffix="*"
                        class="modal__input"
                        autocomplete="off"
                        required
                      >
                      </v-text-field>
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

import { mapActions, mapGetters } from "vuex";

export default {
  
  props: ["$this"],

  data: function() {
    
    return {
      
      modal: this.$this.modal.edit,

      valid: true,

      form: this.$this.form,

      formData: this.$this.formData,

      langs: [
        
        { id:'en', value: 'English' },
        
        { id:'it', value: 'Italian' },
        
        { id:'de', value: 'German' },

        { id:'ph-fil', value: 'Filipino' },

        { id:'ph-bis', value: 'Visayan' },
      
      ]

    };

  },

  methods: {
    
    ...mapActions("provider_type", ["post_provider_type", "update_provider_type", "get_provider_type_name"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    modalClose(done) {
      
     
        $(".error-provider").remove();

        $("#provider_type_name").removeAttr(
          "style"
        );
        this.file = "";
        
        this.$this.$bvModal.hide('provider-type-edit-modal');

        this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
      
        this.currentLanguage = this.$i18n.locale;

    },
    
    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      // formData.append("index", this.form.id);
      formData.append("provider_type_name",  this.form.provider_type_name );

      this.post_provider_type(formData)
        .then(resp => {
          
          this.$this.btnloading = false;
          this.$bvModal.hide("provider-type-edit-modal");

          if (this.$this.providerTypeResponseStatus) {

            let response = this.$this.providerTypeResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_provider_type(response);

            let message = {
              update: this.$t( 'updated_message1' ) + this.$t( 'label.provider_types' ) + ` ID: ${this.$this.form.id} ` + this.$t( 'updated_message2' ),
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
            this.$this.successfullySavedProviderType();
          }
        })
        .catch(error => {
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

    selectLang(event){
      // this.form.language = event.target.value;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", event.target.value);
      this.$this.currentLanguage = event.target.value;
      // this.$session.set("local_lang", event.target.value);
      this.form.language = event.target.value;
      this.get_provider_type_name(formData).then( resp => {
      
        if( resp ) {
          
          this.form.provider_type_name = resp;
        
        } else {
          
          this.form.provider_type_name = "";
        
        }

      });
        
    },
  
  }

};

</script>

<style>

</style>
