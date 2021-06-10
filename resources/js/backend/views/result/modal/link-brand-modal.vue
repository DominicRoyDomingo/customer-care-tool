<template>
  <div>
  <b-modal
    id="linkbrand-modal"
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
        <v-card-title class="title blue-grey lighten-4 text-capitalize">
          <span>
            {{ $t('label.link_to_brand') + ' (' + resultName + ')' }}
          </span>
           <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="onClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        </v-card-title>
         <v-container>            
            <div class='row justify-content-center pb-5'>
              <div class='col-md-9'>
                <label> {{ $t('label.brand') }} <span class='text-danger'>*</span></label>
                <v-select
                  v-model="brandsel"
                  label="label"
                  :options="brandopt"
                  placeholder='Select Brand'
                  :reduce="item => item.id"
                  multiple
                  taggable
                  @input="_onCheck" 
                  :loading="isLoading"
                > 
                <template #list-header>
                  <li style="margin-left:20px;" class="mb-2">
                    <b-link href="#" @click="onShowCreate">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                      {{ $t("label.new_brand") }}
                    </b-link>
                  </li>
                </template>
                </v-select>
              </div>
            </div>
        </v-container>
        <v-card-actions align='right'>
          <v-container>
            <v-btn color="error" text tile @click="onClose">
              {{ $t("cancel") }}
            </v-btn>
                <v-btn color="success" tile
                  @click="_onSubmit"
                  class="bg-success text-white">
                  <div v-if="isSubmit" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    {{ $t("button.save") }}
                  </div>
                </v-btn>
          </v-container>
        </v-card-actions>
      </v-card>
    </v-app>
  </div>
  </b-modal>
  <CreateBrand :addBrandOpt="addBrandOpt" :resultName="resultName"/>
</div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import toast from "./../../../helpers/toast.js";
import axios from "axios";
import i18n from '../../../i18n.js';
import CreateBrand from './create-brand-modal'
let lang = i18n.locale;

function brandService(token){
  
  this.getBrands = async (paginate) => {
      var params = { api_token : token, lang : lang }    
      Object.assign(params, paginate)
      var query = await axios.post('/api/result/brands', params)
      if(query.status == 200){
          return query.data
      }
      return false
  }


  this.onSelectedTerm = async(terms) => {
      var params = { api_token : token, lang : lang } 
      Object.assign(params, terms)
      var query = await axios.post('/api/result/update-brand', params)
      if(query.status == 200){
          return query.data
      }
      return false

  }

  this.getResultBrand = async(id) => {
      var params = { api_token : token, lang : lang } 
      var query = await axios.get('/api/result/result-brand/' + id, { params : params })
      if(query.status == 200){
          return query.data
      }
      return false 
  }

  this.search = async (obj) => {
      var params = { api_token : token, lang : lang }
      Object.assign(params, obj)
      /*var query = await axios.post('/api/result/terms/search', params)
      if(query.status == 200){
          return query.data
      }*/
      return false
  }


}


export default {

  mixins: [toast],

  data: function() {
    return {
        brandsel : [],
        brandopt : [],
        checkbox : [],
        terms : [],
        service : null,
        currentPage: 1,
        perPage: 10,
        total_rows : 0,
        isLoading : false,
        isSearching : false,
        isSpinning : [],
        isSubmit : false,
        sort_by : 'index',
        sort_desc : false,
        items : [],
        filter : '',
        showEntriesOpt: [
            { value: 10, text: "10" },
            { value: 30, text: "30" },
            { value: 50, text: "50" },
            { value: 100, text: "100" },
        ],
        tableHeaders: [
          {
            key: "index",
            thClass: "text-left",
            label: "#",
            thStyle: { width: "2%" },
            sortable: false,
          },
          {
            key: "name",
            label: "Terminology",
            thClass: "text-left wide-column",
            thStyle: { width: "100%" },
            sortable: false,
          },
        ]
    };
  },
  components : {
      CreateBrand
  },
  props : {
    rowIndex : {
        type : Number,
        default : 0
    },
    resultName : {
        type : String,
        default : ''
    },
    brand_session : {
        type : Object,
        default : {}
    }
  },
  created : function(){
      this.service = new brandService(this.$user.api_token);
  },
  watch : {
     
  },
  methods: {
      onClose(){
        this.$bvModal.hide("linkbrand-modal");
      },
      resetAll(){
        this.currentPage = 1
        this.terms = []
        this.items = []
        this.brandsel = [];
        this.isLoading = false
        this.checkbox.forEach((item) => {
          if(item.hasOwnProperty('is_check')){
              item.is_check = false
          }
       })
      },
      onShow(){
           this.isLoading = true;           
           this.initTable()
           var $this = this
           this.service.getResultBrand(this.rowIndex).then((res) => {
              this.isLoading = false              
              if(res){
                  this.brandsel = JSON.parse(res.brands)                  
              }
              else
              {               
                  this.brandsel = [this.brand_session.id]                 
              }              
          })
      },
      onShowCreate(){
         this.$bvModal.show('create-brand-modal');
      },
      _onSearch(){

          this.currentPage = 1
          this.isLoading = true
          this.isSearching = true
          this.searchTable()

      },
      _onCheck(brands){
          
          this.brandsel = brands

      },
      _onSubmit(){

          var params = {
                result_id : this.rowIndex,
                brands : this.brandsel
          }

          this.isSubmit = true;  
          this.service.onSelectedTerm(params).then((res) => {
              let message = `${this.$t("updated_message1")} ID: ${this.rowIndex} ${this.$t("updated_message2")}`;
              this.$bvToast.toast(message, {
                title: this.$t("record_updated"),
                variant: 'info',
                solid: true,
              });

              this.isSubmit = false;  
          })

      },
      addBrandOpt : function(obj){
          this.brandopt.unshift(obj)
      },
      initTable(){
          var paginate = {
              current : ( this.currentPage - 1 ) * this.perPage,
              limit : this.perPage,
              terms : this.brandsel
          }; 

          this.isLoading = true;
          this.service.getBrands(paginate).then((res) => {
              this.isLoading = false;
              if(res){
                  
                  this.brandopt = res.query.map((item) => ({
                      id : item.id,
                      label : item.name
                  }))
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
          this.service.search(params).then((res) => {
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
  },
};
</script>

<style>
.v-input--selection-controls__input{
  margin-top: -8px;
}
</style>
