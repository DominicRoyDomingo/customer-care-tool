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
                          <small style="color:red;" v-if="question_name_required">{{ $t('label.name') + $t('is_required') }}</small>
                        </b-form-group>
                        <b-form-group style="margin-bottom:1%">
                          <label for="name">
                            {{ $t("label.type_of_data") }}
                             <strong class="text-danger">*</strong>
                          </label>
                          <v-select
                            v-model="form.type_of_data"
                            :options="parent.type_of_data"
                            id="slot_item"
                            label="Standard"
                          >
                          </v-select>
                          <small style="color:red" v-if="question_type_of_data_required">{{ $t('label.type_of_data') + $t('is_required') }}</small>
                        </b-form-group>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <div class="float-left ml-2">
                    <b-link
                      class="font-weight-bold text-left"
                      href="/admin/questions/types"
                    >
                      {{ $t("label.manage_type_of_question").toUpperCase() }}
                    </b-link>
                  </div>
                  <v-spacer></v-spacer>
                  <div class="float-right mr-2">
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
                      <div v-if="modal.button.loading" class="text-center">
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
                  </div>
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

export default {

  mixins: [toast],

  props: ["parent"],


  data: function() {
    return {
      modal: this.parent.questionTypeModal.add,

      submitted: false,

      valid: true,

      modalId: this.parent.questionTypeModal.id,

      keep_open: false,

      form: {
        name: "",
        type_of_data: {}
      },

      formData: this.parent.formData,

      question_name_required: false,

      question_type_of_data_required: false,

      brand_name_required: false

    };
  },
  
  methods: {
    ...mapActions("question_types", [
      "storeQuestionType",
      "getQuestionTypes"
    ]),

    focusInput() {
      this.$refs.myField.focus();
    },

    modalClose(modalId) {
      this.parent.$bvModal.hide(modalId);
    },

    fieldRequiredDefaultValue(){
      this.question_name_required = false;
      this.question_type_of_data_required = false;
    },

    onSubmit() {
      var vm = this;
      vm.modal.button.loading = true;

      vm.fieldRequiredDefaultValue()
      if( this.form.name == '' ) {
        this.question_name_required = true;
      }
      if( this.form.type_of_data == '' || this.form.type_of_data == null ) {
        this.question_type_of_data_required = true;
      }

      if (!this.isValid) {
        this.focusInput();
        vm.modal.button.loading = false;
        return false;
      }

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("locale", this.$i18n.locale);

      // formData.append("lang", this.form.language ? this.form.language : 'en');

      formData.append("name", this.form.name);
      formData.append("type_of_data", this.form.type_of_data);
      
      this.storeQuestionType(formData).then((response) => {
        vm.modal.button.loading = false;

          let message = {
              create:
                `${vm.form.name}` +
                vm.$t("created_message") +
                vm.$t("label.type_of_question"),
              title: {
                create: vm.$t("new_record_created"),
              },
            };


          if (response.data.duplicate) {
            vm.makeToast(
              "danger",
              vm.$t("errors.duplicate"),
              vm.form.name + vm.$t("question_type_name_exist")
            );

            return false;
          }
          
          if (response.data.success) {
            vm.modalClose(vm.modal.id);

            vm.makeToast(
              "success",
              message.title.create,
              message.create
            );

            vm.form = {};
            vm.getQuestionTypes({api_token : vm.$user.api_token, locale : vm.$i18n.locale});
          }
      });
    },
  },

  computed: {
    isValid(){
      if ( this.question_name_required || this.question_type_of_data_required ) {
        return false
      }
      return true
    }
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
