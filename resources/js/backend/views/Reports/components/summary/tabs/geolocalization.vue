<template>
  <div class="row">
    <div class="col-md-12">
      <h4 class="card-title text-uppercase mb-0">
        {{ $t("reports") }}
        <small class="text-muted text-capitalize">
          {{ $t("summary") }}
        </small>
      </h4>
    </div>
    <div class="col-md-3">
      <div class="card border-0 mb-0">
        <div
          class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
        >
          <h6 class="m-0 font-weight-bold text-muted text-uppercase">
            {{ $t("filter_by") }} :
            <span class="font-weight-normal text-capitalize">
              {{ $t("localization") }}
            </span>
          </h6>
        </div>
        <div class="card-body mb-2 bg-light">
          <form action="">
            <div class="form-group">
              <b-form-select
                v-model="selectedLocalization"
                :options="localizations"
                class="mb-3"
                value-field="value"
                text-field="text"
                @change="onSelectLocalization"
              >
                <template #first>
                  <b-form-select-option :value="''" disabled>
                    Select Localization
                  </b-form-select-option>
                </template>
              </b-form-select>
            </div>

            <div class="form-group" v-if="selectedLocalization != ''">
              <v-select
                label="place"
                :options="places"
                :filterable="false"
                v-model="selectedPlaces"
                multiple
                @option:selected="onSelectPlace"
                @option:deselected="onDeselectPlace"
                @open="onOpen"
                @close="onClose"
                @search="query => searchPlace = query"
              >
                <template #list-footer>
                  <li ref="load" class="loader" v-show="hasNextPage">
                    Loading more options...
                  </li>
                </template>
                <!-- <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                      <b-link href="#" @click="openCityModal">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ $t("label.new_city") }}
                      </b-link>
                  </li>
                </template>  -->
              </v-select>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-9">
       <div class="row mt-0 justify-content-between">
        <div class="col-md-5 row align-items-center">
          <span class='col-md-2'>Show:</span>
          <select class='form-control col-md-3 p-0 px-2' placeholder="Display" v-model="perPage">
            <option value='10'>10</option>
            <option value='20'>20</option>
            <option value='30'>30</option>
            <option value='40'>40</option>
            <option value='50'>50</option>
            <option value='100'>100</option>
          </select>
          <span class='col-md'>Entries</span>
        </div>
        <div class='form-group col-md-4'>
          <input type='text' class='form-control' placeholder='Search' v-model="searchbar" v-on:keyup="onSearch"/>
        </div>
      </div>
      <div class="row mb-0">
        <div class="col-md-12 mb-0">
          <b-overlay
            id="overlay-background"
            :show="isLinked"
            :variant="'light'"
            opacity="0.85"
            :blur="'1px'"
            rounded="sm"
          >
            <b-table
              class="mb-0"
              striped
              show-empty
              :empty-text="$t('no_record_found')"
              :fields="tableHeaders"
              :per-page="perPage"
              :busy="isLoading"
              :items="items.data"
            >
              <template v-slot:table-busy>
                <div class="d-flex justify-content-center p-2">
                  <b-spinner label="Small Loading..."></b-spinner>
                </div>
              </template>
              <!-- <template v-slot:cell(term_name)="data">
                <span>
                  <ul class="list-unstyled pl-0">
                    <b-media tag="li">
                      <template v-if="data.item.img_url" #aside>
                        <b-img
                          :src="data.item.img_url"
                          alt="placeholder"
                          style="width: 50px; height: 30px"
                        />
                      </template>
                      <span
                        class="mt-0 mb-1 font-weight-bold"
                        v-html="data.item.term_name"
                      />
                    </b-media>
                  </ul>
                </span>
              </template> -->

              <template v-for="(field,index) in tableHeaders" v-slot:[`cell(${field.key})`]="data">
                <span :key="index" v-if="index == 0">
                  <ul class="list-unstyled pl-0">
                    <b-media tag="li">
                      <template v-if="data.item.img_url" #aside>
                        <b-img
                          :src="data.item.img_url"
                          alt="placeholder"
                          style="width: 50px; height: 30px"
                        />
                      </template>
                      <span
                        class="mt-0 mb-1 font-weight-bold"
                        v-html="data.item.term_name"
                      />
                    </b-media>
                  </ul>
                </span>

                <span :key="index" v-else>
                  <ul class="list-unstyled pl-0">
                    <b-media tag="li">
                      <v-tooltip right>
                        <template v-slot:activator="{ on, attrs }">
                          <span 
                            class="text--disabled"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <a href="#" @click.prevent="onShowProvider(field.key, data.item.places[field.key].providers, data.item.base_name)">
                              {{ data.item.places[field.key].count_providers }}
                            </a>
                          </span>
                        </template>
                        <span>{{ $t('tooltips.view_provider_list')}}</span>
                      </v-tooltip>
                    </b-media>
                  </ul>
                </span>
              </template>
            </b-table>
          </b-overlay>
        </div>
      </div>
      <div class="row mt-0" v-if="total_rows > 0">
        <div class="col-md-6">
          <span>
            Showing
            <strong v-text="showing.from" />
            to
            <strong v-text="showing.to" />
            of
            <strong v-text="showing.total" />
            {{ $t("label.entries") }}
          </span>
        </div>
        <div class="col-md-6">
          <b-pagination
            v-model="currentPage"
            :total-rows="total_rows"
            :per-page="perPage"
            align="right"
          />
        </div>
      </div>
    </div>
    <ProviderModalShow :parent="this"></ProviderModalShow>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

