<template>
  <div>
    <b-modal
      id="province-modal"
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
              {{ $t("label.add") + " " + $t("label.province") }}
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
              @click="$bvModal.hide('province-modal')"
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
      this.$bvModal.show("province-modal");
    },

    UpdateProvince(province) {
      let vm = this;

      this.selected_province = province;
      this.province_name = province.name;

      let params = {
        api_token: this.$user.api_token
      };

      axios
      .get("/api/location/fetch/province/" + province.id, {params})
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
        name: this.province_name,
        country_id: (this.country != null) ? this.country.id : this.selected_country.id,
      };
      this.saving = true;

      if(this.action == "Add"){
        this.add_province(params)
          .then((resp) => {
            this.saving = false;
            console.log("province/resp");
            console.log(resp);
            this.$bvModal.hide("province-modal");
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

              let data = {
                country: this.country
              }

              this.$bvModal.hide("province-modal");

              this.$emit('added-province', data);
              this.onResetForm();
            }
          })
          .catch((error) => {
            this.loading = false;
            this.saving = false;
          });
      }
      else{
        params.province_id = this.selected_province.id;
        params.index = this.selected_province.array_index;

        this.update_province(params)
          .then((resp) => {
            this.saving = false;
            console.log("province/resp");
            console.log(resp);
            this.$bvModal.hide("province-modal");
            if (this.response_status) {
              console.log("province/response_status");
              console.log(this.response_status);
              let notification_message = this.$t("toasts.updated_province");
              notification_message = notification_message.replace(
                /%variable%/g,
                this.selected_province.id
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

              this.$bvModal.hide("province-modal");

              this.$emit('updated-province', data);
              this.onResetForm();
            }
          })
          .catch((error) => {
            this.loading = false;
            this.saving = false;
          });
      }
    },

    onResetForm(){
      this.country = null;
      this.selected_country = null;
      this.selected_province = null;
      this.province_name = "";
    }
  },
};
</script>
