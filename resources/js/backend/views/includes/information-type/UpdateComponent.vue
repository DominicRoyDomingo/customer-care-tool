<template>
  <div class="create">
    <b-modal
      id="information-type-edit-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      :title="$t(modal.name)"
    >
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
                        v-model="form.information_type_name"
                        :rules="[(v) => !!v || $t('label.name_required')]"
                        :label="$this.translations[$this.selectedLanguage].name + ` ${$this.nameTranslation != '' ? $this.nameTranslation : ''}`"
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
                        ref="selectEdit"
                      ></v-autocomplete>
                    </span>
                      <!-- <label for="name">
                        {{ $this.translations[$this.selectedLanguage].name }}
                      </label>
                      
                      <input
                        id="name"
                        type="text"
                        name="name"
                        v-model="form.information_type_name"
                        class="form-control"
                        :placeholder="$this.translations[$this.selectedLanguage].name"
                        autocomplete="off"
                        aria-describedby="name"
                      /> -->
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
      <!-- <v-app id="service-type__container">
        <v-container>
          <div class="p-2 margin-top">
            <form
              @submit.prevent="onSubmit"
              method="post"
              enctype="multipart/form-data"
              @keydown="form.errors.clear($event.target.name)"
            >
              <b-form-group>
                <label for="name">
                  {{ $t("label.name") }}
                </label>
                
                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="form.information_type_name"
                  class="form-control"
                  :placeholder="$t('label.name')"
                  autocomplete="off"
                  aria-describedby="name"
                />
              </b-form-group>

              <b-form-group>

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
              </b-form-group>
            </form>
          </div>
        </v-container>
      </v-app> -->
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

      submitted: false,

      valid: true,

      items: ['Long Text', 'Short Text', 'Date', 'Email Format', 'Numeric'],

      form: this.$this.form,

      formData: this.$this.formData,

      currentLanguage: this.$i18n.locale,

      formGroups: [],

      langs: [
        
        { id:'en', value: 'English' },
        
        { id:'it', value: 'Italian' },
        
        { id:'de', value: 'German' },

        { id:'ph-fil', value: 'Filipino' },

        { id:'ph-bis', value: 'Visayan' },
      
      ]

    };

  },

  created() {           
    window.addEventListener("click",() => {
      if(this.$refs.selectEdit != undefined) this.$refs.selectEdit.blur();
    });  
  },

  methods: {
    
    ...mapActions("information_type", ["post_information_type", "update_information_type", "get_information_type_name"]),
    
    modalClose(done) {
        
        this.$this.$bvModal.hide('information-type-edit-modal');

        this.form.reset();

    },
    
    onSubmit() {
      this.modal.button.loading = true;
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      // formData.append("index", this.form.id);
      formData.append("information_type_name",  this.form.information_type_name );
      formData.append("information_type_data",  this.form.information_type_data );

      this.post_information_type(formData)
        .then(resp => {
          
          this.$this.btnloading = false;
          this.$bvModal.hide("information-type-edit-modal");

          if (this.$this.informationTypesResponseStatus) {

            let response = this.$this.informationTypesResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_information_type(response);

            let message = {
              update: this.$t( 'updated_message1' ) + this.$t( 'label.information_type' ) + ` ID: ${this.$this.form.id} ` + this.$t( 'updated_message2' ),
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
            this.$this.successfullySavedInformationType();
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
      this.form.language = event.target.value;
      this.get_information_type_name(formData).then( resp => {
      
        if( resp ) {
          
          this.form.information_type_name = resp;
        
        } else {
          
          this.form.information_type_name = "";
        
        }

      });
        
    }
  
  }

};

</script>

<style>

</style>
