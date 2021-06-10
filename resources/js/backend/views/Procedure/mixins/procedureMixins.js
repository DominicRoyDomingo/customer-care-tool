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
	}
}