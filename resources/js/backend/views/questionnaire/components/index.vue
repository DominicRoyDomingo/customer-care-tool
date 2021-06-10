<template>
  <div class="animated fadeIn">
    <div class="card border-0">
      <div class="card-body">
        <div class="row">
          <v-col cols="12" sm="10" md="10">
            <h4 class="card-title">
              {{ $t("question") }}
              <small class="text-muted text-capitalize">
                {{ $t("questionnaires") }}
              </small>
            </h4>
          </v-col>
          <v-col cols="12" sm="2" md="2">
            <v-btn color="success" block tile @click="on_add">
              <i class="fas fa-plus"></i>
              &nbsp;{{ $t("button.new") }}
              {{ $t("questionnaire") }}
            </v-btn>
          </v-col>
        </div>

        <div class="row mt-4 mb-0">
          <div class="col-md-8">
            <b-form inline>
              <label
                class="mr-sm-2 text-capitalize"
                for="inline-form-custom-select-pref"
              >
                {{ $t("show") }}
              </label>
              <b-form-select
                id="inline-form-custom-select-pref"
                class="mb-2 mr-sm-2 mb-sm-0"
                :options="showEntriesOpt"
                v-model="perPage"
              />
              <label class="mr-sm-2" for="inline-form-custom-select-pref">
                {{ $t("label.entries") }}
              </label>

              <b-input-group-prepend
                style="margin-left: 10px; margin-top: -10px"
              >
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
                        margin-top: 5px;
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
                        <v-list-item-content
                          @click="on_sort_language(option.value)"
                        >
                          <v-list-item-title>
                            {{ option.text }}
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-menu>
              </b-input-group-prepend>
            </b-form>
          </div>
          <div class="col-md-4">
            <div class="">
              <b-form inline @submit.prevent="on_search_submit">
                <el-input
                  v-model="filter"
                  autocomplete="off"
                  :placeholder="$t('type_and_enter')"
                  clearable
                >
                  <el-button
                    slot="append"
                    :disabled="!filter"
                    @click="on_search_submit"
                  >
                    <i class="fas fa-search"></i>
                  </el-button>
                </el-input>
              </b-form>
            </div>
          </div>
        </div>

        <div class="row mb-0">
          <div class="col-md-12 mb-0">
            <b-overlay
              id="overlay-background"
              :show="isLinked"
              :variant="'light'"
              opacity="0.85"
              :blur="'1px'"
              rounded="sm"
            >
              <b-table
                class="mb-0"
                striped
                show-empty
                :empty-text="$t('no_record_found')"
                :fields="fields"
                :per-page="perPage"
                :busy="isLoading"
                :items="items"
              >
                <template v-slot:table-busy>
                  <div class="d-flex justify-content-center p-2">
                    <b-spinner label="Small Loading..."></b-spinner>
                  </div>
                </template>

                <template v-slot:cell(question_name)="data">
                  <strong
                    class="text-muted"
                    v-html="data.item.questionnaire_name"
                  />
                </template>

                <template v-slot:cell(created_at)="data">
                  {{ data.item.created_at | toLocaleString }}
                </template>

                <template v-slot:cell(actions)="data">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="#c8ced3"
                        small
                        v-bind="attrs"
                        v-on="on"
                        :disabled="data.item.is_loading"
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
                        @click="on_action(data.item, action.value)"
                      >
                        <v-list-item-title>
                          <b-icon
                            :icon="action.icon"
                            :variant="action.variant"
                          />
                          <span v-html="action.title" />
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </b-table>
            </b-overlay>
          </div>
        </div>

        <div class="row mt-0" v-if="total_rows > 0">
          <div class="col-md-6">
            <span>
              {{ $t("showing") }}
              <strong v-text="showing.from" />
              {{ $t("label.to") }}
              <strong v-text="showing.to" />
              of
              <strong v-text="showing.total" />
              {{ $t("label.entries") }}
            </span>
          </div>
          <div class="col-md-6">
            <b-pagination
              v-model="currentPage"
              :total-rows="total_rows"
              :per-page="perPage"
              align="right"
            />
          </div>
        </div>
      </div>
    </div>

    <CreateQuestionModal
      v-if="modal.create.show"
      :item="item"
      :action="action"
      @on-success="on_success"
      @on-close="on_close"
    />

    <OrganizeQuestionModal
      v-if="modal.organize.show"
      :item="item"
      @on-success="on_success"
      @on-close="on_close"
    />
  </div>
