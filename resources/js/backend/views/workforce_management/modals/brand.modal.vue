<template>
<span class="create">
  <b-modal id="brand-modal" @hide="hide" hide-footer hide-header size="lg">
    <br>
     <div style="position: absolute; top: 5px; right: 10px; z-index: 999">
        <v-btn
          icon
          color="pink"
          @click="modalClose"
        >
          <v-icon>mdi-close-box-outline</v-icon>
        </v-btn>
      </div>
      <v-row class="px-3">
        <v-col
          cols="6"
          md="4"
        >
          <v-card
            class="mx-auto my-12"
            max-width="200"
          >

            <v-img
              v-if="url != null"
              height="156"
              :src="url"
            ></v-img>

           <v-img
              v-else
              height="156"
              src="https://customer-care-tool.s3.us-east-2.amazonaws.com/image-placeholder/image-placeholder.png"
            ></v-img>

            <div 
              class="d-flex flex-column" 
              align="center" 
              style="height: 64px; justify-content: center; color: #fff; cursor: pointer;" 
              :style="[url != null ? {'background-color': '#828282'} : {'background-color': '#BDBDBD'}]"
              @click="selectFile()" 
            >
              <v-icon color="#fff"> mdi-camera-plus-outline </v-icon>
              <!-- <input ref="file" type="file" @change="onGetFile" hidden>
                    <div @click="selectFile()" class="button_upload_image">
                      <i class="fa fa-image" style="font-size: 20px"></i><br>
                      Upload brand logo
                    </div> -->

              <span class="white--text" style="font-size: 0.80rem !important;"> {{ url != null ? 'Change Brand Logo' : 'Upload Brand Logo'}} </span>
              <input 
                ref="file"
                id="fileUpload" 
                type="file" 
                @change="onGetFile"
                accept=".png, .jpg, .jpeg"
                plain
                hidden
              >
            </div>
          </v-card>
        </v-col>

        <v-col
          cols="12"
          sm="6"
          md="8"
          class="d-flex flex-column px-5 pt-4"
        >
          <div
            style="color: #4F4F4F"
            class="mb-4 h5"
          >{{ $t("brands_add_new_button") }}
          </div>
          <b-form-group>
              <el-input
                :placeholder="$t('label.brand_name')"
                id="name"
                name="brand_name"
                v-model="form.brand_name"
                clearable
              ></el-input>
              <small style="color:red" v-if="brand_required"> {{ $t("brands_name") }} {{ $t('is_required')}}</small>
          </b-form-group>

          <b-form-group>
              <el-input
                :placeholder="$t('label.website')"
                id="website"
                name="website"
                v-model="form.website"
                clearable
              ></el-input>
               <small style="color:red" v-if="website_required"> {{ $t("label.website") }} {{ $t('is_required')}}</small>
          </b-form-group>
          <b-form-group>
              <el-select id="categories" v-model="form.brandCategories" multiple :placeholder="$t('label.select_categories')">
                <el-option
                  v-for="category in newSpecializationCategories"
                  :key="category.id"
                  :label="category.category_name"
                  :value="category.id">
                </el-option>
              </el-select>
              <small style="color:red" v-if="category_required"> {{ $t("label.select_categories") }} {{ $t('is_required')}}</small>
          </b-form-group>

          <b-form-group>
              <b-form-checkbox
                id="checkbox-1"
                v-model="form.isDefault"
                name="checkbox-1"
                value="1"
                unchecked-value="0"
              >
                <span style="font-size: 0.80rem !important">Set as Default Brand</span>
              </b-form-checkbox>
          </b-form-group>
        </v-col>
        <v-col
          md="12"
          class="px-5"
        >
          <span class="float-right">
                <v-btn
                  depressed
                  small
                  dark
                  color="#56BFFF"
                  @click="onSubmit"
                >
                  Add a Brand
                </v-btn>
              </span>
        </v-col>
      </v-row>
      <br>
  </b-modal>
</span>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import toast from "./../../../helpers/toast.js";
import Form from "./../../../shared/form.js";

