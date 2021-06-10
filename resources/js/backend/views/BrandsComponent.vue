<template>

    <div>

        <div class="card">

            <div class="card-header">

                <i class="fas fa-list-alt"></i> {{ $t( 'brands_list' )}}

                <button type="button" class="btn btn-success btn-sm float-right"  @click="modal.add.isVisible = true"><i class="fa fa-plus"></i> {{ $t( modal.add.button.new ) }} </button>

            </div>

            <div class="card-body">

                <b-row>

                    <b-col lg="6" md="6" sm="6">

                        <span class="float-left">

                            <el-select v-model="length" @change="resetPagination()" style="width:80px !important">

                                <el-option :value="10">10</el-option>

                                <el-option :value="25">25</el-option>

                                <el-option :value="50">50</el-option>

                                <el-option :value="100">100</el-option>

                            </el-select>

                        </span>

                    </b-col>

                    <b-col lg="6" md="6" sm="6">

                        <span class="float-right">

                            <el-input
                                :placeholder="$t( 'search_here' )"
                                @input="resetPagination()"
                                autocomplete="search"
                                v-model="search"
                            >

                            <i slot="prefix" class="el-input__icon el-icon-search"></i>

                        </el-input>

                        </span>

                    </b-col>

                </b-row>

                <Create :$this="this" :token="token"></Create>

                <b-row class="mt-3">

                    <b-col md="12" lg="12">
                
                       <datatable
                        :columns="columns"
                        :sortKey="sortKey"
                        :sortOrders="sortOrders"
                        :tableClass="'table table-striped'"
                        :headerClass="'table-active'"
                        @sort="sortBy"
                        >

                        <tbody>

                          <tr v-for="(row, index) in paginated" :key="index">

                            <td>{{ index +=1 }}</td>

                            <td><img :src="`https://med4care-storage.s3.eu-west-2.amazonaws.com/agora-publishing/`+row.logo" style="height:30px;width:30px;border-radius:50%"/></td>

                            <td>{{row.name}}</td>

                            <td>{{row.website}}</td>

                            <td class="text-center">{{row.created_at | moment("YYYY/MM/D") }}</td>

                            <td class="text-center">{{row.updated_at | moment("YYYY/MM/D") }}</td>

                            <td class="text-center">

                              <!-- <el-button-group> -->

                                <el-tooltip :content="$t('edit_brands_tooltip')" placement="top">

                                  <el-button
                                    type="primary"
                                    class=""
                                    size="mini"
                                    icon="el-icon-edit"
                                    @click="onEdit(row)"
                                  > Edit </el-button>

                                </el-tooltip>

                                <el-tooltip :content="$t('delete_brands_tooltip')" placement="top">

                                  <el-button
                                    type="danger"
                                    size="mini"
                                    @click="onDelete(row)"
                                    icon="el-icon-delete"
                                    :loading="row.loading"
                                  > Delete </el-button>

                                </el-tooltip>

                              <!-- </el-button-group> -->

                            </td>

                          </tr>

                          <tr v-if="records_found">

                              <td colspan="7" class="text-center"><i> {{ $t( 'no_record_found' )}} </i></td>

                          </tr>

                        </tbody>

                      </datatable>

                      <pagination
                        :client="true"
                        :filtered="filterData"
                        :pagination="pagination"
                        @next="++pagination.currentPage"
                        @prev="--pagination.currentPage"
                      ></pagination>

                  </b-col>

              </b-row>

          </div>

          <Edit :$this="this"></Edit>
          
    </div>

  </div>

</template>

<script>

// Components
import Create from "./includes/brands/CreateComponent.vue";

import Edit from "./includes/brands/UpdateComponent.vue";

import Loading from "vue-loading-overlay";

import datatable from "./../components/DataTable.vue";

import pagination from "./../components/Pagination.vue";

// Custom Script
import Form from "./../shared/form.js";

import { fetchList, softDelete } from "./../api/brands.js";


