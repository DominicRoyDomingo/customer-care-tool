import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "../../../helpers/toast.js";
export default {
  mixins: [toast],
  data() {
    return {
      isLoading: true,
      listAddShow: true,
      btnloading: false,
      filter: "",
      currentPage: 1,
      language: this.$i18n.locale,
      perPage: 10,
      itemSelected: {},
      
      contact_types: [],

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
          value: "ph-fil",
          text: "Filipino",
        },
        {
          value: "ph-bis",
          text: "Visayan",
        }
      ],

      form: new Form({
        id: "",
        type_name: "",
        action: '',
        default: '',
        language: '',
        conversation: ''
      }),

      sortbyLangId: '',

      noTranslationsOptions: [
        { value: 'all', text: "All", disabled: true },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
      ],

      contactTypeTable: []
    };
  },
  created() {
    // this.listcaterories();
    // this.listProfession();
  },
  methods: {
    listContactTypes() {
      let params = {
        api_token: this.$user.api_token
      };
      axios
        .get("/api/contact_types", {
          params,
        })
        .then((resp) => {
          this.contact_types = resp.data;
        });
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
    
    setSelectLangauge(value) {
      // if (value === "en") {
      //   return this.itemSelected.is_english;
      // } else if (value == "it") {
      //   return this.itemSelected.is_italian;
      // } else {
      //   return this.itemSelected.is_german;
      // }

      if (value === "en") {
        return this.itemSelected.is_english;
      } else if (value == "it") {
        return this.itemSelected.is_italian;
      } else if (value == "de") {
        return this.itemSelected.is_german;
      } else if (value == "ph-fil") {
        return this.itemSelected.is_filipino;
      } else {
        return this.itemSelected.is_visayan;
      }

    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

  watch: {
    language(value) {
      this.form.type_name = this.setSelectLangauge(value);
    },
  },
};