// Helpers
import Form from "../../../../../helpers/form";
import * as moment from "moment/moment";

// Mixins
import ReportMixin from "../../../mixins/ReportMixin";

// Modals
import ProviderModalShow from "../modals/list_of_providers.vue";

export default {
  mixins: [ReportMixin],
  components: { ProviderModalShow },
  name: "InfiniteScroll",
  data() {
    // first day of the week
    let startDate = this.local_string(moment().startOf("week"));

    // Last day of the week
    let endDate = this.local_string(moment().endOf("week"));

    return {
      isFetching: false,
      showChart: false,
      isRefresh: false,
      date: {},
      localizations: [
        { value: 'City', text: 'Cities' },
        { value: 'Province', text: 'Provinces' },
        { value: 'Region', text: 'Regions' },
      ],

      selectedLocalization: "",

      form: new Form({
        id: "",
        date: "",
      }),

      isLinked: false,
      searchbar : '',
      place : null,
      isLoading: true,
      currentPage: 1,
      perPage: 10,
      total_rows: 0,
      sortbyLang: '',
      filter: "",
      sortbyTerm: "",
      showing: {
        to: 0,
        from: 0,
        total: 0,
      },
      selectedPlaces: [],
      ul: "",
      scrollTop: "",
      observer: null,
			limit: 10,
      places: [],
      places: [],
      placePage: 1,
      placesLength: 0,
      searchPlace: "",
      searchedPlace: "",
      provincePlaces: [],
      provincePlacePage: 1,
      provincePlacesLength: 0,
      provinceSearchPlace: "",
			provinceSearchedPlace: "",
			regionPlaces: [],
      regionPlacePage: 1,
      regionSearchPlace: "",
      regionSearchedPlace: "",
      tableHeaders: [
        {
          key: "term_name",
          label: this.$t("label.terminilogy"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
      ],
      place: "",
      term_name: "",
      providers: [],
    };
  },

  created() {
    this.load_service_terminology_items(); // load statistic table
  },

  mounted () {
    this.observer = new IntersectionObserver(this.infiniteScroll);
  },
  computed: {
    ...mapGetters("categ_terms", {
      items: "get_services_terms_items",
    }),

    filtered () {
			// console.log(parent.places)
			return this.parent.parent.places.filter(place => place.place.toLowerCase().includes(this.search.toLowerCase()));
		},
		paginated () {
			return this.filtered.slice(0, this.limit);
		},
		hasNextPage () {
			return this.placesLength != 0;
		},
  },

  methods: {
    ...mapActions("categ_terms", ["get_services_terms"]),
    ...mapActions("city", ["get_all_city"]),
    ...mapActions("division", ["get_all_division"]),

    load_service_terminology_items(place = "") {
      this.place = place;

      let params = { 
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
        filter: this.filter,
        sortbyLang: this.sortbyLang,
        sortbyTerm: this.sortbyTerm,
        perPage: this.perPage,
        page: this.currentPage,
        places: this.selectedPlaces,
        search : this.searchbar
      };
      let vm = this;
      this.get_services_terms(params)
        .then((_) => {
          
          this.total_rows = this.filter ? this.items.data.length : this.items.total;
          this.showing.to = this.items.to;
          this.showing.from = this.items.from;
          this.showing.total = this.items.total;
          if(place != "") {
       
              vm.tableHeaders.push({
                key: place.place,
                label: `${place.place.slice(0,place.place.lastIndexOf(","))} Providers`,
                thClass: "text-left",
                thStyle: { width: "25%" },
                sortable: true,
              })
          }
        })
        .finally(() => {
          this.isLinked = false;
          this.isLoading = false;
        });
    },

    onSearch(){
        var place = this.place
        let params = { 
          api_token: this.$user.api_token,
          locale: this.$i18n.locale,
          brand_id: this.$brand.id,
          filter: this.filter,
          sortbyLang: this.sortbyLang,
          sortbyTerm: this.sortbyTerm,
          perPage: this.perPage,
          page: this.currentPage,
          places: this.selectedPlaces,
          search : this.searchbar
        };

        let vm = this;
        this.get_services_terms(params)
          .then((_) => {
            
            this.total_rows = this.filter ? this.items.data.length : this.items.total;
            this.showing.to = this.items.to;
            this.showing.from = this.items.from;
            this.showing.total = this.items.total;
            if(place != "") {
         
                vm.tableHeaders.push({
                  key: place.place,
                  label: `${place.place.slice(0,place.place.lastIndexOf(","))} Providers`,
                  thClass: "text-left",
                  thStyle: { width: "25%" },
                  sortable: true,
                })
            }
          })
          .finally(() => {

          });
    },
    on_select_localization(value) {
      this.date = !_.isEmpty(this.date) ? this.date : this.dateModel;
      this.items = [];
      this.isFetching = true;
      this.load_insight_items();
    },

    on_refresh() {
      this.isRefresh = true;
      this.load_insight_items();
    },

    set_card_box_value(name) {
      let array = [];
      this.items.forEach((item) => {
        item.summaries.forEach((sum) => {
          if (sum.name === name) {
            array.push({
              title: sum.title,
              value: sum.value,
            });
          }
        });
      });
      return array;
    },

    view_item_name(data, type) {
        let name = "";
        switch (this.$i18n.locale) {
          case "en":
              name = data.is_english;
              break;
          case "de":
              name = data.is_german;
              break;
          case "it":
              name = data.is_italian;
              break;
          case "ph-fil":
              name = data.is_filipino;
              break;
          case "ph-bis":
              name = data.is_bisaya;
              break;
        }

        if (type === 'type') {
          return name ? name : data.term_type_name;
        }

        if (type === 'spec') {
          return name ? name : data.description_name;
        }

    },

    async onOpen () {
      if (this.hasNextPage) {
        await this.$nextTick();
        this.observer.observe(this.$refs.load)
      }
    },
    
    onClose () {
      this.observer.disconnect();
    },

    async infiniteScroll ([{isIntersecting, target}]) {
      if (isIntersecting) {
        setTimeout(() => {
          this.ul = target.offsetParent;
          this.scrollTop = target.offsetParent.scrollTop;
          this.placePage = this.placePage+1;
          let params = {
            api_token: this.$user.api_token,
            lang: this.$i18n.locale,
            page: this.placePage,
            search: this.searchedPlace,
          };
          this.get_all_city(params).then((data) => {
            this.placesLength = data.length
            this.places = this.places.concat(data);
          });
        }, 1000);
      }
    },

   async loadPlaces(isSearched = false) {
      let params = {
        api_token: this.$user.api_token,
        lang: this.$i18n.locale,
        page: this.placePage,
        search: this.searchedPlace,
      };
      switch (this.selectedLocalization) {
        case 'City':
          this.get_all_city(params).then((data) => {
            this.placesLength = data.length;
            if(this.placePage == 1 || isSearched == true) {
              this.places = data;
              return;
            }
            this.places = this.places.concat(data);
          });
          break;
        case 'Province':
          this.get_all_division(params).then((data) => {
            this.placesLength = data.length;
            if(this.placePage == 1 || isSearched == true) {
              this.places = data;
              return;
            }
            this.places = this.places.concat(data);
          });
          break;
      }
      
    },

    onSelectLocalization() {
      this.selectedPlaces = []
      this.tableHeaders = [
        {
          key: "term_name",
          label: this.$t("label.terminilogy"),
          thClass: "text-left",
          thStyle: { width: "25%" },
          sortable: true,
        },
      ],
      this.loadPlaces();
    },

    onSelectPlace(place) {
      let lastPlace = place[place.length - 1];
      this.load_service_terminology_items(lastPlace);
    },
    
    onDeselectPlace(value) {
      console.log('descelect');
      let tableHeaders = this.tableHeaders
      let indexOfTableHeader = tableHeaders.findIndex(tableHeader => tableHeader.key === value.place);
      if (indexOfTableHeader > -1) {
        tableHeaders.splice(indexOfTableHeader, 1);
      }
      this.tableHeaders = tableHeaders
    },

    onShowProvider( place, providers, termName ) {
      this.place = place;
      this.providers = providers;
      this.term_name = termName
      this.$bvModal.show("show-providers-list");
    },

    performSearchVariousPlace: _.debounce(function(query,name) {
      switch (name) {
        case 'city':
          this.searchedPlace = query
          break;
        case 'province':
          this.provinceSearchedPlace = query
          break;
        case 'region':
          this.regionSearchedPlace = query
          break;
      }
    }, 1000)
  },
  watch: {
    currentPage(value) {
      this.isLinked = true;
      this.load_service_terminology_items();
    },

    perPage() {
      this.isLinked = true;
      this.load_service_terminology_items();
    },

    places: {
      handler: function(value) {
        if(this.ul != "") {
          setTimeout(() => {
            this.ul.scrollTop = this.scrollTop;
          }, 10);
        }
      }
    },

    searchPlace(query) {
      this.performSearchVariousPlace(query,'city')
    },

    searchedPlace: {
      handler: function(value) {
        this.placePage = 1
        this.loadPlaces(true)
      }
    },
  },
};
</script>

