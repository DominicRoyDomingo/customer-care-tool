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
              <span class="white--text" style="font-size: 0.80rem !important;"> {{ url != null ? 'Change Workspace Logo' : 'Upload Workspace Logo'}}</span>
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
                :placeholder="$t('label.workspace_name')"
                id="org_name"
                name="org_name"
                v-model="form.org_name"
                clearable
              ></el-input>
          </b-form-group>

          <b-form-group>
              <b-form-checkbox
                id="checkbox-1"
                v-model="form.isDefault"
                name="checkbox-1"
                value="1"
                unchecked-value="0"
              >
                <span style="font-size: 0.80rem !important">Set as Default Workplace</span>
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
                  Add a Workspace
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
  props: ["$this", "parent"],

  data: function() {

    return {

      modal: this.$this.modal.create,

      submitted: false,

      keep_open: false,

      url: null,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
    };

  },

  methods: {
    ...mapActions("organization", ["post_organization"]),
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
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("org_name", this.form.org_name);
      formData.append("isDefault", this.form.isDefault);
      formData.append("logo", this.form.logo);
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);

      this.post_organization(formData)
        .then((resp) => {
          $(".error").remove();

          $("#org_name").removeAttr("style");
            if (this.$this.responseStatus) {
                this.$this.makeToast(
                    "success",
                    "ORGANIZATION",
                    this.form.org_name + " " + this.$t("organization_success")
                );
                
                // this.$notify({

                //   title: resp.data.title,

                //   message: resp.data.message + ' ' + this.$t( 'successfull_date_entry' ),

                //   type: resp.data.type

                // });

                this.form.reset();

                this.form.language = this.$i18n.locale;

                this.modal.button.loading = false;
                this.parent.loadWorkspaces()
                if (this.keep_open == true) {
                    this.modal.isVisible = true;
                } else {
                    this.modal.isVisible = false;

                    this.keep_open == false;
                }
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
      this.form.logo = event.target.files[0];
      console.log(this.form.logo);
      this.url = URL.createObjectURL(this.form.logo);
    },
  },
};
</script>

<style>
  .create-organization .el-dialog__header{
    display: none;
  }
</style>
