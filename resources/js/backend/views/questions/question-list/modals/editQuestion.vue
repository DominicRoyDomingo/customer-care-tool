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
                      <v-col cols="12" md="10" class="modal__input-container">
                        <b-form-group style="margin-bottom:1%">
                          <v-text-field
                            v-model="form.question"
                            :label="$t('label.question_phrase')"
                            suffix="*"
                            ref="myField"
                            class="modal__input"
                            autocomplete="off"
                          >
                          </v-text-field>
                          <small style="color:red; margin-top:-23px;position:absolute" v-if="question_required">{{ $t('label.name') + $t('is_required') }}</small>
                        </b-form-group>
                      </v-col>     
                      <v-col cols="12" md="2" class="modal__input-container">
                        <v-img v-if="form.url" height="50" :src="form.url"></v-img>

                        <v-img
                          v-else
                          height="50"
                          src="https://customer-care-tool.s3.us-east-2.amazonaws.com/image-placeholder/image-placeholder.png"
                        ></v-img>

                        <div
                          class="d-flex flex-column"
                          align="center"
                          style="
                            height: 25px;
                            justify-content: center;
                            color: #fff;
                            cursor: pointer;
                            opacity: 0.5
                          "
                          :style="[
                            form.url != null
                              ? { 'background-color': '#828282' }
                              : { 'background-color': '#BDBDBD' },
                          ]"
                          @click="chooseFiles"
                        >
                          <v-icon color="#fff"> mdi-camera-plus-outline </v-icon>
                          <input
                            id="fileUpload"
                            type="file"
                            @change="onGetFile"
                            accept=".png, .jpg, .jpeg"
                            plain
                            hidden
                          />
                        </div>
                      </v-col>  
                    </v-row>                                     
                    <v-row>
                        <v-col cols="12" md="12" class="modal__input-container">
                          <label for="form-type">
                              {{ $t("label.type_of_question") }}
                               <strong class="text-danger">*</strong>
                          </label>
                          <b-form-group>
                            <el-select
                              name="form-type"
                              v-model="form.type"
                              :placeholder="$t('label.select_type_of_question')"
                            >
                              <el-option
                                v-for="toq in questionTypeList"
                                :key="toq.id"
                                :label="toq.name"
                                :value="toq.id"
                              >
                              </el-option>
                            </el-select>
                            <small style="color:red; margin-top:-4px;position:absolute" v-if="type_required">{{ $t('label.type_of_question') + $t('is_required') }}</small>
                          </b-form-group>

                          <label for="form-choices">
                            {{ $t("label.choices") }}
                          </label>
                          <b-form-group>
                            <el-select
                              name="form-choices"
                              multiple
                              v-model="form.choices"
                              :placeholder="$t('label.select_choices')"
                            >
                              <el-option
                                v-for="choice in getChoicesList"
                                :key="choice.id"
                                :label="choice.choice_name"
                                :value="choice.id"
                              >
                              </el-option>
                            </el-select>
                          </b-form-group>

                          <label for="form-evaluation_method">
                            {{ $t("label.evaluation_method") }}
                            <strong class="text-danger">*</strong>
                          </label>
                          <b-form-group>
                            <el-select
                              name="form-evaluation_method"
                              v-model="form.evaluation_method"
                              :placeholder="$t('label.select_evaluation_method')"
                            >
                              <el-option
                                v-for="ev in evaluationMethod"
                                :key="ev.value"
                                :label="ev.text"
                                :value="ev.value"
                              >
                              </el-option>
                            </el-select>
                            <small style="color:red; margin-top:-4px;position:absolute" v-if="evaluation_method_required">{{ $t('label.evaluation_method') + $t('is_required') }}</small>
                          </b-form-group>

                          <div v-if="form.evaluation_method === 'Test' && form.choices.length > 0">
                            <label for="form-choices">
                              {{ $t("label.correct_answer") }}
                            </label>
                            <b-form-group >
                              <el-select
                                name="form-correct_answer"
                                v-model="form.correct_answer"
                                :placeholder="$t('label.select_correct_answer')"
                              >
                                <el-option
                                  v-for="choice in selectedChoices"
                                  :key="choice.value"
                                  :label="choice.text"
                                  :value="choice.value"
                                >
                                </el-option>
                              </el-select>
                            </b-form-group>
                          </div>

                          <b-form-group>
                            <b-form-checkbox
                              id="checkbox-1"
                              v-model="form.is_active"
                              name="checkbox-1"
                              value="1"
                              unchecked-value="0"
                            >
                              <span style="font-size: 0.8rem !important"
                                >{{ $t("label.is_active") }}?</span
                              >
                            </b-form-checkbox>
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

