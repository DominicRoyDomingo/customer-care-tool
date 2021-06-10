<template>
  <div class="animated fadeIn">
    <v-card
      class="px-5 providers-card"
      color="#f9f9f9"
      height="100%"
    >
      <v-card tile flat class="flex-grow-1" color="#f9f9f9" height="100%">
        <div class="d-flex flex-column" style="height: 100%; padding-top: 24px; padding-bottom: 16px;">
          <div class="row">
            <div class="col">
              <span class="title font-weight-light text--secondary">
                {{ $t("label.providers") }}
              </span>
            </div>

            <v-spacer></v-spacer>

            <v-col cols="12" sm="2" md="2">
              <v-btn
                color="success"
                @click="createProvider()"
                block
                tile
                x-small
                elevation="0"
                v-if="isBrandChanged"
                height="40"
              >
                <v-icon dark>mdi-plus-circle-outline</v-icon>
                {{ $t(modal.add.button.new) }}
              </v-btn>
            </v-col>
          </div>

          <div v-if="this.loadFilter">
            <h6 class="font-weight-bold">{{ $t("filter_by") }} :</h6>

          <SearchFilter 
            @close_filter="loadItems"
            @reload_filter="loadItems"
            :parent="this"
          />
          </div>
          
          <div color="#f9f9f9">
            <div class="row mt-1">
              <div class="col-md-8">
                <b-form inline>
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    {{ $t("show") }}
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    :options="showEntriesOpt"
                    v-model="perPage"
                  />
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    {{ $t("label.entries") }}
                  </label>
                  <b-input-group-prepend class="mr-2">
                    <v-menu offset-y :rounded="false">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          title="Orphan Filters"
                          color="info"
                          dark
                          v-bind="attrs"
                          v-on="on"
                          tile
                          depressed
                          style="height: 33px; min-width: 64px; padding: 0 16px"
                        >
                          <v-icon :size="18"> mdi-filter-menu </v-icon>
                        </v-btn>
                      </template>
                      <v-list dense class="profile__row-menu">
                        <v-list-item-group color="primary">
                          <v-list-item
                            v-for="(option, index) in sortOptions"
                            :key="index"
                          >
                            <v-list-item-content
                              @click="sortProviderBy(option.value)"
                            >
                              <v-list-item-title>
                                {{ option.text }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                  </b-input-group-prepend>
                  <b-input-group-prepend class="mr-2" v-if="isAlgoliaAllowed">
                    <v-btn
                      id="popover-3"
                      color="warning"
                      @click="syncToAlgolia()"
                      block
                      tile
                      x-small
                      elevation="0"
                      v-if="isBrandChanged"
                      height="33"
                      style="padding: 0 16px"
                    >
                      <span v-if="isAlgoliaSyncing" class="text-center">
                        <v-progress-circular
                          size="20"
                          width="1"
                          color="white"
                          indeterminate
                        />
                      </span>
                      <span v-else>{{ $t("button.sync_to_algolia") }}</span>
                    </v-btn>
                    <b-popover class="provider-popover" target="popover-3" triggers="hover focus" >
                      <template #title>{{ $t('provider_popover_queue') }}</template>
                      <span class="provider-popover-text">{{ algoliaSummary.new }} {{ $t('provider_popover_new_providers') }}</span><br>
                      <span class="provider-popover-text">{{ algoliaSummary.update }} {{ $t('provider_popover_providers_to_be_updated') }}</span>
                    </b-popover>
                  </b-input-group-prepend>
                  
                  <b-input-group-prepend>
                    <div class="text-center provider-entry font-weight-bold font-italic text-md-body-1">
                      <span v-if="isLoading">
                        No {{$t('label.entries_available')}}
                      </span>
                      <span v-else>
                        {{ items.total == 0 ? 'No' : items.total }} {{$t('label.entries_available')}}
                      </span>
                    </div>
                  </b-input-group-prepend>
                </b-form>
              </div>
              <div class="col-md-4">
                <div class="">
                  <v-text-field
                    solo
                    color="#e2e0e2"
                    background-color="#e2e0e2"
                    hide-details
                    :label="$t('table.search')"
                    flat
                    height="40px"
                    class="search-text-field"
                    v-model="searched"
                  >
                    <v-icon slot="prepend-inner">
                        mdi-magnify
                    </v-icon>
                    <v-menu
                      key="b-xl"
                      rounded="Custom"
                      :offset-y="true"
                      :nudge-bottom="20"
                      :nudge-left="601"
                      slot="append"
                      :close-on-content-click="false"
                      content-class="elevation-1"
                      v-model="isMenuOpened"
                      >
                      <template v-slot:activator="{ attrs, on }">
                          <v-btn
                            icon
                            color="#e2e0e2"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon color="#686768">mdi-menu-down</v-icon>
                          </v-btn>
                      </template>
                      <v-card
                          class="mx-auto"
                          max-width="650.39"
                          min-width="650.39"
                          elevation="0"
                      >
                          <v-card-text>
                            <v-row>
                              <v-col cols="12" sm="4" md="4">
                                <el-select filterable v-model="selectedByProviderType" clearable placeholder="Choose a Provider Type" class="el-select-wrapper">
                                  <el-option
                                    v-for="item in providerTypeTerms"
                                    :key="item.value"
                                    :label="item.base_name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                              <v-col cols="12" sm="8" md="8">
                                <el-select 
                                  filterable 
                                  v-model="selectedByServiceType" 
                                  clearable 
                                  placeholder="Choose a Category of Service" 
                                  @change="changeCategoryOfService($event)"
                                  class="el-select-wrapper">
                                  <el-option
                                    v-for="item in categoryServicesTerms"
                                    :key="item.id"
                                    :label="item.base_name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                            </v-row>
                            <v-row v-if="isServiceTypeSelected">
                              <v-col cols="12" sm="12" md="12">
                                <el-select filterable v-model="selectedByService" clearable placeholder="Choose a Service" class="el-select-wrapper">
                                  <el-option
                                    v-for="item in servicesCategoryTerms"
                                    :key="item.id"
                                    :label="item.base_name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col cols="12" sm="12" md="12">
                                <el-select filterable v-model="selectedByCountry" @change="changeCountry($event)" clearable placeholder="Choose a Country" class="el-select-wrapper">
                                  <el-option
                                    v-for="item in countries"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                            </v-row>
                            <v-row v-if="isCountrySelected">
                              <v-col cols="12" sm="6" md="6">
                                <el-select filterable v-model="selectedByProvinceOrDivison"  @change="changeProvinceOrDivision($event)" clearable placeholder="Choose a Province/Division" class="el-select-wrapper">
                                  <el-option
                                    v-for="item in divisions"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                              <v-col cols="12" sm="6" md="6">
                                <el-select filterable v-model="selectedByCity" clearable placeholder="Choose a City" class="el-select-wrapper">
                                  <el-option
                                    v-for="item in cities"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                  </el-option>
                                </el-select>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col cols="12" sm="12" md="12" class="text-right">
                                <v-btn
                                  color="#e2e0e2"
                                  small
                                  elevation="0"
                                  class="white--text"
                                  @click="isMenuOpened = false"
                                >
                                  {{ $t("cancel") }}
                                </v-btn>
                                <v-btn
                                  color="#4cb8ff"
                                  small
                                  elevation="0"
                                  @click="onSearch"
                                  class="white--text"
                                >
                                  <div>
                                    <div>
                                      {{$t('table.search')}}
                                    </div>
                                  </div>
                                </v-btn>
                              </v-col>
                            </v-row>
                          </v-card-text>
                      </v-card>
                    </v-menu>
                  </v-text-field>
                </div>
              </div>
            </div>
            <div class="d-flex justify-space-between " align="center">
              <div cols="12" sm="12" class="col-md-5-8" height="48px">
                
              </div>
              
            </div>
        </div>
          <div class="d-flex flex-column flex-grow-1 flex-shrink-0">
            <div>
              <table class="table b-table provider-table table-borderless b-table-stacked-m" style="margin-bottom: 0rem;">
                <thead role="rowgroup"><!---->
                  <tr role="row">
                    <th role="columnheader" scope="col" tabindex="0" aria-colindex="1" aria-sort="ascending" class="text-left ___w-40">
                      <div>{{$t("label.name_of_provider")}}</div>
                      <span class="sr-only"> (Click to sort Descending)
                      </span>
                    </th>
                    <th role="columnheader" scope="col" tabindex="0" aria-colindex="2" aria-sort="none" class="text-center ___w-22">
                      <div>{{$t("label.city")}}</div>
                      <span class="sr-only"> (Click to sort Ascending)
                      </span>
                    </th>
                    <th role="columnheader" scope="col" tabindex="0" aria-colindex="2" aria-sort="none" class="text-center ___w-21">
                      <div>{{$t("label.services_count")}}</div>
                      <span class="sr-only"> (Click to sort Ascending)
                      </span>
                    </th>
                    <th role="columnheader" scope="col" tabindex="0" aria-colindex="3" class="text-center ___w-17">
                      <div>{{$t("table.action")}}</div>
                      <span class="sr-only"> (Click to clear sorting)
                      </span>
                    </th>
                  </tr>
                </thead>
              </table>
            </div>
            
              <b-table
                show-empty
                stacked="md"
                :borderless="true"
                ref="table"
                :sticky-header="true"
                tbody-tr-class="d-flex flex-wrap provider_table_tr_line_height"
                thead-tr-class="d-flex flex-wrap"
                thead-class="hidden_header"
                :fields="tableHeaders"
                :busy="isLoading"
                :items="items.data"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                @sort-changed="sortChanged"
                class="provider-table"
              >
                <template v-slot:table-busy>
                  <v-fade-transition>
                    <v-overlay opacity="0.8" style="z-index: 999999 !important">
                      <v-progress-circular
                        indeterminate
                        size="80"
                        width="2"
                        color="white"
                        class="text-center"
                      ></v-progress-circular>
                    </v-overlay>
                  </v-fade-transition>
                </template>

                <template v-slot:cell(name)="data">
                  <div style="margin-top: 3px;">
                    <a 
                      href="#"
                      class="black--text font-weight-bold"
                      @click="redirectToProviderServices(data.item)"
                      v-html="data.item.provider_name"
                    >
                    </a>
                    <v-tooltip top z-index="99999999" v-if="data.item.provider_and_its_type_items.length != 0 && data.item.provider_sync_reference == null">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          icon
                          color="blue"
                          v-bind="attrs"
                          v-on="on"
                          @click="onAddToSyncReference(data.item, data.index)"
                        >
                          <v-icon>mdi-information</v-icon>
                        </v-btn>
                      </template>
                      <span>{{ $t('provider_no_sync_reference') }}</span>
                    </v-tooltip>
                  </div>
                </template>

                <template v-slot:cell(city)="data">
                  <div class="px-10">
                    <span class="black--text font-weight-bold">
                      <span class="font-italic" v-if="data.item.city_item != null">{{ data.item.city_item.name}}</span>
                      <span v-else></span>
                    </span>
                  </div>
                </template>

                <template v-slot:cell(services_count)="data">
                  <div style="margin-top: 3px;">
                    <span class="black--text font-weight-bold">
                      {{ data.item.provider_services_count }}
                    </span>
                  </div>
                </template>

                <template v-slot:cell(actions)="data">
                    <v-menu offset-y>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="#56CCF2"
                          dark
                          v-bind="attrs"
                          v-on="on"
                          depressed
                          small
                        >
                          {{ $t("button.more_actions") }}
                          <v-icon small right>
                            mdi-chevron-down
                          </v-icon>
                        </v-btn>
                      </template>
                      <v-list dense class="profile__row-menu">
                        <v-list-item-group color="primary">
                          <v-list-item @click="onEdit(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="yellow darken-3">
                                mdi-map-marker-check
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.edit") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onDelete(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="red lighten-1">
                                mdi-map-marker-remove
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.delete") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onLinkBrand(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="blue lighten-1">
                                mdi-briefcase-plus
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("label.link_to_brand") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onAddServices(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-plus-box
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.add_services") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onAddToGroup(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-group
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("button.add_to_group") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="onAddActor(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-account-plus
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ $t("actor_add") }}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                          <v-list-item @click="changeAccountStatus(data.item, data.index)">
                            <v-list-item-icon>
                              <v-icon color="green lighten-1">
                                mdi-checkbox-marked-circle
                              </v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                              <v-list-item-title>
                                {{ data.item.account_status == "free" ?  $t("button.mark_as_upgraded") : $t("button.mark_as_free")}}
                              </v-list-item-title>
                            </v-list-item-content>
                          </v-list-item>
                        </v-list-item-group>
                      </v-list>
                    </v-menu>
                </template>
              </b-table>
          </div>
          <div>
              <v-col sm="12">
                <b-pagination
                  v-if="!this.isLoading"
                  v-model="currentPage"
                  :total-rows="tableTotalRows"
                  :per-page="perPage"
                  align="right"
                ></b-pagination>
              </v-col>
          </div>
        </div>
      </v-card>
    </v-card>

    <Create :parent="this" ref="createComponent"></Create>
    <Edit :parent="this"></Edit>
    <ProviderType :$this="this" @loadTable="loadProviderTypes"></ProviderType>
    <ProviderGroup :$this="this" @loadTable="loadProviderGroups"></ProviderGroup>
    <ProvinceModal :parent="this" ref="provinceModal" @added-province="addedProvince" @hide="hideProvinceModal"></ProvinceModal>
    <CityModal :parent="this" ref="cityModal" @added-city="addedCity" @hide="hideCityModal"></CityModal>
    <ActorModal :parent="this" @loadActor="loadActors"></ActorModal>
    <PhysicalCodeType :$this="this" @loadTable="loadPhysicalCodeTypes"></PhysicalCodeType>
    <InformationType :$this="this" @loadTable="loadInformationTypes"></InformationType>
    <JobDescription :parent="this"></JobDescription>
    <JobCategory :parent="this"></JobCategory>
    <JobProfession :parent="this"></JobProfession>
    <LinkBrandModal :parent="this"></LinkBrandModal>
    <CreateBrand :$this="this" :parent="this"></CreateBrand>
    <ProviderService :parent="this"></ProviderService>
    <ExistingImage :parent="this"></ExistingImage>
    <AddToGroupModal :$this="this"></AddToGroupModal>
    <LinkActorModal :parent="this" />
    <CreateService :parent="this" @done-success="loadProviderServicesGrouped" />
    <CreateMedicalTermModal :parent="this" @done-success="loadLinkedProviderTerms()"/>
    <AddPaymentPlanModal :parent="this"/>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import Create from "./includes/providers/CreateComponent.vue";
