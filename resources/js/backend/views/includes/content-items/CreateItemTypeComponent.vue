<template>

  <div class="create">

    <b-modal
      id="create-item-type"
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
                {{ $t('publishing_type_name') }}
            </label>

            <el-input :placeholder="$t('publishing_type_name')" id="type_name" name="type_name" v-model="form.type_name" clearable></el-input>

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

import { createItemType } from "./../../../api/item_type.js";

export default {
  
  props: ["$this"],

  data: function() {
    
    return {
      
      modal: this.$this.modal.itemType,

      modalId: this.$this.modalId,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

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
            
            this.$this.$bvModal.hide("create-item-type");

            this.$this.$bvModal.show(this.$this.modalId);
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.$this.$bvModal.hide("create-item-type");

        this.$this.$bvModal.show(this.$this.modalId);
      
      }
    
      this.keep_open = false;

      

    },
    
    onSubmit() {
      
      if( this.form.type_name == '' ){
        
        this.$alert( this.$t( 'fill_up_publishing_type_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      this.modal.button.loading = true;
      
      const data = {
       
        id: this.form.id,
        
        type_name: this.form.type_name,
        
        language: this.form.language
      
      };
      
      let formData = new FormData();
      
      formData.append("data",  JSON.stringify( data ) );

      createItemType(formData).then(resp => {
      
        if( resp.action == 'duplicate' ) {
          
          this.$this.makeToast(

            resp.data.type, 

            resp.data.title,

            resp.data.data.name+ ' ' + this.$t( 'duplicate_publishing_type_entry' )

          );

          // this.$notify({
            
          //   title: resp.data.title,
            
          //   message: resp.data.data.name+ ' ' + this.$t( 'duplicate_publishing_type_entry' ),
            
          //   type: resp.data.type
          
          // });
          
          this.modal.button.loading = false;
        
        } else {
          
          this.$this.makeToast(

            resp.data.type, 

            this.$t( 'item_type_created' ),

            resp.data.message + ' ' + this.$t( 'successfull_publishing_type_entry' )

          );
          
          this.$emit('loadTable')
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;

          if( this.keep_open == true ) {
            
            this.$this.$bvModal.show("create-item-type");


          } else {

            this.$this.$bvModal.hide("create-item-type");

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
