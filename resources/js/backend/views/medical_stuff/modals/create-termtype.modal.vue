<template>
  <b-modal
    id="term-type-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <div>
          <span v-if="parent.typeForm.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.type_of_term") }}
          </span>

          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("button.update") }} "{{ parent.itemSelected.base_name }}"
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
              @keyup="parent.typeForm.errors.clear($event.target.name)"
            >
              <div
                class="form-group mb-4"
                v-show="parent.typeForm.action === 'Update'"
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
                  :label="$t('label.name')"
                  id="name"
                  type="text"
                  name="name"
                  v-model="parent.typeForm.term_type"
                  hide-details="auto"
                />
                <small
                  v-if="
                    !parent.typeForm.term_type &&
                    parent.typeForm.errors.has('name')
                  "
                  v-text="parent.typeForm.errors.get('name')"
                  class="text-danger"
                />
              </div>

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
                  <div v-if="parent.btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    <span v-if="parent.typeForm.action == 'Add'">
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

  // computed: {
  //   ...mapGetters("categ_terms", {
  //     item: "get_response_item",
  //   }),
  // },

  methods: {
    ...mapActions("categ_terms", ["post_term_type"]),

    onClose() {
      this.$emit("on-close");
      this.formReset();
      this.$bvModal.hide("term-type-modal");
    },

    onSave() {
      this.parent.btnloading = true;

      let action = this.parent.typeForm.action;

      let params = {
        id: this.parent.typeForm.id,
        api_token: this.$user.api_token,
        action: action,
        brand_id: this.$brand.id,
        name: this.parent.typeForm.term_type,
        locale: action === "Add" ? this.$i18n.locale : this.parent.language,
      };

      this.post_term_type(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("term-type-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.typeForm.errors.record(error.response.data.errors);
            if (this.parent.typeForm.term_type) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.parent.typeForm.errors.get("name")
              );
            }
          }
          this.parent.btnloading = false;
        });
    },

    formReset() {
      this.parent.typeForm.term_type = "";
    },
  },
};
</script>
