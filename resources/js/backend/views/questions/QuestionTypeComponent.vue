<template>
<div class="animated fadeIn">
    <v-app id="tags__container">
      <v-card tile flat>
        <v-card-title>
          <v-row>
            <v-col>
              <span class="title font-weight-light text--secondary">
                {{ $t('label.type_of_questions') }}
              </span>
            </v-col>
            <v-spacer> </v-spacer>
            <v-col>
              <v-btn
                color="success"
                @click="createQuestionType()"
                class="float-right"
                tile
              >
                <v-icon dark>mdi-plus</v-icon>
                <!-- <v-icon small dark style="margin-top: 15px; margin-left: -3px;">
                  mdi-plus
                </v-icon> -->
                &nbsp;
                {{ $t("button.new_type") }}
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col  cols="12" sm="6" md="2"
                class="caption font-weight-regular text--secondary text-right"
                style="display:flex; padding-top: 15px"
              >
                {{ $t("button.show") }}
                <b-form-select
                  :options="showEntriesOpt"
                  v-model="perPage"
                  style="border-radius: 0; width: 40% !important; margin-top: -8px; margin-left: 5px; margin-right: 5px"
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
                      style="height: 33px; min-width: 64px; padding: 0 16px; margin-right: 7px; margin-top: -5px;"
                    >
                      <v-icon :size="18">
                        mdi-filter-menu
                      </v-icon>
                    </v-btn>
                  </template>
                  <v-list dense class="profile__row-menu">
                    <v-list-item-group color="primary">
                      <v-list-item v-for="(option, index) in noTranslationsOptions" :key="index">
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
                <b-form inline style="margin-right:-8px" @submit.prevent="on_search_submit">
                  <b-input-group class="mb-2 mr-sm-2">
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
                  </b-input-group>
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
                v-model="questionTypeTable"
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
                v-model="questionTypeTable"
              > -->

                <template v-slot:cell(name)="data">
                  <div style="margin-top: 10px">
                    <span
                      class="text-capitalize p-1 mr-1 mb-1"
                      style="font-size: 14px"
                      v-html="data.item.question_type_name"
                    />
                  </div>
                </template>

                <template v-slot:cell(actions)="data">
                  <span>
                    <b-button size="sm" variant="secondary" @click="onEdit(data.item, data.index)">
                      <i class="fas fa-edit text-success"></i>
                      {{$t('button.edit').toUpperCase()}}
                    </b-button>
                    <b-button size="sm" variant="secondary" @click="onDelete(data.item)">
                      <b-spinner v-if="data.item.is_loading" small label="Small Spinner" type="grow"></b-spinner>
                      <span v-else>
                        <i class="fas fa-trash text text-danger"></i>
                        {{$t('button.delete').toUpperCase()}}
                      </span>
                    </b-button>
                  </span>
                </template>

                <template v-slot:cell(created_at)="data">
                  <span>{{ data.item.created_at | toLocaleString }}</span>
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
                  <strong v-text="questionTypeTable.length" />
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

      <addQuestionTypeModal :$this="this"></addQuestionTypeModal>

      <editQuestionTypeModal :$this="this"></editQuestionTypeModal>
    </v-app>
  </div>
  
</template>

<script>
// imports here
import { mapActions, mapGetters } from "vuex";

import Loading from "vue-loading-overlay";

import Form from "./../../shared/form.js";

import toast from "./../../helpers/toast.js";

// modal add
import addQuestionTypeModal from "./question-type/modals/addQuestiontype.vue";

// modal edit
import editQuestionTypeModal from "./question-type/modals/editQuestiontype.vue";

