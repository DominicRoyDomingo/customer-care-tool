<template>
  <v-row justify="center">
    <v-dialog
      v-model="parent.isTypeFormModalOpen"
      persistent
      max-width="500"
      scrollable
    >
      <v-card :min-height="parent.form.action == 'Add' ? 300 : 400">
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <div>            
            {{
              parent.form.action == "Add"
                ? $t("label.add_type_title")
                : $t("label.update_type_title")
            }}
          </div>
          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="
              parent.typeReferrer == 'profile'
                ? parent.meta.page != 'add-attachments-modal'
                  ? [
                      (parent.isTypeFormModalOpen = false),
                      parent.showProfileModal(5),
                    ]
                  : [
                      (parent.isTypeFormModalOpen = false),
                      parent.$bvModal.show('add-attachments-modal'),
                    ]
                : [(parent.isTypeFormModalOpen = false)]
            "
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
            <form
              class="form"
              @keyup="parent.form.errors.clear($event.target.name)"
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
                    v-model="parent.form.name"
                    :label="$t('label.name')"
                    suffix="*"
                    class="modal__input"
                    autocomplete="off"
                    style="position:relative; width:100%;margin: 10px"
                  >
                </v-text-field>
                <small
                  id="job"
                  v-if="parent.form.errors.has('name')"
                  v-text="parent.form.errors.get('name')"
                  class="text-danger"
                  style="margin-top: -15px; position:absolute"
                />
                <!-- <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="parent.form.name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="brand"
                />
                 -->
                <br />
                <label for="category_name">
                  {{ $t("label.category_of_document") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-select
                  v-model="parent.form.category_name"
                  label="category_name"
                  :reduce="(category) => category.id"
                  :options="parent.document_categories"
                >
                  <template v-slot:option="option">
                    <span v-html="option.document_category_name"></span>
                  </template>
                  <template #list-header>
                    <li style="margin-left:20px;" class="mb-2">
                      <b-link href="#" @click="parent.openAddCategoryModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("label.new_category") }}
                      </b-link>
                    </li>
                  </template>
                </v-select>
                <small
                  v-if="parent.form.errors.has('document_category_id')"
                  v-text="parent.form.errors.get('document_category_id')"
                  class="text-danger"
                />
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
              parent.typeReferrer == 'profile'
                ? parent.meta.page != 'add-attachments-modal'
                  ? [
                      (parent.isTypeFormModalOpen = false),
                      parent.showProfileModal(5),
                    ]
                  : [
                      (parent.isTypeFormModalOpen = false),
                      parent.$bvModal.show('add-attachments-modal'),
                    ]
                : [(parent.isTypeFormModalOpen = false)]
            "
          >
            {{ $t("cancel") }}
          </v-btn>
          <v-btn color="success" tile dark @click="onSave">
            <div v-if="parent.btnloading">
              <v-progress-circular
                size="20"
                width="1"
                color="white"
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
  computed: {
    ...mapGetters("attachments", {
      responseStatus: "get_response_status",
    }),
  },
  methods: {
    ...mapActions("attachments", ["store_type"]),
    onSave() {
      this.parent.btnloading = true;
      // this.parent.form.id
      this.store_type({
        api_token: this.$user.api_token,
        name: this.parent.form.name,
        document_category_id: this.parent.form.category_name,
        action: this.parent.form.action,
        id: null,
        locale: this.parent.language,
      })
        .then((resp) => {
          this.parent.btnloading = false;
          if (this.responseStatus) {
            let message = {
              create:
                `${this.parent.form.name}` +
                this.$t("toasts.new_document_type"),
              update:
                this.$t("updated_message1") +
                this.$t("label.category") +
                " " +
                `${this.parent.form.name}` +
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

            this.parent.successfullySavedDocType();
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
