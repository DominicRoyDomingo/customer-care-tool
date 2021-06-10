import Form from "../../../helpers/form.js";
import toast from "../../../helpers/toast.js";
import axios from "axios";
import i18n from '../../../i18n.js';

let languange = i18n.locale;
let lang = '?lang=' + languange;

export default {
   mixins: [toast],
   data(){
   		return {
	   		 showAdvanceSearch: false,
	         isAdvanceSearch: false,	         
	         listAddShow: true,
	         btnloading: false,
	         filter: "",
	         currentPage: 1,
	         perPage: 10,
	         total_rows : 0,
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
   		}
   },
   methods : {

   		async loadCourseType(){

   			var query = await axios.get("/api/course/get-types", { params : {
   				lang : languange,
   				api_token: this.$user.api_token
   			}})

   			if(query.status == 200){
   				return query.data
   			}

   			return false
   		},

   		async addCourseType(course_type){
   			var param = lang + "&api_token=" + this.$user.api_token
   			var query = await axios.post("/api/course/add-type" + param, 
   				{ course_type : course_type });

   			if(query.status == 200){
   				return query.data
   			}

   			return false

   		},

   		async singleCourseType(id, _lang){
   			var param = lang + "&api_token=" + this.$user.api_token
   			var query = await axios.get("/api/course/get-edit-type/" + id + '/' + _lang, { params : {
   				api_token: this.$user.api_token
   			}})   				

   			if(query.status == 200){
   				return query.data
   			}

   			return false

   		},

   		async updateCourseType(course_name, id, _lang){

   			var param = lang + "&api_token=" + this.$user.api_token
   			var query = await axios.post("/api/course/update-type" + param, 
   			{ 
   				data : JSON.stringify({language : _lang, id : id, name : course_name })
   			})

   			if(query.status == 200){
   				return query.data
   			}

   			return false

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
         async deleteCourseType(item){
            let baseName = item.name;
            let records = "Course Type";
            let id = item.id
            let $this = this
            let message = `${this.$t("question_record_delete")} ${baseName} ${this.$t("from")} ${records} ${this.$t("records")}?`;
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
                              btnClass: "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
                              action: function (value) {
                                    var param = "?api_token=" + $this.$user.api_token
                                    axios.put("/api/course/delete-type/" + id + param, 
                                    { 
                                       lang : languange
                                    }).then((res) => {
                                          if(res.status == 200){
                                             resolve(res.data)
                                          }
                                    })
                              }
                           },
                           no: {
                              text: this.$t("no"),
                              btnClass:
                                 "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
                              action: function () {
                              },
                           },
                        }
                    })
               })
            },

            async searchCourseType(text){
               var param = lang + "&api_token=" + this.$user.api_token
               let query = await axios.post( "/api/course/search-type" + param, { search : text} );

               if(query.status == 200){
                  return query.data
               }

               return false
            },

            async sortList(data){
               var param = lang + "&api_token=" + this.$user.api_token
               let query = await axios.post( "/api/course/sort-type" + param, data);

               if(query.status == 200){
                  return query.data
               }

               return false
            }
        }

}