import SearchFilter from "./includes/providers/SearchFilterComponent.vue";
import Edit from "./includes/providers/UpdateComponent.vue";
import JobDescription from "./includes/providers/actor/jobs_modal/JobDescriptionModal.vue";
import JobCategory from "./includes/providers/actor/jobs_modal/JobCategoryModal.vue";
import JobProfession from "./includes/providers/actor/jobs_modal/JobProfessionModal.vue";
import ActorModal from "./includes/providers/actor/CreateComponent.vue";
import AddPaymentPlanModal from "./includes/providers/CreatePaymentPlanModal.vue";
import ProviderType from "./includes/provider-type/CreateComponent.vue";
import ProviderGroup from "./includes/provider-group/CreateComponent.vue";
import PhysicalCodeType from "./includes/physical-code-type/CreateComponent.vue";
import InformationType from "./includes/information-type/CreateComponent.vue";
import ProviderService from "./includes/provider-services/CreateComponent.vue";
import ExistingImage from "./includes/providers/ExistingImagesModal.vue";
import CreateService from "./includes/provider-services/CreateServiceComponent";
import EditProviderService from "./includes/provider-services/UpdateComponent.vue";
import CreateMedicalTermModal from "./includes/actor/CreateLinkTermComponent.vue";
import CreateBrand from "./includes/brands/CreateeComponent.vue";
import AddToGroupModal from "./includes/providers/AddToGroupComponent.vue";
import Loading from "vue-loading-overlay";
import Form from "./../shared/form.js";
import toast from "./../helpers/toast.js";
import text from "./../helpers/text.js";
import ProvinceModal from "./../views/locations/modals/province.modal.vue";
import CityModal from "./../views/locations/modals/city.modal.vue";
import LinkBrandModal from "./includes/providers/LinkBrandComponent.vue";
import LinkActorModal from "./includes/providers/LinkActorComponent.vue";

