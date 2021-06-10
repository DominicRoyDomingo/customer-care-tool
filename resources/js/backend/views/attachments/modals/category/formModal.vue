<template>
  <v-row justify="center">
    <v-dialog
      v-model="parent.isCategoryFormModalOpen"
      persistent
      max-width="500"
    >
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <div>
            {{
              parent.form.action == "Add"
                ? $t("label.add_category_title")
                : $t("label.update_category_title")
            }}
          </div>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="
              parent.categoryReferrer == 'type'
                ? [
                    (parent.isCategoryFormModalOpen = false),
                    (parent.isTypeFormModalOpen = true),
                    (parent.form.category_name = null),
                    parent.setCategories(),
                  ]
                : [(parent.isCategoryFormModalOpen = false)]
            "
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
            <form
              class="form"
              @keyup="parent.form.errors.clear($event.target.category_name)"
            >
              <div class="form-group">
                <v-row v-if="parent.form.action != 'Add'">
                  <v-spacer></v-spacer>
                  <v-col cols="4">
                    <label class="mr-sm-2" for="inline-form-custom-select-pref">
                      {{ $t("label.language") }}
                    </label>
                    <b-form-select
                      id="inline-form-custom-select-pref"
                      class="mb-2 mr-sm-2 mb-sm-0"
                      v-model="parent.language"
                      :options="parent.languageOptions"
                    />
                  </v-col>
                </v-row>
                <br v-else />
                <v-text-field
                    v-model="parent.form.category_name"
                    :label="$t('label.category_of_document')"
                    suffix="*"
                    class="modal__input"
                    autocomplete="off"
                    style="position:relative; width:100%;margin: 10px"
                  >
                  </v-text-field>
                  <small style="color:red; margin-top:-15px;position:absolute" v-if="category_name_required"> {{ $t("label.category_of_document") }} {{ $t('is_required')}}</small>

                <!-- <input
                  id="category_name"
                  type="text"
                  name="category_name"
                  v-model="parent.form.category_name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="category_name"
                /> -->
                <!-- <small
                  v-if="parent.form.errors.has('name')"
                  v-text="parent.form.errors.get('name')"
                  class="text-danger"
                /> -->
              </div>
            </form>
          </v-container>
        </v-card-text>
        <v-card-actions class="modal__footer blue-grey lighten-5">
          <v-spacer></v-spacer>
          <v-btn
            color="error"
            text
            tile
            @click="
              parent.categoryReferrer == 'type'
                ? [
                    (parent.isCategoryFormModalOpen = false),
                    (parent.isTypeFormModalOpen = true),
                    (parent.form.category_name = null),
                    parent.setCategories(),
                  ]
                : [(parent.isCategoryFormModalOpen = false)]
            "
          >
            {{ $t("cancel") }}
          </v-btn>
          <v-btn
            color="success"
            tile
            dark
            @click="onSave"
            :disabled="parent.btnloading"
          >
            <div v-if="parent.btnloading">
              <v-progress-circular
                size="20"
                width="1"
                color="success"
                indeterminate
              >
              </v-progress-circular>
            </div>
            <div v-else>
              <div v-if="parent.form.action == 'Add'">
                <v-icon left>mdi-briefcase-plus</v-icon>
                {{ $t("button.save") }}
              </div>
              <div v-else>
                <v-icon left>mdi-briefcase-edit</v-icon>
                {{ $t("button.save") }}
              </div>
            </div>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],
  data() {
    return {
      category_name_required: false
    };
  },
  computed: {
    ...mapGetters("attachments", {
      responseStatus: "get_response_status",
    }),
  },
  methods: {
    ...mapActions("attachments", ["store_category"]),
    onSave() {
      this.category_name_required = false;
      if( this.parent.form.category_name == '' || this.parent.form.category_name == null ) {
        this.category_name_required = true;
        return false;
      }

      this.parent.btnloading = true;
      this.store_category({
        api_token: this.$user.api_token,
        name: this.parent.form.category_name,
        action: this.parent.form.action,
        id: this.parent.form.id,
        locale: this.parent.language,
      })
        .then((resp) => {
          this.parent.btnloading = false;
          if (this.responseStatus) {
            let message = {
              create:
                `${this.parent.form.category_name}` +
                this.$t("toasts.new_document_category"),
              update:
                this.$t("updated_message1") +
                this.$t("label.category") +
                " " +
                `${this.parent.form.category_name}` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              this.parent.form.action === "Add" ? "success" : "warning",
              this.parent.form.action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.action === "Add"
                ? message.create
                : message.update
            );

            if (this.parent.categoryReferrer != "type")
              this.parent.successfullySavedDocCategory();
            else {
              this.parent.setCategories();
              this.parent.isCategoryFormModalOpen = false;
              this.parent.isTypeFormModalOpen = true;
              this.parent.form.category_name = resp.data.id;
              this.parent.language = this.$i18n.locale;
            }
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.isCategoryFormModalOpen = false;
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
