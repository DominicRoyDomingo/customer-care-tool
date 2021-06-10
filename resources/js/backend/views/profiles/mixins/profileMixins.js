import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";

function client_engagement_form() {
  return {
    engagement: null,
    engagement_id: null,
    engagement_date: formatDateToYMD(new Date()),
    platform_id: null,
    details: "",
  };
}

function attachment_form() {
  return null;
}

function attachment_info_form() {
  return {
    id: null,
    file_url: null,
    description: "",
    document_category_id: null,
    document_type_id: null,
    tracker_notes: "",
  };
}

export default {
  mixins: [toast],
  data() {
    return {
      isLoading: true,
      listAddShow: true,
      btnloading: false,
      filter: "",
      currentPage: 1,
      language: "en",
      perPage: 10,
      itemSelected: {},
      profileSelected: {
        profile_name: "",
      },
      profileViewed: {
        profile_name: "",
      },
      loadedProfile: {},
      loadedBrand: {},
      editingIndex: 0,
      editingItem : null,
      profileModalTabIndex: 0,

      name_has_matches: false,
      primary_email_matches: false,
      contacts_have_matches: false,

      meta: {
        page: "",
      },
      date_today: "",

      //Main Profile Resources
      brands: [],
      categories: [],
      contact_types: [],
      document_categories: [],
      document_types: [],
      descriptions: [],
      engagements: [],
      platform_items: [],
      platform_types: [],
      professions: [],
      profiles: [],

      //locations
      countries: [],
      provinces: [],
      cities: [],

      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

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

      form: new Form({
        id: null,
        profile_id: null,

        //Profile Form Controls
        add_contacts: false,
        add_notes: false,
        add_jobs: false,
        add_engagements: false,
        add_attachments: false,
        email_match_found: false,
        email_matched_profile: {},
        email_matching_state: "Default",
        email_match_found: false,
        email_matched_profile: {},
        email_matching_state: "Default",
        date_today: "",

        //Common Data
        profile_name: "",
        first_name: "",
        middlename: "",
        surname: "",
        nickname: "",
        primary_email: "",

        //profile
        notes: [],
        contacts: [],
        brands: [],
        jobs: [],
        engagements: [],
        attachments: [],
        attachments_info: [],

        //brand
        name: "",
        website: "",
        logo: "",

        //contact type
        type_name: "",

        //location
        location: {
          country_id: null,
          province_id: null,
          city_id: null,
        },

        country_name: "",

        //province
        province_name: "",

        //city
        city_name: "",

        //jobs
        category: "",
        profession: "",
        description: "",

        //engagement
        engagement: "",
        engagement_brands: [],
        administrative: "",
        activity: "",

        //platform
        brand_id: null,
        platform_type_id: null,

        //client engagements
        client_engagements: [],

        //For main form
        type: "",
        action: "",

        //For modals
        sub_type: "",
        sub_action: "",
      }),

      isTypeFormModalOpen: false,
      isCategoryFormModalOpen: false,
    };
  },
  created() {
    var today = new Date();
    var dd = today.getDate();

    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = "0" + dd;
    }

    if (mm < 10) {
      mm = "0" + mm;
    }

    this.date_today = yyyy + "-" + mm + "-" + dd;
  },
  methods: {
    //Jobs
    async fetchCountries() {
      let params = {
        api_token: this.$user.api_token,
      };
      await axios
        .get("/api/location/countries", {
          params,
        })
        .then((resp) => {
          this.countries = resp.data;
        });
    },

    async fetchProvinces(country_id) {
      let params = {
        api_token: this.$user.api_token,
      };

      this.form.country_name = this.countries.filter(
        (country) => country.id == this.form.location.country_id
      )[0].name;

      this.form.location.province_id = null;
      this.form.location.city_id = null;

      await axios
        .get("/api/location/provinces/" + country_id, {
          params,
        })
        .then((resp) => {
          this.provinces = resp.data;
        });
    },

    async fetchCities(province_id) {
      let params = {
        api_token: this.$user.api_token,
      };

      this.form.province_name = this.provinces.filter(
        (province) => province.id == this.form.location.province_id
      )[0].name;

      this.form.location.city_id = null;

      await axios
        .get("/api/location/cities/" + province_id, {
          params,
        })
        .then((resp) => {
          this.cities = resp.data;
        });
    },

    //Jobs
    fetchCategories() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/select/job_category/all", {
          params,
        })
        .then((resp) => {
          this.categories = resp.data;
        });
    },

    fetchProfessions() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/select/job_profession/all", {
          params,
        })
        .then((resp) => {
          this.professions = resp.data;
        });
    },

    fetchDescriptions() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/select/job_description/all", {
          params,
        })
        .then((resp) => {
          this.descriptions = resp.data;
        });
    },
    //Jobs

    //Actions
    fetchEngagements() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/actions/engagements", {
          params,
        })
        .then((resp) => {
          this.engagements = resp.data;
        });
    },

    fetchActivity() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/actions/activity", {
          params,
        })
        .then((resp) => {
          this.activity = resp.data;
        });
    },

    //Platforms
    fetchPlatformItems() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/platform/items", {
          params,
        })
        .then((resp) => {
          this.platform_items = resp.data;
        });
    },

    fetchPlatformTypes() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/platform/types", {
          params,
        })
        .then((resp) => {
          this.platform_types = resp.data;
        });
    },

    //Brands
    fetchBrands() {
      let params = {
        api_token: this.$user.api_token,
        org_id: this.$org_id,
      };
      axios
        .get("/api/brands", {
          params,
        })
        .then((resp) => {
          console.log(resp.data)
          this.brands = resp.data;
        });
    },

    //Brands
    fetchContactTypes() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/contact_types", {
          params,
        })
        .then((resp) => {
          this.contact_types = resp.data;
        });
    },

    async fetchDocumentCategories() {
      let params = {
        api_token: this.$user.api_token,
      };

      await axios
        .get("/api/document_categories/", {
          params,
        })
        .then((resp) => {
          this.document_categories = resp.data;
        });
    },

    fetchDocumentCategory(document_category_id = "all") {
      let params = {
        api_token: this.$user.api_token,
        document_category_id: document_category_id,
      };

      return new Promise((resolve, reject) => {
        axios
          .get("/api/document_categories/", {
            params,
          })
          .then((resp) => {
            commit("set_response_status", resp.data);
            resolve(resp.data);
          })
          .catch((error) => reject(error));
      });
    },

    async fetchDocumentTypes() {
      let params = {
        api_token: this.$user.api_token,
      };

      await axios
        .get("/api/document_types/", {
          params,
        })
        .then((resp) => {
          this.document_types = resp.data;
        });
    },

    //Initialize all resources and fetch all data
    InitializeProfileResources(init_for = "all") {
      if (init_for == "jobs" || init_for == "all") {
        this.fetchCategories();
        this.fetchProfessions();
        this.fetchDescriptions();
      }

      if (init_for == "client_engagements" || init_for == "all") {
        this.fetchBrands();
        this.fetchEngagements();
        this.fetchPlatformItems();
        this.fetchPlatformTypes();
      }

      if (init_for == "contacts" || init_for == "all") {
        this.fetchContactTypes();
      }

      if (init_for == "brands" || init_for == "all") {
        this.fetchBrands();
      }

      if (init_for == "location" || init_for == "all") {
        this.fetchCountries();
      }

      if (init_for == "attachments" || init_for == "all") {
        this.fetchDocumentTypes();
        this.fetchDocumentCategories();
      }

      this.fetchActivity();
    },

    initBrand(brandId = 0) {
      if (brandId != 0 && brandId != "0") {
        this.loadBrand(brandId).then(() => {
          this.initProfile();
        });
      } else {
        this.loadedBrand = {
          id: 0,
          brand_name: "No Brand",
        };
        this.initProfile();
      }
    },
    //Notes
    addNote() {
      this.form.notes.push({
        date_requested: this.date_today,
        notes: "",
      });
    },

    deleteNote(index) {
      this.form.notes.splice(index, 1);
    },

    //Contacts
    addContact() {
      this.form.contacts.push({
        id: null,
        contact_type_id: null,
        contact_info: "",
      });
    },

    deleteContact(index) {
      this.form.contacts.splice(index, 1);
    },

    //Jobs
    addJob() {
      this.form.jobs.push({
        id: "",
        job_category_id: null,
        job_profession_id: null,
        job_description_id: null,
      });
    },

    deleteJob(index) {
      this.form.jobs.splice(index, 1);
    },

    //Client Engagements
    addClientEngagement() {
      this.form.client_engagements.push(client_engagement_form());
    },

    deleteClientEngagement(index) {
      this.form.client_engagements.splice(index, 1);
    },

    //Client Engagements
    addAttachment() {
      this.form.attachments.push(attachment_form());
      this.form.attachments_info.push(attachment_info_form());
    },

    deleteAttachment(index) {
      this.form.attachments.splice(index, 1);
      this.form.attachments_info.splice(index, 1);
    },

    resetBrands() {
      this.form.brands = [];
    },

    resetBrandForm() {
      this.form.name = "";
      this.form.website = "";
      this.form.logo = "";
    },

    resetClientEngagementsForm() {
      this.form.client_engagements.splice(
        0,
        this.form.client_engagements.length
      );
      this.addClientEngagement();
    },

    resetContactTypeForm() {
      this.form.type_name = "";
    },

    resetProvinceForm() {
      this.form.province_name = "";
    },

    resetCityForm() {
      this.form.city_name = "";
    },

    //Job
    resetCategoryForm() {
      this.form.category = "";
    },
    resetProfessionForm() {
      this.form.category = "";
      this.form.profession = "";
    },
    resetDescriptionForm() {
      this.form.profession = "";
      this.form.description = "";
    },

    resetEngagementForm() {
      this.form.engagement = "";
      this.form.engagement_brands = [];
    },

    resetPlatformItemForm() {
      this.form.platform_type_id = null;
      this.form.brand_id = null;
    },

    deepCopy(variable_to_copy) {
      return _.cloneDeep(variable_to_copy);
      return JSON.parse(JSON.stringify(variable_to_copy));
    },

    clearErrors() {
      this.form.errors.errors = {};
    },

    resetJobsForm() {
      this.form.category = "";
      this.form.profession = "";
      this.form.description = "";
    },

    resetContacts() {
      this.form.contacts = [];
    },

    resetProfileForm() {
      this.form.id = null;
      this.form.profile_id = null;

      this.form.first_name = "";
      this.form.middlename = "";
      this.form.surname = "";
      this.form.nickname = "";
      this.form.primary_email = "";

      this.form.add_notes = false;
      this.form.add_contacts = false;
      this.form.add_jobs = false;
      this.form.add_engagements = false;
      this.form.add_attachments = false;

      this.form.notes = [];
      this.form.contacts = [];
      this.form.brands = [];
      this.form.engagements = [];
      this.form.jobs = [];
      this.form.client_engagements = [];
      this.form.attachments = [];
      this.form.attachments_info = [];

      this.form.location = {
        country_id: null,
        province_id: null,
        city_id: null,
      };

      this.form.category = "";
      this.form.profession = "";
      this.form.description = "";

      this.form.engagement = "";
      this.form.administrative = "";
      this.form.activity = "";
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
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },
};
