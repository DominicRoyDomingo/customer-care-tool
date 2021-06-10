<template>

  <div class="edit">

    <el-dialog
      :title="`${$t(modal.name)} ${this.form.sequence}`"
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

            <div class="col-lg-12 col-md-12 text-center">
              
              <h4>{{ form.item_name }}</h4>

            </div>

            <label for="author_idea">
                {{ $t('item_person_in_charge') }}
            </label>

            <el-row>
              <el-col :span="24">
                <el-select filterable v-model="form.author_idea" clearable :placeholder="$t('label.select_author')" id="author_idea" style="display: block">
                  <el-option 
                    :value="user.id" 
                    v-for="(user, index) in $this.users"
                    :key="index"
                    :label="user.full_name"></el-option>
                </el-select>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="24">
                <el-input
                  type="textarea"
                  :rows="4"
                  :placeholder="$t('label.notes')"
                  v-model="form.notes">
                </el-input>
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

    </el-dialog>

  </div>

</template>

<script>



import { updateContent, getContentName , updateStatus} from "./../../../../api/content.js";

import contentMixins from "./../mixins/contentMixins.js";

export default {
  
  props: ["$this"],

  mixins: [contentMixins],
  
  data: function() {
    
    return {
      
      modal: this.$this.modal.status,

      form: this.$this.form,

      formData: this.$this.formData,

      nextStatus: [],

      langs: [
        
            { id:'en', value: 'English' },
            
            { id:'it', value: 'Italian' },
            
            { id:'de', value: 'German' },
        
        ],

        formGroups: [],

        submitted: false,
      
    };

  },

  methods: {

    
    onSubmit() {
      
      this.modal.button.loading = true;
      
      let formData = new FormData();
      
      formData.append("id", this.form.id  );
      formData.append("author_idea",  this.form.author_idea );
      formData.append("author_task",  this.$userId);
      formData.append("notes",  this.form.notes );
      formData.append("status",  this.form.status );
      
      updateStatus(formData).then(resp => {
        
        this.errors()

        this.$this.makeToast(
          resp.data.type, 
          this.$t( 'content_status_updated' ),
          this.$t( 'status_1_alert' ) + resp.data.message + this.$t( 'status_2_alert' ) + resp.data.status
        );

        this.form.reset();
        
        this.modal.isVisible = false;
        
        this.$this.loadContent();
        
        this.form.language = this.$i18n.locale;
        
        this.modal.button.loading = false;
      
      }).catch(error => {

        this.errors()

        let errors = error.response.data.errors;

        for (let field of Object.keys(errors)) {

            let el = $(`#${field}`)

            el.attr('style', 'border-color: #ff3b3b; box-shadow: 1px 1px 3px 1px #ff3b3b !important;')

            el.after($('<span style="color: #ff3b3b;" class="error">'+errors[field][0]+'</span>'))
            
        }

        this.modal.button.loading = false;

      });
    
    },
    
  },

};

</script>

<style>


.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}

.margin-top {

  margin-top: -20px !important;

}

.dialog-footer {

  margin-top: -20px !important;

}

</style>
