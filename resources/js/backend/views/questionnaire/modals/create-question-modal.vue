<template>
  <div>
    <b-modal
      id="question-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
    >
      <v-app id="create__container">
        <v-card class="border-0">
          <v-card-title class="title blue-grey lighten-4 text-capitalize">
            <span v-if="action == 'add'">
              {{ $t("label.new") }} {{ $t("questionnaire") }}
            </span>
            <span
              class="d-inline-block text-truncate"
              style="max-width: 700px; letter-spacing: normal"
              v-else
            >
              {{ $t("button.edit") }} : "{{ item.base_name }}"
            </span>
            <v-spacer></v-spacer>
            <v-btn icon color="error lighten-2" @click="on_close">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-container>
            <div class="row">
              <div class="col-md-12">
                <form
                  class="form p-2"
                  @submit.prevent="on_form_submit"
                  @keyup="form.errors.clear($event.target.name)"
                >
                  <div class="form-group mb-4" v-show="action === 'edit'">
                    <div class="form-inline">
                      <label
                        class="mr-sm-2"
                        for="inline-form-custom-select-pref"
                      >
                        Language
                      </label>
                      <b-form-select
                        id="inline-form-custom-select-pref"
                        class="mb-2 mr-sm-2 mb-sm-0"
                        v-model="language"
                        :options="languageOptions"
                      />
                    </div>
                    <hr />
                  </div>
                  <div class="form-group mb-2">
                    <v-text-field
                      v-model="form.name"
                      :label="$t('label.name')"
                      type="text"
                      name="name"
                      hide-details="auto"
                    />
                    <small
                      v-if="!form.name && form.errors.has('name')"
                      v-text="form.errors.get('name')"
                      class="text-danger"
                    />
                  </div>

                  <v-card-actions class="float-right">
                    <v-btn color="error" text tile @click="on_close">
                      {{ $t("cancel") }}
                    </v-btn>
                    <v-btn
                      color="success"
                      tile
                      type="submit"
                      class="bg-success text-white"
                      :disabled="btnLoading"
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
                        <span v-if="action === 'add'">
                          {{ $t("button.save") }}
                        </span>
                        <span v-else>{{ $t("button.save_change") }}</span>
                      </div>
                    </v-btn>
                  </v-card-actions>
                  <br />
                </form>
              </div>
            </div>
          </v-container>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import Form from "../../../helpers/form";
import qMixins from "../mixins/qMixins";
import { post_question } from "./../mixins/query";
export default {
  props: {
    action: { type: String },
    item: { type: Object },
  },
  mixins: [qMixins],
  data() {
    let isItem = Object.values(this.item).length > 0;
    return {
      btnLoading: false,
      language: this.$i18n.locale,
      form: new Form({
        id: isItem ? this.item.id : "",
        name: isItem ? this.item.base_name : "",
      }),
    };
  },
  watch: {
    language(value) {
      let noteArray = JSON.parse(this.item.name);
      this.form.name = noteArray[value];
      this.dParams.locale = value;
    },
  },
  methods: {
    on_close() {
      this.$emit("on-close");
    },

    on_form_submit() {
      this.btnLoading = true;
      let params = {
        ...this.dParams,
        id: this.form.id,
        name: this.form.name,
        type: this.form.type,
        action: this.action,
        type: "internal",
      };

      post_question(params)
        .then((data) => {
          this.$emit("on-success", data);
          this.on_close();
        })
        .catch((error) => {
          let response = error.response;
          if (response) {
            this.form.errors.record(response.data.errors);
            if (this.form.name) {
              this.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.form.errors.get("name")
              );
            }
          }
        })
        .finally(() => {
          this.btnLoading = false;
        });
    },
  },
};
</script>
 