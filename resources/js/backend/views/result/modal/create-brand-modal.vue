<template>
  <div>
    <b-modal
      id="create-brand-modal"
      hide-header
      hide-footer
      @hide="onReset"
      size="lg"
    >
      <v-app id="contact_type__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{$t("label.add")}}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('create-brand-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <form
            class="form"
            @submit.prevent="onSave"
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
                   <label>{{ $t("brand_btn_save") }}</label>
                </div>
            <div class="form-group">

                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="brandname"
                  class="form-control"
                  :placeholder="$t('label.brand_name')"
                  autocomplete="off"
                  aria-describedby="brand"
                />
                <small
                  id="job"
                  class="text-danger"
                  v-if="isRequired"
                >Name is required</small>

            </div>

            <div class="form-group">

                <input
                  id="website"
                  type="text"
                  name="website"
                  v-model="brandwebsite"
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
                  :reduce="item => item.code"
                  multiple
                  taggable
                > 
                </v-select>

            </div>
            <div class='form-group'>    
              <span><input type='checkbox' v-model="is_default" /> {{ $t("result.brand_default") }} </span>
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
              @click="$bvModal.hide('create-brand-modal')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSave">
              <div v-if="isLoading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <span>Save</span>
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
import i18n from '../../../i18n.js';
import toast from "./../../../helpers/toast.js";
let lang = i18n.locale;

export default {
  mixins: [toast],
  data() {
    return {
      loading: true,
      isLoading : false,
      isRequired : false,
      btnloading : false,
      categorySel : [],
      categoryOpt : [],
      brandwebsite : '',
      brandname : '',
      is_default : 0
    };
  },
  props : {
    addBrandOpt : {
        type : Function,
        default : function(obj){
            return obj
        }
    },
    resultName : {
        type : String,
        default : ''
    }
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
      brandname(val, old){
          if(val != '' || val != null){
              this.isRequired = false
          }
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

      if(this.brandname == '' || this.brandname == null){
        this.isRequired = true
        return
      }
      this.isLoading = true;

      var params = {
        api_token : this.$user.api_token,
        locale : lang,
        name : this.brandname,
        website :this.brandwebsite,
        file : this.file,
        categories : this.categorySel,
        isDefault : (this.is_default) ? 1 : 0,
        org_id : this.workspaces[0].organization
      }

      this.post_brand(params)
        .then((resp) => {
          this.isLoading = false;
          var data = resp.data
          if (resp.status == 200) {
            
            this.addBrandOpt({
                id : data.id,
                label : data.brand_name
            })

            let notification_message = this.$t("toasts.added_brand");
            notification_message = notification_message.replace(
              /%variable%/g,
              data.brand_name
            );

            let message = {
              create: notification_message,
              update: notification_message,
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.$bvModal.hide('create-brand-modal');
          
          }

        })
        .catch((error) => {
              this.isLoading = false;
              
              this.$bvModal.hide('create-brand-modal');

              this.makeToast(
                "danger",
                "DUPLICATE",
                error.response.data.errors.name[0]
              );
        });
    },
    onReset(){
        this.categorySel = null
        this.brandwebsite = ''
        this.brandname = ''
    }
  },
};
</script>
