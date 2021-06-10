<template>
  <div>
    <b-modal
      id="brand-modal"
      @hide="$bvModal.hide('brand-modal')"
      hide-header
      hide-footer
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.action == "Add"
                  ? $t("label.add")
                  : $t("label.add")
              }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('brand-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
            @keyup="parent.form.errors.clear($event.target.name)"
          >

            <div class='row pa-2'>
              <div class='col-md-5'>
                  <div class="card">
                      <a href='#' @click="openFilePicker"><img ref="img-preview" class="card-img-top" src="/img/backend/brand/no-image-img.jpg" alt="Card image cap"></a>
                      <div class="card-footer cursor-pointer" @click="openFilePicker">
                        <div class='card-text text-center'><v-icon color='gray'>mdi-camera</v-icon></div>
                        <p class="card-text text-center">Add Brand Logo</p>                        
                      </div>
                  </div>
              </div>
              <div class='col'>
                 <div class="form-group text-center">
                   <label>Add a Brand</label>
                </div>
            <div class="form-group">

                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="parent.form.name"
                  class="form-control"
                  :placeholder="$t('label.brand_name')"
                  autocomplete="off"
                  aria-describedby="brand"
                />
                <small
                  id="job"
                  v-if="parent.form.errors.has('name')"
                  v-text="parent.form.errors.get('name')"
                  class="text-danger"
                />

            </div>

            <div class="form-group">

                <input
                  id="website"
                  type="text"
                  name="website"
                  v-model="parent.form.website"
                  class="form-control"
                  autocomplete="off"
                  aria-describedby="brand"
                  :placeholder="$t('label.website')"
                />

            </div>

            <div class="form-group" style='display:none;'>
                <label for="logo">{{ $t("label.logo") }}</label>
                <b-form-file
                  ref="img-file"
                  id="img-file"
                  @change="onGetFile"
                  accept=".png, .jpg, .jpeg"
                  plain
                ></b-form-file>

            </div>
            <div class='form-group'>     
         
                <v-select
                  v-model="categorySel"
                  label="category"
                  :options="categoryOpt"
                  placeholder='Select Categories'
                  multiple
                  taggable
                > 
                </v-select>

            </div>
            <div class='form-group'>    
              <span><input type='checkbox' v-model="is_default" /> Set as Default Brand </span>
            </div>
          </div>
        </div>

          </form>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('brand-modal')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSave">
              <div v-if="parent.btnloading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div v-if="parent.form.action == 'Add'">
                  <v-icon left>mdi-account-plus</v-icon>
                  {{ $t("brand_btn_save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-account-edit</v-icon>
                  {{ $t("brand_btn_save") }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import axios from 'axios'
import { fetchList } from '../../../api/category.js'

export default {
  props: ["parent"],

  data() {
    return {
      loading: true,
      categorySel : [],
      categoryOpt : [],
      is_default : 0
    };
  },

  computed: {
    ...mapGetters("brand", {
        responseStatus: "get_response_status",
    }),
    ...mapGetters("workspace", {
        active_workspace: "active_workspace",
        workspaces : 'workspaces'
    })
  },
  watch : {
      is_default(val, old){
        
      }
  },
  created : function(){
      fetchList().then((res) => {
          if(res){
              this.categoryOpt = res.map((item) => ({
                  category : item.name,
                  code : item.id
              }))
          }
      })
  },
  methods: {
    ...mapActions("brand", ["post_brand"]),
    openFilePicker(){
      this.$refs['img-file'].$el.click()
    },
    onGetFile(event) {
      var $this = this
      this.file = event.target.files[0];
      var reader = new FileReader();
      reader.readAsDataURL(this.file);
      reader.onload = function () {
         $this.$refs['img-preview'].setAttribute('src', reader.result);
      };
          
    },
    hide() {
      this.$emit("hide");
    },
    onSave() {
      this.parent.btnloading = true;
      var catmap = this.categorySel.map((item) => item.code );
      let formData = new FormData();
      console.log(catmap)
      //formData.append("id", this.parent.form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("action", this.parent.form.action);
      formData.append("name", this.parent.form.name);
      formData.append("website", this.parent.form.website);
      formData.append("file", this.file);
      formData.append("categories", JSON.stringify(catmap))
      formData.append('isDefault', (this.is_default) ? 1 : 0);
      formData.append('org_id', this.workspaces[0].organization);

      this.post_brand(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("brand-modal");
          if (this.responseStatus) {
            let notification_message = this.$t("toasts.added_brand");
            notification_message = notification_message.replace(
              /%variable%/g,
              this.parent.form.name
            );

            let message = {
              create: notification_message,
              update: notification_message,
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.create,
              message.create
            );

            if(this.parent.meta.page == "edit-client-engagement-modal"){
              this.parent.$bvModal.show("platform-modal");
              this.parent.fetchBrands();
              return
            }

            this.parent.addedNewBrandSuccessfully();
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>
