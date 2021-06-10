import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";
export default {
  mixins: [toast],
  data() {
    return {
      total_rows: 0,
      showing: {
        to: 0,
        from: 0,
        total: 0,
      },
      isLoading: true,
      isLinked: false,
      listAddShow: true,
      btnloading: false,
      filter: "",
      currentPage: 1,
      language: this.$i18n.locale,
      perPage: 10,
      itemSelected: {},
      categories: [],
      categoryOption: [],
      professionOption: [],

      
      noTranslationsOptions: [
        { value: 'all', text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],

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
        {
          value: "ph-bis",
          text: "Visayan",
        },
        {
          value: "ph-fil",
          text: "Filipino",
        },
      ],

      form: new Form({
        id: "",
        category: "",
        profession: "",
        description: "",
        type: "",
        action: "",
        default: '',
        language: '',
        convertion: ''
      }),

    };
  },
  created() {
    // this.listcaterories();
    // this.listProfession();
  },
  methods: {

    listcaterories() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.language,
        brand_id: this.$brand.id,
        type: 'category'
      };
      axios
        .get("/api/jobs", { params })
        .then((resp) => {
          this.categoryOption = resp.data;
        });
    },

    listProfession() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/select/job_profession/all", {
          params,
        })
        .then((resp) => {
          this.professionOption = resp.data;
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

  watch: {
    language(value) {
      switch (this.form.type) {
        case "category":
          this.form.category = this.setSelectLangauge(value);
          break;
        case "profession":
          this.form.profession = this.setSelectLangauge(value);
          break;
        case "description":
          this.form.description = this.setSelectLangauge(value);
          break;
      }
    },
  },
};
