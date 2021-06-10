<template>

  <div class="create">

    <b-modal
      id="organization-category-add-modal"
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
                {{ $t('organization_category_category') }}
            </label>

            <el-input :placeholder="$t('organization_category_category')" id="category" name="name" v-model="form.category" clearable></el-input>

          </b-form-group>

          <b-form-group>

            <label for="name">
                {{ $t('organization_category_icon') }}
            </label>

            <b-form-file id="img-file" @change="onGetFile" accept=".png, .jpg, .jpeg" plain></b-form-file>

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

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  
  props: ["$this"],

  data: function() {
    
    return {
      
      modal: this.$this.modal.add,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

    };

  },

  methods: {
    
    ...mapActions("organization_categories", ["post_organization_category","add_organization_category","update_brand"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    modalClose(done) {
      
      if (this.form.isDataEmpty()) {
        
        this.$confirm( this.$t( 'close_transaction_alert' ) , {
          
          confirmButtonText: "OK",
          
          cancelButtonText: this.$t( 'cancel' ),
          
          type: "error"
        
        })
          .then(resp => {
            
            this.$this.$bvModal.hide('organization-category-add-modal');

            this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
          
          })
          
          .catch(_ => {});
      
      } else {
        
        this.file = "";
        
        this.$this.$bvModal.hide('organization-category-add-modal');

        this.$this.modal.tags != undefined ? this.$this.$bvModal.show(this.$this.modalId) : this.form.reset();
      
      }
    
      this.keep_open = false;

    },
    
    onSubmit() {

      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("category",  this.form.category );
      formData.append("icon",  this.form.icon );

      this.post_organization_category(formData)
        .then(resp => {
          
          this.$this.btnloading = false;
          this.$bvModal.hide("organization-category-add-modal");

          if (this.$this.responseStatus) {

            console.log(this.$this.responseStatus)
            this.add_organization_category(this.$this.responseStatus);

            let message = {
              create: `${this.form.category}` + this.$t( 'created_message' ),
              title: {
                create: this.$t( 'new_record_created' ),
              }
            };
            
            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.$this.successfullySavedOrganizationCategory();
          }
        })
        .catch(error => {
          console.log(error)
        });

    }
  
  }

};

</script>

<style>


</style>
