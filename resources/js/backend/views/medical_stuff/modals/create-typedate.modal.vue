<template>
  <b-modal
    id="typedate-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <div>
          <span v-if="parent.typeDateForm.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.type_of_date") }}
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
              @keyup="parent.typeDateForm.errors.clear($event.target.name)"
            >
              <div
                class="form-group mb-4"
                v-show="parent.typeDateForm.action === 'Update'"
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
                  v-model="parent.typeDateForm.name"
                  hide-details="auto"
                />
                <small
                  v-if="
                    !parent.typeDateForm.name &&
                    parent.typeDateForm.errors.has('name')
                  "
                  v-text="parent.typeDateForm.errors.get('name')"
                  class="text-danger"
                />
              </div>
              <p
                class="mt-3 float-left"
                v-if="$options._componentTag !== 'IndexCreateTypeDate'"
              >
                <a
                  href="/admin/categorized-terms/type-dates"
                  class="text-uppercase text-danger"
                  v-html="`${$t('label.goto_date_type_page')}`"
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
                    <span v-if="parent.typeDateForm.action == 'Add'">
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
    ...mapActions("categ_terms", ["post_type_date"]),
    onClose() {
      this.$emit("on-close");
      this.resetAll();
      this.$bvModal.hide("typedate-modal");
    },

    onSave() {
      this.btnloading = true;

      let form = this.parent.typeDateForm;
      let locale =
        form.action == "Add" ? this.$i18n.locale : this.parent.language;

      let params = {
        id: form.id,
        api_token: this.$user.api_token,
        action: form.action,
        name: form.name,
        locale: locale,
      };

      this.post_type_date(params)
        .then((resp) => {
          this.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("typedate-modal");
        })
        .catch((error) => {
          if (error.response) {
            form.errors.record(error.response.data.errors);
            if (form.name) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                form.errors.get("name")
              );
            }
          }
          this.btnloading = false;
        });
    },

    resetAll() {
      this.btnloading = false;
      this.parent.typeDateForm.reset();
    },
  },
};
</script>
