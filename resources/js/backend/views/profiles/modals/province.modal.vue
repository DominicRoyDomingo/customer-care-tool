<template>
  <div>
    <b-modal
      id="province-modal"
      @hide="parent.showProfileModal(2)"
      hide-header
      hide-footer
      size="md"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.sub_action == "Add"
                  ? $t("label.add") +
                    " Province/Division to " +
                    parent.form.country_name
                  : $t("button.update")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('province-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >
            <div class="form-group">
              <v-container>
                <label for="province_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="province_name"
                  type="text"
                  name="province_name"
                  v-model="parent.form.province_name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="job"
                />
                <p v-if="is_required" class='text-danger'>This field Name is required</p>
              </v-container>
            </div>
          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('province-modal')"
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
                <div v-if="parent.form.sub_action == 'Add'">
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save_change") }}
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
      is_required : false
    };
  },

  computed: {
    ...mapGetters({
      response_status: "location/get_response_status",
    }),
  },

  methods: {
    ...mapActions("location", ["add_province"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;

      this.is_required = false
      if(this.parent.form.province_name == '' ||this.parent.form.province_name == null ){
          this.is_required = true
          return
      }

      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        name: this.parent.form.province_name,
        country_id: this.parent.form.location.country_id,
      };

      this.add_province(params)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("province-modal");
          if (this.response_status) {
            let notification_message = this.$t("toasts.added_province");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.province_name
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

            this.parent.addedNewProvinceSuccessfully();
            this.parent.form.province_name = "";
          }
        })
        .catch((error) => {
          this.parent.errorToast(
            error.response.data.message,
            error.response.data.errors.name[0]
          );

          this.parent.btnloading = false;
        });
    },
  },
};
</script>