</template>

<script>
import Qmixin from "./../mixins/qMixins";
import { fetch_list, delete_item } from "./../mixins/query";
import CreateQuestionModal from "./../modals/create-question-modal";
import OrganizeQuestionModal from "./../modals/organize-question.modal";
export default {
  mixins: [Qmixin],
  components: { CreateQuestionModal, OrganizeQuestionModal },
  data() {
    return {
      isCreatedTerm: false,
      isLoading: true,
      btnloading: false,
      isLinked: false,

      perPage: 10,
      currentPage: 1,
      total_rows: 0,
      showing: { to: 0, from: 0, total: 0 },

      sortbyLang: "",
      filter: "",
      action: "",
      item: {},
      modal: {
        create: {
          show: false,
        },
        organize: {
          show: false,
        },
      },

      items: [],
    };
  },

  created() {
    this.load_question_items();
  },

  watch: {
    filter(value) {
      if (!value) {
        this.isLinked = true;
        this.load_question_items();
      }
    },
    perPage(value) {
      this.isLinked = true;
      this.load_question_items();
    },
    currentPage(value) {
      this.isLinked = true;
      this.load_items();
    },
  },
  methods: {
    load_question_items() {
      let params = {
        ...this.dParams,
        perPage: this.perPage,
        page: this.currentPage,
        filter: this.filter,
        sortbyLang: this.sortbyLang,
      };
      fetch_list(params)
        .then((resp) => {
          this.items = resp.data;
          this.showing.to = resp.to;
          this.showing.from = resp.from;
          this.showing.total = resp.total;
          this.total_rows = resp.total;
        })
        .catch((error) => console.log(error))
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
    },

    on_action(item, title) {
      switch (title) {
        case "edit":
          this.on_edit(item);
          break;
        case "delete":
          this.on_delete(item);
          break;
        case "organize":
          this.on_organize(item);
          break;
      }
    },

    on_add() {
      this.modal.create.show = true;
      this.isLinked = true;
      this.action = "add";

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("question-modal");
      }, 1000);
    },

    on_edit(item) {
      this.modal.create.show = true;
      this.isLinked = true;
      this.action = "edit";
      this.item = item;

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("question-modal");
      }, 1000);
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = this.$t("questionnaires");

      this.delete_confirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          delete_item({ ...this.dParams, id: item.id })
            .then((_) => {
              this.deleteToast(baseName, records);
              this.items.splice(this.array_index(this.items, item.id), 1);
            })
            .catch((error) => console.log(error));
        }
      });
    },

    on_organize(item) {
      this.modal.organize.show = true;
      this.isLinked = true;
      this.item = item;

      setTimeout(() => {
        this.isLinked = false;
        this.$bvModal.show("organie-question-modal");
      }, 1000);
    },

    on_search_submit() {
      this.isLinked = true;
      this.load_question_items();
    },

    on_success(item) {
      const records = this.$t("questionnaires");
      if (this.action === "add") {
        this.storeToast(item.questionnaire_name, records);
      } else {
        this.updateToast(item.id, records);
      }

      this.isLinked = true;
      this.load_question_items();
    },

    on_close() {
      this.$bvModal.hide("question-modal");
      this.$bvModal.hide("organie-question-modal");

      setTimeout(() => {
        this.item = {};
        this.modal.create.show = false;
        this.modal.organize.show = false;
      }, 700);
    },

    on_sort_language(value) {
      this.sortbyLang = value !== "all" ? value : "";
      this.isLinked = true;
      this.load_question_items();
    },
  },
};
</script>