import questionMixin from "./../mixins/questionMixin";

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

      file: null,

      url: this.$this.url,

      formData: this.$this.formData,

      question_required: false,

      type_required: false,

      evaluation_method_required: false,

      evaluationMethod: [
        { value: "Test", text: "Test" },
        { value: "Survey", text: "Survey" },
      ],
      selectedChoices : [],

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },

        { id: "ph-fil", value: "Filipino" },
        
        { id: "ph-bis", value: "Visayan" },
      ],
    };
  },
  mounted(){
    this.loadQuestioTypes()
    this.loadChoices()
  },
  methods: {
    ...mapActions("questions", [
      "getChoices",
      "storeQuestion",
      "getQuestions",
      "updateQuestion"
    ]),

    loadQuestioTypes(){
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,        
        locale: this.$i18n.locale,
      };
      this.$store.dispatch('question_types/getQuestionTypes', params).then((response) => {
          this.isLoading = false;
      });
    },

    loadChoices(){
      let params = {
        api_token: this.$user.api_token,
        locale : lang
      };
      this.getChoices(params)
        .then((resp) => {})
        .catch((error) => {
          console.log(error);
        });
    },

    focusInput() {
      // this.$refs.myField.focus();
    },

    modalClose(modalId) {

      this.form.reset();
      this.$this.$bvModal.hide(modalId);
    },

    fieldRequiredDefaultValue(){
      this.question_required = false;
      this.type_required = false;
      this.evaluation_method_required = false;
    },

    onSubmit() {
      var vm = this;
      this.modal.button.loading = true
      this.fieldRequiredDefaultValue()
      // this.type_of_data_required = false;
      // this.question_required = false;
      
      if(  this.form.question === '' ) {
        this.question_required = true;
      }

      if(  this.form.type === '' ) {
        this.type_required = true;
      }

      if(  this.form.evaluation_method === '' ) {
        this.evaluation_method_required = true;
      }

      if (!this.isValid) {
        this.focusInput();
        return false;
      }

      let formData = new FormData();

      formData.append("id", this.form.id);

      formData.append("api_token", this.$user.api_token);
      formData.append("locale", this.$i18n.locale);

      formData.append("lang", this.form.language ? this.form.language : 'en');
      formData.append("question", this.form.question);
      formData.append("type", this.form.type);
      formData.append("choices", this.form.choices);
      formData.append("evaluation_method", this.form.evaluation_method);
      formData.append("correct_answer", this.form.correct_answer);
      formData.append("is_active", this.form.is_active);
      formData.append("file", this.file);
      
      this.updateQuestion({formData : formData, id: this.form.id}).then(function(res){
          vm.modal.button.loading = false;
          let message = {
              update:
                vm.$t("updated_message1") +
                vm.$t("label.question") +
                ` ID: ${vm.form.id} ` +
                vm.$t("updated_message2"),
              title: {
                update: vm.$t("record_updated"),
              },
            };
          if (!res.data.success && res.data.error === 'duplicate') {
            vm.makeToast(
              "danger",
              vm.$t("errors.duplicate"),
              vm.form.question + vm.$t("questions_question_exist")
            );

            // reset data
            // vm.form.reset();
            return false;
          }

          if (res.data.success) {
            vm.modalClose(vm.modal.id)
            vm.form.reset();

            vm.makeToast(
              "success",
              message.title.update,
              message.update
            );
            vm.loadQuestions();
          }
          
      }).catch((error) => {
        console.log(error);
      });
    },
    onGetFile(event) {
      if (event.target.files[0] == undefined) return;
      this.file = event.target.files[0];
      this.form.url = URL.createObjectURL(this.file);
    },
    chooseFiles() {
      document.getElementById("fileUpload").click();
    },
    loadQuestions(){
      let params = {api_token: this.$user.api_token,sortbyLang: this.sortbyLangId,locale: this.$i18n.locale};
      this.getQuestions(params).then((response) => {
          console.log("Questions Successfully fetched!");
      });
    },
    selectLang(event) {
      this.form.language = event.target.value
      this.form.question = this.getCurrentQuestionNameByLocale(event.target.value)
    },
  },

  computed: {
    ...mapGetters("question_types", {
      questionTypeList: "questionTypeList",
    }),

    ...mapGetters("questions", {
      getChoicesList: "getChoices",
      getCurrentQuestionNameByLocale: "getCurrentQuestionNameByLocale"
    }),



    typeOfQuestionOption(){
      var optionType = [];
      this.questionTypeList.forEach(function(val){
          optionType.push({value: val.id, text: val.name});
      })
      return optionType;
    },

    choicesOption(){
      var optionChoices = [];
      this.getChoicesList.forEach(function(val){
          optionChoices.push({value: val.id, text: val.choice_name});
      })
      return optionChoices;
    },    
    isValid(){
      if (this.question_required || this.type_required || this.evaluation_method_required) {
        return false
      }
      return true
    },
  },
  watch: {
    'form.choices': function(arrVal){
      var vm = this;
      var arrSelectedChoices = [];
      if (arrVal) {
        arrVal.forEach(function(choiceId){
          var foundChoice = null;
          foundChoice =  vm.getChoicesList.find(x => x.id === choiceId);
          arrSelectedChoices.push({value: foundChoice.id, text: foundChoice.choice_name});
        });
      }
      vm.selectedChoices = arrSelectedChoices;
    },
  }
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}

.el-select {
  display: block;
}
</style>
