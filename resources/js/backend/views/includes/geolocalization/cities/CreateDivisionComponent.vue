<template>
  <div>
    <b-modal
      id="geolocalization-city-province-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div v-if="parent.selected_country != null">
              {{ $t("label.add") + " " + $t("label.province") + " " + $t("label.to") + " " + parent.selected_country.name}}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="showModalCity"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-form
            class="form"
            v-model="form"
            @submit.prevent="onSave"
          >
            <div class="form-group">
              <v-container>
                <label for="province_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <v-text-field
                  id="province_name"
                  name="province_name"
                  v-model="province_name"
                  :placeholder="$t('label.required')"
                  :readonly="saving"
                  :rules="[
                    v => !!v || $t('errors.this_field_is_required')
                  ]"
                />

              </v-container>
            </div>
          </v-form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-btn
              v-if="showLocationsLink"
              color="error"
              text
              tile
              link
              :loading="saving"
              :disabled="saving"
              target="_blank"
              href="/admin/locations"
            >
              <v-icon>
                mdi-open-in-new
              </v-icon>
              {{ $t("label.go_to_locations_page") }}
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              :loading="saving"
              :disabled="saving"
              @click="showModalCity"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" 
              :loading="saving"
              :disabled="saving"
              tile dark @click="onSave">
              <div v-if="saving">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div>
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
import axios from 'axios';
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],
  
  data() {
    return {
      loading: true,
      selected_country: null,
      country: null,
      selected_province: null,
      province_name: "",
      action: "",
      saving: false,
      form: false,
      showLocationsLink: false
    };
  },

  mounted() {

  },

  computed: {
    ...mapGetters({
      countries: "location/countries",
      response_status: "location/get_response_status",
    }),

    formTitle() {
      if(this.country != null){
        return this.country.name + ", " + this.province_name;
      }
      else{
        return this.province_name;
      }
    }
  },

  methods: {
    ...mapActions("location", ["add_province", "update_province"]),

    InitializeModal(country, action, showLocationsLink = false) {
      this.country = country;
      this.showLocationsLink = showLocationsLink;
      this.action = action;
      this.$bvModal.show("geolocalization-city-province-modal");
    },

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.action,
        name: this.province_name,
        country_id: (this.country != null) ? this.country.id : this.parent.selected_country.id,
      };
      this.saving = true;
        this.add_province(params)
          .then((resp) => {
            this.saving = false;
            console.log("province/resp");
            console.log(resp);
            this.$bvModal.hide("geolocalization-city-province-modal");
            if (this.response_status) {
              console.log("province/response_status");
              console.log(this.response_status);
              let notification_message = this.$t("toasts.added_province");

              notification_message = notification_message.replace(
                /%variable%/g,
                this.province_name
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

							this.showModalCity()

            }
          })
          .catch((error) => {
            this.saving = false;
            let errors = error.response.data.errors;
            this.toastError(errors);
          });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    onResetForm(){
      this.country = null;
      this.selected_province = null;
      this.province_name = "";
		},
		
		showModalCity() {
			this.$bvModal.hide("geolocalization-city-province-modal");

			this.$bvModal.show("geolocalization-city-city-modal");
		}
  },
};
</script>
