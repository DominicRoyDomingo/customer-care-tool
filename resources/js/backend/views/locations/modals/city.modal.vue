<template>
  <div>
    <b-modal
      id="city-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                formTitle
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
          <v-form
            class="form"
            v-model="form"
            @submit.prevent="onSave"
          >
            <div class="form-group">
              <v-container>
                <label for="province_name" v-if="action == 'Add' && province == null">
                  {{ $t("label.province") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-autocomplete
                  v-if="action == 'Add' && province == null"
                  v-model="selected_province"
                  :items="provinces"
                  @input="selected_country = selected_province.country_id"
                  item-text="name"
                  item-value="id"
                  :rules="[
                    v => !!v || $t('errors.this_field_is_required')
                  ]"
                  clearable
                  class="mb-5"
                  :label="$t('label.required')"
                ></v-autocomplete>

                <label for="city_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <v-text-field
                  id="city_name"
                  name="city_name"
                  v-model="city_name"
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
              @click="$bvModal.hide('city-modal')"
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
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
      selected_country: null,
      selected_province: null,
      selected_city: null,
      selected_index: null,
      loading: true,
      country: null,
      province: null,
      city_name: "",
      action: "",
      saving: false,
      form: false,
      showLocationsLink: false,
    };
  },

  computed: {
    ...mapGetters({
      provinces: "location/provinces",
      response_status: "location/get_response_status",
    }),

    formTitle() {
      let output = "";
      
      if(this.action == "Add"){
        if(this.province != null){
          output = this.$t("label.add") + " " + this.$t("label.city") + " " + this.$t("label.to") + ": " + this.province.name;
        }
        else{
          output = this.$t("label.add") + " " + this.$t("label.city");
        }
      }
      else{
        if(this.city_name != null){
          output = this.$t("button.update") + " " + this.$t("label.city") + ": " + this.city_name;
        }
        else{
          output = "N/A";
        }
      }

      return output;
    }
  },

  methods: {
    ...mapActions("location", ["add_city", "update_city"]),

    InitializeModal(data, action, showLocationsLink = false) {
      console.log(data);
      let country = data.country;
      let province = data.province;

      this.country = country;
      this.province = province;
      this.action = action;
      this.$bvModal.show("city-modal");
      console.log("showModal");
      this.showLocationsLink = showLocationsLink;
    },

    UpdateCity(city) {
      this.selected_country = city.country_id;
      this.selected_province = city.division_id;
      this.selected_city = city.id;
      this.city_name = city.name;
      this.selected_index = city.array_index;
      this.InitializeModal({country: city.country_id, province: city.division_id}, "edit"); 
    },

    hide() {
      this.$emit("hide");
    },

    onSave() {
      let vm = this;
      // this.parent.btnloading = true;
      let params = {
        api_token: this.$user.api_token,
        action: this.action,
        name: this.city_name,
        country_id:  (this.country != null) ? this.country.id : this.selected_country,
        province_id:  (this.province != null) ? this.province.id : (this.selected_province != null) ? this.selected_province : null
      };
      this.saving = true;

      if(this.action == "Add"){
        this.add_city(params)
          .then((resp) => {
            this.saving = false;
            console.log("city/resp");
            console.log(resp);
            this.$bvModal.hide("city-modal");
            if (this.response_status) {
              console.log("city/response_status");
              console.log(this.response_status);

              let notification_message = this.$t("toasts.added_city");
              
              // console.log("Added City: " + this.cities[this.cities.length].name);
              // console.log(this.cities[this.cities.length]);

              notification_message = notification_message.replace(
                /%variable%/g,
                this.city_name
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
                province: this.province
              }

              this.$emit('added-city', data);
              this.onResetForm();
            }
          })
          .catch((error) => {
            this.loading = false;
          });
      }
      else{
        params.country_id = this.selected_country;
        params.province_id = this.selected_province;
        params.city_id = this.selected_city;
        params.id = this.selected_city;
        params.index = this.selected_index;

        this.update_city(params)
          .then(resp => {
            this.saving = false;
            console.log("Resp2");
            console.log(resp);
            if (resp) {
              console.log("city/response_status");
              console.log(resp);
              let notification_message = this.$t("toasts.updated_province");
              notification_message = notification_message.replace(
                /%variable%/g,
                this.selected_country.id
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

              vm.$bvModal.hide("city-modal");

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
      this.province = null;
      this.city = null;
      this.selected_country = null;
      this.selected_province = null;
      this.city_name = "";
    },
  },
};
</script>
