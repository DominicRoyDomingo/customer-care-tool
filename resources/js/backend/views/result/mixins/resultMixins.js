import toast from "../../../helpers/toast.js";
import axios from "axios";
import i18n from '../../../i18n.js';

let lang = i18n.locale;

export default {
   mixins: [toast],
   data(){
   		return {
   			showEntriesOpt: [
	            { value: 10, text: "10" },
	            { value: 30, text: "30" },
	            { value: 50, text: "50" },
	            { value: 100, text: "100" },
	        ],
	        noTranslationsOptions: [
	          { value: "all", text: "All", disabled: true },
	          { value: "en", text: "No English Translation" },
	          { value: "de", text: "No German Translation" },
	          { value: "it", text: "No Italian Translation" },
	          { value: "ph-fil", text: "No Filipino Translation" },
	          { value: "ph-bis", text: "No Visayan Translation" },
	        ],
   		}
   },
   methods : {
   		async get_all(){
		      var params = { api_token : this.$user.api_token, lang : lang }
		      var query = await axios.get('/api/result', { params : params });
		      if(query.status == 200){
		          return query.data
		      }
		      return false
		},
		async add_result(obj){
		      var params = { api_token : this.$user.api_token, lang : lang }
		      Object.assign(params, obj)
		      var query = await axios.post('/api/result/add', params);
		      if(query.status == 200){
		          return query.data
		      }
		      return false
		 },
		 async single_result(obj){
		      var params = { api_token : this.$user.api_token, lang : lang }
		      Object.assign(params, obj)
		      var query = await axios.get('/api/result/single-result', { params : params });
		      if(query.status == 200){
		          return query.data
		      }
		      return false

		 },
		 async update_result(obj){
		    var params = { api_token : this.$user.api_token, lang : lang }
		    Object.assign(params, obj)
		    var query = await axios.post('/api/result/update', params);
		      if(query.status == 200){
		          return query.data
		      }
		      return false
		  },
		  async search_result(obj){
		    var params = { api_token : this.$user.api_token, lang : lang }
		    Object.assign(params, obj)
		    var query = await axios.post('/api/result/search', params);
		      if(query.status == 200){
		          return query.data
		      }
		      return false
		  },
		  async delete_result(obj, $this, msg){

		    var params = { api_token : this.$user.api_token, lang : lang }
		    Object.assign(params, obj)

		    let message = `${$this.$t("question_record_delete")} ${msg.baseName} ${$this.$t("from")} result records ?`;

		    return new Promise((resolve, reject) => {

		      $.confirm({
		         title: $this.$t("confirmation_record_delete"),
		         content: message,
		         type: "red",
		         typeAnimated: true,
		         columnClass: "medium",
		         buttons: {
		                yes: {
		                  text: $this.$t("yes"),
		                  btnClass: "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
		                  action: function (value) {
		                        var param = "?api_token=" + $this.$user.api_token
		                        $this.isLoading = true
		                        axios.post("/api/result/delete", params).then((res) => {
		                              if(res.status == 200){
		                                 resolve(res.data)
		                              }
		                        })
		                  }
		               },
		               no: {
		                  text: $this.$t("no"),
		                  btnClass:
		                     "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
		                  action: function () {
		                      resolve(false)
		                  },
		               },
		            }
		        })
		    })
		},
		async sortByLang(obj){
	      var params = { api_token : this.$user.api_token, lang : lang }
	      Object.assign(params, obj)
	      var query = await axios.post('/api/result/orphan-filter', params);
	      if(query.status == 200){
	          return query.data
	      }
	      return false
	  }
   }
}