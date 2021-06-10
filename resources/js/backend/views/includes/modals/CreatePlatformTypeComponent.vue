<template>

  <div class="create">

    <b-modal
      id="create-platform-type"
      hide-footer
      size="md"
      :title="$t(modal.add.name)"
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
                {{ $t('platform_type_name') }}
            </label>

            <el-input :placeholder="$t('platform_type_name')" id="name" name="name" v-model="form.name" clearable></el-input>

          </b-form-group>

          <b-form-group>

            <!-- <span class="float-left">

              <div class="form-check form-check-inline mr-1">

                <input class="form-check-input" v-model="keep_open" type="checkbox">

                <label class="form-check-label" for="inline-checkbox3"><small>Keep the form open?</small></label>

              </div>

            </span> -->
            
            <span class="float-right">

              <el-button size="small" @click="modalClose">{{ $t(modal.add.button.cancel)}}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="primary"
                :loading="modal.add.button.loading"
              >{{ $t(modal.add.button.save) }}</el-button>

            </span>

          </b-form-group>

        </form>

      </div>

    </b-modal>

  </div>

</template>

<script>

import Form from "./../../../shared/form.js";

import { createPlatform } from "./../../../api/platform_type.js";

import { getBrandsAndPlatformTypes } from "./../../../api/content_item.js";

export default {
  
  props: ["this_cpc", "parent"],

  data: function() {
    
    return {

      submitted: false,

      keep_open: false,

      modal: {
        
        add: {
          
          name: "platform_type_add_new_button",
          
          isVisible: false,
          
          button: {
            
            save: "save_button",
            
            cancel: "cancel",
            
            new: "platform_type_add_new_button",
            
            loading: false
          
          }

        },

      },
      
      form: new Form({
        
        id: "",
        
        name: "",
        
        language: this.$i18n.locale
      
      }),

      formData: new FormData(),

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
            
            this.this_cpc.$bvModal.hide('create-platform-type');

            this.this_cpc.$bvModal.show(this.this_cpc.modalId);
          
          })
          
          .catch(_ => {});
      
      } else {

        this.this_cpc.$bvModal.hide('create-platform-type');

        this.this_cpc.$bvModal.show(this.this_cpc.modalId);
      
      }

      this.keep_open = false;
    
    },
    
    onSubmit() {

      if( this.form.name == '' ){
        
        this.$alert( this.$t( 'fill_up_platform_type_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      this.modal.add.button.loading = true;
      
      const data = {
       
        id: this.form.id,
        
        name: this.form.name,
        
        language: this.form.language
      
      };
      
      let formData = new FormData();
      
      formData.append("data",  JSON.stringify( data ) );

      createPlatform(formData).then(resp => {
          
        this.$notify({
        
        title: resp.data.title,
        
        message: resp.data.message + ' ' + this.$t( 'successfull_platform_type_entry' ),
        
        type: resp.data.type
        
        });

        getBrandsAndPlatformTypes().then(resp => {

          this.parent.brands = resp.brands;

          this.parent.platform_type = resp.platformTypes
  
        });
        
        this.form.language = this.$i18n.locale;
        
        this.modal.add.button.loading = false;

        if( this.keep_open == true ) {
        
            this.this_cpc.$bvModal.hide("create-platform-type");

        } else {

            this.this_cpc.$bvModal.hide("create-platform-type");

            this.this_cpc.$bvModal.show(this.this_cpc.modalId);

            this.keep_open == false;

        }
        
      
      });
    
    }
  
  }

};

</script>

<style scoped>

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
