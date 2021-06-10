<template>
  <div class="animated fadeIn">
    <v-app id="locations__container" light>
        <v-card>
        <v-toolbar
            color="secondary"
            dark
            flat
            >

            <v-toolbar-title>{{ $t('label.locations') }}</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn v-if="tab==0" rounded @click="modalAddNewRegion()" color="primary">
                <v-icon>mdi-plus</v-icon>
                {{ $t('label.add_region') }}
            </v-btn>

            <v-btn v-if="tab==1" rounded @click="modalAddNewProvince()" color="primary">
                <v-icon>mdi-plus</v-icon>
                {{ $t('label.add_province') }}
            </v-btn>


            <v-btn v-if="tab==2" rounded @click="modalAddNewCity({country: null, province: null})" color="primary">
                <v-icon>mdi-plus</v-icon>
                {{ $t('label.add_city') }}
            </v-btn>

        </v-toolbar>
        
        <v-row class="px-5">
            <v-col cols="3">
                <v-autocomplete
                    v-model="perPageProvince"
                    :items="showEntriesOptProvince"
                    hide-no-data
                    item-text="text"
                    item-value="value"
                    :label="$t('label.entries')"
                    dense
                    outlined
                    class="ml-5 w-50"
                ></v-autocomplete>
            </v-col>
            <v-col cols="6">
                
            </v-col>
            <v-col cols="3">
                <!--
                <v-text-field
                v-model="provinceFilters.province"
                v-model="province"
                @change="provinceFilters.province = (province == undefined) ? '' : province"
                label="Search"
                prepend-inner-icon="mdi-magnify"
                rounded
                outlined
                dense
                ></v-text-field>
                -->

                <v-text-field
                    v-if="tab == 0"
                    v-model="regionFilters.region"
                    :label="$t('label.search_region')"
                    append-icon="mdi-map-search"
                    @click:append="searchRegions()"
                    rounded
                    outlined
                    dense
                ></v-text-field>
                
                <v-text-field
                    v-if="tab == 1"
                    v-model="provinceFilters.province"
                    :label="$t('label.search_province')"
                    append-icon="mdi-map-search"
                    @click:append="searchProvinces()"
                    rounded
                    outlined
                    dense
                ></v-text-field>
                
                <v-text-field
                    v-if="tab == 2"
                    v-model="cityFilters.city"
                    :label="$t('label.search_city')"
                    append-icon="mdi-map-search"
                    @click:append="searchCities()"
                    rounded
                    outlined
                    dense
                ></v-text-field>
            </v-col>
        </v-row>
        <v-row class="" no-gutters>
            <v-col cols="6">     
                <v-tabs
                    hide-slider
                    v-model="tab"
                    center-active
                    grow
                >
                    <v-tab>
                        {{ $t("label.regions") }}
                    </v-tab>
                    <v-tab>
                        {{ $t("label.provinces") }}
                    </v-tab>
                    <v-tab>
                        {{ $t("label.cities") }}
                    </v-tab>
                </v-tabs>
            </v-col>
            <v-col cols="3" class="text-right">
                <v-autocomplete
                    v-if="tab == 2"
                    v-model="cityFilters.province"
                    :items="selection_pool.provinces"
                    item-text="name"
                    item-value="name"
                    @change="searchCities()"
                    clearable
                    dense
                    filled
                    hide-details
                    :label="$t('label.filter_by_province')"
                ></v-autocomplete>
            </v-col>
            <v-col cols="3" class="text-right px-2">
                <v-autocomplete
                    v-if="tab == 0"
                    v-model="regionFilters.country"
                    :items="selection_pool.countries"
                    item-text="name"
                    item-value="name"
                    @change="searchRegions()"
                    clearable
                    dense
                    filled
                    hide-details
                    :label="$t('label.filter_by_country')"
                ></v-autocomplete>
                <v-autocomplete
                    v-if="tab == 1"
                    v-model="provinceFilters.country"
                    :items="selection_pool.countries"
                    item-text="name"
                    item-value="name"
                    @change="searchProvinces()"
                    clearable
                    dense
                    filled
                    hide-details
                    :label="$t('label.filter_by_country')"
                ></v-autocomplete>
                <v-autocomplete
                    v-if="tab == 2"
                    v-model="cityFilters.country"
                    :items="selection_pool.countries"
                    item-text="name"
                    item-value="name"
                    @change="searchCities()"
                    clearable
                    dense
                    filled
                    hide-details
                    :label="$t('label.filter_by_country')"
                ></v-autocomplete>
            </v-col>
        </v-row>
        
        <v-tabs-items v-model="tab" class="pt-0">
            <v-tab-item>
                <v-card
                color="basil"
                flat
                >
                    <RegionsTable ref="regionTable" :items="regions" :parent="vm" :filters="regionFilters" :currentPage="currentPageRegion" :perPage="perPageRegion" :totalRows="totalRegionRows" @start-loading="isLoading = true" @end-loading="isLoading = false"></RegionsTable>
                </v-card>
            </v-tab-item>
            
            <v-tab-item>
                <v-card
                color="basil"
                flat
                >
                    <ProvincesTable ref="provinceTable" :items="provinces" :parent="vm" :filters="provinceFilters" :currentPage="currentPageProvince" :perPage="perPageProvince" :totalRows="totalProvinceRows"></ProvincesTable>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card
                color="basil"
                flat
                >
                    <CitiesTable ref="cityTable" :parent="vm" :filters="cityFilters" :perPage="perPageCity" @start-loading="isLoading = true" @end-loading="isLoading = false"></CitiesTable>
                </v-card>
            </v-tab-item>
        </v-tabs-items>
        </v-card>
            
        <ProvinceModal :parent="this" ref="provinceModal" @added-province="addedProvince" @updated-province="updatedProvince" @hide="hideProvinceModal"></ProvinceModal>
        <RegionModal :parent="this" ref="regionModal"></RegionModal>
        <CityModal :parent="this" ref="cityModal" @start-save="vm.$refs.cityTable.isLoading = true" @added-city="endCityLoading" @updated-city="endCityLoading" @hide="hideCityModal"></CityModal>
    </v-app>
  </div>
