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
      api_token: this.$user.api_token,
      perPage: 10,
      itemSelected: {},
      editingIndex: null,
      categoryReferrer: null,
      typeReferrer: null,
      isCategoryFormModalOpen: false,
      isTypeFormModalOpen: false,
      tableHeaders: [
        {
          key: "name",
          label: this.$t("label.name"),
          thClass: "text-left",
          sortable: true,
        },
        {
          key: "created_at",
          label: this.$t("table.created_at"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },
        {
          key: "updated_at",
          label: this.$t("table.updated_at"),
          thClass: "text-center",
          thStyle: { width: "15%" },
          tdClass: "text-center",
          sortable: true,
        },
        {
          key: "actions",
          label: this.$t("table.action"),
          thClass: "text-center",
          thStyle: { width: "20%" },
          tdClass: "text-center",
        },
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
          value: "it",
          text: "Italian",
        },
        {
          value: "de",
          text: "German",
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
      document_categories: [],
      form: new Form({
        id: "",
        category_name: "",
        name: "",
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
      attachementTable: []
    };
  },
  methods: {
    async setCategories() {
      await axios
        .get("/api/document_categories", {
          params: {
            api_token: this.api_token,
            locale: this.language,
          },
        })
        .then((response) => {
          this.document_categories = [];
          response.data.map((item) => {
            this.document_categories.push(
              JSON.parse(
                '{"id": ' +
                  item.id +
                  ', "document_category_name": "' +
                  item.document_category_name.replaceAll('"', "'") +
                  '", "category_name": "' +
                  item.document_category_name.replace(/\s<.*/gim, "") +
                  '"}'
              )
            );
          });
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

    resetForm() {
      this.form.id = "";
      this.form.category_name = "";
      this.form.name = "";
      this.form.errors.clear();
    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

  watch: {
    language(value) {
      this.btnloading = true;

      if (this.isCategoryFormModalOpen) {
        axios
          .get("/api/document_categories/" + this.form.id, {
            params: {
              api_token: this.api_token,
              locale: value,
            },
          })
          .then((response) => {
            this.form.category_name = response.data.document_category_name.includes(
              "<small"
            )
              ? null
              : response.data.document_category_name;

            this.btnloading = false;
          });
      } else {
        axios
          .get("/api/document_types/" + this.form.id, {
            params: {
              api_token: this.api_token,
              locale: value,
            },
          })
          .then((response) => {
            this.form.name = response.data.document_type_name.includes("<small")
              ? null
              : response.data.document_type_name;

            this.setCategories();

            this.btnloading = false;
          });
      }
    },
  },
};
