<template>
  <div>
    <b-modal
      id="city-modal"
      hide-header
      hide-footer
      @hide="parent.showProfileModal(2)"
      size="md"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.sub_action == "Add"
                  ? $t("label.add") +
                    " City for " +
                    parent.form.province_name +
                    ", " +
                    parent.form.country_name
                  : $t("button.update")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('city-modal')"
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
                <label for="city_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="city_name"
                  type="text"
                  name="city_name"
                  v-model="parent.form.city_name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="job"
                />
              </v-container>
              <v-container v-if="is_empty">
                <div style='color:red;'>
                  This field is required
                </div>
              </v-container>
            </div>
          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn color="error" text tile @click="$bvModal.hide('city-modal')">
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
      is_empty : false
    };
  },

  computed: {
    ...mapGetters({
      response_status: "location/get_response_status",
    }),
  },

  methods: {
    ...mapActions("location", ["add_city"]),

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;
      this.is_empty = false
      let params = {
        api_token: this.$user.api_token,
        action: "Add",
        name: this.parent.form.city_name,
        province_id: this.parent.form.location.province_id,
      };

      if(params.name == '' || params.name == null){
          this.is_empty = true
          return;
      }

      this.add_city(params)
        .then((resp) => {
          this.parent.btnloading = false;

          if(!resp.data.status){

            this.parent.makeToast(
              "error",
              "Duplicate",
              "Duplicate City: " + params.name
            );

            return;
          }

          this.$bvModal.hide("city-modal");
          if (this.response_status) {
            let notification_message = this.$t("toasts.added_city");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.city_name
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

            this.parent.addedNewCitySuccessfully();
            this.parent.form.city_name = "";
          }
        })
        .catch((error) => {
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
