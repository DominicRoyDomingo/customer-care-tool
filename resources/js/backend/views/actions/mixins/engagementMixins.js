import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";
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
      editingIndex: null,

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
        },
      ],

      form: new Form({
        id: "",
        activity: "",
        engagement: "",
        brands: [],
        administrative: "",
        action: "",
        previous_action: "",
        type: "",
        name: "",
        website: "",
        logo: "",
        language: '',
        conversation: '',
        default: ''
      }),
    };
  },
  created() {
    // this.listcaterories();
    // this.listProfession();
  },
  methods: {
    setSelectLangauge(value) {
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

    resetBrandForm() {
      this.form.name = "";
      this.form.website = "";
      this.form.logo = "";
    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

  watch: {
    language(value) {
      if (this.itemSelected.action_name) {
        this.form.administrative = this.setSelectLangauge(value);
      } else if (this.itemSelected.engagement_name) {
        this.form.engagement = this.setSelectLangauge(value);
      } else if (this.itemSelected.activity) {
        this.form.activity = this.setSelectLangauge(value);
      }
    },
  },
};