export default {
  mixins: [toast],
  components: {
    addQuestionTypeModal,
    editQuestionTypeModal
  },
  data: function() {
    return {
        isLoading: true,

        btnloading: false,

        filter: "",

        tableTotalRows: "",

        searched: "",

        currentPage: 1,

        perPage: 10,

        sortBy: 'name',

        files: [],

        sortDesc: false,

        showEntriesOpt: [
            { value: 10, text: "10" },

            { value: 30, text: "30" },

            { value: 50, text: "50" },

            { value: 100, text: "100" },
        ],
        questionTypeTable: [],
        tableHeaders: [
            {
            key: "name",
            label: this.$t("table.type_of_question"),
            thStyle: { width: "40%" },
            thClass: "text-left",
            tdClass: "text-left",
            sortable: true,
            },
            {
            key: "type_of_data",
            label: this.$t("table.type_of_data"),
            thStyle: { width: "10%" },
            thClass: "text-left",
            tdClass: "text-left",
            sortable: true,
            },

            {
            key: "actions",
            label: this.$t("table.action"),
            thClass: "text-center",
            thStyle: { width: "30%" },
            tdClass: "text-center",
            },
        ],
        modal: {
            add: {
                name: this.$t('label.new_type_of_question'),

                id: "questionType-add-modal",

                isVisible: false,

                button: {
                    save: "save_button",

                    cancel: "cancel",

                    new: "questionType_add_new_button",

                    loading: false,
                },
            },
            edit: {
                name: "Edit Type of Question",

                id: "questionType-edit-modal",

                isVisible: false,

                button: {
                    update: "button.save_change",

                    cancel: "cancel",

                    new: "questionType_add_new_button",

                    loading: false,
                },
            },

        },

        type_of_data: [ "decimal", "integer", "choices", 'any'],

        form: new Form({
          id: null,

          name: "",

          type_of_data: "",

          language: this.$i18n.locale,

          locale_name: "",

          translated_name: {},

          created_at: "",

          updated_at: "",
        }),

        formData: new FormData(),

        link_option: '',

        title: '',

        description: '',

        convertion: '',

        language: '',
      
        sortbyLangId: '',

        noTranslationsOptions: [
          { value: 'all', text: "All", disabled: true },
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
    ...mapActions("question_types", [
      "getQuestionTypes"
    ]),

    createQuestionType() {
      // let params = {
      //   api_token: this.$user.api_token,
      //   lang: this.$i18n.locale
      // };
      this.$bvModal.show("questionType-add-modal");
    },

    sortChanged(e) {
      console.log('sort');
    },

    sortbyLang(value) {
      this.sortbyLangId = value;
      this.loadItems();
    },

    loadItems(){
      this.isLoading = true;
      
      let params = {
        ...this.defaultParams,
        ...this.set_params_detail()
      };

      this.getQuestionTypes(params).then((response) => {
          this.isLoading = false;
      });
    },
    closeAddQuestionTypeModal() {
      this.$bvModal.hide("brand-modal");
    },
    onEdit(item, index) {
        this.form.reset();
        let questionTypeById      = {...this.questionTypeById(item.id)};

        this.form.id              = questionTypeById.id;
        this.form.name            = item.translated_name[this.$i18n.locale] ?? "";
        this.form.type_of_data    = questionTypeById.type_of_data;
        this.form.language        = this.$i18n.locale;
        this.form.locale_name     = questionTypeById.locale_name;
        this.form.translated_name = questionTypeById.translated_name;
        this.form.created_at      = questionTypeById.created_at;
        this.form.updated_at      = questionTypeById.updated_at;
        
        let params = {
          api_token: this.$user.api_token,
          id: this.form.id
        };
        this.$store.dispatch('question_types/setCurrentQuestionType', params)
        
        this.$bvModal.hide(this.modal.add.id);
        this.$bvModal.show(this.modal.edit.id);
    },
    onDelete(item, index) {
      let vm = this;

      $.confirm({
        title:
          vm.$t("confirmation_record_delete"),
        content:
          vm.$t("question_record_delete") +
          `${item.question_type_name}` +
          vm.$t("from") +
          vm.$t("label.type_of_question") +
          vm.$t("records") + '?',
        type: "red",
        typeAnimated: true,
        columnClass: "medium",
        buttons: {
          yes: {
            text: vm.$t("yes"),
            btnClass:
              "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
            action: function(value) {
              if (value) {
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.isLoading = false;
                vm.$store.dispatch('question_types/deleteQuestionType', params).then((response) => {  
                    var resp = response;

                    vm.isLoading = false;
                    if(resp.data == false) {
                      vm.makeToast(
                        "danger",
                        vm.$t("data_used"),
                        vm.$t("used_data_alert")
                      );
                      return false;
                    }

                    vm.loadItems()

                    vm.makeToast(
                      "danger",
                      vm.$t("delete_record"),
                      `${item.base_name}` +
                        vm.$t("delete_record_message") +
                        vm.$t("label.type_of_question") +
                        vm.$t("records")
                    );
                }).catch((error) => {
                    console.log(error);
                  });
              }
            },
          },
          no: {
            text: vm.$t("no"),
            btnClass:
              "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
            action: function() {},
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
  },
  computed: {
    ...mapGetters("question_types", {
      items: "questionTypeList",
      questionTypeById: "questionTypeById",
      tableSettings: "tableSettings"
    }),

    totalRows() {
      return this.tableSettings.total;
    },

    defaultParams() {
       return {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          filter: this.filter,
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
  }
};
</script>

<style lang="scss">
th:nth-last-child(2) {
  text-align: center;
}
.description_text{
  width: 200;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

th:nth-last-child(3) {
  text-align: center;
}
</style>
