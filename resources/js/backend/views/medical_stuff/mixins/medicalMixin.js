import Form from "../../../helpers/form.js";
import toast from "../../../helpers/toast.js";

export default {
   mixins: [toast],
   data() {
      return {
         showAdvanceSearch: false,
         isAdvanceSearch: false,
         isLoading: true,
         listAddShow: true,
         btnloading: false,
         filter: "",
         currentPage: 1,
         perPage: 10,
         itemSelected: "",
         language: this.$i18n.locale,
         filterBrand: '',
         filterLinkTerm: '',
         termName: '',
         sortbyLang: '',

         showEntriesOpt: [
            { value: 10, text: "10" },
            { value: 30, text: "30" },
            { value: 50, text: "50" },
            { value: 100, text: "100" },
         ],

         languageOptions: [
            { value: "en", text: "English" },
            { value: "de", text: "German" },
            { value: "it", text: "Italian" },
            { value: "ph-fil", text: "Filipino" },
            { value: "ph-bis", text: "Visayan" },
         ],

         noTranslationsOptions: [
            { value: 'all', text: "All", disabled: true },
            { value: "en", text: "No English Translation" },
            { value: "de", text: "No German Translation" },
            { value: "it", text: "No Italian Translation" },
            { value: "ph-fil", text: "No Filipino Translation" },
            { value: "ph-bis", text: "No Visayan Translation" },
         ],

         meta: { page: "" },

         date_today: "",

         form: new Form({
            id: "",
            name: "",
            term_type: '',
            medical_term: '',
            profession: '',
            body_part: '',
            specialization: '',
            description: '',
            struct_type: '',
            structure_type: "",
            type: "",
            action: "",
            specializations: '',
            body_parts: '',
            term_types: '',
            file: "",
            notes: [],
            date_today: "",
            language: this.$i18n.locale,
         }),

         brandForm: new Form({
            id: "",
            brand_id: "",
            brands: '',
            action: "",
            linkedType: ''
         }),

         typeForm: new Form({
            id: "",
            name: "",
            term_type: '',
            action: "",
            term_types: '',
            file: ""
         }),

         mtForm: new Form({
            id: "",
            name: "",
            term_types: "",
            specializations: "",
            action: "",
            file: "",
         }),

         iconForm: new Form({
            id: "",
            icon: "",
            term: "",
            term_name: "",
            provider_type: "",
            action: "",
         }),

         articleForm: new Form({
            id: "",
            title: "",
            link: "",
            actors: "",
            item_type: '',
            actor_type: "",
            type_dates: "",
            publish_date: "",
            action: "",
         }),

         typeAuthor: new Form({
            id: "",
            name: "",
            action: "Add",
            default: "",
            convertion: "",
            language: this.$i18n.locale
         }),

         typeDateForm: new Form({
            id: "",
            name: "",
            action: ""
         }),

         htmlTagForm: new Form({
            id: "",
            description: "",
            action: ""
         }),

         termDescForm: new Form({
            id: "",
            name: "",
            action: ""
         }),

         termConform: new Form({
            id: "",
            description: "",
            value: "",
            note: "",
            action: "",
            with_value: "",
         }),

         no_photo:
            "https://customer-care-tool.s3.us-east-2.amazonaws.com/no-photo.png",

      }
   },
   computed: {

      totalRows() {
         return this.items.length;
      },

   },
   created() {
      this.get_date_today();

   },
   methods: {

      onReset() {
         this.language = this.$i18n.locale;
      },

      deleteConfirm(name, records) {
         let message = `${this.$t("question_record_delete")} ${name} ${this.$t("from")} ${records} ${this.$t("records")}?`;
         return new Promise((resolve, reject) => {
            $.confirm({
               title: this.$t("confirmation_record_delete"),
               content: message,
               type: "red",
               typeAnimated: true,
               columnClass: "medium",
               buttons: {
                  yes: {
                     text: this.$t("yes"),
                     btnClass:
                        "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                     action: function (value) {
                        resolve(value);
                     },
                  },
                  no: {
                     text: this.$t("no"),
                     btnClass:
                        "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                     action: function () {
                     },
                  },
               },
            });
         });
      },

      unLinkConfirm() {
         let message = `${this.$t("label.unlink_message")}`;
         return new Promise((resolve, reject) => {
            $.confirm({
               title: this.$t("confirm_action"),
               content: message,
               type: "red",
               typeAnimated: true,
               columnClass: "medium",
               buttons: {
                  yes: {
                     text: this.$t("confirm"),
                     btnClass:
                        "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                     action: function (value) {
                        resolve(value);
                     },
                  },
                  no: {
                     text: this.$t("no"),
                     btnClass:
                        "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                     action: function (value) {
                        reject(value);
                     },
                  },
               },
            });
         });
      },

      actionConfirm(name, records) {
         let message = `${this.$t("question_record_delete")} ${records} ?`;
         return new Promise((resolve, reject) => {
            $.confirm({
               title: this.$t("confirm_action"),
               content: message,
               type: "red",
               typeAnimated: true,
               columnClass: "medium",
               buttons: {
                  yes: {
                     text: this.$t("confirm"),
                     btnClass:
                        "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                     action: function (value) {
                        resolve(value);
                     },
                  },
                  no: {
                     text: this.$t("no"),
                     btnClass:
                        "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                     action: function () {
                     },
                  },
               },
            });
         });
      },

      deleteNote(index) {
         this.form.notes.splice(index, 1);
      },

      //Notes
      addNote() {
         this.form.notes.push({
            date_requested: this.date_today,
            notes: "",
         });
      },

      getRemoveItemIndex(items, id) {
         const i = items.map((res) => res.id).indexOf(id);
         return i;
      },

      defaultParams() {
         return {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            filter: this.filter,
            brand_id: this.$brand.id,
         }
      },

      get_date_today() {
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

      set_item_search(data) {
         if (!data) {
            return "";
         }
         return data.map((x) => parseInt(x.id));
      },
   },

   watch: {
      language(value) {
         switch (value) {
            case 'en':
               this.mtForm.name = this.itemSelected.is_english;
               this.articleForm.title = this.itemSelected.is_english;
               this.articleForm.link = (this.itemSelected.english_link === 'null' || this.itemSelected.english_link === null ? "" : this.itemSelected.english_link);
               this.typeForm.term_type = this.itemSelected.is_english;
               this.typeDateForm.name = this.itemSelected.is_english;
               // this.termConform.note = this.itemSelected.is_english;
               break;
            case 'it':
               this.mtForm.name = this.itemSelected.is_italian;
               this.articleForm.title = this.itemSelected.is_italian;
               this.articleForm.link = (this.itemSelected.italian_link === 'null' || this.itemSelected.italian_link === null ? "" : this.itemSelected.italian_link);
               this.typeForm.term_type = this.itemSelected.is_italian;
               this.typeDateForm.name = this.itemSelected.is_italian;
               // this.termConform.note = this.itemSelected.is_italian;
               break;
            case 'de':
               this.mtForm.name = this.itemSelected.is_german;
               this.articleForm.title = this.itemSelected.is_german;
               this.articleForm.link = (this.itemSelected.german_link === 'null' || this.itemSelected.german_link === null ? "" : this.itemSelected.german_link);
               this.typeForm.term_type = this.itemSelected.is_german;
               this.typeDateForm.name = this.itemSelected.is_german;
               // this.termConform.note = this.itemSelected.is_german;
               break;
            case 'ph-bis':
               this.mtForm.name = this.itemSelected.is_bisaya;
               this.articleForm.title = this.itemSelected.is_bisaya;
               this.articleForm.link = (this.itemSelected.bisaya_link === 'null' || this.itemSelected.bisaya_link === null ? "" : this.itemSelected.bisaya_link);
               this.typeForm.term_type = this.itemSelected.is_bisaya;
               this.typeDateForm.name = this.itemSelected.is_bisaya;
               // this.termConform.note = this.itemSelected.is_bisaya;
               break;
            case 'ph-fil':
               this.mtForm.name = this.itemSelected.is_filipino;
               this.articleForm.title = this.itemSelected.is_filipino;
               this.articleForm.link = (this.itemSelected.filipino_link === 'null' || this.itemSelected.filipino_link === null ? "" : this.itemSelected.filipino_link);
               this.typeForm.term_type = this.itemSelected.is_filipino;
               this.typeDateForm.name = this.itemSelected.is_filipino;
               // this.termConform.note = this.itemSelected.is_filipino;
               break;
         }
      },

      filterBrand(value) {
         this.loadBrands();
      }

   },
}