import axios from "axios";
import Form from "../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";


function variable() {
  return {
    variable_name: "",
    variable_text: "",
    variable_type: "Text"
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
      
      templates: [],

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
        id: "",
        template_name: "",
        preview: "",
        variables: [],
        html_content: "",
        action: ""
      }),
    };
  },
  created() {
    // this.listcaterories();
    // this.listProfession();
  },
  methods: {
    listTemplates() {
      let params = {
        api_token: this.$user.api_token,
      };
      axios
        .get("/api/templates", {
          params,
        })
        .then((resp) => {
          this.templates = resp.data;
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

    //Variables
    addVariable() {
      this.form.variables.push(variable());
    },

    deleteVariable(index) {
      this.form.variables.splice(index, 1);
    },
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

};