export default {
  mixins: [toast, text],

  components: {
    Create,
    Loading,
    Edit,
    ProviderType,
    JobDescription,
    JobCategory,
    JobProfession,
    PhysicalCodeType,
    InformationType,
    ActorModal,
    ProviderGroup,
    CreateBrand,
    EditProviderService,
    ExistingImage,
    ProviderService,
    ProvinceModal,
    AddToGroupModal,
    CreateService,
    LinkActorModal,
    CityModal,
    LinkBrandModal,
    CreateMedicalTermModal,
    SearchFilter,
    AddPaymentPlanModal,
  },

  data: function() {
    return {

      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],
      itemSelected: {},
      newSpecializationCategories: [],
      newSpecializations: [],
      newPhysicalCodeTypes: [],
      new_other_infos: [],
      newInformationTypes: [],
      professionOption: [],
      linkedMedicalTerm: [],
      paymentPlans: [],
      isLoading: false,
      isSpecializationFocused: false,
      isPaymentPlanModalVisible: false,
      isAlgoliaSyncing: false,
      isFilterOn: false,
      isLoadingServices: false,
      isBrandChanged: false,
      btnloading: false,
      isProviderLoaded: false,
      isCountrySelected: false,
      isServiceTypeSelected: false,
      isServicesLoading: true,
      isAlgoliaAllowed: false,
      isMenuOpened: "",
      itemSelected: "",
      drawer: null,
      filter: "",
      selected: null,
      searched: "",
      filterBy: "",
      termsFilter: "",
      filterBySearch: "",
      selectedByProviderType: "",
      selectedByServiceType: "",
      selectedByService: "",
      selectedByParameter: "",
      selectedByCountry: "",
      selectedByProvinceOrDivison: "",
      selectedByCity: "",
      selectedBrand: "",
      filterServices: "",
      providers_iden: "",
      provider_name: "",
      provider_type_name: "",
      providerTypes: "",
      plan: null,
      isSearchByHasValue: false,
      isProviderType: true,
      isServiceRemoved: false,
      sortBy: 'name',
      sortDesc: false,
      existingImages: [],
      currentPage: 1,
      perPage: 10,
      contact_number: [],
      provider_provider_types: [],
      algoliaSummary: [],
      selectedServices: [],
      originalSelectedServices: [],
      terminologies: [],
      linkedActors: [],
      initialProviderProviderTypesLength: 0,
      filterCountry: "",
      filterActor: "",
      filterProviderType: "",
      filterCity: "",
      filterProvince: "",
      filterProviderBy: "",
      tableTotalRows: "",
      lastPage: "",
      brandId: "",
      language: this.$i18n.locale,
      files: [],
      slide: 0,
      sliding: null,
      termsCurrentPage: 1,
      termsPerPage: 10,
      termsTotalRow: 1,
      tableTermLoading: true,
      isTermOverlay: false,

      paymentplan_modal: {
        id:"",
        isVisible: false,
        type: "create",
        data:{},
        button: {
          name: "SAVE",
          save: "save_button",
          cancel: "cancel",
          loading: false,
        },
      },

      actorshowEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

			actorSortBy: "is_linked_term",
      actorSortDesc: true,

      actorIsPaginate: false,
      actorTableActorLoading: true,
      actorCurrentPage: 1,
      actorPerPage: 10,
      actorTotalRow: 1,
      actorFilter: null,

      actorSelectedTerm: "",
      actorTerm: "",
      actorIsOverlay: false,

      actorTableHeaders: [
        {
          key: "index",
          thClass: "text-left",
          label: this.$t("label.adding_to") + " ?",
          filterByFormatted: true,
        },
      ],

      filterFields: [
        'name',
        'city_item',
        'division_item',
        'website'
      ],

      tableHeaders: [
        {
          key: "name",
          label: this.$t("label.name_of_provider"),
          thClass: "text-left flex-grow-1",
          thStyle: { width: "50%" },
          tdClass: "___w-40",
          sortable: true,
        },

        {
          key: "city",
          label: this.$t("label.city"),
          thClass: "text-center flex-grow-1",
          thStyle: { 'max-width': "323.43" },
          tdClass: "text-center ___w-22",
          sortable: true,
        },

        {
          key: "services_count",
          label: this.$t("label.services_count"),
          thClass: "text-center flex-grow-1",
          thStyle: { 'max-width': "339.43" },
          tdClass: "text-center ___w-21",
          sortable: true,
        },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center flex-grow-1",
          thStyle: { 'max-width': "237.57" },
          tdClass: "text-center ___w-17",
        },
      ],

      tableServicesHeaders: [
        {
          key: "name",
          label: this.$t("service_category"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },

        {
          key: "day_start",
          label: this.$t("label.day_start"),
          thClass: "text-center",
          thStyle: { width: "25%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "day_end",
          label: this.$t("label.day_end"),
          thClass: "text-center",
          thStyle: { width: "25%" },
          tdClass: "text-center",
          sortable: true,
        },

        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "25%" },
          tdClass: "text-center",
        },
      ],

      modal: {
        add: {
          name: "provider_add_new_button",

          id: "provider-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_add_new_button",

            loading: false,
          },
        },

        edit: {
          name: "provider_add_edit_button",

          id: "provider-edit-modal",

          isVisible: false,

          button: {
            update: "button.save_change",

            cancel: "cancel",

            new: "provider_add_new_button",

            loading: false,
          },
        },

        createJobDescription: {
          name: "job_description_new_button",

          id: "job-description-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "job_description_new_button",

            loading: false,
          },
        },

        createActor: {
          name: "actor_add_new_button",

          id: "actor-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "actor_add_new_button",

            loading: false,
          },
        },

        createProviderType: {
          name: "provider_type_add_new_button",

          id: "provider-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        createProviderGroup: {
          name: "provider_group_add_new_button",

          id: "provider-group-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_group_add_new_button",

            loading: false,
          },
        },

        createProviderServices: {
          name: "button.add_services",

          id: "provider-services-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        addToGroup: {
          name: "provider_group",

          id: "provider-group-add-to-group-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_type_add_new_button",

            loading: false,
          },
        },

        selectBrand: {
          name: "provider_type_add_new_button",

          id: "select-brand-modal",

          isVisible: false,
        },

        brand: {
          name: "label.link_to_brand",

          id: "link-brand-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "provider_add_new_button",

            loading: false,
          },
        },

        createBrand: {
          name: "brand_add",

          isVisible: false,

          button: {
              save: "save_button",

              cancel: "cancel",

              new: "content_add_new_button",

              loading: false,
          },
        },

        createPhysicalCodeType: {
          name: "physical_code_type_new_button",

          id: "physical-code-type-add-modal", 

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "physical_code_type_new_button",

            loading: false,
          },
        },

        createInformationType: {
          name: "information_type_new_button",

          id: "information-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "information_type_new_button",

            loading: false,
          },
        },
      },

      sortOptions: [
        { value: "all", text: "All", disabled: true },
        { value: "nsa", text: "No services added" },
        { value: "nrfas", text: "Not ready for Algolia sync" },
        { value: "npt", text: "No provider type" },
      ],

      languageOptions: [
        { value: "en", text: "English" },
        { value: "de", text: "German" },
        { value: "it", text: "Italian" },
        { value: "ph-fil", text: "Filipino" },
        { value: "ph-bis", text: "Visayan" },
      ],

      typeForm: new Form({
        id: "",
        name: "",
        term_type: '',
        action: "",
        term_types: '',
        file: ""
      }),

      mtForm: new Form({
        id: "",
        name: "",
        term_types: "",
        specializations: "",
        action: "",
        file: "",
      }),

      imagesForm: new Form({
        images: [],
        imagesPlaceholder: [],
      }),

      form: new Form({
        id: "",
        name: "",
        provider_type: [],
        provider_type_name: "",
        provider_group_name: "",
        provider_group_image: [],
        provider_group: null,
        country: null,
        division: null,
        facebook_url: "",
        linkedin: "",
        city: null,
        website: "",
        address: "",
        providers: "",
        brand_name: "",
        website: "",
        logo: "",
        isDefault: 0,
        services: "",
        postal_code: "",
        specializationCategories: "",
        fieldOfProfessions: [],
        brands: "",
        physical_codes: [],
        other_infos: [],
        specializations: [],
        provider_other_infos: [],
        action: "",
        language: this.$i18n.locale,
      }),

      formData: new FormData(),
      
    };
  },

  mounted() {
    if(this.$brand == false) return;
    
    this.getAdvanceQueryValues()
    this.loadItems()
    this.checkAlgoliaPermission()
    this.isBrandChanged = true;
    this.isProviderLoaded = false
  },

  methods: {
    ...mapActions("providers", [
      "get_providers",
      "delete_provider",
      "post_sync_reference",
      "remove_from_provider_array",
      "add_provider_profile",
      "update_provider",
      "post_change_account_status",
      "post_sync_to_algolia",
      "get_algolia_summary",
    ]),
    ...mapActions("provider_type", ["get_provider_type_all"]),
    ...mapActions("country", ["get_countries"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities"]),
    ...mapActions("brand", ["get_brands"]),
    ...mapActions("provider_group", ["get_provider_group_all"]),
    ...mapActions("service_type", ["get_service_type_all"]),
    ...mapActions("services", ["get_services"]),
    ...mapActions("provider_service", ["get_provider_service_items"]),
    ...mapActions("parameter", ["get_parameters"]),
    ...mapActions("physical_code_type", ["get_physical_code_type_all"]),
    ...mapActions("information_type", ["get_information_type_all"]),
    ...mapActions("jobs", ["get_jobs", "get_job_categories", "delete_job_description", "get_filtered_job_professions"]),
    ...mapActions("actor", ["get_actors"]),
    ...mapActions("algolia", ["check_permission"]),
    ...mapActions("categ_terms", ["get_terms", "reset_terms", "get_linked_provider_terms", "get_category_of_services_terms", "get_services_category_terms", "get_professional_terms"]),

    async loadItems(isSearched = false) {
      this.isLoading = true;
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.perPage,
        page: this.currentPage, 
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        filterProviderBy : this.filterProviderBy,
        search: this.searched,
      };
      if(this.selectedByProviderType != "") params.providerType = this.selectedByProviderType
      if(this.selectedByServiceType != "") params.serviceType = this.selectedByServiceType
      if(this.selectedByService != "")  params.service = this.selectedByService
      if(this.selectedByParameter != "") params.parameter = this.selectedByParameter
      if(this.selectedByCountry != "") params.country = this.selectedByCountry
      if(this.selectedByProvinceOrDivison != "") params.provinceOrDivision = this.selectedByProvinceOrDivison
      if(this.selectedByCity != "") params.city = this.selectedByCity
      if(isSearched == false) params.isSearched = isSearched

      this.get_providers(params).then((_) => {
        this.tableTotalRows = this.items.total;
        this.lastPage = this.items.last_page;
        if(this.items.data.length == 0) {
          this.isLoading = false
          return
        };
        this.isLoading = false
        this.isProviderLoaded = true;
      });
    },

    async loadAlgoliaSummary(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
      };
      
      this.get_algolia_summary(params).then((data) => {
        this.algoliaSummary = data;
        // this.isProviderLoaded = true;
      });
    },

    async checkAlgoliaPermission() {
      let params = {
        api_token: this.$user.api_token,
        brand_id: this.$brand.id,
      };

      this.check_permission(params).then((data) => {
        this.isAlgoliaAllowed = data;
        if(this.isAlgoliaAllowed) {
          this.loadAlgoliaSummary()
        }
        // this.isProviderLoaded = true;
      });
    },

    async loadBrands() {
      let params = {
        api_token: this.$user.api_token,
        org_id: this.$org_id,
      };
      this.get_brands(params).then((_) => {
        if (this.brands.length == 0) {
          this.$bvModal
            .msgBoxOk(this.$t("brand_not_existing"), {
              title: this.$t("brands_name"),
              okVariant: "success",
              hideHeaderClose: false,
              centered: true,
            })
            .then((value) => {
              window.location.href = `/admin/brands`;
            });
        } else {
          this.isBrandChanged = false
          this.$bvModal.show("select-brand-modal");
        }
      });
    },

    onLinkBrand(item, index) {
      this.form.reset();
      this.editingIndex = item.provider_index;
      this.modalId = this.modal.brand.id;
      this.form.name = item.provider_name;
      this.form.language = this.$i18n.locale;
      this.form.id = item.id;
      let uniqueBrands = [...new Set(item.selected_brands)];
      this.form.brands = uniqueBrands;
      this.modalId = this.modal.brand.id;
      this.loadSpecializationCategories();
      this.$bvModal.show("link-brand-modal");
    },

    loadSpecializationCategories() {
      let params = {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
      };

      this.get_job_categories(params).then(_ => {
          let mapSpecializationCategories = this.categories.map(s => ({
              category: s.category,
              category_name: s.category_name,
              created_at: s.created_at,
              id: s.id,
              is_english: s.is_english,
              is_german: s.is_german,
              is_italian: s.is_italian,
              is_loading: s.is_loading,
              updated_at: s.updated_at,
          }));
          this.newSpecializationCategories = mapSpecializationCategories;
      });
    },

    loadActors() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        entries: this.actorPerPage,
        page: this.actorCurrentPage,
        search: this.actorFilter,
      };
      this.get_actors(params).then((_) => {
        this.actorTotalRow = this.actors.total;
      })
      .catch((error) => {
        console.log(error);
      })
      .finally(() => {
        this.actorTableActorLoading = false;
        this.actorIsOverlay = false;
      });
    },

    successfullySavedProvider() {
      this.$refs.table.refresh();
    },

    onAddServices(provider, index) { 
      this.form.reset();
      this.filterBy = ""
      this.isServicesLoading = true
      this.$bvModal.show("provider-services-add-modal");
      this.isProviderType = false;
      this.providerTypes = provider.provider_and_its_type_items;
      this.modalId = this.modal.createProviderServices.id;
      this.editingIndex = index;
      this.mtForm.term_types = [this.searchService(this.termtypes)]
      
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        provider_types: this.providerTypes,
        groupByTermType: true,
      };

      this.form.language = this.$i18n.locale;
      this.providers_iden = provider.id;
      this.provider_name = provider.provider_name;
      this.get_terms(params).then((_) => {
        this.isSearchByHasValue = false
        this.isServicesLoading = false
      });
      this.loadServiceItems(provider.id)
    },

    async loadServiceItems(id) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        id: id,
      };
      this.get_provider_service_items(params).then((data) => {
        this.originalSelectedServices = data
        this.selectedServices = data
      });
    },

    loadProfessions() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.form.specializationCategories
      };

      this.get_filtered_job_professions(params).then(resp => {
        this.professionOption = resp;
        this.$bvModal.show("job-description-modal");
      });
    },

    loadSpecializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        type: "description",
        filter: this.form.specializationCategories
      };

      this.get_jobs(params).then(_ => {
        let mapSpecializations = this.specializations.map(s => ({
            id: s.id,
            description: s.description,
            description_name: s.description_name,
            profession_description: s.profession_description,
            created_at: s.created_at,
            is_english: s.is_english,
            is_german: s.is_german,
            is_italian: s.is_italian,
            is_loading: s.is_loading,
            job_category_id: s.job_profession.job_category_profession[0].job_category_id,
            job_profession_id: s.job_profession_id,
            updated_at: s.updated_at,
        }));
        this.newSpecializations = mapSpecializations;
      });
    },

    loadProfessionalTerms() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };
      this.get_professional_terms(params).then((_) => {
      });
    },

    async loadTerms() {
      this.loadProfessionalTerms()
      let params = {
        ...this.termDefaultParams,
        perPage: this.termsPerPage,
        page: this.termsCurrentPage,
        filter: this.termsFilter,
      };
      axios
        .get("/api/terms", { params })
        .then((response) => {
          let items = response.data;
          this.terminologies = items.data;
          this.termsTotalRow = this.filter ? items.data.length : items.total;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.tableTermLoading = false;
          this.isTermOverlay = false;
        });
    },

    loadPhysicalCodeTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_physical_code_types(params).then((_) => {
        this.removeDuplicatePCT()
        this.$bvModal.hide("physical-code-type-add-modal");
        this.$bvModal.show(this.modalId);
      });
    },

    loadInformationTypes() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_information_type_all(params).then((_) => {
        this.$bvModal.hide("information-type-add-modal");
        this.newInformationTypes = this.information_types;
        this.$bvModal.show(this.modalId);
      });
    },

    searchService(termTypes, providerType = false){
        let services = [
          'Service',
          'Servizio',
          'Servizi',
          'Serbisyo',
        ]

        let providerTypes = [
          'Provider',
          'Provider Type',
        ]
        if(!providerType) {
          for (var i=0; i < termTypes.length; i++) {
              for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
                if (services.includes(value)) {
                  return termTypes[i];
                }
              }
          }
        }

        for (var i=0; i < termTypes.length; i++) {
            for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
              if (providerTypes.includes(value)) {
                return termTypes[i];
              }
            }
        }
        
    },

    searchProfession(termTypes) {
        let professionKeywords = [
          'Professional', 
          'Professionals', 
          'Professionista',
        ]

        for (var i=0; i < termTypes.length; i++) {
            for (const [key, value] of Object.entries(JSON.parse(termTypes[i].name))) {
              if (professionKeywords.includes(value)) {
                return termTypes[i];
              }
            }
        }
    },

    createActor() {
      this.loadTerms()
      this.form.physical_codes = [];
      this.form.other_infos = [];
      this.form.fieldOfProfessions = [];

      this.addPhysicalCode()
      this.addOtherInfoDiv()
      this.modalId = this.modal.createActor.id;
      this.form.language = this.$i18n.locale;
      let params = {
          api_token: this.$user.api_token,
          lang: this.form.language,
          locale: this.$i18n.locale,
      };

      this.get_physical_code_type_all(params).then((_) => {
        this.get_information_type_all(params).then((_) => {
          this.newPhysicalCodeTypes = this.physical_code_types
          this.newInformationTypes = this.information_types
          this.$bvModal.hide('link-actor-modal');
          this.$bvModal.show("actor-add-modal");
        });
      });
    },

    createPaymentPlan() {
      this.isPaymentPlanModalVisible = true;
      this.isLinked = true;

      setTimeout(() => {
        this.$nextTick(() => {
          this.isLinked = false;
          this.btnloading = false;
          this.$bvModal.show("paymentplanmodal");
        });
      }, 1000);
    },

    addPhysicalCode() {
      this.form.physical_codes.push({
        physical_code_type: "",
        code: "",
      });
      this.filterPhysicalCodeDiv()
    },

    deletePhysicalCode(index) {
      this.form.physical_codes.splice(index, 1);
      this.removeDuplicatePCT()
      this.filterPhysicalCodeDiv()
    },

    filterPhysicalCodeDiv(){
      let filtered = [];
      this.form.physical_codes.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.physical_codes = filtered;
    },

    addSpecializationDiv() {
      this.form.specializations.push({
        profession: "",
        specialization: "",
        category: "",
      });
      this.filterSpecializationDiv()
    },

    deleteSpecializationDiv(index) {
      this.form.specializations.splice(index, 1);
      this.removeDuplicateSpecializations()
      this.filterSpecializationDiv()
    },

    filterSpecializationDiv(){
      let filtered = [];
      this.form.specializations.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.specializations = filtered;
    },

    addProviderOtherInfoDiv() {
      this.form.provider_other_infos.push({
        type: "",
        value: "",
      });
      this.filterProviderOtherInfoDiv()
    },

    deleteProviderOtherInfoDiv(index) {
      this.form.provider_other_infos.splice(index, 1);
      this.filterProviderOtherInfoDiv()
    },

    removeDuplicatePCT() {
      let params = {
          api_token: this.$user.api_token,
          lang: this.form.language,
          locale: this.$i18n.locale,
      };
      this.get_physical_code_types(params).then((_) => {
        this.newPhysicalCodeTypes = this.physical_code_types
        this.form.physical_codes.forEach(physicalCode => {
          if(physicalCode.physical_code_type == null) return;
          let data = $.grep(this.newPhysicalCodeTypes, function(e){ 
            return e.id != physicalCode.physical_code_type.id; 
          });
          this.newPhysicalCodeTypes = data
        });
      });
    },

    removeAllDuplicates() {
      this.removeDuplicateSpecializations()
      this.removeDuplicatePCT()
    },

    removeDuplicateSpecializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        type: "description",
        filter: this.form.specializationCategories
      };

      this.get_jobs(params).then(_ => {
        let mapSpecializations = this.specializations.map(s => ({
            id: s.id,
            description: s.description,
            description_name: s.description_name,
            profession_description: s.profession_description,
            created_at: s.created_at,
            is_english: s.is_english,
            is_german: s.is_german,
            is_italian: s.is_italian,
            is_loading: s.is_loading,
            job_category_id: s.job_profession.job_category_profession[0].job_category_id,
            job_profession_id: s.job_profession_id,
            updated_at: s.updated_at,
        }));
        this.newSpecializations = mapSpecializations;

        this.form.specializations.forEach(s => {
          if(s.profession == null) return;
          let data = $.grep(this.newSpecializations, function(e){ 
            return e.id != s.profession.id; 
          });
          this.newSpecializations = data
        });
      });
    },

    filterProviderOtherInfoDiv(){
      let filtered = [];
      this.form.provider_other_infos.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.provider_other_infos = filtered;
    },

    onFiltered(filteredItems) {
      
      this.tableTotalRows = filteredItems.length
      this.currentPage = 1
    },

    sortProviderBy(value) {
      this.currentPage = 1;
      this.items.data = [];
      this.filterProviderBy = value
      this.loadItems()
    },
    
    resetFormLocations(){
      this.form.country = null;
      this.form.division = null;
      this.form.city = null;
    },
 
    createProvider() {
      this.form.reset();
      this.resetFormLocations();
      this.form.provider_other_infos = [];
      this.addProviderOtherInfoDiv();
      this.isProviderType = true;
      this.modalId = this.modal.add.id;
      this.mtForm.term_types = [this.searchService(this.termtypes, true)]
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.form.language = this.$i18n.locale;
      this.loadLinkedProviderTerms()
      this.loadPaymentPlans();
      // this.get_linked_provider_terms(params).then((_) => {
      this.get_countries(params).then((_) => {
        this.get_information_type_all(params).then((_) => {
          this.newInformationTypes = this.information_types;
          this.$bvModal.show("provider-add-modal");
        });
      });
      // });
    },

    loadServices() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_services(params).then((_) => {
          this.$bvModal.hide("service-add-modal");
          this.$bvModal.show(this.modalId);
      });
    },

    loadProviderTypes() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        filterByProviderType: true,
      };
      this.get_linked_provider_terms(params).then((_) => {
        this.get_countries(params).then((_) => {
          this.$bvModal.hide("provider-type-add-modal");
          this.$bvModal.show(this.modalId);
        });
      });
    },

    loadLinkedProviderTerms() {
      this.loadTerms()
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      this.get_linked_provider_terms(params).then((_) => {
      });
    },

    loadPaymentPlans() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        // brand_id: this.$brand.id,
      };
      axios.get("/api/select/payment_plans/all", { params }).then((resp) => {
        this.paymentPlans = resp.data
      });
    },

    loadProviderGroups() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.get_provider_group_all(params).then((_) => {
          this.$bvModal.hide("provider-group-add-modal");
          this.$bvModal.show(this.modalId);
      });
    },

    providerTypePage(){
      window.location.href = `/admin/provider-type`;
    },

    handleScroll(el) {
      if((el.srcElement.offsetHeight + el.srcElement.scrollTop) >= el.srcElement.scrollHeight) {
        let params = {
          api_token: this.$user.api_token,
          lang: this.form.language,
          brand_id: this.$brand.id,
          entries: this.perPage,
          page: this.currentPage + 1,
          sort_name: this.sortBy,
          sort_desc: this.sortDesc,
          filterProviderBy : this.filterProviderBy,
          search: this.searched,
          isSearched: false,
        };
        
        if(this.selectedByProviderType != "") params.providerType = this.selectedByProviderType
        if(this.selectedByServiceType != "") params.serviceType = this.selectedByServiceType
        if(this.selectedByService != "")  params.service = this.selectedByService
        if(this.selectedByParameter != "") params.parameter = this.selectedByParameter
        if(this.selectedByCountry != "") params.country = this.selectedByCountry
        if(this.selectedByProvinceOrDivison != "") params.provinceOrDivision = this.selectedByProvinceOrDivison
        if(this.selectedByCity != "") params.city = this.selectedByCity

        if(this.lastPage == this.currentPage) return;
        this.get_providers(params).then((_) => {
          if(this.providersResponseStatus == false ) return;
          this.currentPage = this.currentPage + 1;
        });
      }
    },

    async addProviderProfile(id) {
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", id);
      formData.append("lang", this.form.lang);
      formData.append("brand_id", this.$brand.id);
      this.add_provider_profile(formData).then((_) => {
        if (this.providersResponseStatus) {

          this.makeToast(
            "success",
            'PROVIDER PROFILE',
            'Added Successfully'
          );
          this.loadItems();
        }
      });
    },

    filterByCountryOrProviderType(val) {
      this.filterProviderType = val
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByCity(val) {
      this.filterCity = val
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByCountry(val){
      this.filterCountry = val;
      this.filterProvince = ""
      this.filterCity = ""
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByProvince(val){
      this.filterProvince = val
      this.filterCity = "";
      this.filters(this.filterProviderType, this.filterCountry, this.filterProvince, this.filterCity)
    },

    filterByNOT(val) {
      if(val == "all_terms") {
        this.loadProviderServicesGrouped()
        return;
      }
      this.loadProviderServices(val);
    },

    filters(providerType,country,province,city) {
      this.currentPage = 1;
      this.items.data = [];
      this.isLoading = true;
      this.isCountrySelected = false
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        brand_id: this.brandId,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
      };
      if(providerType != "") params.providerType = providerType
      if(country != "") {
        this.isCountrySelected = true
        params.country = country
        let formData = new FormData();
        formData.append("api_token", this.$user.api_token);
        formData.append("id", country);
        formData.append("lang", this.form.language);
          this.get_divisions(formData).then((resp) => {
        });
        formData.append("country_id", 'country_id');
        if(province != "") {
          params.province = province
          formData.set("id", province);
          formData.append("lang", this.form.language);
          formData.delete("country_id")
        }
        if(city != "") {
          params.city = city;
        }
        this.get_cities(formData).then((resp) => {
        });
        
      }
      
      this.get_providers(params).then((_) => {
        this.tableTotalRows = this.items.total;
        if(this.items.data.length == 0) {
          this.isLoading = false
          return
        };
        this.isLoading = false;
      });
    },

    loadProviderServicesGrouped() {
      this.reset_terms()
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
      };
      if(this.isProviderType == true) {
        params['filterByProviderType'] = true
      } else {
        params['provider_type'] = this.providerType,
        params['groupByTermType'] = true
      }
      
      this.get_terms(params).then((_) => {
        this.isSearchByHasValue = false
        this.$bvModal.show(this.modalId);
      });
    },

    loadProviderServices(filter) {
      this.reset_terms()
      this.isSearchByHasValue = true;
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        filter: this.filter,
        brand_id: this.$brand.id,
        term_search: this.filterBySearch,
        provider_type: this.providerType,
        groupByTermType: true,
      };
      if(filter == 'term_name') params.term_name = true;
      this.get_terms(params).then((_) => {
        
      });
    },

    providerGroupPage(){
      window.location.href = `/admin/article-content-maker/provider-groups`;
    },

    hasSpecializationCategory(){
      
      if(this.form.specializationCategories.length == 0) {
        this.newSpecializations = []
        this.form.specializations = [];
        this.addSpecializationDiv()
        return;
      }
      //  console.log(this.form.specializationCategories)
     
      this.loadSpecializations();
      this.removeDuplicateSpecializations()
    },

    hasSpecializationCategoryUpdate() {
      if(this.form.specializationCategories.length == 0) {
        this.newSpecializations = []
        this.form.specializations = [];
        this.addSpecializationDiv()
        return;
      }
      this.loadSpecializations();
      this.removeDuplicateSpecializations()
    },

    onEdit(item, index) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        locale: this.$i18n.locale,
      };
      
      let formData = new FormData();
      let otherInfos = [];
      this.editingIndex = item.provider_index;
      this.form.reset();
      this.imagesForm.reset();
      this.form.provider_other_infos = [];
      this.form.language = this.$i18n.locale;
      this.isProviderType = true;
      this.mtForm.term_types = [this.searchService(this.termtypes, true)]
      this.form.id = item.id;
      this.form.name = item.provider_name;
      this.contact_number = item.contact_nos;
      this.plan = item.payment_plan;
      this.existingImages = item.images == null ? [] : JSON.parse(item.images)
      this.form.postal_code = item.postal_code == null ? "" : item.postal_code;
      this.form.facebook_url = item.facebook_url == null ? "" : item.facebook_url;
      this.form.linkedin = item.linkedin == null ? "" : item.linkedin;
      this.form.address = item.address == null ? "" : item.address;
      this.form.website = item.website == null ? "" : item.website;
      if (item.country_item != null) {
        formData.append("api_token", this.$user.api_token);
        formData.append("id", item.country_item.id);
        formData.append("lang", this.form.language);
        this.form.country = item.country_item
        this.get_divisions(formData).then((resp) => {
          this.form.division = item.division_item
          if(this.form.division == null) {
            formData.append("country_id", 'country_id');
              this.get_cities(formData).then((resp) => {
                
                this.form.city = item.city_item
            });
            return;
          }
          formData = new FormData();
          formData.append("api_token", this.$user.api_token);
          formData.append("id", item.division_item.id);
          formData.append("lang", this.form.language);
          this.get_cities(formData).then((resp) => {
            this.form.city = item.city_item;
          });
        });
      }

      if (item.provider_other_infos.length !=0) {
        item.provider_other_infos.forEach(function (data) {
          otherInfos.push({
            type: data.information_type,
            value: data.value,
          });
        });
      } else {
        otherInfos.push({
          type: "",
          value: "",
        });
      }
      this.form.provider_other_infos = otherInfos
      this.filterProviderOtherInfoDiv();
      this.initialProviderProviderTypesLength = item.provider_and_its_type_items.length
      this.provider_provider_types = item.provider_and_its_type_items;
      this.modalId = this.modal.edit.id;
      this.loadPaymentPlans()
      this.get_information_type_all(params).then((_) => {
        this.newInformationTypes = this.information_types;
        this.loadProviderTypes();
      });
    },

    onDelete(item, index) {
      let providers = item;
      let vm = this;
      $.confirm({
        title:
          vm.$t("confirmation_record_delete").charAt(0) +
          vm
            .$t("confirmation_record_delete")
            .slice(1)
            .toLowerCase(),
        content:
          vm.$t("question_record_delete") +
          `${item.provider_name.bold()}` +
          vm.$t("from") +
          vm.$t("label.providers") +
          vm.$t("records") + "?",
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
                item.is_loading = true;
                let params = {
                  api_token: vm.$user.api_token,
                  id: item.id,
                };
                vm.delete_provider(params)
                  .then((resp) => {
                    item.is_loading = false;
                    
                    if (vm.providersResponseStatus) {
                      vm.remove_from_provider_array(providers) 
                      vm.loadAlgoliaSummary()
                      vm.makeToast(
                        "danger",
                        vm.$t("delete_record"),
                        `${item.name}` +
                          vm.$t("delete_record_message") +
                          vm.$t("label.providers") +
                          vm.$t("records")
                      );
                    }
                  })
                  .catch((error) => {
                    console.log(error);
                  });
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

    onReset() {
      this.language = this.$i18n.locale;
    },

    onAddToGroup(provider, index) {
      this.form.reset();
      this.editingIndex = provider.provider_index;
      this.modalId = this.modal.addToGroup.id;
      let params = { 
        api_token: this.$user.api_token,
        lang: this.form.language,
      };
      this.form.name = provider.provider_name;
      this.form.id = provider.id;
      this.form.language = this.$i18n.locale;
      this.form.provider_group = provider.provider_group_item;
      this.get_provider_group_all(params).then((_) => {
        this.$bvModal.show(this.modalId);
      });
    },

    onAddActor(provider, index) {
      this.form.reset();
      this.modalId = 'link-actor-modal';
      this.linkedActors = [];
      this.editingIndex = provider.provider_index;
      this.itemSelected = provider;
      if (provider.provider_actors.length > 0) {
        provider.provider_actors.forEach((b) => {
          this.linkedActors.push(b.actor);
        });
      }
      this.$bvModal.show('link-actor-modal');
    },

    onAddToSyncReference(provider, index) {
      this.editingIndex = provider.provider_index;
      let params = {
        api_token: this.$user.api_token,
        id: provider.id,
        lang: this.language, 
      };
      this.post_sync_reference(params)
        .then((resp) => {
          let response = this.providersResponseStatus;
          response.index = this.editingIndex;
          this.update_provider(response)
          if(this.filterProviderBy == 'nrfas') this.remove_from_provider_array(provider) 
          this.loadAlgoliaSummary();
          let message = {
            update:
              this.$t("updated_message1") +
              this.$t("label.providers") +
              ` ID: ${provider.id} ` +
              this.$t("updated_message2"),
            title: {
              update: this.$t("record_updated"),
            },
          };
          
          this.successfullySavedProvider()

          this.makeToast(
              "success",
              'SYNC REFERENCE',
              'Successfully Added to Sync'
            );
          
        })
        .catch((error) => {
          console.log(error);
        });
    },

    changeAccountStatus(provider, index) {
      let accountStatus = provider.account_status == "free" ? "upgraded" : "free";
      this.form.reset();
      this.editingIndex = index;
      this.form.id = provider.id;
      this.form.language = this.$i18n.locale;
      this.form.account_status = provider.locale;

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("id", this.form.id);
      formData.append("account_status", accountStatus);

      this.post_change_account_status(formData)
        .then((resp) => { 
          if (this.providersResponseStatus) {
            let response = this.providersResponseStatus;
            response.index = this.editingIndex;
            this.update_provider(response)
            if(provider.provider_and_its_types != 0) this.loadAlgoliaSummary()
            let message = {
              create:
                `${response.provider_name} ${this.$t("account_status_changed_message")} ${accountStatus}`,
              title: {
                create: this.$t("account_status_changed"),
              },
            };

            this.makeToast(
              "success",
              message.title.create,
              message.create
            );
          }
        })
        .catch((error) => {
          console.log(error)
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#name, #provider_type").removeAttr(
            "style"
          );
          for (let field of Object.keys(errors)) {
            
            let el = $(`#${field}`);
            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error-provider">' +
                  errors[field][0] +
                  "</span>"
              )
            );
          }
        });
    },

    syncToAlgolia() {

      let formData = new FormData();
      this.isAlgoliaSyncing = true;
      let params = {
        api_token: this.$user.api_token,
        brand: this.$brand,
      }

      this.post_sync_to_algolia(params)
        .then((resp) => {
          this.isAlgoliaSyncing = false;
          this.loadAlgoliaSummary();
          let message = {
            synced:
              `Providers ${this.$t("toasts.synced_to_algolia")}`,
            notSynced: 
              `${resp.data} providers cannnot be synched to Algolia due to lack of provider type.`,
            title: {
              synced: `PROVIDERS ${this.$t("synced")}`,
              
            },
          };

          this.makeToast(
            "success",
            message.title.synced,
            message.synced
          );
        })
        .catch((error) => {
          let errors = error.response.data.errors;
        });
    },

    modalAddNewProvince(country) {
      this.$bvModal.hide("provider-add-modal");
      this.$refs.provinceModal.InitializeModal(country, "Add", true);
    },

    addedProvince(data) {
      let country = data.country;
      //let province = data.province;
      this.$refs.createComponent.changeCountry(country);
    },

    addOtherInfoDiv() {
      this.form.other_infos.push({
        type: null,
        value: "",
      });
      this.filterOtherInfoDiv();
    },

    deleteOtherInfoDiv(index) {
      this.form.other_infos.splice(index, 1);
      this.filterOtherInfoDiv();
    },

    filterOtherInfoDiv() {
      let filtered = [];
      this.form.other_infos.forEach((data, index) => {
        data.index = index;
        filtered.push(data);
      });
      this.form.other_infos = filtered;
    },

    hideProvinceModal(){
      if(this.modalId == this.modal.add.id){
        this.$bvModal.show("provider-add-modal");
      }
      else{
        //Show edit/update modal here
      }
    },

    modalAddNewCity(data) {
      let country = data.country;
      let province = data.province;

      this.$bvModal.hide("provider-add-modal");
      this.$refs.cityModal.InitializeModal(data, "Add", true);
    },

    addedCity(data) {
      let province = data.province;
      this.$refs.createComponent.changeDivision(province);
    },

    hideCityModal(){
      if(this.modalId == this.modal.add.id){
        this.$bvModal.show("provider-add-modal");
      }
      else{
        //Show edit/update modal here
      }
    },

    sortChanged(e) {
      this.sortBy = e.sortBy
      this.sortDesc = e.sortDesc
      this.currentPage = 1
      this.items.data = []
      this.loadItems()
    },

    onSearch() {
      this.isMenuOpened = false
      this.currentPage = 1;
      this.items.data = [];
      this.isFilterOn = true
      this.loadItems();
    },

    changeBrand() {
      this.loadBrands()
    },

    changeCountry(country) {
      this.selectedByProvinceOrDivison = "";
      this.selectedByCity = "";
      if(country == "") {
        this.isCountrySelected = false;
        return;
      }
      this.isCountrySelected = true
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", country);
      formData.append("lang", this.form.language);
      this.get_divisions(formData).then((resp) => {
      });
      formData.append("country_id", 'country_id');
      this.get_cities(formData).then((resp) => {
      });
    },
    
    changeCategoryOfService(categoryOfService) {
      this.selectedByService = "";
      if(categoryOfService == "") {
        this.isServiceTypeSelected = false;
        return;
      }
      this.isServiceTypeSelected = true
      let params = {
        api_token: this.$user.api_token,
        categoryId: categoryOfService,
        lang: this.form.language,
      };
      this.get_services_category_terms(params).then((_) => {
      });
    },

    changeProvinceOrDivision(pod) {
      this.selectedByCity = "";
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      if(pod == "") {
        formData.append("id", this.selectedByCountry);
        formData.append("country_id", 'country_id');
        this.get_cities(formData).then((resp) => {
        });
        return;
      }
      formData.append("id", pod);
      this.get_cities(formData).then((resp) => {
      });
    },

    informationTypePage() {
      window.location.href = `/admin/information-type`;
    },

    redirectToProviderServices(provider) {
      history.pushState({page: 1}, "title 1", this.getAdvanceSearchQuery())
      if(provider.provider_profiles == null) {
        let formData = new FormData();
        formData.append("api_token", this.$user.api_token);
        formData.append("id", provider.id);
        formData.append("lang", this.form.lang);
        formData.append("brand_id", this.$brand.id);
        this.add_provider_profile(formData).then((_) => {
          if (this.providersResponseStatus) {

            this.makeToast(
              "success",
              'PROVIDER PROFILE',
              'Added Successfully'
            );
            this.loadItems();
          }
        })
        .finally(() => {
          window.location.href = `/admin/article-content-maker/provider-services?id=${provider.id}`;
        });
      } else {
        window.location.href = `/admin/article-content-maker/provider-services?id=${provider.id}`;
      }
      
    },

    redirectToProviderGroup(id) {
      window.location.href = `/admin/article-content-maker/provider-groups-and-providers?id=${id}`;
    },

    currentUrlQuery() {
      return window.location.search
          .replace("?", "")
          .split("&")
          .filter(v => v)
          .map(s => {
              s = s.replace("+", "%20");
              s = s.split("=").map(s => decodeURIComponent(s));
              return {
                  name: s[0],
                  value: s[1]
              };
          });
    },

    getAdvanceQueryValues() {
      this.currentUrlQuery().forEach(query => {
        if(query.name == 'pt') this.selectedByProviderType = parseInt(query.value)
        if(query.name == 'st') this.selectedByServiceType = parseInt(query.value)
        if(query.name == 'se') this.searched = query.value
        if(query.name == 's') {
          this.selectedByService = parseInt(query.value)
          this.changeCategoryOfService(parseInt(query.value));
        }
        if(query.name == 'p') this.selectedByParameter = parseInt(query.value)

        if(query.name == 'c') {
          this.selectedByCountry = parseInt(query.value)
          this.changeCountry(parseInt(query.value))
        }
        if(query.name == 'pod') this.selectedByProvinceOrDivison = parseInt(query.value)
        if(query.name == 'ci') this.selectedByCity = parseInt(query.value)
      });
    },

    getAdvanceSearchQuery() {
      let params = {};

      if(this.searched != '') params.se = this.searched;
      if(this.selectedByProviderType != '') params.pt = this.selectedByProviderType;
      if(this.selectedByServiceType != '') params.st = this.selectedByServiceType;
      if(this.selectedByService != '') params.s = this.selectedByService;
      if(this.selectedByParameter != '') params.p = this.selectedByParameter;
      if(this.selectedByCountry != '') params.c = this.selectedByCountry;
      if(this.selectedByProvinceOrDivison != '') params.pod = this.selectedByProvinceOrDivison;
      if(this.selectedByCity != '') params.ci = this.selectedByCity;

      let esc = encodeURIComponent;
      let query = Object.keys(params)
          .map(k => esc(k) + '=' + esc(params[k]))
          .join('&');

      return '?'+query;
    },

    setObjectToArray(array) {
      if (array.length < 1 && !array) {
        return [];
      }
      let items = [];
      array.forEach((ele) => {
        items.push(ele.id);
      });
      return items;
    },

    performSearch: _.debounce(function(query) {
      this.searched = query
      this.currentPage = 1;
      this.items.data = [];
      this.isLoading = true
      this.loadItems()
    }, 1000)

    
  },

  watch: {

    isMenuOpened(menu_open) {
      if (menu_open === true) {
        let params = {
          api_token: this.$user.api_token,
          lang: this.form.language,
          brand_id: this.$brand.id,
          locale: this.$i18n.locale,
        };

        this.get_countries(params).then((_) => {
        });
        this.get_linked_provider_terms(params).then((data) => {
        });
        this.get_category_of_services_terms(params).then((_) => {
        });
        this.get_parameters(params).then((_) => {
        });
      }
    },
    searched(query) {
      this.performSearch(query)
    },

    perPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    filterActor(value) {
      this.loadActors();
    },

    currentPage: {
      handler: function(value) {
        this.loadItems()
      }
    },

    division(val) {
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", val.id);
      formData.append("lang", this.form.language);
      this.get_cities(formData).then((resp) => {
        this.form.city = "";
      });
    },

    termsCurrentPage(value) {
      this.isTermOverlay = true;
      this.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    termsPerPage() {
      this.isTermOverlay = true;
      this.loadTerms().catch((error) => {
        console.log(error);
      });
    },

    selectedByProviderType() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByServiceType() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByService() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByParameter() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByCountry() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByProvinceOrDivison() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      }
    },

    selectedByCity() {
      if(this.selectedByProviderType == '' && this.selectedByServiceType == '' && this.selectedByService == '' && this.selectedByParameter == '' && this.selectedByCountry == '' && this.selectedByProvinceOrDivison == '' && this.selectedByCity == '') {
        this.loadItems()
      } 
    },

    termsFilter(value) {
      if (!value) {
        this.tableTermLoading = true;
        this.loadTerms().catch((error) => {
          console.log(error);
        });
      }
    },

    actorFilter(value) {
      if (!value) {
        this.actorTableTermLoading = true;
        this.actorIsOverlay = true
        this.loadActors()
      }
    },

    actorPerPage: {
      handler: function(value) {
        this.loadActors()
      }
    },

    actorCurrentPage: {
      handler: function(value) {
        this.loadActors()
      }
    },
  },

  computed: {
    ...mapGetters("providers", {
      items: "providers",
      providersResponseStatus: "get_response_status",
    }),

    ...mapGetters("provider_type", {
      provider_types: "provider_type_all",
      providerTypeResponseStatus: "get_response_status",
    }),

    ...mapGetters("provider_group", {
      provider_groups: "provider_group_all",
      responseStatus: "get_response_status",
    }),

    ...mapGetters("physical_code_type", {
      physical_code_types: "physical_code_type_all",
      physicalCodeTypesResponseStatus: "get_response_status",
    }),
    
    ...mapGetters("information_type", {
      information_types: "information_type_all",
      informationTypesResponseStatus: "get_response_status",
    }),

    ...mapGetters("service_type", {
      service_types: "service_type_all",
    }),

    ...mapGetters("country", {
      countries: "countries",
    }),

    ...mapGetters("division", {
      divisions: "divisions",
    }),

    ...mapGetters("country", {
      countries: "countries",
    }),

    ...mapGetters("city", {
      cities: "cities",
    }),

    ...mapGetters({
      specializations: "jobs/get_job_items",
      categories: "jobs/get_job_categories",
      jobStatus: "jobs/get_job_status"
    }),

    ...mapGetters("brand", {
      brands: "brands",
    }),

    ...mapGetters("parameter", {
      parameters: "parameters",
    }),

    ...mapGetters("actor", {
      actors: "actors",
      // categories: "categories",
      actorsResponseStatus: "get_response_status",
    }),
    
    ...mapGetters("services", {
      services: "services",
      servicesResponseStatus: "get_response_status",
    }),

    ...mapGetters("provider_service", {
      provider_services: "provider_services",
      providerServiceResponseStatus: "get_response_status",
      provider_id: "provider_id",
    }),

    ...mapGetters("categ_terms", {
      terms: "get_terms_items",
      termtypes: "get_type_terms_items",
      professionalTerms: "get_professional_terms_items",
      providerTypeTerms: "get_linked_provider_terms_items",
      categoryServicesTerms: "get_category_services_terms_items",
      servicesCategoryTerms: "get_services_category_terms_items"
    }),

    termDefaultParams() {
        return {
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          brand_id: this.$brand.id,
        }
    },

    totalRows() {
      return this.items.length;
    },

    totalServiceProviderRows() {
      return this.provider_services.length;
    },

    loadFilter(){
      let data = [
        this.selectedByProviderType,
        this.selectedByServiceType,
        this.selectedByService,
        this.selectedByParameter,
        this.selectedByCountry,
        this.selectedByProvinceOrDivison,
        this.selectedByCity,
      ]

      return (data.find(e => e != null))?true:false;
    },
    
  },
};
</script>

<style>
.provider-expansion-panel-header {
  padding: 0px 6px !important;
  min-height: 48px !important;
}

.provider-expansion-panel-header .expansion-panel-title {
  font-size: 0.700rem !important;
}
.provider-expansion-panel-content .v-expansion-panel-content__wrap {
  padding: 0px 7px 7px !important;
}

.provider-popover-text {
  font-size: 0.825rem !important;
  color: #fff !important;
}

.popover-header {
  color: #fff;
  background-color: #fb8c00;
  border-bottom-color: #fb8c00;
}

.popover-body {
  color: #fb8c00;
}

.popover {
  background-color: #ff8e00;
  border-bottom-color: #ff9917;
}

#__bv_popover_109__ .arrow::before {
    border-right-color: #fb8c00 !important;
}
#__bv_popover_109__ .arrow::after {
    border-right-color: #fb8c00 !important;
}
</style>