</template>

<script>

class Province {
  constructor(province = "", cities = []) {
    this.id = null;
    this.country_id = null;
    this.province = province;
    this.cities = cities;
  }
}

class City {
  constructor(city = "") {
    this.id = null;
    this.country_id = null;
    this.province_id = null;
    this.city = city;
  }
}

class Region {
  constructor(region = "") {
    this.id = null;
    this.country_id = null;
    this.region = region;
  }
}

import { mapActions, mapGetters } from "vuex";
import { API_BRAND } from "./../../common/API.service";

import CityModal from "./modals/city.modal.vue";
import ProvinceModal from "./modals/province.modal.vue";
import RegionModal from "./modals/region.modal.vue";

import ProvincesTable from "./components/provinces.table.vue";
import CitiesTable from "./components/cities.table.vue";
import RegionsTable from "./components/regions.table.vue";

import locationMixins from "./mixins/locationMixins.js";

export default {
    mixins: [locationMixins],
    name: "Locations",
    components: {
        CityModal,
        ProvinceModal,
        ProvincesTable,
        RegionModal,
        CitiesTable,
        RegionsTable,
    },
    data() {
        return {
            vm: this,
            tab: 0,
            form: {
                province: null,
                city: null,
                region: null,
            }
        }
    },
    mounted() {
        this.form.province = new Province();
        this.form.city = new City();
        this.form.region = new Region();
    },
    computed: {  
        ...mapGetters("location", {
            countries: "countries",
            provinces: "provinces",
            regions: "regions",
            cities: "cities",
        }),
    },
    methods:{
        ...mapActions("location", [
            "get_countries",
            "get_provinces",
            "get_cities",
            "get_regions",
            "filter_provinces",
            "filter_cities",
            "filter_regions",
            "delete_province", 
            "delete_city",
            "delete_region",
            "remove_from_provinces_array",
            "remove_from_cities_array",
            "remove_from_regions_array",
        ]),

        searchRegions() {
            let vm = this;
            vm.isLoading = true;

            let params = {
                api_token: vm.$user.api_token,
                ...this.regionFilters,
            }
            console.log("params");
            console.log(params);

            vm.filter_regions(params).then(resp => {
                vm.isLoading = false;
            });
        },

        searchProvinces() {
            let vm = this;
            vm.isLoading = true;

            let params = {
                api_token: vm.$user.api_token,
                ...this.provinceFilters,
            }
            console.log("params");
            console.log(params);

            vm.filter_provinces(params).then(resp => {
                vm.isLoading = false;
            });
        },

        searchCities() {
            let vm = this;
            vm.isLoading = true;

            let params = {
                api_token: vm.$user.api_token,
                ...this.cityFilters,
            }
            console.log("params");
            console.log(params);

            vm.filter_cities(params).then(resp => {
                vm.isLoading = false;
            });
        },

        modalAddNewRegion(country = null) {
            this.$refs.regionModal.InitializeModal(country, "Add");
        },

        editRegion(region){
            this.$refs.regionModal.UpdateRegion(region);
        },

        deleteRegion(region){
            let vm = this;

            let confirmation_message = vm.$t("alerts.are_you_sure_that_you_want_to_delete") + " " + region.region + "?";

            $.confirm({
                title:
                vm.$t("confirmation_record_delete").charAt(0) +
                vm
                    .$t("confirmation_record_delete")
                    .slice(1)
                    .toLowerCase(),
                content: confirmation_message,
                type: "red",
                typeAnimated: true,
                columnClass: "medium",
                buttons: {
                yes: {
                    text: vm.$t("yes"),
                    btnClass:
                    "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                    action: function(value) {
                        if (value) {

                            let notification_message = vm.$t("toasts.deleted_region");
                            notification_message = notification_message.replace(
                            /%variable%/g,
                                region.region
                            );

                            
                            let params = {
                                api_token: vm.$user.api_token,
                                region_id: region.id,
                            }
                            console.log("params");
                            console.log(params);
                            vm.delete_region(params).then(resp => {
                                vm.remove_from_regions_array(region);
                                vm.makeToast(
                                    "danger",
                                    vm.$t("delete_record"),
                                    notification_message
                                );
                            });
                        } else {
                            
                        }
                    },
                },
                no: {
                    text: vm.$t("no"),
                    btnClass:
                    "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                    action: function() {},
                },
                },
            });
        },

        modalAddNewProvince(country = null) {
            this.$refs.provinceModal.InitializeModal(country, "Add");
        },

        editProvince(province){
            this.$refs.provinceModal.UpdateProvince(province);
        },

        deleteProvince(province){
            let vm = this;

            let confirmation_message = vm.$t("alerts.are_you_sure_that_you_want_to_delete") + " " + province.name + "?";

            $.confirm({
                title:
                vm.$t("confirmation_record_delete").charAt(0) +
                vm
                    .$t("confirmation_record_delete")
                    .slice(1)
                    .toLowerCase(),
                content: confirmation_message,
                type: "red",
                typeAnimated: true,
                columnClass: "medium",
                buttons: {
                yes: {
                    text: vm.$t("yes"),
                    btnClass:
                    "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                    action: function(value) {
                        if (value) {

                            let notification_message = vm.$t("toasts.deleted_province");
                            notification_message = notification_message.replace(
                            /%variable%/g,
                                province.name
                            );

                            
                            let params = {
                                api_token: vm.$user.api_token,
                                province_id: province.id,
                            }
                            console.log("params");
                            console.log(params);
                            vm.delete_province(params).then(resp => {
                                vm.remove_from_provinces_array(province);
                                vm.makeToast(
                                    "danger",
                                    vm.$t("delete_record"),
                                    notification_message
                                );
                            });
                        } else {
                            
                        }
                    },
                },
                no: {
                    text: vm.$t("no"),
                    btnClass:
                    "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                    action: function() {},
                },
                },
            });
        },
        
        addedProvince() {
            //this.$refs.provinceTable.refresh();
        },
        
        updatedProvince() {
        },

        hideProvinceModal() {

        },

        modalAddNewCity(data) {
            let country = data.country;
            let province = data.province;

            this.$refs.cityModal.InitializeModal(data, "Add");
        },
        
        editCity(city){
            this.$refs.cityModal.UpdateCity(city);
        },

        deleteCity(city){
            let vm = this;

            let confirmation_message = vm.$t("alerts.are_you_sure_that_you_want_to_delete") + " " + city.name + "?";

            $.confirm({
                title:
                vm.$t("confirmation_record_delete").charAt(0) +
                vm
                    .$t("confirmation_record_delete")
                    .slice(1)
                    .toLowerCase(),
                content: confirmation_message,
                type: "red",
                typeAnimated: true,
                columnClass: "medium",
                buttons: {
                yes: {
                    text: vm.$t("yes"),
                    btnClass:
                    "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                    action: function(value) {
                        if (value) {

                            let notification_message = vm.$t("toasts.deleted_city");
                            notification_message = notification_message.replace(
                            /%variable%/g,
                                city.name
                            );

                            
                            let params = {
                                api_token: vm.$user.api_token,
                                city_id: city.id,
                            }
                            console.log("params");
                            console.log(params);
                            vm.delete_city(params).then(resp => {
                                vm.remove_from_cities_array(city);
                                vm.makeToast(
                                    "danger",
                                    vm.$t("delete_record"),
                                    notification_message
                                );
                            });
                        } else {
                            
                        }
                    },
                },
                no: {
                    text: vm.$t("no"),
                    btnClass:
                    "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                    action: function() {},
                },
                },
            });
        },

        endCityLoading() {
            if(this.$refs.cityTable != undefined)
                this.$refs.cityTable.isLoading = false;
        },
        hideCityModal() {

        },
    }
}
</script>