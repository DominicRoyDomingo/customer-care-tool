<template>
  <div class="create">
      <b-modal
        :id="modal.id"
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
                  @click="modalClose(modal.id)"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content">
                <v-container>
                  <v-row>
                    <v-col cols="9">
                    </v-col>
                    <v-col cols="3">
                      <div>
                        <select
                        class="form-control"
                        @change.prevent="selectLang($event)"
                      >
                        <option
                          :value="language.id"
                          :selected="language.id === form.language"
                          v-for="(language, langInd) in langs"
                          :key="langInd"
                        >
                          {{ language.value }}
                        </option>
                      </select>
                      </div>
                    </v-col>
                  </v-row>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                         <b-form-group style="margin-bottom:1%">
                          <v-text-field
                            v-model="form.name"
                            :label="$t('label.name')"
                            suffix="*"
                            ref="myField"
                            class="modal__input"
                            autocomplete="off"
                          >
                          </v-text-field>
                          <small style="color:red; margin-top:-15px;position:absolute" v-if="question_type_name_required">{{ $t('label.name') + $t('is_required') }}</small>
                         </b-form-group>
                        <label for="name">
                          {{ $t("label.type_of_data") }}
                           <strong class="text-danger">*</strong>
                        </label>
                        <v-select
                          v-model="form.type_of_data"
                          :options="$this.type_of_data"
                          id="slot_item"
                          label="Standard"
                        >
                        </v-select>
                        <small style="color:red" v-if="type_of_data_required">{{ $t('label.request_type') + $t('is_required') }}</small>
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
                    @click="modalClose(modal.id)"
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
                        {{ $t(modal.button.update) }}
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
import { mapActions, mapGetters } from "vuex";

import toast from "./../../../../helpers/toast.js";

import i18n from '../../../../i18n.js';

let lang = i18n.locale;

export default {

  mixins: [toast],

  props: ["$this"],


  data: function() {
    return {
      modal: this.$this.modal.edit,

      submitted: false,

      valid: true,

      modalId: this.$this.modal.id,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      question_type_name_required: false,

      type_of_data_required: false,

      brand_name_required: false,

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },

        { id: "ph-fil", value: "Filipino" },
        
        { id: "ph-bis", value: "Visayan" },
      ],

      cloneCurrentQuestionType : {}

    };
  },
  mounted() {
    // this.loadCurrentQuestionType();
  },
  methods: {
    focusInput() {
      this.$refs.myField.focus();
    },

    modalClose(modalId) {
      this.$this.$bvModal.hide(modalId);
    },

    onSubmit() {
      var vm = this;
      this.type_of_data_required = false;
      this.question_type_name_required = false;
      
      if( this.form.name == '' ) {
        this.focusInput();
        this.question_type_name_required = true;
        return false;
      }
      if( this.form.type_of_data == '' || this.form.type_of_data == null ) {
        this.type_of_data_required = true;
        return false;
      }

      let formData = new FormData();
      formData.append("id", this.form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language ? this.form.language : 'en');
      formData.append("name", this.form.name);
      formData.append("type_of_data", this.form.type_of_data);
      this.$store.dispatch('question_types/updateQuestionType', {formData : formData, id: this.form.id}).then((response) => {
          if (response.data.success === false && response.data.message === 'Duplicate') {
            vm.makeToast(
              "danger",
              vm.$t("errors.duplicate"),
              this.form.name + vm.$t("question_type_name_exist")
            );
          }
          let message = {
              update:
                this.$t("updated_message1") +
                this.$t("label.type_of_question") +
                ` ID: ${this.form.id} ` +
                this.$t("updated_message2"),
              title: {
                update: this.$t("record_updated"),
              },
            };
          
          if (response.data.success === true) {
              vm.makeToast(
                "success",
                message.title.update,
                message.update
              );
          }
          

          vm.form = {};
          this.$store.dispatch('question_types/getQuestionTypes', {api_token : this.$user.api_token});
      });
    },
    selectLang(event) {
      this.form.language = event.target.value
      this.form.name = this.getCurrentQuestionTypeNameByLocale(event.target.value)
    },
  },

  computed: {
      ...mapGetters("question_types", {
        getCurrentQuestionType: "getCurrentQuestionType",
        getCurrentQuestionTypeNameByLocale: "getCurrentQuestionTypeNameByLocale",
      }),
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
