<template>
  <span class="create">
    <el-dialog
      :visible.sync="modal.isVisible"
      width="45%"
      :before-close="modalClose"
      append-to-body
      class="create-organization"
    >
    <!-- modal.name -->
      <div style="position: absolute; top: 5px; right: 10px;">
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
              @click="chooseFiles"
            >
              <v-icon color="#fff"> mdi-camera-plus-outline </v-icon>
              <span class="white--text" style="font-size: 0.80rem !important;"> {{ url != null ? 'Change Brand Logo' : 'Upload Brand Logo'}}</span>
              <input 
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
          >{{$t(modal.name)}}
          </div>
          <b-form-group>
              <el-input
                :placeholder="$t('label.brand_name')"
                id="brand_name"
                name="brand_name"
                v-model="parent.form.brand_name"
                clearable
              ></el-input>
          </b-form-group>

          <b-form-group>
              <el-input
                :placeholder="$t('label.website')"
                id="website"
                name="website"
                v-model="parent.form.website"
                clearable
              ></el-input>
          </b-form-group>
          <b-form-group>
              <el-select v-model="form.brandCategories" multiple :placeholder="$t('label.select_categories')">
                <!-- <el-option>
                  <span style="float: left">test</span>
                </el-option> -->
                <el-option
                  v-for="category in parent.newSpecializationCategories"
                  :key="category.id"
                  :label="category.category_name"
                  :value="category.id">
                </el-option>
              </el-select>
          </b-form-group>

          <b-form-group>
              <b-form-checkbox
                id="checkbox-1"
                v-model="parent.form.isDefault"
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

                <!-- <el-button
                  size="small"
                  native-type="submit"
                  :loading="modal.button.loading"
                  style="color: #56BFFF !important"
                >
                  Add a Brand
                </el-button> -->
                <v-btn
                  depressed
                  small
                  dark
                  :loading="modal.button.loading"
                  color="#56BFFF"
                  @click="onSubmit"
                >
                  Update Brand
                </v-btn>
              </span>
        </v-col>
      </v-row>
    </el-dialog>
  </span>
</template>

<script>
import { inviteUser } from "./../../../api/invite_user.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent", "parent"],

  data: function() {

    return {

      modal: this.parent.modal.editBrand,

      submitted: false,

      keep_open: false,

      url: null,

      file: null,

      form: this.parent.form,

      formData: this.parent.formData,

      formGroups: [],
    };

  },

  methods: {

    ...mapActions("brand", ["post_brand", "update_brand"]),

    modalClose(done) {
      if (this.form.isDataEmpty()) {
        this.$confirm(this.$t("close_transaction_alert"), {
          confirmButtonText: "OK",

          cancelButtonText: this.$t("cancel"),

          type: "error",
        })
          .then((resp) => {
            this.form.reset();
            this.url = null;
            this.modal.isVisible = false;

            $(".error").remove();

            $("#class, #status, #sequence").removeAttr("style");
          })

          .catch((_) => {});
      } else {
 
        this.form.reset();
        this.url = null;
        this.file = "";

        this.keep_open = false;

        this.modal.isVisible = false;

      }

      this.keep_open = false;
    },

    onSubmit() {
        let vm = this;
        this.modal.button.loading = true;
        let params = {
            id: this.parent.form.id,
            api_token: this.$user.api_token,
            locale: this.$i18n.locale,
            action: this.parent.form.action,
            name: this.parent.form.brand_name,
            website: this.parent.form.website,
            isDefault: this.parent.form.isDefault,
            org_id: this.$org_id,
            file: this.file,
            categories: this.parent.form.brandCategories,
        };

        this.post_brand(params)
            .then((resp) => {
            $(".error").remove();

            $("#org_name").removeAttr("style");
                if (this.responseStatus) {
                    let brand = vm.responseStatus;
                    brand.brand_index = vm.parent.editingIndex;
                    vm.update_brand(brand);
                    let message = {
                        create: `${this.parent.form.brand_name}` + this.$t("created_message"),
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
                    
                    this.parent.makeToast(
                        "success",
                        message.title.update,
                        message.update
                    );

                    this.parent.successfullySavedBrand()

                    this.modal.button.loading = false;

                    this.url = null;

                    this.modal.isVisible = false;
                    
                    if(this.parent.form.action != "") {
                    
                    this.parent.$bvModal.show(this.parent.modalId);

                    return;
                    }

                    this.parent.form.reset();

                    this.parent.form.language = this.$i18n.locale;
                    

                    
                }
            })
            .catch((error) => {

            $(".field-error").remove();

            $("#org_name").removeAttr("style");

            let errors = error.response.data.errors;

            for (let field of Object.keys(errors)) {
                let el = $(`#${field}`);

                el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 1px 1px 3px 1px #ff3b3b !important;"
                );

                el.after(
                $(
                    '<span style="color: #ff3b3b;" class="field-error">' +
                    errors[field][0] +
                    "</span>"
                )
                );
            }

            this.modal.button.loading = false;
        });
    },

    chooseFiles() {
        document.getElementById("fileUpload").click()
    },

    onGetFile(event) {
        if(event.target.files[0] == undefined) return;
        this.file = event.target.files[0];
        
        this.url = URL.createObjectURL(this.file);
        },
    },

    computed: {
        ...mapGetters("brand", {
        responseStatus: "get_response_status",
        }),
    },
};
</script>

<style>
    .create-organization .el-dialog__header{
        display: none;
    }

    .el-select {
        display: block;
    }
</style>
