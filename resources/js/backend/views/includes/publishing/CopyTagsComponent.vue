<template>

  <div class="edit">

    <b-modal
      id="publishing-copy-tags"
      hide-footer
      size="md"
      :title="`${$t('copy_tags')} ${$t($this.publishing_name)}`"
    >

      <div class="p-2 margin-top">

        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >

          <b-form-group>


            <label for="other_publishing">
                {{ $t('other_publishing') }}
            </label>

            <el-row>
              <el-col :span="24">
                <b-form-select v-model="tag" @change="updateDropdowns">                   
                    <option v-for="(tag, indexOpt) in $this.otherTags" 
                        :key="indexOpt"
                        :value="tag"
                    >
                        {{ tag.publishing_name }}
                    </option>
                </b-form-select>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <div>
                    <b-card>
                        <div 
                            class="chip mt-2 mb-0" 
                            v-for="(name, index) in this.tags" 
                            :key="index"
                        >
                            {{name}}
                        <i 
                            class="close fas fa-times"
                        ></i>
                        </div>
                    </b-card>
                </div>
              </el-col>
            </el-row>

            
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

    </b-modal>
    
  </div>

</template>

<script>

import { attachTags } from "./../../../api/publishing.js";

import publishingMixins from "./mixins/publishingMixins.js";

export default {
  
  props: ["$this"],

  mixins: [publishingMixins],
  
  data: function() {
    
    return {
      
      modal: this.$this.modal.copy_tags,

      form: this.$this.form,

      formData: this.$this.formData,

      modalId: this.$this.modalId,

      tag: "",

      tags: [],

      tags_ids: [],

    };

  },

  methods: {
    updateDropdowns(tag){
        this.tags       = tag.tags_name
        this.tags_ids   = tag.tag
    },
    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ), {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {

            this.$this.$bvModal.hide('publishing-copy-tags');

            this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
            
            done();
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.file = "";
        
        this.$this.$bvModal.hide('publishing-copy-tags');

        this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
      
      }
    
    },
    
    onSubmit() {
      
      this.modal.button.loading = true;
      
      let formData = new FormData();

      let mergedTags = this.arrayUnique(this.$this.form.tags.concat(this.tags_ids));

      this.$this.form.tags = mergedTags;

      this.$this.$bvModal.hide('publishing-copy-tags');

      this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();

      this.form.language = this.$i18n.locale;
        
      this.modal.button.loading = false;
    
    },

    arrayUnique(array) {

      var a = array.concat();

      for(var i=0; i<a.length; ++i) {

        for(var j=i+1; j<a.length; ++j) {

            if(a[i] === a[j])

              a.splice(j--, 1);

        }

      }

      return a;

    }
    
  },

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

.tags-button {

  width: 100%;

}

</style>
