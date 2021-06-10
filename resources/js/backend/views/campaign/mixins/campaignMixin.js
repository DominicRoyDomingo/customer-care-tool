import axios from "axios";
import Form from "./../../../helpers/form.js";
import toast from "./../../../helpers/toast.js";
import Snackbar from "./../components/Snackbar.vue";

import { mapGetters, mapActions } from "vuex";


export default {
  mixins: [toast],
  data() {
    return {
      isSearchRecipient: false,
      recipientDisabled: true,
      btnLoading: false,
      btnloading: false,
      isLoading: true,
      currentPage: 1,
      perPage: 10,
      filter: "",
      selectedItem: "",

      snackbar: false,
      snackbarText: "",
      snackbarColor: "",
      snackbarTimeout: "",

      tab: null,
      selectedCampaign: null,

      language: this.$i18n.locale,

      defaultState: {
        sender_name: this.$user.full_name,
        sender_email: this.$user.email,
      },

      form: new Form({
        id: "",
        campaign: "",
        recipient: "",
        subject: "",
        brand: "",
        sender_name: this.$user.full_name,
        sender_email: this.$user.email,
        content: "",
        template: null,
        variables: null,
        action: "",
        sent: "",
      }),

      showEntriesOpt: [
        { value: 10, text: "10" },
        { value: 30, text: "30" },
        { value: 50, text: "50" },
        { value: 100, text: "100" },
      ],

      modal: {
        name: '',
        title: "",
        button: {
          loading: false,
          name: "",
        },
      },

      languageOptions: [
        { value: "en", text: "English" },
        { value: "de", text: "German" },
        { value: "it", text: "Italian" },
      ],

      // for select
      profileOptions: [],
      campaigns: [],
      brands: [],

    };
  },

  computed: {},

  methods: {
    ...mapActions("profile", ["on_recipient"]),

    resetCampaignData(){
      this.form.template = null;
      this.form.campaign = "";
      this.form.brand = null;
      this.form.sender_name = this.defaultState.sender_name;
      this.form.sender_email = this.defaultState.sender_email;
      this.form.subject = "";
      this.form.content = "";
    },

    async getCampaign(id) {
      this.selectedCampaign = null;
      let params = {
        api_token: this.$user.api_token,
      };

      await axios
        .get("/api/campaigns/" + id, {
          params,
        })
        .then((resp) => {
          this.selectedCampaign = resp.data;
        });
    },

    getBrandId(brand) {
      if(brand == null){
        return;
      }

      this.recipientDisabled = true;
      this.isSearchRecipient = true;

      this.recipients = []; //set recipients as empty

      this.listprofiles(brand.id);
    },

    listprofiles(brand) {
      this.form.recipient = ""; //set recipient as null
      this.profileOptions = []; //set Profiles as empty

      let params = {
        api_token: this.$user.api_token,
        brand: brand,
      };

      this.on_recipient(params).then((resp) => {
        if (resp.data.length > 0) {
          let data = [
            {
              id: "all",
              profile_name: "All",
            },
          ];
          this.profileOptions = [...resp.data, ...data];
          this.recipientDisabled = false;
        }
        this.isSearchRecipient = false;

      });
    },

    listcampaigns() {
      this.form.campaign = '';
      let params = {
        api_token: this.$user.api_token,
        locale: this.locale,
      };

      axios
        .get("/api/select/campaigns/all", {
          params,
        })
        .then((resp) => {
          this.campaigns = resp.data;
        });
    },

    listbrands() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.locale,
        org_id: this.$org_id
      };

      axios
        .get("/api/brands", {
          params,
        })
        .then((resp) => {
          this.brands = resp.data;
        });
    },

    addRecipient(items) {
      items.forEach(item => {
        if (item.id === "all") {
          this.form.recipient = item;
        }
      });
    },
    
    slugify (str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();
      
        // remove accents, swap ñ for n, etc
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        var to   = "aaaaeeeeiiiioooouuuunc------";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }
    
        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes
    
        return str;
    }
  },

  computed: {
    totalRows() {
      return this.items.length;
    },
  },

  watch: {
    language(lang) {
      if (lang == "en") {
        this.form.campaign = this.selectedItem.is_english;
      } else if (lang == "it") {
        this.form.campaign = this.selectedItem.is_italian;
      } else {
        this.form.campaign = this.selectedItem.is_german;
      }
    },
  },
};
