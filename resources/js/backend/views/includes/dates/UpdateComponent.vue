<template>

  <div class="edit">

    <b-modal
      id="update-modal"
      hide-footer
      size="md"
      :title="$t(modal.name)"
    >

      <div class="card">

        <div class="card-body">

            <b-row>

                <b-col lg="12" md="12" sm="12">

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

              <td class="text-center">{{row.status }} <small v-if="row.convertion==true" style="color:red">(No {{ row.language }} translation yet)</small></td>

              <td class="text-center">{{row.sequence }}</td>

              <td class="text-center">

                <el-button-group> 
                 
                  <b-button size="sm" variant="success" @click="openEditModal(row)">

                    <i class="fas fa-edit"></i>

                    {{$t('button.edit')}}

                  </b-button>

                  <b-button size="sm" variant="danger" @click="onDelete(row)">

                      <i class="fas fa-trash"></i>

                      {{$t('button.delete')}}

                  </b-button>

                </el-button-group> 

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
          
    </div>

    </b-modal>

  </div>

</template>

<script>

import { updateDateStatus , getDateStatusName, softDelete } from "./../../../api/date.js";

import datatable from "./../../../components/DataTable.vue";

export default {
  
  props: ["$this"],

  components: {
    
    datatable,
  
  },

  data: function() {

    let sortOrders = {};

    let columns = [
      
      { label: 'table.date_status_name', name: 'name'},

      { label: 'date_sequence', name: 'status'},
      
      { label: 'table.action', name: 'action'}
    
    ];

    columns.forEach(column => {
      
      sortOrders[column.name] = 2;
    
    });
    
    return {
      
      modal: this.$this.modal.edit,

      submitted: false,
  
      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

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
  methods: {

    openEditModal(date){

      this.form.id = date.id;

      getDateStatusName( this.form.id, this.$i18n.locale ).then( resp => {
        
        if( resp ) {
          
          this.form.status = resp.status;
        
        } else {
          
          this.form.status = "";
        
        }

      });

      this.form.sequence = date.sequence;

      this.form.class = this.$t(date.class);

      this.form.isLast = date.isLast == 1 ? true : false;

      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("edit-modal");

    },
    
    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ), {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.form.reset();
            
            this.modal.isVisible = false;

            $('.error').remove();
        
            $('#class, #status, #sequence').removeAttr('style')

            done();
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.file = "";
        
        this.modal.isVisible = false;
      
      }
    
    },


    onDelete(date) {
      
      // this.$confirm( this.$t( 'delete_transaction_alert' ), {
        
      //   confirmButtonText: "OK",
        
      //   cancelButtonText: this.$t( 'cancel' ),
        
      //   type: "error"
      
      // })
      this.$bvModal
        .msgBoxConfirm(
          this.$t( 'question_record_delete' ) + `${date.status}` + this.$t( 'from' ) + this.$t( 'table.date_status_name' ) + this.$t( 'records' ),
          {
            title: this.$t('confirmation_record_delete'),
            okVariant: "danger",
            okTitle: this.$t( "yes"),
            cancelTitle: this.$t( 'cancel' ),
            hideHeaderClose: false,
            centered: true
          }
        )
        .then(resp => {
          
          if(resp) {

            date.loading = true;
            
            softDelete(date.id).then(resp => {
              
              date.loading = false;
              
              this.$notify({
                
                title: this.$t( 'delete' ),
                
                message: resp.message + ' ' + this.$t( 'remove_date_alert' ),
                
                type: resp.type
            
              });
          
              this.$this.loadDate();
              this.$this.onEdit(this.$this.class)

            } );
    
          }

        } );

    },
    
    onSubmit() {
      
      if( this.form.name == '' ){
        
        this.$alert( this.$t( 'fill_up_platform_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      this.modal.button.loading = true;
      
      let formData = new FormData();
      
      formData.append("id", this.form.id  );
      formData.append("status",  this.form.status );
      formData.append("sequence",  this.form.sequence );
      formData.append("isLast",  this.form.isLast );
      formData.append("class",  this.form.class );
      formData.append("language",  this.form.language, );
      
      updateDateStatus(formData).then(resp => {
          $('.error').remove();
        
          $('#class, #status, #sequence').removeAttr('style')

          this.$this.makeToast(
            resp.data.type, 
            "DATE STATUS UPDATED",
            this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' )
          );

          this.form.reset();
          
          this.modal.isVisible = false;
          
          this.$this.loadDate();
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;
        
      
      })
      .catch(error => {

        $('.error').remove();
        
        $('#class, #status, #sequence').removeAttr('style')

        let errors = error.response.data.errors;

        for (let field of Object.keys(errors)) {

            let el = $(`#${field}`)

            el.attr('style', 'border-color: #ff3b3b; box-shadow: 1px 1px 3px 1px #ff3b3b !important;')

            el.after($('<span style="color: #ff3b3b;" class="error">'+errors[field][0]+'</span>'))
            
        }

        this.modal.button.loading = false;

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

      let data = this.$this.items;

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

<style>

.margin-top {

  margin-top: -20px !important;

}

.dialog-footer {

  margin-top: -20px !important;

}

.avatar-uploader .el-upload {

  border: 1px dashed #d9d9d9;

  border-radius: 6px;

  cursor: pointer;

  position: relative;

  overflow: hidden;

  width: 150px;

}

.avatar-uploader .el-upload:hover {

  border-color: #409eff;

}

.avatar-uploader-icon {

  font-size: 28px;

  color: #8c939d;

  width: auto;

  /*width: 178px;*/

  /*height: 150px;*/

  line-height: 150px;

  text-align: center;

}

.avatar {

  width: 178px;

  height: 178px;

  display: block;

}

</style>
