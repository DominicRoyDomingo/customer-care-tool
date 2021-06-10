import toast from "../../../../helpers/toast.js";

export default {
   mixins: [toast],
   data() {
      return {
         showAdvanceSearch: false,
         isAdvanceSearch: false,
         isLoading: true,
         btnloading: false,
         filter: "",
         currentPage: 1,
         perPage: 10,
         itemSelected: "",
         termName: '',
         language:'',
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

         meta: {
            page: "",
         },

         date_today: "",

         total_rows: 0,

         showing: {
            to: 0,
            from: 0,
            total: 0,
         },
      }
   },

   computed: {
   },

   created() {
      this.get_date_today();
   },

   watch: {
      language(value) {
         switch (value) {
            case 'en':
               this.form.name = this.choice.data.is_english;
               break;
            case 'it':
               this.form.name = this.choice.data.is_italian;
               break;
            case 'de':
               this.form.name = this.choice.data.is_german;
               break;
            case 'ph-bis':
               this.form.name = this.choice.data.is_bisaya;
               break;
            case 'ph-fil':
               this.form.name = this.choice.data.is_filipino;
               break;
         }
      },
      
   },

   methods: {
      defaultParams() {
         return {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            filter: this.filter,
            sortbyLang: this.sortbyLang,
         }
      },

      view_item_name(data, type) {
         let name = "";
         switch (this.$i18n.locale) {
            case "en":
               name = data.is_english;
               break;
            case "de":
               name = data.is_german;
               break;
            case "it":
               name = data.is_italian;
               break;
            case "ph-fil":
               name = data.is_filipino;
               break;
            case "ph-bis":
               name = data.is_bisaya;
               break;
         }

         if (type === 'type') {
            return name ? name : data.term_type_name;
         }

         if (type === 'spec') {
            return name ? name : data.description_name;
         }

         if (type === 'item_type') {
            
            return name ? name : data.item_type_name;
         }

      },

      get_item_array(data, type) {

         if (!data) {
            return null
         }

         let arrrayItems = []

         data.forEach(ele => {

            let data = {
               id: ele.id,
               is_loading: ele.is_loading,
               base_language: ele.base_language,
               has_translation: ele.has_translation,
            }

            if (type === 'type') {
               data = {
                  ...data,
                  is_service: ele.is_service,
                  name: ele.name,
                  on_select_term_type_name: ele.on_select_term_type_name,
                  term_type_name: ele.term_type_name,
               }

            } else {
               data = {
                  ...data,
                  description: ele.description,
                  description_name: ele.description_name,
                  job_profession_id: ele.job_profession_id,
                  on_select_description_name: ele.on_select_description_name,
                  select_description_name: ele.select_description_name,
               }
            }

            arrrayItems.push(this.set_item_array(data, ele));

         });

         return arrrayItems;

      },

      set_item_array(data, item) {
         let object = {}
         switch (this.$i18n.locale) {
            case 'en':
               object = {
                  ...data,
                  base_name: item.is_english ?? item.base_name
               }
               break;
            case 'it':
               object = {
                  ...data,
                  base_name: item.is_italian ?? item.base_name
               }
               break;
            case 'de':
               object = {
                  ...data,
                  base_name: item.is_german ?? item.base_name
               }
               break;
            case 'ph-fil':
               object = {
                  ...data,
                  base_name: item.is_filipino ?? item.base_name
               }
               break;
            case 'ph-bis':
               object = {
                  ...data,
                  base_name: item.is_bisaya ?? item.base_name
               }
               break;
         }

         return object;
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

      getRemoveItemIndex(items, id) {
         const i = items.map((res) => res.id).indexOf(id);
         return i;
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
   }
}