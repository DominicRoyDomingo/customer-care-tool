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

              {{ $t('brands_name') }}

              <span class="text-danger">*</span>

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

            <span class="float-right" v-if="isShowButton">

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

import { updateBrands, getBrandsName } from "./../../../api/brands.js";

import vue2Dropzone from "vue2-dropzone";

import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
  
  props: ["$this"],

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

      modal: this.$this.modal.edit,

      submitted: false,

      form: this.$this.form,

      formData: this.$this.formData,

      isShowButton: true,

      formGroups: [],

      langs: [
        
        { id:'en', value: 'English' },
        
        { id:'it', value: 'Italian' },
        
        { id:'de', value: 'German' },
      
      ]
      
    };

  },
  
  methods: {

    afterComplete(file) {
      
      this.isShowButton = true;

      this.form.image = JSON.parse(file.xhr.responseText).image_name;

    },

    sendingUpload() {
      
      this.isShowButton = false;

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
            
            done();
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.form.reset();
        
        this.modal.isVisible = false;
      
      }
    
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
      
      updateBrands(formData).then(resp => {
        
        if( resp.data.action == 'duplicate' ) {
        
          this.$notify({
            
            title: resp.data.title,
            
            message: resp.data.data.name + ' ' + this.$t( 'duplicate_brands_entry' ),
            
            type: resp.data.type
          
          });
          
          this.modal.button.loading = false;
        
        } else {
          
          this.$notify({
            
            title: resp.data.title,
            
            message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),
            
            type: resp.data.type
          
          });

          this.form.reset();
          
          this.modal.isVisible = false;
          
          this.$this.loadBrands();
          
          this.form.language = this.$i18n.locale;
          
          this.modal.button.loading = false;
        
        }
      
      });
    
    },
    
    selectLang(event){

      this.form.language = event.target.value;

      getBrandsName( this.form.id, event.target.value ).then( resp => {
        
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
