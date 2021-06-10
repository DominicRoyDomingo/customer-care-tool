<template>
  <div>
    <b-modal
      id="region-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div v-if="country != null">
              {{
                formTitle
              }}
            </div>
            
            <div v-if="country == null">
              {{ $t("label.add") + " " + $t("label.region") }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="hideRegionModal"
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
                <label for="country_name" v-if="action == 'Add' && country == null">
                  {{ $t("label.country") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-autocomplete
                  v-if="action == 'Add' && country == null"
                  v-model="selected_country"
                  :items="countries"
                  item-text="name"
                  :rules="[
                    v => !!v || $t('errors.this_field_is_required')
                  ]"
                  clearable
                  return-object
                  class="mb-5"
                  :label="$t('label.required')"
                ></v-autocomplete>

                <label for="region_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <v-text-field
                  id="region_name"
                  name="region_name"
                  v-model="region_name"
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
              @click="hideRegionModal"
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
                <div v-if="action == 'Add'">
                  {{ $t("button.save") }}
                </div>
                <div v-else>
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
import axios from 'axios';
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],
  
  data() {
    return {
      loading: true,
      selected_country: null,
      country: null,
      selected_region: null,
      region_name: "",
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
        return this.country.name + ", " + this.region_name;
      }
      else{
        return this.region_name;
      }
    }
  },

  methods: {
    ...mapActions("location", ["add_region", "update_region"]),

    InitializeModal(country, action, showLocationsLink = false) {
      this.country = country;
      this.showLocationsLink = showLocationsLink;
      this.action = action;
      this.$bvModal.show("region-modal");
    },

    UpdateRegion(region) {
      let vm = this;

      this.selected_region = region;
      this.region_name = region.region;

      let params = {
        api_token: this.$user.api_token
      };

      axios
      .get("/api/location/fetch/region/" + region.id, {params})
      .then(resp => {
        let country = resp.data.country;

        vm.InitializeModal(
          country, "edit"
        ); 
      });

    },

    hide() {
      this.$emit("hide");
    },

    onSave() {
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.action,
        region: this.region_name,
        country_id: (this.country != null) ? this.country.id : this.selected_country.id,
      };

      this.saving = true;

      if(this.action == "Add"){
        this.add_region(params)
          .then((resp) => {
            this.saving = false;
            console.log("region/resp");
            console.log(resp);
            this.$bvModal.hide("region-modal");
            if (this.response_status) {
              console.log("region/response_status");
              console.log(this.response_status);
              let notification_message = this.$t("toasts.added_region");

              notification_message = notification_message.replace(
                /%variable%/g,
                this.region_name
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

              let data = {
                country: this.country
              }

              this.$bvModal.hide("region-modal");

              this.onResetForm();
            }
          })
          .catch((error) => {
            this.loading = false;
            this.saving = false;
          });
      }
      else{
        params.region_id = this.selected_region.id;
        params.index = this.selected_region.array_index;

        this.update_region(params)
          .then((resp) => {
            this.saving = false;
            console.log("region/resp");
            console.log(resp);
            this.$bvModal.hide("region-modal");
            if (this.response_status) {
              console.log("region/response_status");
              console.log(this.response_status);
              let notification_message = this.$t("toasts.updated_region");
              notification_message = notification_message.replace(
                /%variable%/g,
                this.selected_region.id
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

              let data = {
                country: this.country
              }

              this.$bvModal.hide("region-modal");

              this.$emit('updated-region', data);
              this.onResetForm();
            }
          })
          .catch((error) => {
            this.loading = false;
            this.saving = false;
          });
      }
    },

		hideRegionModal() {
			this.$bvModal.hide('region-modal')
			this.onResetForm();
		},

    onResetForm(){
      this.country = null;
      this.selected_country = null;
      this.selected_region = null;
      this.region_name = "";
    }
  },
};
</script>
