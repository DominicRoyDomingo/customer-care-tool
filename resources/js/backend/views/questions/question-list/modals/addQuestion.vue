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
            <form ref="form" v-model="valid" @submit.prevent="$this.isUpdate ? onUpdate() : onSubmit()" lazy-validation>
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
                  <v-row v-if="$this.isUpdate">
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
                        <v-img v-if="form.url != null" height="50" :src="form.url"></v-img>

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
                          <!-- <span class="white--text" style="font-size: 0.8rem !important">
                            {{
                              form.url != null ? "Change photo" : "Upload photo"
                            }}</span
                          > -->
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
                          <b-form-group>
                            <label for="form-type">
                              {{ $t("label.type_of_question") }}
                            </label>
                            <v-select
                              id="form-type"
                              :options="questionTypeList"
                              v-model="form.type"
                              name="form-type"
                              label="base_name"
                              required
                            >
                              <template #spinner="{ loading }">
                                <b-spinner
                                  v-if="loading"
                                  class="text-info"
                                  small
                                  label="Small Spinner"
                                />
                              </template>

                              <template #list-header>
                                <li style="margin-left: 20px" class="mb-2">
                                  <b-link
                                    v-b-tooltip.hover
                                    :title="`${$t('label.new')}  ${$t(
                                      'label.type_of_question'
                                    )}`"
                                    href="javascript:;"
                                    @click="show_child_modal('questionType-add-modal')"
                                  >
                                    <b-spinner
                                      v-if="isAddQuestionType"
                                      small
                                      label="Small Spinner"
                                    />
                                    <i v-else class="fa fa-plus" aria-hidden="true" />
                                    {{ $t("label.type_of_question") }}
                                  </b-link>
                                </li>
                              </template>
                              <template #option="{ on_select_question_type_name }">
                                <span v-html="on_select_question_type_name" />
                              </template>
                            </v-select>
                            <small style="color:red; margin-top:-4px;position:absolute" v-if="type_required">{{ $t('label.type_of_question') + $t('is_required') }}</small>
                          </b-form-group>
                          <b-form-group>
                            <label for="form-choices">
                              {{ $t("label.choices") }}
                            </label>
                            <v-select
                              id="form-choices"
                              :options="getChoicesList"
                              v-model="form.choices"
                              name="form-choices"
                              label="base_name"
                              multiple
                            >
                              <template #spinner="{ loading }">
                                <b-spinner
                                  v-if="loading"
                                  class="text-info"
                                  small
                                  label="Small Spinner"
                                />
                              </template>

                              <template #list-header>
                                <li style="margin-left: 20px" class="mb-2">
                                  <b-link
                                    v-b-tooltip.hover
                                    :title="`${$t('label.new')}  ${$t(
                                      'label.choices'
                                    )}`"
                                    href="javascript:;"
                                    @click="show_child_modal('choice-add-modal')"
                                  >
                                    <b-spinner
                                      v-if="isAddQuestionType"
                                      small
                                      label="Small Spinner"
                                    />
                                    <i v-else class="fa fa-plus" aria-hidden="true" />
                                    {{ $t("label.choices") }}
                                  </b-link>
                                </li>
                              </template>
                              <template #option="{ on_select_choice_name }">
                                <span v-html="on_select_choice_name" />
                              </template>
                            </v-select>
                          </b-form-group>
                          <b-form-group>
                            <label for="form-evaluation-method">
                              {{ $t("label.evaluation_method") }}
                            </label>
                            <v-select
                              id="form-evaluation-method"
                              :options="evaluationMethod"
                              v-model="form.evaluation_method"
                              name="form-evaluation-method"
                              label="value"
                            >
                              <template #option="{ value }">
                                <span v-html="value" />
                              </template>
                            </v-select>
                            <small style="color:red; margin-top:-4px;position:absolute" v-if="evaluation_method_required">{{ $t('label.evaluation_method') + $t('is_required') }}</small>
                          </b-form-group>

                          <div v-if="form.evaluation_method.value === 'Test' && form.choices.length > 0">
                            <b-form-group >
                              <label for="form-correct-answer">
                                {{ $t("label.correct_answer") }} 
                              </label>
                              <v-select
                                id="form-correct-answer"
                                :options="form.choices"
                                v-model="form.correct_answer"
                                name="form-correct-answer"
                                label="base_name"
                                multiple
                              >
                                <template #option="{ on_select_choice_name }">
                                  <span v-html="on_select_choice_name" />
                                </template>
                              </v-select>
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
                    :type="modal.button.loading ? 'button' : 'submit'"
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
                        {{ $this.isUpdate ? $t($this.modal.edit.button.update) : $t($this.modal.add.button.save) }}
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </form>
          </v-card>

          <addQuestionTypeModal :parent="this"></addQuestionTypeModal>

          <addChoicesModal :parent="this"></addChoicesModal>
          
        </v-app>
        
      </b-modal>
    
  </div>
  
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import toast from "./../../../../helpers/toast.js";

import questionMixin from "./../mixins/questionMixin";

import i18n from '../../../../i18n.js';

import Form from "./../../../../shared/form.js";

import addQuestionTypeModal from "./addQuestiontype.vue";

import addChoicesModal from "./addChoices.vue";

let lang = i18n.locale;

export default {

  mixins: [toast],

  components: {
    addQuestionTypeModal,
    addChoicesModal
  },

  props: ["$this"],


  data: function() {
    return {

      modal: this.$this.modal[this.$this.isUpdate ? 'edit' : 'add'],

      questionTypeModal: {
            add: {
                name: this.$t('label.new_type_of_question'),

                id: "questionType-add-modal",

                isVisible: false,

                button: {
                    save: "save_button",

                    cancel: "cancel",

                    new: "questiontype_add_new_button",

                    loading: false,
                },
            },
      },

      choiceModal: {
            add: {
                name: "Add Choices",

                id: "choice-add-modal",

                isVisible: false,

                button: {
                    save: "save_button",

                    cancel: "cancel",

                    new: "choice_add_new_button",

                    loading: false,
                },
            },
      },

      type_of_data: [ "decimal", "integer", "choices", 'any'],

      isAddQuestionType: false,

      submitted: false,

      valid: true,

      modalId: this.$this.modal.id,

      keep_open: false,

      // form: new Form({
      //   id:null,
      //   question: "",
      //   type: {},
      //   choices: [],
      //   evaluation_method: {},
      //   correct_answer: null,
      //   url: null,
      //   file: null,
      // }),
      form: this.$this.form,

      formQuestionType: new Form({
        id: null,

        name: "",

        type_of_data: "",

        language: this.$i18n.locale,

        locale_name: "",

        translated_name: {},

        created_at: "",

        updated_at: "",
      }),

      formData: this.$this.formData,

      question_required: false,

      type_required: false,

      evaluation_method_required: false,


      // typeOfQuestionOption: [
      //   { value: 'all', text: "All", disabled: true },
      //   { value: "en", text: "No English Translation" },
      //   { value: "de", text: "No German Translation" },
      //   { value: "it", text: "No Italian Translation" },
      //   { value: "ph-fil", text: "No Filipino Translation" },
      //   { value: "ph-bis", text: "No Visayan Translation" },
      // ],
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
      this.$this.$bvModal.hide(modalId);
    },

    fieldRequiredDefaultValue(){
      this.question_required = false;
      this.type_required = false;
      this.evaluation_method_required = false;
    },

    formatChoices(choices){
      var vm = this;
      var strChoice = "";
      if (choices) {
        var inc = 0;
        choices.forEach(function(c){
          inc++;
          if (inc === 1) {
            strChoice += ""+c.id;
          }else{
            strChoice += "," + c.id;
          }
          
        });
        
      }
      return strChoice;
    },

    onSubmit() {
      var vm = this;
      vm.modal.button.loading = true;
      this.fieldRequiredDefaultValue()
      if(  this.form.question === '' || this.form.question === null ) {
        this.question_required = true;
      }

      if(Object.keys(this.form.type).length === 0) {
        this.type_required = true;
      }

      if(Object.keys(this.form.evaluation_method).length === 0) {
        this.evaluation_method_required = true;
      }

      if (!this.isValid) {
        vm.modal.button.loading = false;
        this.focusInput();
        return false;
      }

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("locale", this.$i18n.locale);

      
      // if(Object.keys(this.form.choices).length > 0) {
      //     this.formatChoices(this.form.choices)
      // }
      // console.log((Object.keys(this.form.correct_answer).length !== 0) ? this.formatChoices(this.form.correct_answer) : "0");
      // return false;
      formData.append("lang", this.form.language ? this.form.language : 'en');
      formData.append("question", this.form.question);
      formData.append("type", (Object.keys(this.form.type).length !== 0) ? this.form.type.id : 0);
      formData.append("choices", (Object.keys(this.form.choices).length !== 0) ? this.formatChoices(this.form.choices) : "0");
      formData.append("evaluation_method", this.form.evaluation_method.value);
      formData.append("correct_answer", (Object.keys(this.form.correct_answer).length !== 0) ? this.formatChoices(this.form.correct_answer) : "0");
      formData.append("is_active", this.form.is_active);
      formData.append("image", this.form.file);
      
      vm.storeQuestion(formData).then(function(res){
          vm.modal.button.loading = false;
          let message = {
              create:
                `${vm.form.question}` +
                vm.$t("created_message") +
                vm.$t("label.question"),
              title: {
                create: vm.$t("new_record_created"),
              },
            };
          if (!res.data.success && res.data.error === 'duplicate') {
            vm.makeToast(
              "danger",
              vm.$t("errors.duplicate"),
              vm.form.question + vm.$t("questions_question_exist")
            );
            vm.modal.button.loading = false;
            vm.form = {};
            return false;
          }

          if (res.data) {
            vm.$emit('done-create', res.data)

            vm.modalClose(vm.modal.id);

            vm.makeToast(
              "success",
              message.title.create,
              message.create
            );

            // vm.loadQuestions();
            vm.form.reset();
          }

          
      }).catch((error) => {
        console.log(error);
      });
    },

    onUpdate() {
      var vm = this;
      // var vm = this;
      this.modal.button.loading = true
      this.fieldRequiredDefaultValue()
      // this.type_of_data_required = false;
      // this.question_required = false;
      
      if(  this.form.question === '' || this.form.question === null ) {
        this.question_required = true;
      }

      if(Object.keys(this.form.type).length === 0) {
        this.type_required = true;
      }

      if(Object.keys(this.form.evaluation_method).length === 0) {
        this.evaluation_method_required = true;
      }

      if (!this.isValid) {
        vm.modal.button.loading = false;
        this.focusInput();
        return false;
      }

      let formData = new FormData();

      formData.append("id", this.form.id);

      formData.append("api_token", this.$user.api_token);
      formData.append("locale", this.$i18n.locale);

      formData.append("lang", this.form.language ? this.form.language : 'en');

      formData.append("question", this.form.question);
      formData.append("type", (Object.keys(this.form.type).length !== 0) ? this.form.type.id : 0);
      formData.append("choices", (Object.keys(this.form.choices).length !== 0) ? this.formatChoices(this.form.choices) : "0");
      formData.append("evaluation_method", this.form.evaluation_method.value);
      formData.append("correct_answer", (Object.keys(this.form.correct_answer).length !== 0) ? this.formatChoices(this.form.correct_answer) : "0");
      formData.append("is_active", this.form.is_active);

      formData.append("file", this.form.file);
      
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
            vm.$emit('done-update', res.data)

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
      this.form.file = event.target.files[0];

      this.form.url = URL.createObjectURL(this.form.file);
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
    show_child_modal(modalId){
      if (modalId === 'questionType-add-modal') {
        this.$bvModal.show('questionType-add-modal');
      }
      if (modalId === 'choice-add-modal') {
        this.$bvModal.show('choice-add-modal');
      }
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
    isValid(){
      if (this.question_required || this.type_required || this.evaluation_method_required) {
        return false
      }
      return true
    }
  },
  watch: {
    'form.choices': function(arrVal){
      var vm = this;
      var arrSelectedChoices = [];
      if (arrVal && arrVal.length > 0) {
        arrVal.forEach(function(choiceId){
          // var foundChoice = null;
          // foundChoice =  vm.getChoicesList.find(x => x.id === choiceId);
          arrSelectedChoices.push(choiceId);
        });
      }
      console.log(arrSelectedChoices, "arrSelectedChoices");
      // setTimeout(function(){
        vm.selectedChoices = arrSelectedChoices
      // },5000)
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
