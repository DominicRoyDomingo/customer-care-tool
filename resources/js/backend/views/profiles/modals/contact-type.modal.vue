<template>
  <div>
    <b-modal
      id="contact-type-modal"
      @hide="parent.showModalOnHideContactType"
      hide-header
      hide-footer
      size="md"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t("contact_type_title") }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('contact-type-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <!-- <div
              class="form-group mb-4"
              v-show="parent.form.action === 'Update'"
            >
              <v-container>
                <span class="float-right">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    Language
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </span>
              </v-container>
            </div> -->
            <br />
            <div class="form-group">
              <v-container>
                <label for="name">
                  {{ $t("label.type_name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="type_name"
                  type="text"
                  name="type_name"
                  v-model="parent.form.type_name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="contact_type"
                />
                <small
                  id="type_name"
                  v-if="parent.form.errors.has('type_name')"
                  v-text="parent.form.errors.get('type_name')"
                  class="text-danger"
                />
              </v-container>
            </div>
          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('contact-type-modal')"
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
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
    };
  },

  computed: {
    ...mapGetters("contact_types", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("contact_types", ["post_contact_type"]),
    hide() {
      this.$emit("hide");
    },
    onSave() {
      this.parent.btnloading = true;

      let params = {
        api_token: this.$user.api_token,
        action: this.parent.form.action,
        type_name: this.parent.form.type_name,
        locale: this.parent.language,
      };

      this.post_contact_type(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("contact-type-modal");
          if (this.responseStatus) {
            let notification_message = this.$t("toasts.added_contact_type");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.type_name
            );

            let message = {
              create: notification_message,
              update: notification_message,
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.parent.addedNewContactTypeSuccessfully();
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
