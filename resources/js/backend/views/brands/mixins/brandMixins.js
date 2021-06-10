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
      language: "en",
      perPage: 10,
      itemSelected: {},
      brands: [],

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

      modal: {

        add: {

          name: "category_add_new_button",

          isVisible: false,

          button: {

            save: "save_button",

            cancel: "cancel",

            new: "category_add_new_button",

            loading: false

          }

        },

      },


      sortbyLang: '',
      noTranslationsOptions: [
        { value: 'all', text: "All" },
        { value: "en", text: "No English Translation" },
        { value: "de", text: "No German Translation" },
        { value: "it", text: "No Italian Translation" },
        { value: "ph-fil", text: "No Filipino Translation" },
        { value: "ph-bis", text: "No Visayan Translation" },
     ],

      form: new Form({
        id: "",
        name: "",
        categories: [],
        website: "",
        logo: "",
        isDefault: 0,
        action: ""
      }),

      formData: new FormData(),


      no_image: 'https://customer-care-tool.s3.us-east-2.amazonaws.com/image-placeholder/image-placeholder.png'
    };
  },
  created() {
    // this.listcaterories();
    // this.listProfession();
  },
  methods: {
    listBrands() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/brands", {
          params,
        })
        .then((resp) => {
          this.brands = resp.data;
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
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

};
