<template>
  <div class="term-note">
    <b-modal
      id="term-note-modal"
      hide-footer
      hide-header
      no-close-on-backdrop
      size="lg"
    >
      <div class="card mb-0">
        <div
          class="card-header py-2 d-flex flex-row align-items-center justify-content-between"
        >
          <h5 class="m-0 font-weight-bold">
            {{ $t("table.add_notes") }}
            <small class="text-capitalize" v-html="`(${term.term_name})`" />
          </h5>
          <div class="no-arrow">
            <v-btn icon color="error lighten-2" @click="on_cancel">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </div>
        </div>
        <div class="card-body">
          <form @submit.prevent="on_form_submit">
            <div class="form-group" v-if="term.note">
              <a
                href="javascript:;"
                class="float-right"
                v-if="!isEdit"
                @click="on_edit"
              >
                <i class="fas fa-edit"></i>
              </a>
              <div v-else>
                <div class="form-inline">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    Language
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="language"
                    :options="parent.languageOptions"
                  />
                </div>
                <hr />
              </div>
            </div>
            <div class="form-group">
              <label for="note">
                {{ this.$t("label.notes") }}
              </label>
              <textarea
                name="note"
                v-model="form.note"
                id="note"
                rows="5"
                class="form-control"
                @keydown="form.errors.clear('note')"
                :disabled="textDisable"
              />
              <small
                v-if="form.errors.has('note')"
                id="helpId"
                class="form-text text-danger"
                v-text="form.errors.get('note')"
              />
            </div>
            <div class="form-group">
              <div class="float-right">
                <v-btn color="error" text tile @click="on_cancel">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                  :disabled="btnLoading || textDisable"
                >
                  <div v-if="btnLoading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    />
                  </div>
                  <div v-else>
                    <span v-if="!term.base_term_note">
                      {{ $t("button.save") }}
                    </span>
                    <span v-else>{{ $t("button.save_change") }}</span>
                  </div>
                </v-btn>
              </div>
            </div>
          </form>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import Form from "../../../helpers/form";
export default {
  props: ["itemSelected", "parent"],

  data() {
    return {
      textDisable: false,
      btnLoading: false,
      locale: this.$i18n.locale,
      isEdit: false,
      action: "add",
      defaultNote: "",
      language: this.$i18n.locale,
      term: {},
      form: new Form({
        id: "",
        note: "",
      }),
    };
  },

  watch: {
    language(value) {
      let noteArray = JSON.parse(this.term.note);
      this.form.note = noteArray[value];
      this.locale = value;
    },
  },

  mounted() {
    this.term = this.itemSelected;
    if (this.term) {
      this.form.id = this.term.id;

      if (this.term.note) {
        this.form.note = this.term.base_term_note;
        this.textDisable = true;
        this.locale = this.parent.language;
        this.action = "edit";
        this.defaultNote = this.term.base_term_note;
      }
    }
  },
  methods: {
    on_form_submit() {
      this.btnLoading = true;
      let params = {
        ...this.parent.termDefaultParams,
        id: this.form.id,
        note: this.form.note,
        locale: this.locale,
        action: this.action,
      };

      axios
        .post("/api/terms/post-term-note", params)
        .then((resp) => {
          const records = this.$t("label.notes");

          if (!this.form.note) {
            if (this.action === "add") {
              this.parent.storeToast(this.form.note, records);
            } else {
              this.parent.updateToast(this.form.id, records);
              this.form.note = JSON.parse(resp.data.note)[this.$i18n.locale];
            }
          }

          this.term = resp.data;
          this.isEdit = false;
          this.textDisable = true;
          this.$emit("done-success");
        })
        .catch((error) => {
          if (error.response) {
            // this.form.errors.record(error.response.data.errors);
            if (this.form.note) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.form.errors.get("note")
              );
            }
          }
        })
        .finally(() => {
          this.btnLoading = false;
        });
    },

    on_edit() {
      this.action = "edit";
      this.isEdit = true;
      this.textDisable = false;
    },

    on_cancel() {
      this.$emit("close-modal");
      this.$bvModal.hide("term-note-modal");
    },
  },
};
</script>

