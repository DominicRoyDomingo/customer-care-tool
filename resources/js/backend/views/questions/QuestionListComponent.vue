<template>
  <div class="animated fadeIn">
    <v-app id="tags__container">
 <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t("label.questions") }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createQuestion()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-plus</v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp; {{ $t("label.new") }} {{ $t("label.question") }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col
              cols="12"
              sm="6"
              md="2"
              class="caption font-weight-regular text--secondary text-right"
              style="display: flex; padding-top: 15px"
            >
              {{ $t("button.show") }}
              <b-form-select
                :options="showEntriesOpt"
                v-model="perPage"
                style="
                  border-radius: 0;
                  width: 40% !important;
                  margin-top: -8px;
                  margin-left: 5px;
                  margin-right: 5px;
                "
              />{{ $t("label.entries") }}
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <b-input-group-prepend>
                <v-menu offset-y :rounded="false">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="info"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      tile
                      depressed
                      style="
                        height: 33px;
                        min-width: 64px;
                        padding: 0 16px;
                        margin-right: 7px;
                        margin-top: -5px;
                      "
                    >
                      <v-icon :size="18"> mdi-filter-menu </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item
                        v-for="(option, index) in noTranslationsOptions"
                        :key="index"
                      >
                        <v-list-item-content @click="sortbyLang(option.value)">
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>
            </v-col>
            <v-spacer></v-spacer>

            <v-col cols="12" sm="6" md="6">
              <div class="float-right">
                <b-form inline style="margin-right: -8px" @submit.prevent="on_search_submit">
                  <el-input
                  prefix-icon="el-icon-search"
                  v-model="filter"
                  autocomplete="off"
                  :placeholder="$t('type_and_enter')"
                  clearable
                >
                </el-input>
                  <!-- <b-input-group class="mb-2 mr-sm-2">
                    <template #append>
                      <b-input-group-text>
                        <i class="fa fa-search" aria-hidden="true"></i>
                      </b-input-group-text>
                    </template>
                    <b-form-input
                      v-model="filter"
                      id="inline-form-input-username"
                      :placeholder="$t('search_here')"
                      style="width: 300px"
                      type="search"
                    />
                  </b-input-group> -->
                </b-form>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-container fluid>
              <b-table
                class="mb-0"
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="tableHeaders"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
                v-model="questionTable"
              >
              <!-- <b-table
                striped
                show-empty
                stacked="md"
                ref="table"
                :fields="tableHeaders"
                :current-page="currentPage"
                :per-page="perPage"
                :filter="filter"
                :busy="isLoading"
                :items="items"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                @sort-changed="sortChanged"
                v-model="questionTable"
              > -->
                <template v-slot:cell(question_name)="data">
                  <div style="margin-top: 10px">
                    <span
                      class="text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="data.item.question_name"
                    />
                  </div>
                </template>

                <template v-slot:cell(question_type_name)="data">
                  <div style="margin-top: 10px">
                    <span
                      class="text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="formatQuestionTypeName(data.item.question_type)"
                    />
                  </div>
                </template>

                <template v-slot:cell(status)="data">
                  <div style="margin-top: 10px">
                    <span class="text-left coloring text-gra">
                      {{
                        data.item.status.charAt(0).toUpperCase() +
                        data.item.status.slice(1)
                      }}
                    </span>
                  </div>
                </template>

                <!-- <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
                </template> -->

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="#c8ced3"
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        <span v-if="data.item.is_loading" class="text-center">
                          <v-progress-circular
                            size="20"
                            width="1"
                            color="red"
                            indeterminate
                          >
                          </v-progress-circular>
                        </span>
                        <span v-else>{{ $t("button.more_actions") }}</span>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item
                        link
                        v-for="action in actions"
                        :key="action.value"
                        @click="on_action(data.item, action.value, data.index)"
                        class="link-term-disabled"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                            class="d-icon"
                          />

                          <span
                            v-html="action.title"
                            :class="{
                                  'text-primary' : 
                                  action.value === 'link-questions' || 
                                  action.value === 'link-terminologies' || 
                                  action.value === 'link-brand'
                            }"
                            class="d-span"
                          />
                        </v-list-item-title>
                      </v-list-item>
                      <v-spacer></v-spacer>

                      <v-list-item link @click="onDelete(data.item, data.index)">
                        <v-list-item-title>
                          <b-icon icon="trash-fill" variant="danger" />

                          <span>
                            {{ $t("button.delete") }}
                          </span>
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>

                <template slot="empty">
                  <section class="section">
                    <div class="content has-text-grey has-text-centered">
                      <center>{{ $t("table.empty") }}</center>
                    </div>
                  </section>
                </template>
              </b-table>
            </v-container>
          </v-row>
          <v-row>
            <v-spacer>
              <v-col cols="12" sm="6" md="4">
                <span>
                  Showing
                  <strong v-text="questionTable.length" />
                  of
                  <strong v-text="totalRows" />
                  {{ $t("label.entries") }}
                </span>
              </v-col>
            </v-spacer>
            <v-col>
              <v-spacer>
                <b-pagination
                  v-if="!this.isLoading"
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  style="float: right"
                ></b-pagination>
              </v-spacer>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

     <linkToTermsModal :form='form' />
     <addQuestionModal :$this="this" @done-create="createdQuestion" @done-update="updatedQuestion"></addQuestionModal>

     <linkToBrandModal :parent='this' @done-create="createdQuestionBrand"/>
     <!-- <editQuestionModal :$this="this"></editQuestionModal>       -->

    </v-app>
  </div>
