import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";


export default {
  mixins: [toast],
  data() {
    return {
      isLoading: true,
      isLoadingCountries: true,
      isLoadingProvinces: true,
      isLoadingCities: true,
      isLoadingRegions: true,
      listAddShow: true,
      btnloading: false,
      language: "en",
      itemSelected: {},

      languageOptions: [
        {
          value: "en",
          text: "English",
        },
        {
          value: "de",
          text: "German",
        },
        {
          value: "it",
          text: "Italian",
        },
      ],

      regionFilters: {
        country: "",
        region: "",
      },
      currentPageRegion: 1,
      perPageRegion: 10,
      showEntriesOptRegion: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

      provinceFilters: {
        country: "",
        province: "",
      },
      currentPageProvince: 1,
      perPageProvince: 10,
      showEntriesOptProvince: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],
      
      selection_pool: {
        countries: [],
        provinces: [],
        regions: [],
        //cities: [], cities pool unnecessary
      },

      cityFilters: {
        country: "",
        province: "",
        city: "",
      },
      perPageCity: 10,
      showEntriesOptCity: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],
    };
  },

  mounted() {
    this.fetchCountries();
    this.fetchProvinces();
    // this.fetchRegions();
    //this.fetchCities(); //Call only when needed
  },

  methods: {
    //Jobs
    async fetchCountries() {
      let params = {
        api_token: this.$user.api_token,
      };
      await this.get_countries(params)
      .then((resp) => {
        this.selection_pool.countries = resp.data;
        this.isLoadingCountries = false;
      });
    },

    async fetchProvinces(country_id = "all") {
      let params = {
        api_token: this.$user.api_token,
        country_id: country_id
      };

      await this.get_provinces(params)
      .then((resp) => {
        this.selection_pool.provinces = resp.data;
        this.isLoadingProvinces = false;
      });
    },

    async fetchRegions(country_id = "all") {
      let params = {
        api_token: this.$user.api_token,
        country_id: country_id
      };

      await this.get_regions(params)
      .then((resp) => {
        this.selection_pool.regions = resp.data;
        this.isLoadingRegions = false;
        console.log(this.isLoadingRegions);
      });
    },

    async fetchCities(province_id = "all") {
      let params = {
        api_token: this.$user.api_token,
        province_id: province_id
      };

    await this.get_cities(params)
      .then(() => {
        
        this.isLoadingCities = false;
        console.log(this.isLoadingCities);
      });
    },
    
    setSelectLangauge(value) {
      if (value === "en") {
        return this.itemSelected.is_english;
      } else if (value == "it") {
        return this.itemSelected.is_italian;
      } else {
        return this.itemSelected.is_german;
      }
    },

  },

  computed: {
    totalRegionRows() {
      return this.regions.length;
    },
    totalProvinceRows() {
      return this.provinces.length;
    },
    totalCityRows() {
      return this.cities.length;
    },
    isDoneInitializing(){
      let output = true;

      if(this.isLoadingCountries){
        output = false;
      }

      if(this.isLoadingProvinces){
        output = false;
      }

      if(this.isLoadingCities){
        output = false;
      }
      console.log(this.isLoadingCities)
      if(this.isLoadingRegions){
        output = false;
      }
      
      this.isLoading = false;

      return output;
    }
  },
};
