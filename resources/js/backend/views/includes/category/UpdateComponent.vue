<template>

  <div class="edit">

    <el-dialog
      :title="$t(modal.name)"
      :visible.sync="modal.isVisible"
      width="35%"
      :before-close="modalClose"
    >

      <div class="p-2 margin-top">

        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >

          <b-form-group>

            <div class="col-lg-3 col-md-3 float-right">

                <div class="row">
              
                    <select class="form-control" @change.prevent="selectLang($event)" style="margin-bottom:-35px">
                        
                        <option
                        :value="language.id"
                        :selected="language.id === form.language"
                        v-for="(language, langInd) in langs"
                        :key="langInd"
                        >
                        {{language.value}}
                        </option>

                    </select>

                </div>

            </div>

          </b-form-group>

          <b-form-group>

            <label for="name">

              {{ $t('category_name') }}

              <span class="text-danger">*</span>

            </label>

            <el-input :placeholder="$t('category_name')" id="name" name="name" v-model="form.name" clearable></el-input>

            <code v-if="form.errors.has('name')" v-text="form.errors.get('name')"></code>

          </b-form-group>

          <b-form-group>

            <span class="float-right">

              <el-button size="small" @click="modalClose">{{ $t(modal.button.cancel)}}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="success"
                :loading="modal.button.loading"
              >{{ $t(modal.button.update) }}</el-button>

            </span>

          </b-form-group>

        </form>

      </div>

    </el-dialog>

  </div>

</template>

<script>

import { updateCategory, getCategoryName } from "./../../../api/category.js";

export default {
  
  props: ["$this"],

  data: function() {
    
    return {
      
      modal: this.$this.modal.edit,

      submitted: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      langs: [
        
        { id:'en', value: 'English' },
        
        { id:'it', value: 'Italian' },
        
        { id:'de', value: 'German' },
      
      ]
      
    };

  },
  
  methods: {
    
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
            
            done();
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.file = "";
        
        this.modal.isVisible = false;
      
      }
    
    },
    
    onSubmit() {
      
      if( this.form.name == '' ){
        
        this.$alert( this.$t( 'fill_up_category_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      this.modal.button.loading = true;
      
      let formData = new FormData();
      formData.append('_method', 'PUT')
      formData.append("name",  this.form.name );
      formData.append("lang",  this.form.language, );
      
      updateCategory(formData,this.form.id).then(resp => {
        
        if( resp.data.action == 'duplicate' ) {

          this.$this.makeToast(

            resp.data.type, 

            resp.data.title,

            resp.data.data.name + ' ' + this.$t( 'duplicate_category_entry' )

          );
          // this.$notify({
            
          //   title: resp.data.title,
            
          //   message: resp.data.data.name + ' ' + this.$t( 'duplicate_category_entry' ),
            
          //   type: resp.data.type
          
          // });
          
          this.modal.button.loading = false;
        
        } else {
          
          this.$this.makeToast(

            resp.data.type, 

            this.$t( 'category_updated' ),

            this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' )

          );
          // this.$notify({
            
          //   title: resp.data.title,
            
          //   message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),
            
          //   type: resp.data.type
          
          // });

          this.form.reset();
          
          this.modal.isVisible = false;
          
          this.$this.loadCategory();
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;
        
        }
      
      });
    
    },
    
    selectLang(event){

      this.form.language = event.target.value;

      getCategoryName( this.form.id, event.target.value ).then( resp => {
        
        if( resp ) {
          
          this.form.name = resp.name;
        
        } else {
          
          this.form.name = "";
        
        }

      });
        
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
