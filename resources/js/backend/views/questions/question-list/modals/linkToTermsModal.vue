<template>
  <b-modal
    id="linkterm-modal"
    @shown="onShow"
    @hidden="resetAll"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >

  <div class="create">
     <v-app id="create__container">
      <v-card>
          <template #header>
            <h6 class="mb-0 font-weight-bold text-info">
              {{ $t("label.terminilogies") }}
            </h6>
          </template>

        <v-card-title class="title blue-grey lighten-4 text-capitalize">
          <span>
            {{$t('button.link_to_terms') + ' (' + questionlabel + ')'}}
          </span>
           <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        </v-card-title>
         <v-container>
          <div class='row justify-content-between align-items-center'>
            <div class='col-md'>
                {{ $t("button.show") }}
                <b-form-select
                  :options="showEntriesOpt"
                  v-model="perPage"
                  style="
                    border-radius: 0;
                    width: 25% !important;
                    margin-top: -8px;
                    margin-left: 5px;
                    margin-right: 5px;
                  "
                />{{ $t("label.entries") }}
            </div>
            <div class='col-md'>
                <b-input-group class="mb-2 mr-sm-2">
                  <template #append>
                    <b-input-group-text>
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </b-input-group-text>
                  </template>
                  <b-form-input
                    v-model="filter"
                    :placeholder="$t('search_here') + ' ' + $t('enter_key')"
                    style="width: 300px"
                    type="search"
                    v-on:keyup.enter="_onSearch"
                  />
                </b-input-group>
            </div>
          </div>
          <div class='mt-5 p-2'>
            <b-card no-body>
              <template #header>
                <h6 class="mb-0 font-weight-bold text-info">
                  Terms
                </h6>
              </template>

                 <b-table 
                    class="mb-0"                   
                    show-empty
                    :empty-text="$t('no_record_found')"
                    :fields="tableHeaders"
                    :current-page="currentPage"
                    :busy="isLoading"
                    :items="items"
                    :tbody-tr-class="_tr_class"
                    sticky-header
                    :head-variant="'light'"
                  >
                  
                  <template v-slot:table-busy>
                      <div class="d-flex justify-content-center p-2">
                        <b-spinner label="Small Loading..."></b-spinner>
                      </div>
                  </template>

                   <template v-slot:cell(name)="data">
                    <div class='row p-2 align-items-center'>
                       <b-form-checkbox
                        :id="'checkbox-' + data.item.id"
                        v-model="checkbox[data.item.id]"
                        @change="_onCheck"
                        :name="'checkbox-' + data.item.id"
                        :value="{
                            id : data.item.id,
                            is_check : true,
                            item_index : data.index,
                            name : data.item.base_name
                        }"
                        :unchecked-value="{
                            id : data.item.id,
                            is_check : false,
                            item_index : data.index,
                            name : data.item.base_name
                        }"
                        style="width:300px;"
                      >
                          <strong class="text-capitalize check-box"><span v-html="data.item.name"></span></strong>
                      </b-form-checkbox>
                      <div v-if="data.item.is_linked">
                        <small class='font-weight-bold badge badge-success ml-2 text-uppercase'>{{ $t("linked") }}</small>  
                      </div>
                      <div v-if="data.item.is_loading">
                          <b-spinner variant="primary" type="grow" label="Spinning"></b-spinner>
                      </div>
                    </div>          
                  </template>
                </b-table>
              </b-card>
              <div class='d-flex justify-content-center'>
               <b-pagination
                v-model="currentPage"
                :total-rows="total_rows"
                :per-page="perPage"
                aria-controls="my-table"
              ></b-pagination>
            </div>
        </div>
        </v-container>
      </v-card>
    </v-app>
  </div>

  </b-modal>

</template>

<style>
.thead-light .th-white{
  background-color:white !important;
}

</style>

<script>
import { mapActions, mapGetters } from "vuex";
import toast from "./../../../../helpers/toast.js";
import questionMixin from '../mixins/questionMixin.js'