</template>

<script>
// imports here
import { mapActions, mapGetters } from "vuex";

import Loading from "vue-loading-overlay";

import Form from "./../../shared/form.js";

import toast from "./../../helpers/toast.js";

import choiceMixin from "./question-list/mixins/questionMixin";

// modal add
import linkToTermsModal from "./question-list/modals/linkToTermsModal.vue";

// modal link to brand
import linkToBrandModal from "./question-list/modals/linkToBrandModal.vue";

// modal add
import addQuestionModal from "./question-list/modals/addQuestion.vue";

// modal edit
// import editQuestionModal from "./question-list/modals/editQuestion.vue";

export default {
  mixins: [toast],
  components: {
    linkToTermsModal,
    addQuestionModal,
    linkToBrandModal
  },
 data: function () {
    return {
      isUpdate: false,

      isLoading: true,

      btnloading: false,

      filter: "",

      tableTotalRows: "",

      searched: "",

      currentPage: 1,

      perPage: this.finalPerPage ?? 10,

      sortBy: "name",

      files: [],

      items: [],

      itemsSelected: {},

      sortDesc: false,

      showEntriesOpt: [
        { value: 10, text: "10" },

        { value: 30, text: "30" },

        { value: 50, text: "50" },

        { value: 100, text: "100" },
      ],
      questionTable: [],
      tableHeaders: [
        {
          key: "question_name",
          label: this.$t("table.question"),
          thClass: "text-left",
          tdClass: "text-left",
          sortable: true,
        },
        {
          key: "question_type_name",
          label: this.$t("table.type"),
          thClass: "text-left",
          tdClass: "text-left",
          sortable: true,
        },
        {
          key: "status",
          label: this.$t("table.status"),
          thClass: "text-left",
          tdClass: "text-left",
          sortable: true,
        },
        // {
        //   key: "created_at",
        //   label: this.$t("table.created_at"),
        //   thClass: "text-left",
        //   tdClass: "text-left",
        //   thStyle: { width: "10%" },
        //   sortable: true,
        // },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
      ],

      actions: [
        {
          value: "edit",
          title: this.$t("button.edit"),
          icon: "pencil-square",
          variant: "success",
        },
        {
          value: "link-questions",
          title: this.$t("button.link_questions"),
          icon: "link45deg",
          variant: "primary",
        },
        {
          value: "link-terminologies",
          title: this.$t("button.link_to_terms"),
          icon: "link45deg",
          variant: "primary",
        },
        {
          value: "link-brand",
          title: this.$t("button.link_to_brand"),
          icon: "link45deg",
          variant: "primary",
        },
      ],

      modal: {
        add: {
              name: this.$t("label.new") + " " +this.$t("label.question"),

              id: "question-add-modal",

              isVisible: false,

              button: {
                save: "save_button",

                cancel: "cancel",

                new: "questionType_add_new_button",

                loading: false,
              },
        },
        edit: {
              name: this.$t("label.edit_question"),

              id: "question-edit-modal",

              isVisible: false,

              button: {
                update: "button.save_change",

                cancel: "cancel",

                new: "question_add_new_button",

                loading: false,
              },
        },
      },

      type_of_data: ["decimal","integer","choices","any"],

      questionId: null,

      questionName: "",

      form: new Form({
          id: null,

          url: null,

          question: "",

          type: {},

          choices: [],

          status: "",

          terms: "",

          locale_question: "",

          translated_question: {},

          image: "",

          evaluation_method: {},

          file: null,

          created_at: "",

          updated_at: "",

          language: this.$i18n.locale,

          correct_answer: [],
      }),

      brands: [],

      formData: new FormData(),

      link_option: "",

      title: "",

      description: "",

      convertion: "",

      language: "",

      sortbyLangId: "",

        url: null,

      noTranslationsOptions: [
        { value: "all", text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],
      filter_select: false,
    };
  },

  mounted() {
    this.loadItems();
  },

  methods: {
    ...mapActions("questions", [
      "getQuestions"
    ]),

    ...mapActions("brand", ["get_brands"]),

    createQuestion() {
      this.isUpdate = false;
      // let params = {
      //   api_token: this.$user.api_token,
      //   lang: this.$i18n.locale
      // };
      this.form.reset();
      this.$bvModal.show(this.modal.add.id);
    },

    sortChanged(e) {
      console.log("sort");
    },

    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },
    loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filterBrand,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {});
    },
    loadItems() {

      let params = {
        ...this.defaultParams,
        ...this.set_params_detail()
      };

      this.isLoading = true;
      this.getQuestions(params)
        .then((response) => {
          this.isLoading = false;
          this.items = response;
        });
    },
    closeAddQuestionModal() {
      this.$bvModal.hide("brand-modal");
    },
    showTermModal(item, index, opt){
      this.form.id = item.id
      Object.assign(this.form, { base_name : item.base_name })
      this.$bvModal.show("linkterm-modal");
    },

    showBrandModal(item, index, opt){
      this.form.id = item.id
      this.$bvModal.show("linkbrand-modal");
    },

    showLinkQuestionModal(item, index, opt){
      this.form.id = item.id
      this.$bvModal.show("linkquestion-modal");
    },
    linkTo(item, index, opt){
      
    },
    onEdit(item, index) {
        this.isUpdate = true;

        let questionByIdData      = {...this.questionById(item.id)};
        this.form.reset();
        this.form.id                  = questionByIdData.id;
        this.form.question            = questionByIdData.translated_question[this.$i18n.locale] ?? "";
        this.form.type                = item.question_type;

        let choices = {};

        let correct_answer = [];

        if (questionByIdData.question_links) {
        //   // get the choices from  question links
            choices = questionByIdData.question_links.map(function (value, index, array) {
              return value.choice; 
            });
            // // get the correct answer from question links 
            correct_answer = questionByIdData.question_links.map(function (value, index, array) {
              if (value.iscorrect === 1) {
                return value.choice
              }
            });

            // remove empty
            correct_answer = correct_answer.filter(function (el) {
              return el != null;
            });

            // if (correct_answer) {
            //   correct_answer = correct_answer.choice
            // }
        }

        let evalMethod = { value: questionByIdData.evaluation_method, text: questionByIdData.evaluation_method };
        if (choices[0] === null) {
          choices = [];
        }
        
        this.form.language            = this.$i18n.locale;
        this.form.choices             = choices;
        this.form.is_active           = (questionByIdData.status).toLowerCase() === 'active' ? 1 : 0;
        this.form.evaluation_method   = evalMethod;
        this.form.correct_answer      = correct_answer;
        this.form.url                 = questionByIdData.image;

        let params = {
          ...{id: item.id},
          ...this.defaultParams
        };
        this.$store.dispatch('questions/setCurrentQuestion', params)

      this.$bvModal.show(this.modal.add.id);
      // this.$bvModal.show(this.modal.edit.id);
    },
    onDelete(item, index) {
      let vm = this;

      $.confirm({
        title: vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.question_name}` +
          vm.$t("from") +
          vm.$t("label.question") +
          vm.$t("records") +
          "?",
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function (value) {
              if (value) {
                // item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.isLoading = false;
                vm.$store.dispatch('questions/deleteQuestion', params).then((response) => {
                  var resp = response;

                  vm.isLoading = false;
                  if (resp.data == false) {
                    vm.makeToast(
                      "danger",
                      vm.$t("data_used"),
                      vm.$t("used_data_alert")
                    );
                    return false;
                  }

                  vm.loadItems();

                  vm.makeToast(
                    "danger",
                    vm.$t("delete_record"),
                    `${item.base_name}` +
                      vm.$t("delete_record_message") +
                      vm.$t("label.question") +
                      vm.$t("records")
                  );
                })
                .catch((error) => {
                  console.log(error);
                });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function () {},
          },
        },
      });
    },
    set_params_detail() {
      return {
        filter: this.filter,
        sortbyLang: this.sortbyLangId !== "" ? this.sortbyLangId : 'all',
        perPage: this.perPage,
        page: this.currentPage,
      };
    },

    on_search_submit(value) {
      this.currentPage = 1;
      this.loadItems();
    },

    formatQuestionTypeName(qtn){
      if (qtn) {
        return qtn.question_type_name
      }
      return ""
    },
    createdQuestion(e){
      this.currentPage = 1;
      this.filter = "";
      this.loadItems();
    },
    updatedQuestion(e){
      this.currentPage = 1;
      this.filter = "";
      this.loadItems();
    },
    on_action(item, title, index) {
      if (title === 'edit') {
        this.onEdit (item, index)
      }

      if (title === 'link-terminologies') {
        this.showTermModal(item, index, this.$t('button.link_to_terminologies'))
      }

      if (title === 'link-brand') {
        this.questionId = item.id;
        this.questionName = item.base_name;

        this.brands = [];
        this.brands = item.brand;
        
        this.itemsSelected = item;

        this.showBrandModal(item, index)
      }

      if (title === 'link-questions') {
        this.questionId = item.id;
        this.questionName = item.base_name;

        this.brands = [];
        this.brands = item.brand;

        this.showLinkQuestionModal(item, index)
      }
    },
    createdQuestionBrand(e){
      this.loadItems()
    }
  },
  computed: {
    ...mapGetters("questions", {
      tableSettings: "questionList",
      questionListItems: "questionListItems",
      questionById: "questionById",
    }),

    ...mapGetters("brand", {
      itemBrands: "brands",
    }),

    totalRows() {
      return this.tableSettings.total
    },

    finalPerPage: {
      get: function () {
        return this.tableSettings.per_page 
      },
      set: function (newValue) {
        this.perPage = this.newValue;
      }
    },

    defaultParams() {
       return {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          sortbyLang: this.sortbyLangId,
       }
    },
  },
  watch: {
    perPage: function(currentVal) {
      this.perPage = currentVal;
      this.loadItems()
    },
    currentPage: function(currentVal) {
      var vm = this;
      this.currentPage = currentVal;
        vm.loadItems()
    },
    filter: function(currentVal){
      this.currentPage = 1;
      this.loadItems();
    }
  }
};
</script>

<style lang="scss">
th:nth-last-child(2) {
  text-align: center;
}
.description_text {
  width: 200;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

th:nth-last-child(3) {
  text-align: center;
}
</style>
