 <style>
.hidden_header {
  display: none;
}
</style>
 <template>
  <div>
    <b-modal
      id="organie-question-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card class="border-0">
          <v-card-title class="bg-light text-uppercase">
            {{ $t("organize") }} {{ $t("question") }}
            <small
              class="d-inline-block text-truncate ml-1 mt-1"
              style="max-width: 700px; letter-spacing: normal"
              v-html="`(${item.questionnaire_name})`"
            />
            <v-spacer></v-spacer>
            <v-btn icon color="error lighten-2" @click="on_close">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-container>
            <div class="row">
              <div class="col-md-12">
                <div class="form-row d-flex justify-content-center">
                  <div class="form-group col-md-9">
                    <label for="question">
                      {{ $t("question") }}
                      <span class="text-danger">*</span>
                      <small class="text-muted">
                        Choose &amp; Add to sequence question.
                      </small>
                    </label>
                    <v-select
                      id="question"
                      :options="questions"
                      v-model="form.question"
                      name="question"
                      label="base_name"
                      :disabled="selectLoading"
                      :loading="selectLoading"
                      :placeholder="`Choose ${$t('question')}`"
                    >
                      <template #spinner="{ loading }">
                        <b-spinner
                          v-if="loading"
                          class="text-info"
                          small
                          label="Small Spinner"
                        />
                      </template>

                      <!-- <template #list-header>
                            <li style="margin-left: 20px" class="mb-2">
                              <b-link
                                v-b-tooltip.hover
                                :title="`${$t('label.new')}  ${$t(
                                  'label.type_of_term'
                                )}`"
                                href="javascript:;"
                                @click="show_child_modal('term-type-modal')"
                              >
                                <b-spinner
                                  v-if="isAddTermType"
                                  small
                                  label="Small Spinner"
                                />
                                <i
                                  v-else
                                  class="fa fa-plus"
                                  aria-hidden="true"
                                />
                                {{ $t("label.type_of_term") }}
                              </b-link>
                            </li>
                          </template> -->
                      <template #option="{ question_name }">
                        <li class="text-capitalize" v-html="question_name" />
                      </template>
                    </v-select>
                  </div>
                  <div class="form-group col-md-2" v-if="form.question">
                    <label>&nbsp;</label>
                    <b-button
                      block
                      size="sm"
                      variant="success"
                      class="text-white mt-1"
                      @click="on_link_questions"
                      :disabled="btnLoading"
                    >
                      <b-spinner
                        v-if="btnLoading"
                        small
                        label="Small Spinner"
                      />
                      {{ $t("label.add") }}
                    </b-button>
                  </div>
                </div>
                <hr />
              </div>
            </div>

            <div class="row mt-3 pr-5 pl-5" v-if="totalRows > 0">
              <div class="col-sm-6">
                <b-form inline class="mb-0">
                  <label class="mr-sm-2 text-capitalize">
                    {{ $t("show") }}
                  </label>
                  <b-form-select
                    class="mb-2 mr-sm-2 mb-sm-0"
                    :options="showEntriesOpt"
                    v-model="perPage"
                  />
                  <label class="mr-sm-2" v-text="$t('label.entries')" />
                </b-form>
              </div>
              <div class="col-sm-6">
                <b-pagination
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  :align="'right'"
                />
              </div>
              <div class="col-sm-12 mt-0">
                <b-overlay
                  id="overlay-background"
                  :show="isLinked"
                  :variant="'light'"
                  opacity="0.85"
                  :blur="'1px'"
                  rounded="sm"
                >
                  <ul
                    class="list-unstyled ml-0"
                    style="margin-top: -20px"
                    v-sortable="{ onEnd: reorder }"
                  >
                    <li v-for="(seq, index) in sequences.data" :key="index">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            {{ seq.pivot.sequence }}
                          </span>
                        </div>
                        <input
                          disabled
                          type="text"
                          class="form-control text-capitalize"
                          :value="seq.base_name"
                        />
                        <div class="input-group-append">
                          <b-button
                            v-b-tooltip.hover
                            title="Remove"
                            variant="lignt"
                            @click="on_delete(seq)"
                          >
                            <b-spinner
                              v-if="seq.is_loading"
                              small
                              label="Small Spinner"
                            />
                            <b-icon v-else icon="x" class="text-danger" />
                          </b-button>
                        </div>
                      </div>
                    </li>
                  </ul>
                </b-overlay>
              </div>
            </div>

            <div class="alert alert-light text-center" v-else role="alert">
              <i class="text-danger"> {{ $t("no_record_found") }}. </i>
            </div>
            <br />
          </v-container>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import Form from "../../../helpers/form";
import qMixins from "../mixins/qMixins";
import {
  get_question_sequence,
  get_questions,
  delete_question_sequence,
  sort_question_sequence,
  post_question_sequence,
} from "./../mixins/query";
export default {
  props: {
    item: { type: Object },
  },
  mixins: [qMixins],
  data() {
    return {
      btnLoading: false,
      isLinked: false,
      selectLoading: true,
      currentPage: 1,
      perPage: 10,
      questions: [],
      sequences: {},
      form: new Form({
        id: "",
        question: "",
      }),
    };
  },
  computed: {
    totalRows() {
      return this.sequences.total;
    },
  },

  watch: {
    perPage() {
      this.isLinked = true;
      this.load_question_sequence();
    },
    currentPage() {
      this.isLinked = true;
      this.load_question_sequence();
    },
  },

  created() {
    this.load_question_sequence();
    this.load_questions();
  },

  methods: {
    reorder({ oldIndex, newIndex }) {
      const movedItem = this.sequences.data.splice(oldIndex, 1)[0];
      this.sequences.data.splice(newIndex, 0, movedItem);

      let params = {
        ...this.dParams,
        questionnaire_id: this.item.id,
        sequences: this.set_sequence_json(this.sequences.data),
      };

      // Sort questions
      this.isLinked = true;
      sort_question_sequence(params).then((_) => {
        this.sequences.data = [];
        this.load_question_sequence();
      });
    },

    load_questions() {
      let params = {
        ...this.dParams,
        perPage: this.qPerPage,
        currentPage: this.qCurrentPage,
      };

      get_questions(params)
        .then((data) => {
          this.questions = data;
        })
        .finally(() => {
          this.selectLoading = false;
        });
    },

    load_question_sequence() {
      let params = {
        ...this.dParams,
        id: this.item.id,
        perPage: this.perPage,
        page: this.currentPage,
      };

      get_question_sequence(params)
        .then((data) => {
          this.sequences = data;
        })
        .finally(() => {
          this.isLinked = false;
        });
    },

    on_delete(item) {
      let baseName = item.base_name;
      let records = this.$t("questionnaires") + " Content";

      this.delete_confirm(baseName, records).then((resp) => {
        if (resp) {
          item.is_loading = true;
          let params = {
            ...this.dParams,
            id: this.item.id,
            questionnaire_content_id: item.pivot.id,
          };
          delete_question_sequence(params)
            .then((_) => {
              this.to_notify("delete", baseName);
              this.isLinked = true;
              this.load_question_sequence();
            })
            .catch((error) => console.log(error));
        }
      });
    },

    on_link_questions() {
      this.btnLoading = true;
      let params = {
        ...this.dParams,
        question_id: this.form.question ? this.form.question.id : "",
        questionnaire_id: this.item.id,
      };
      post_question_sequence(params)
        .then((data) => {
          this.isLinked = true;
          this.load_question_sequence();
          this.to_notify("add", this.form.question.base_name);
          this.on_reset();
        })
        .finally(() => {
          this.btnLoading = false;
        });
    },

    to_notify(action, name) {
      let variant = "success";
      let title = this.$t("linked");
      let message = `${name} ${this.$t("has_been_addedd")} ${
        this.item.base_name
      }`;

      if (action === "delete") {
        variant = "danger";
        title = this.$t("delete_record");
        message = `${name} ${this.$t("has_been_remove")}  ${
          this.item.base_name
        }`;
      }

      this.makeNodeToast(variant, title, message);
    },

    on_reset() {
      this.form.reset();
    },

    on_close() {
      this.$emit("on-close");
    },
  },
};
</script>
 