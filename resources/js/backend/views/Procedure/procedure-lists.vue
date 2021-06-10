<template>
  <v-app>
  	<v-main>
  		<v-container>
  			<div class="card border-0">  				
      			<div class="card-body">
      				<v-row justify="space-between">
	  					<v-col>
	  						<h4 class="card-title">Procedure Lists</h4>
	  					</v-col>
	  					<v-col>
	  						<v-row justify="end">
		  						<div class='w-50'>
						  			<v-btn
						              color="success"
						              class="float-right"
						              block
						              tile
						              @click="showModal"
						            >
						              <i class="fas fa-notes-medical"></i>
						              &nbsp; {{ "New Procedure" }}
						            </v-btn>
					        	</div>
				        	</v-row>
	  					</v-col>
	  				</v-row>
  					<ProcedureTable />
  				</div>
  			</div>
  		</v-container>
  	</v-main>
  </v-app>
</template>

<script>
import toast from "../../helpers/toast.js";	
import axios from "axios";
import i18n from '../../i18n.js';
import procedureMixins from './mixins/procedureMixins.js';
import ProcedureTable from './components/procedure-table';

export default {
	mixins: [toast, procedureMixins],
	components : {
		ProcedureTable
	},
	data(){
		return {

		}
	},
	props : {
		brand_session : {
	        type : Object,
	        default : {}
    	}
	},
	methods : {
		showModal(){
			console.log("asdasd")
		}
	}
}
</script>