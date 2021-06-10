<template>
  <b-modal
    id="html-tag-create"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <div>
          <span v-if="parent.htmlTagForm.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.html_tag") }}
          </span>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("button.update") }} "{{ parent.htmlTagPriorityName }}"
          </span>
        </div>

        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <v-row class="p-1">
          <v-col sm="12" md="12" cols="12">
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.htmlTagForm.errors.clear($event.target.description)"
            >
              <div
                class="form-group mb-4"
                v-show="parent.htmlTagForm.action === 'Update'"
              >
                <div class="form-inline">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    Language
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </div>
                <hr />
              </div>

              <div class="form-group">
                <v-text-field
                  :label="$t('label.description')"
                  id="description"
                  type="text"
                  name="description"
                  v-model="parent.htmlTagForm.description"
                  hide-details="auto"
                />
                <small
                  v-if="
                    !parent.htmlTagForm.description &&
                    parent.htmlTagForm.errors.has('description')
                  "
                  v-text="parent.htmlTagForm.errors.get('description')"
                  class="text-danger"
                />
              </div>
              <p
                class="mt-3 float-left"
                v-if="parent.isShowHtmlTag"
              >
                <a
                  href="/admin/categorized-terms/html-tags-priority"
                  class="text-uppercase text-danger"
                  v-html="`${$t('label.goto_html_tag_page')}`"
                />
              </p>
              <v-card-actions class="float-right mb-0">
                <v-btn color="error" text tile @click="onClose">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                >
                  <div v-if="btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    <span v-if="parent.htmlTagForm.action == 'Add'">
                      {{ $t("button.save") }}
                    </span>
                    <span v-else>{{ $t("button.save_change") }}</span>
                  </div>
                </v-btn>
              </v-card-actions>
            </form>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
      itemSelected: {},
      btnloading: false,
    };
  },
  methods: {
    ...mapActions("categ_terms", ["post_html_tag"]),
    onClose() {
      this.$emit("on-close");
      this.resetAll();
      this.$bvModal.hide("html-tag-create");
    },

    onSave() {
      this.btnloading = true;

      let form = this.parent.htmlTagForm;
      let locale =
        form.action == "Add" ? this.$i18n.locale : this.parent.language;

      let params = {
        id: form.id,
        api_token: this.$user.api_token,
        action: form.action,
        description: form.description,
        locale: locale,
      };
      this.post_html_tag(params)
        .then((resp) => {
          this.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("html-tag-create");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            if (form.description) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                form.errors.get("description")
              );
            }
          }
          this.btnloading = false;
        });
    },

    resetAll() {
      this.btnloading = false;
      this.parent.htmlTagForm.reset();
    },
  },
};
</script>
