<template>

  <div class="create">

    <b-modal
      id="create-platform-type"
      hide-footer
      size="md"
      :title="$t(modal.name)"
    >

      <div class="p-2 margin-top">

        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >

          <b-form-group>

            <label for="name">

                {{ $t('brands_name') }}

            </label>
            
            <select v-model="form.brand" class="form-control text-capitalize" >

              <option
                :value="type.id" 
                v-for="(type, index) in $this.brands"
                :key="index"
              >{{type.name}}</option>

            </select>

          </b-form-group>


          <b-form-group>

            <label for="name">

                {{ $t('platform_type_name') }}

            </label>
            
            <!-- <select v-model="form.platform_type" class="form-control text-capitalize" >

              <option
                :value="type.id" 
                v-for="(type, index) in $this.platform_type"
                :key="index"
              >{{type.platform_type}} <small v-if="type.convertion==true" style="color:red">(No {{ type.language }} translation yet)</small></option>

            </select> -->

          </b-form-group>

          <b-form-group>

            <!-- <span class="float-left">

              <div class="form-check form-check-inline mr-1">

                <input class="form-check-input" v-model="keep_open" type="checkbox">

                <label class="form-check-label" for="inline-checkbox3"><small>Keep the form open?</small></label>

              </div>

            </span> -->
            
            <span class="float-right">

              <el-button size="small" @click="modalClose">{{ $t(modal.button.cancel)}}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="primary"
                :loading="modal.button.loading"
              >{{ $t(modal.button.save) }}</el-button>

            </span>

          </b-form-group>

        </form>

      </div>

    </b-modal>

  </div>

</template>

<script>

// import Api from "./../../../shared/api.js";

import { createPlatform } from "./../../../api/platform_item.js";

import { getLastStatus } from "./../../../api/content_item.js";

export default {
  
  props: ["$this"],

  data: function() {
    
    return {
      
      modal: this.$this.modal.platformType,

      modalId: this.$this.modalId,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: []

    };

  },

  methods: {
    
    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ) , {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.$this.$bvModal.hide("create-platform-type");

            this.$this.$bvModal.show(this.$this.modalId);
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.$this.$bvModal.hide("create-platform-type");

        this.$this.$bvModal.show(this.$this.modalId);
      
      }

      this.keep_open = false;
    
    },
    
    onSubmit() {

      if( this.form.brand == '' ){
        
        this.$alert( this.$t( 'fill_up_platform_item_brand' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }

      if( this.form.platform_type == '' ){
        
        this.$alert( this.$t( 'fill_up_platform_item_brand' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      this.modal.button.loading = true;
      
      const data = {
       
        id: this.form.id,
        
        brand: this.form.brand,

        platform_type: this.form.platform_type,
        
        language: this.form.language
      
      };
      
      let formData = new FormData();
      
      formData.append("data",  JSON.stringify( data ) );

      createPlatform(formData).then(resp => {
        
        if( resp.data.title == 'Duplicate' ) {
          
        //   this.$notify({
            
        //     title: resp.data.title,
            
        //     message: resp.data.message + ' ' + this.$t( 'duplicate_publishing_item_entry' ),
            
        //     type: resp.data.type
          
        //   });
   
          this.$this.makeToast(

            resp.data.type, 

            resp.data.title,

            resp.data.message + ' ' + this.$t( 'duplicate_publishing_item_entry' ),

          );
          
          this.modal.button.loading = false;
        
        } else {
   
        //   this.$notify({
            
        //     title: resp.data.title,
            
        //     message: resp.data.message + ' ' + this.$t( 'successfull_platform_item_entry' ),
            
        //     type: resp.data.type
          
        //   });

          this.$this.makeToast(

            resp.data.type, 

            this.$t( 'platform_type_created' ),

            resp.data.message + ' ' + this.$t( 'successfull_platform_item_entry' ),

          );

          this.$this.loadItem(this.$this.search, this.$this.params);
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;

          if( this.keep_open == true ) {
            
            this.$this.$bvModal.hide("create-platform-type");

          } else {

            this.$this.$bvModal.hide("create-platform-type");

            this.$this.$bvModal.show(this.$this.modalId);

            this.keep_open == false;

          }

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
