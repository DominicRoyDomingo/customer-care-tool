<template>
  <div>
    <b-modal
      id="geolocalization-city-city-modal"
      @hide="hide"
      hide-header
      hide-footer
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            
              {{ $t("label.new_city") }}
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('geolocalization-city-city-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-form
            class="form"
            v-model="form"
            ref="form"
            @submit.prevent="onSave"
          >
            <div class="form-group">
              <v-container>
                <label for="country_name" v-if="action == 'Add' && country == null">
                  {{ $t("label.country") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-autocomplete
                  v-model="parent.selected_country"
                  :items="countries"
                  item-text="name"
                  :rules="[
                    v => !!v || $t('errors.country_required')
                  ]"
									@change="filterProvince"
                  clearable
                  return-object
                  class="mb-5"
                  :label="$t('label.country')"
                ></v-autocomplete>

                <label for="division_name" v-if="action == 'Add' && country == null">
                  {{ $t("label.division") }}
                  <strong class="text-danger">*</strong>
                </label>
                <v-autocomplete
                  v-model="selected_division"
                  :items="provinces"
                  item-text="name"
                  :rules="[
                    v => !!v || $t('errors.division_required')
                  ]"
                  clearable
                  return-object
                  class="mb-5"
                  :label="$t('label.division')"
                >
                <template v-slot:prepend-item v-if="provinceFilters.country != 'null'">
                  <v-list>
                    <v-list-item-group>
                      <v-list-item
                        ripple
                        @click="addDivision"
                      >
                        <v-list-item-content>
                        <v-list-item-title>
                            <v-icon color="blue">mdi-plus</v-icon>
                            <span class="blue--text">{{ $t('label.add_province') }}</span>
                          </v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                  <v-divider class="mt-2"></v-divider>
                </template>
                </v-autocomplete>

                <label for="province_name">
                  {{ $t("label.name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <v-text-field
                  id="city_name"
                  name="city_name"
                  v-model="city_name"
                  :placeholder="$t('label.city')"
                  :readonly="saving"
                  :rules="[
                    v => !!v || $t('errors.city_required')
                  ]"
                />

              </v-container>
            </div>
          </v-form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-btn
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
              @click="$bvModal.hide('geolocalization-city-city-modal')"
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
      country: null,
      selected_division: null,
      city_name: "",
      action: "",
      saving: false,
      form: false,
			showLocationsLink: false,
			provinceFilters: {
        country: "null",
        province: "",
      },
    };
  },

  mounted() {

  },

  computed: {
    ...mapGetters({
      countries: "location/countries",
      response_status: "location/get_response_status",
		}),
		
		 ...mapGetters("location", {
        countries: "countries",
        provinces: "provinces",
        cities: "cities",
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
    ...mapActions("location", ["add_city", "update_province", "filter_provinces"]),

    filterProvince(value) {
      let vm = this;
      value = value == undefined ? "null" : value.name
      console.log(value)
			vm.isLoading = true;
			vm.provinceFilters.country = value
			let params = {
					api_token: vm.$user.api_token,
					...this.provinceFilters,
			}

			vm.filter_provinces(params).then(resp => {
					vm.isLoading = false;
			});
    },
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

    addDivision() {

      this.$bvModal.hide("geolocalization-city-city-modal");

      this.$bvModal.show("geolocalization-city-province-modal");

    },

    hide() {
      this.$emit("hide");
    },

    onSave() {
      let vm = this;
      if(this.$refs.form.validate() == false) return;
      let params = {
        api_token: this.$user.api_token,
        action: this.action,
        name: this.city_name,
        country_id:  this.parent.selected_country.id,
        province_id:  this.selected_division.id
      };

      this.saving = true;
        this.add_city(params)
          .then((resp) => {
            this.saving = false;
            console.log("city/resp");
            console.log(resp);
            this.$bvModal.hide("geolocalization-city-city-modal");
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
              vm.parent.cityPlacePage = 1;
              vm.parent.loadCityPlaces()
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
      this.parent.selected_country = null;
      this.selected_province = null;
      this.province_name = "";
    }
  },
};
</script>
