<template>

  <div class="create">

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

            <label for="name">
                {{ $t('brands_name') }}
            </label>

            <el-input :placeholder="$t('brands_name')" id="name" name="name" v-model="form.name" clearable></el-input>

          </b-form-group>

          <b-form-group>

            <label for="name">
                {{ $t('brands_website') }}
            </label>

            <el-input :placeholder="$t('brands_website')" id="website" name="website" v-model="form.website" clearable></el-input>

          </b-form-group>

          <b-form-group>

            <label for="photo">{{ $t( 'brands_image' ) }}</label>

            <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" @vdropzone-sending="sendingUpload"  @vdropzone-complete="afterComplete"></vue-dropzone>

            <input type="hidden" v-model="form.image" name="image">

          </b-form-group>

          <b-form-group>

            <!-- <span class="float-left">

              <div class="form-check form-check-inline mr-1">

                <input class="form-check-input" v-model="keep_open" type="checkbox">

                <label class="form-check-label" for="inline-checkbox3"><small>Keep the form open?</small></label>

              </div>

            </span> -->

            <span class="float-right" v-if="isShowButton">

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

    </el-dialog>

  </div>

</template>

<script>

import { createBrands } from "./../../../api/brands.js";

import vue2Dropzone from "vue2-dropzone";

import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
  
  props: ["$this","token"],

  components: {

    vueDropzone: vue2Dropzone

  },

  data: function() {

    
    return {
      
      dropzoneOptions: {

          url: '/admin/brands/uploadImage',

          headers: {

            "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content

          },
          
          thumbnailWidth: 250,

          thumbnailHeight: 250,

          maxFiles: 1,

          addRemoveLinks: true,

      },

      images: [],
      
      modal: this.$this.modal.add,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      isShowButton: true,

      formGroups: [],

    };

  },

  methods: {

    afterComplete(file) {
      // console.log( JSON.parse(file.xhr.responseText).image_name );

      this.isShowButton = true;

      this.form.image = JSON.parse(file.xhr.responseText).image_name;

    },

    sendingUpload() {
      
      this.isShowButton = false;

    },

    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ) , {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.form.reset();
            
            this.modal.isVisible = false;
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.modal.isVisible = false;
      
      }

      this.keep_open = false;

    },
    
    onSubmit() {
      
      if( this.form.name == '' ){
        
        this.$alert( this.$t( 'fill_up_brands_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }
      
      if( this.form.website == '' ){
        
        this.$alert( this.$t( 'fill_up_brands_name' ), {
          
          confirmButtonText: "OK",
          
          type: "error"
        
        })
        
        return false;
      
      }

      this.modal.button.loading = true;
      
      const data = {
       
        id: this.form.id,
        
        name: this.form.name,

        website: this.form.website,
        
        image: this.form.image,

        language: this.form.language
      
      };
      
      let formData = new FormData();
      
      formData.append("data",  JSON.stringify( data ) );

      createBrands(formData).then(resp => {
        
        if( resp.action == 'duplicate' ) {
          
          this.$notify({
            
            title: resp.data.title,
            
            message: resp.data.data.name + ' ' + this.$t( 'duplicate_brands_entry' ),
            
            type: resp.data.type
          
          });
          
          this.modal.button.loading = false;
        
        } else {
          
          this.$notify({
            
            title: resp.data.title,
            
            message: resp.data.message + ' ' + this.$t( 'successfull_brands_entry' ),
            
            type: resp.data.type
          
          });
          console.log(resp.data)
          this.form.reset();
          
          this.$this.loadBrands();
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;

          if( this.keep_open == true ) {
            
            this.modal.isVisible = true;

          } else {

            this.modal.isVisible = false;

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

.el-icon-upload{

  font-size: 50px;

  color: #c0c4cc;

  margin: 0px 0 10px;

  line-height: 50px;

}

.select-class-image{

  background-color:#e6f2e9;

  padding:5px;

  height:250;

  overflow:auto

}

.select-image{

  height:100px;

  width:100px;

  margin:5.5px;

  border-radius:3px;

}

.select-image:hover  {

  cursor: pointer;

  opacity: 0.3;

}

.image-div {
  display: flex;
  margin: 25px;
}
.image {
  max-width: 250px;
  margin: 15px;
}

</style>