export default {

  mixins: [toast, questionMixin],

  data: function() {
    return {
        checkbox : [],
        terms : [],
        questionlabel : '',
        service : null,
        currentPage: 1,
        perPage: 10,
        total_rows : 0,
        isLoading : false,
        isSearching : false,
        isSpinning : [],
        sort_by : 'index',
        sort_desc : false,
        items : [],
        filter : '',
        tableHeaders: [
          {
            key: "name",
            label: 'Linking to ?',
            thClass: "th-white text-left",
            thStyle: { width: "100%" },
            sortable: false,
          },
        ]
    };
  },
  props : {
      form : {
          type : Object,
          default : function(){
            return {}
          }
      }
  },
  watch : {
      currentPage(newVal, oldVal){
          if(!this.isSearching){
              this.initTable()
          }else{
              this.searchTable()
          }                    
      },
      perPage(newVal, oldVal){
          if(!this.isSearching){
              this.initTable()
          }else{
              this.searchTable()
          }
      },
      terms(newVal, oldVal){

      }   
  },
  methods: {
      onClose(){
        this.$bvModal.hide("linkterm-modal");
      },
      resetAll(){
        this.currentPage = 1
        this.terms = []
        this.items = []
        this.isLoading = false
        this.checkbox.forEach((item) => {
          if(item.hasOwnProperty('is_check')){
              item.is_check = false
          }
       })
      },
      onShow(){
           this.isLoading = true;
           this.questionlabel = this.form.base_name         
           var $this = this
           this.getQuestionTerms(this.form.id).then((res) => {
              this.isLoading = false
              if(res){
                  this.terms = JSON.parse(res.terms)                  
              }
              this.initTable()
          })
      },
      _tr_class(item, type){
          if(item != null){
            if(item.is_linked){
              return 'bg-light'
            }
          }
          
          return ''
      },
      _onSearch(){

          this.currentPage = 1
          this.isLoading = true
          this.isSearching = true
          this.searchTable()

      },
      _onCheck(selected){

           var params = {
                id : this.form.id,
                term : JSON.stringify(selected)
          }

          this.items[selected.item_index].is_loading = true
          this.onSelectedTerm(params).then((res) => {
              this.items[selected.item_index].is_loading = false              
              if(selected.is_check){
                  this.linkedToast(selected.name, this.questionlabel);
                  this.items[selected.item_index].is_linked = true
                  this.terms.push(res)
              }
              else
              {
                  this.items[selected.item_index].is_linked = false
                  var filter = this.terms.filter((item) => item != selected.id)
                  this.terms = filter
                  this.unlinkedToast(selected.name, this.questionlabel)
              }
              
          })
          
      },
      initTable(){
          var paginate = {
              current : ( this.currentPage - 1 ) * this.perPage,
              limit : this.perPage,
              terms : this.terms
          }; 

          this.isLoading = true;
          this.getTerms(paginate).then((res) => {
              this.isLoading = false;
              if(res){
                  this.total_rows = res.total
                  this.items = res.query.map((item) => {
                      Object.assign(item, { is_loading : false })
                      return item
                  })
                  
                  if(this.terms.length){
                      this._checkTermSel()
                  }
              }
          });
      },
      searchTable(){
          var filter = this.filter
          var params = {
              current : ( this.currentPage - 1 ) * this.perPage,
              limit : this.perPage,
              terms : this.terms,
              search : filter
          };
          this.search(params).then((res) => {
              this.isLoading = false;
              if(res){
                  this.total_rows = res.total
                  this.items = res.query.map((item) => {
                      Object.assign(item, { is_loading : false })
                      return item
                  })
                  
                  if(this.terms.length){
                      this._checkTermSel()
                  }

                  if(filter == ''){
                      this.isSearching = false
                  }
              }
          })
      },
      _checkTermSel(){
        if(this.terms.length){
            var terms = this.terms
            var $this = this
            for(var i in terms){
                var t = terms[i]                
                this.items.map((item, index) => {
                    if(item.id == t){
                        Object.assign(item, { is_linked : true })
                        $this.checkbox[t] = {
                            id : t,
                            is_check : true,
                            item_index : index,
                            name : item.base_name
                        }
                    }
                })
            }
        }
      }
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
