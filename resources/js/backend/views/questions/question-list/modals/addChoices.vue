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
                          <small style="color:red;" v-if="name_required">{{ $t('label.choice') + $t('is_required') }}</small>
                        </b-form-group>

                        
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <div class="ml-2">
                    <b-link
                      class="font-weight-bold"
                      href="/admin/questions/choices"
                    >
                      {{ $t("label.manage_choices").toUpperCase() }}
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
      modal: this.parent.choiceModal.add,

      submitted: false,

      valid: true,

      modalId: this.parent.choiceModal.id,

      keep_open: false,

      form: {
        name: ""
      },

      formData: this.parent.formData,

      name_required: false,

    };
  },
  
  methods: {
    ...mapActions("question_types", [
      "storeQuestionType",
      "getQuestionTypes"
    ]),

    ...mapActions("question_choice", [
      "post_choice", 
    ]),

    ...mapActions("questions", [
      "getChoices"
    ]),

    focusInput() {
      this.$refs.myField.focus();
    },

    modalClose(modalId) {
      this.parent.$bvModal.hide(modalId);
    },

    fieldRequiredDefaultValue(){
      this.name_required = false;
    },

    onSubmit() {

      var vm = this;
      vm.modal.button.loading = true;

      vm.fieldRequiredDefaultValue()
      if( this.form.name == '' ) {
        this.name_required = true;
      }

      if (!this.isValid) {
        this.focusInput();
        vm.modal.button.loading = false;
        return false;
      }

      let params = {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          sortbyLang: this.sortbyLang,
          form: this.form,
          type: 'create',
      };

      this.post_choice(params)
        .then((resp) => {
          if (resp.data) {
            vm.modal.button.loading = false;
            vm.modalClose(vm.modal.id);

            vm.makeToast(
              "success",
              vm.$t("new_record_created"),
              `${vm.form.name}` +
              vm.$t("created_message") +
              vm.$t("label.choice")
            );

            vm.loadChoices()
          }
          
      })
      .catch((error) => {
          vm.modal.button.loading = false;
      });
    },
    loadChoices(){
      let params = {
        api_token: this.$user.api_token,
        locale : this.$i18n.locale
      };
      this.getChoices(params)
        .then((resp) => {})
        .catch((error) => {
          console.log(error);
        });
    },
  },

  computed: {
    isValid(){
      if ( this.name_required) {
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