export default {
  props: ["parent"],
  mixins: [toast],
  data() {
    return {
      file: null,
      loading: true,

      submitted: false,

      keep_open: false,

      url: null,

      newSpecializationCategories: [],

      form: new Form({

        id: "",
        brand_name: "",
        brandCategories: [],
        website: "",
        logo: "",
        isDefault: 0,
        language: this.$i18n.locale,
        action: 'Add'
      }),
      brand_required: false,
      website_required: false,
      category_required: false,
    };
  },

  computed: {
    ...mapGetters("brand", {
      responseStatus: "get_response_status",
    }),
    ...mapGetters({
            categories: "jobs/get_job_categories",
        }),
  },
  mounted(){
    this.loadSpecializationCategories();
  },
  methods: {
    ...mapActions("brand", ["post_brand", "add_brand", "update_brand"]),
    ...mapActions("jobs", ["get_jobs", "get_job_categories", "delete_job_description", "get_filtered_job_professions"]),
    selectFile(){
      let fileInputElement = this.$refs.file;
      fileInputElement.click();

      // Do something with chosen file 
    },
    loadSpecializationCategories() {
            let params = {
                api_token: this.$user.api_token,
                locale: this.$i18n.locale,
            };

            this.get_job_categories(params).then(_ => {
                let mapSpecializationCategories = this.categories.map(s => ({
                    category: s.category,
                    category_name: s.category_name,
                    created_at: s.created_at,
                    id: s.id,
                    is_english: s.is_english,
                    is_german: s.is_german,
                    is_italian: s.is_italian,
                    is_loading: s.is_loading,
                    updated_at: s.updated_at,
                }));
                this.newSpecializationCategories = mapSpecializationCategories;
            });
        },

    modalClose(done) {
      $(".field-error").remove();

      $("#name,#categories").removeAttr("style");
      if (this.form.isDataEmpty()) {
        this.$confirm(this.$t("close_transaction_alert"), {
          confirmButtonText: "OK",

          cancelButtonText: this.$t("cancel"),

          type: "error",
        })
          .then((resp) => {
            this.form.reset();
            this.url = null;
            this.$bvModal.hide("brand-modal");

            $(".error").remove();

            $("#class, #status, #sequence").removeAttr("style");
          })

          .catch((_) => {});
      } else {
 
        this.form.reset();
        this.url = null;
        this.file = "";
        this.keep_open = false;
        this.$bvModal.hide("brand-modal");

      }

      this.keep_open = false;
    },

    chooseFiles() {
        document.getElementById("fileUpload").click()
    },
    onGetFile(event) {
      this.file = event.target.files[0];
      const image = event.target.files[0];
      this.url = URL.createObjectURL(image);

      console.log( this.url );
    },
    // onGetFile(event) {
    //   console.log( event );
    //     if(event.target.files[0] == undefined) return;
    //     this.file = event.target.files[0];
        
    //     this.url = URL.createObjectURL(this.file);
    //     },
  
    hide() {
      this.$emit("hide");
    },
    onCloseModal() {
      this.form.action = '';
      this.form.brand_name = '';
      this.form.website = '';
      this.form.isDefault = '';
      this.form.brandCategories = '';
      this.$bvModal.hide("brand-modal");
    },
    onSubmit() {
      this.brand_required = false;
      this.website_required = false;
      this.category_required = false;
      if( this.form.brand_name === '') {
        this.brand_required = true;
        return false;
      }
      // if( this.form.website === '' ) {
      //   this.website_required = true;
      //   return false;
      // }
      if( this.form.brandCategories.length === 0 ) {
        this.category_required = true;
        return false;
      }
        // this.modal.button.loading = true;
        let params = {
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            name: this.form.brand_name,
            website: this.form.website,
            isDefault: this.form.isDefault,
            org_id: this.$org_id,
            file: this.file,
            categories: this.form.brandCategories,
        };

        this.post_brand(params)
            .then((resp) => {
            $(".field-error").remove();

            $("#name,#categories").removeAttr("style");
                if (this.responseStatus) {
                    let message = {
                    create: `${this.form.brand_name}` + this.$t("created_message") + this.$t("label.brand"),
                    update:
                        this.$t("updated_message1") +
                        this.$t("label.brands") +
                        ` ID: ${this.parent.form.id} ` +
                        this.$t("updated_message2"),
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
                    // this.modal.button.loading = false;

                    this.url = null;

                    this.form.reset();

                    this.form.language = this.$i18n.locale;

                    this.onCloseModal();
                    
                    this.$emit("loadTable");
                    
                }
                console.log( resp );
            })
            .catch((error) => {
              // console.log( error.response.data.errors.name[0] );
                // this.modal.button.loading = false;
                this.makeToast(
                  "danger",
                  "DUPLICATE",
                  error.response.data.errors.name[0]
                );
                return false;
            });
    },
  },
};
</script>

<style scoped>
.create-organization .el-dialog__header{
        display: none;
    }

    .el-select {
        display: block;
    }
</style>
