<template>
<div>

<v-row>
	<v-col cols="6">
	  <v-row>
	  	<v-col>
	      <span class='caption font-weight-regular text--secondary text-left'>{{ $t("button.show") }}</span>
	      <b-form-select :options="showEntriesOpt" v-model="perPage" class='w-50 mx-2'/>
	      <span class='caption font-weight-regular text--secondary text-left'>{{ $t("label.entries") }}</span>
      	</v-col>
      	<v-col>
      		<b-input-group-prepend>
              <v-menu offset-y :rounded="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    color="info"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    tile
                    depressed
                  >
                    <v-icon :size="18"> mdi-filter-menu </v-icon>
                  </v-btn>
                </template>
                <v-list dense class="profile__row-menu">
                  <v-list-item-group color="primary">
                    <v-list-item
                      v-for="(option, index) in noTranslationsOptions"
                      :key="index"
                    >
                      <v-list-item-content @click="sortbyLang(option.value)">
                        <v-list-item-title>
                          {{ option.text }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
              </v-menu>
            </b-input-group-prepend>
      	</v-col>
      </v-row>
	</v-col>
	<v-col cols="6">
		<v-row justify="end">
			<v-col cols='6'>
				<b-input-group class="mb-2 mr-sm-2">
		          <template #append>
		            <b-input-group-text>
		              <i class="fa fa-search" aria-hidden="true"></i>
		            </b-input-group-text>
		          </template>
		          <b-form-input
		            v-model="filter"
		            :placeholder="$t('search_here')"
		            type="search"
		          />
		        </b-input-group>
		    </v-col>
		</v-row>
	</v-col>
</v-row>

 <b-table
	striped
	show-empty
	:empty-text="$t('no_record_found')"
	:fields="tableHeaders"
	:current-page="currentPage"
	:per-page="perPage"
	:busy="isLoading"
	:items="items"
	:sort-by.sync="sort_by"
	:sort-desc.sync="sort_desc"
	>
	<template v-slot:table-busy>
	  <div class="d-flex justify-content-center p-2">
	    <b-spinner label="Small Loading..."></b-spinner>
	  </div>
	</template>

	<template v-slot:cell(index)="data">
	  {{ (data.index + 1 )+ ((currentPage * perPage) - perPage) }}
	</template>

	<template v-slot:cell(name)="data">
	  <span v-html="data.item.name"> </span>
	</template>

	<template v-slot:cell(created_at)="data">
	  <span>{{ data.item.created_at }}</span>
	</template>

</b-table>


</div>
</template>

<script>

import procedureMixins from '../mixins/procedureMixins.js';

export default {
	mixins: [procedureMixins],
	data(){
		return {
			isLoading : false,
	        sort_by : 'index',
	        sort_desc : false,
			perPage : 10,
	        total_rows : 0,
	        currentPage : 1,
	        items : [],
	        rowIndex : 0,
			filter : '',
			tableHeaders: [
	          {
	            key: "index",
	            thClass: "text-left",
	            label: "#",
	            thStyle: { width: "5%" },
	            sortable: true,
	          },
	          {
	            key: "actor",
	            label: 'Actor',
	            thClass: "text-left wide-column",
	            thStyle: { width: "50%" },
	            sortable: true,
	          },
	          {
	            key: "procedure",
	            label: 'Procedure',
	            thClass: "text-right",
	            thStyle: { width: "20%" },
	            tdClass: "text-right",
	            sortable: true,
	          },
	          {
	            key: "actions",
	            label: this.$t("table.action"),
	            thClass: "text-center",
	            thStyle: { width: "20%" },
	            tdClass: "text-center",
	          },
	        ],
		}
	},
	methods : {
		sortbyLang(value){

		}
	}
}

</script>