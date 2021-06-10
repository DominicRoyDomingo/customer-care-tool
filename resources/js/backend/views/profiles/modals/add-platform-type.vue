<template>
  <div>
    <b-modal id="add-platform-type" @hide="hide" hide-header hide-footer size="md">
      <v-app id="job-description__container">
        <v-card>
        	 <v-card-title class="title blue-grey lighten-4 text--secondary">
        	 	Add Platform Type
        	 </v-card-title>
        	  <v-card-text>
        	  	<div class='form-group mt-5'>
	        	  	<label for="contact">Platform Type</label>
	        	  	<input
				        type="text"
				        class="form-control"
				        v-model='platform_text'
				      />
			     </div>
        	  </v-card-text>
        	  <v-card-actions class="modal__footer blue-grey lighten-5">
        	  	<v-spacer></v-spacer>
	            <v-btn color="error" text tile @click="onSave">
	              {{ $t("cancel") }}
	            </v-btn>
	            <v-btn color="success" tile dark @click="onSave">
	            <div>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("button.save") }}
                </div>
                 </v-btn>
        	  </v-card-actions>
       </v-card>
     </v-app>
   </b-modal>
 </div>	
</template>


<script>

import { createPlatform } from "./../../../api/platform_type.js";

export default {
	props: ["parent"],
	data(){
		return {
			platform_text : ''
		}
	},
	methods : {
		hide() {
	      this.parent.$bvModal.hide("add-platform-type");
	    },
		onSave(){

			var obj = {
				name : this.platform_text 
			}

			createPlatform({ data : JSON.stringify(obj) }).then((res) => {
				if(res){
					this.parent.fetchPlatformTypes()
					this.hide()
				}
			})
		}
	}
}

</script>