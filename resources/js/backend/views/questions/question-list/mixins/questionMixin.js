import toast from "../../../../helpers/toast.js";
import axios from "axios";
import i18n from '../../../../i18n.js';

let lang = i18n.locale;

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

   watch: {
      // language(value) {
      //    switch (value) {
      //       case 'en':
      //          this.form.name = this.question.data.is_english;
      //          break;
      //       case 'it':
      //          this.form.name = this.question.data.is_italian;
      //          break;
      //       case 'de':
      //          this.form.name = this.question.data.is_german;
      //          break;
      //       case 'ph-bis':
      //          this.form.name = this.question.data.is_bisaya;
      //          break;
      //       case 'ph-fil':
      //          this.form.name = this.question.data.is_filipino;
      //          break;
      //    }
      // },
      
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
      view_question_name(data, type) {
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

         if (type === 'question') {
            return name ? name : data.term_type_name;
         }

      },
      async getTerms(paginate){
         var params = { api_token : this.$user.api_token, lang : lang }    
         Object.assign(params, paginate)
         var query = await axios.post('/api/questions/terms', params)
         if(query.status == 200){
             return query.data
         }
         return false
     },
     async onSelectedTerm(terms){
         var params = { api_token : this.$user.api_token, lang : lang } 
         Object.assign(params, terms)
         var query = await axios.post('/api/questions/terms/update-question-terms', params)
         if(query.status == 200){
             return query.data
         }
            return false
     },
     async getQuestionTerms(id){
         var params = { api_token : this.$user.api_token, lang : lang } 
         var query = await axios.get('/api/questions/terms/single-question-terms/' + id, { params : params })
         if(query.status == 200){
             return query.data
         }
            return false 
     },
     async search(obj){
         var params = { api_token : this.$user.api_token, lang : lang }
         Object.assign(params, obj)
         var query = await axios.post('/api/questions/terms/search', params)
         if(query.status == 200){
             return query.data
         }
         return false
     }

   }
}