export default {

  props:[ 'token' ],
  
  components: {
    
    Create,
    
    Edit,
    
    datatable,
    
    pagination,

    Loading
  
  },
  
  data: function() {
    
    let sortOrders = {};

    let columns = [
      
      {width: '5%', label: 'ID', name: 'id', type: 'number'},
      
      {label: '', name: 'logo'},

      {label: 'table.brands_name', name: 'name'},

      {label: 'table.brands_website', name: 'website'},
      
      {width: '20%', label: 'table.created_at', name: 'created_at'},
      
      {width: '20%', label: 'table.updated_at', name: 'updated_at'},
      
      {width: '20%', label: 'table.action', name: 'action'}
    
    ];

    columns.forEach(column => {
      
      sortOrders[column.name] = 2;
    
    });

    return {

      isLoading: false,
      
      fullPage: true,
      
      records_found: false,
      
      items: [],

      modal: {
        
        add: {
          
          name: "brands_add_new_button",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "brands_add_new_button",
            
            loading: false
          
          }

        },
        
        edit: {
          
          name: "brands_add_edit_button",
          
          isVisible: false,
          
          button: {
            
            update: "update",
            
            cancel: "cancel",
            
            new: "brands_add_edit_button",
            
            loading: false
          
          }

        }

      },
      
      form: new Form({
        
        id: "",
        
        name: "",

        website: "",

        image: "",

        language: this.$i18n.locale
      
      }),

      formData: new FormData(),

      no_image: "https://lex-brands.s3.us-east-2.amazonaws.com/images/no-photo.png",

      // for data tables,
      columns: columns,
      
      sortKey: "name",
      
      sortOrders: sortOrders,
      
      length: 10,
      
      search: "",
      
      tableData: {
        
        client: true
      
      },
      pagination: {
        
        currentPage: 1,
        
        total: "",
        
        nextPage: "",
        
        prevPage: "",
        
        from: "",
        
        to: ""
      
      }
    
    };
  
  },

  mounted() {
    
    this.loadBrands();

    $("#imagePreview").css(
      "background-image","url(https://lex-brands.s3.us-east-2.amazonaws.com/images/no-photo.png)"
    );
    
    $("#imagePreview")
      .hide()
      .fadeIn(650);
  
  },
  
  methods: {

    loadBrands() {
      
      this.isLoading = true;
      
      fetchList().then(resp => {
        
        this.items = resp;

        this.pagination.total = Object.keys(this.items).length;
        
        if( this.pagination.total == 0 ) {
          
          this.records_found = true;
        
        }

      });
    
    },
    
    onEdit(brands) {
      
      this.form.id = brands.id;
      
      this.form.name = brands.name;

      this.form.website = brands.website;

      this.form.image = brands.logo;
      
      this.modal.edit.isVisible = true;
    
    },

    onDelete(brands) {
      
      this.$confirm( this.$t( 'delete_transaction_alert' ), {
        
        confirmButtonText: "OK",
        
        cancelButtonText: this.$t( 'cancel' ),
        
        type: "error"
      
      })
        .then(resp => {
          
          brands.loading = true;
          
          softDelete(brands.id).then(resp => {
            
            brands.loading = false;

            if (resp == false) {
              
              this.$notify({
              
                title: this.$t( 'data_used' ),
                
                message: this.$t( 'used_data_alert' ),
                
                type: "error"
              
              });
              
              return false;
            
            }
            
            this.$notify({
              
              title: this.$t( 'delete' ),
              
              message: resp.message + ' ' + this.$t( 'remove_brands_alert' ),
              
              type: resp.type
            
            });
            
            this.loadBrands();
          
          });
        
        });
    
    },

    // for custom datatables
    paginate(array, length, pageNumber) {
      
      this.pagination.from = array.length ? (pageNumber - 1) * length + 1 : " ";

      this.pagination.to = pageNumber * length > array.length ? array.length : pageNumber * length;

      this.pagination.prevPage = pageNumber > 1 ? pageNumber : "";

      this.pagination.nextPage = array.length > this.pagination.to ? pageNumber + 1 : "";

      return array.slice((pageNumber - 1) * length, pageNumber * length);
    
    },

    resetPagination() {
      
      this.pagination.currentPage = 1;

      this.pagination.prevPage = "";

      this.pagination.nextPage = "";

    },

    sortBy(key) {

      this.resetPagination();
      
      this.sortKey = key;

      this.sortOrders[key] = this.sortOrders[key] * -1;

    },

    getIndex(array, key, value) {

      return array.findIndex(i => i[key] == value);

    }

  },
computed: {
    
    filterData() {

      let data = this.items;

      if (this.search) {

        data = data.filter(row => {

          return Object.keys(row).some(key => {

            return ( String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1 );

          });

        });

      }

      let sortKey = this.sortKey;

      let order = this.sortOrders[sortKey] || 1;

      if (data.length > 0) {

        data = data.slice().sort((a, b) => {

          let index = this.getIndex(this.columns, "name", sortKey);

          a = String(a[sortKey]).toLowerCase();

          b = String(b[sortKey]).toLowerCase();

          if (this.columns[index].type && this.columns[index].type === "date") {

            return ( (a === b ? 0 : new Date(a).getTime() > new Date(b).getTime() ? 1 : -1) * order );

          } else if (
            
            this.columns[index].type &&
            this.columns[index].type === "number"
          ) {
            
            return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;

          } else {

            return (a === b ? 0 : a > b ? 1 : -1) * order;

          }

        });

      }

      if( data.length == 0 ) {

          this.records_found = true;

      } else {

          this.records_found = false;

      }

      return data;

    },

    paginated() {

      return this.paginate(

        this.filterData,

        this.length,

        this.pagination.currentPage

      );

    }

  }

};

</script>

<style lang="scss">

  th:nth-last-child(2) {

    text-align: center;

  }

  th:nth-last-child(3) {

    text-align: center;

  }

</style>