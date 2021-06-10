<template>
  <div class="edit">
    <v-app id="edit__container">
      <el-dialog
        :visible.sync="modal.isVisible"
        width="50%"
        :before-close="modalClose"
      >
        <template v-slot:title>
          <div class="text--secondary text-subtitle-1">
            {{ $t(modal.name) }}
          </div>
          <v-divider></v-divider>
        </template>
        <div class="p-2 margin-top">
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <b-form-group>
              <v-container>
                <div class="col-lg-3 col-md-3 float-right">
                  <div class="row">
                    <v-container>
                      <select
                        class="form-control"
                        @change.prevent="selectLang($event)"
                        style="margin-bottom:-35px;"
                      >
                        <option
                          :value="language.id"
                          :selected="language.id === form.language"
                          v-for="(language, langInd) in langs"
                          :key="langInd"
                        >
                          {{ language.value }}
                        </option>
                      </select>
                    </v-container>
                  </div>
                </div>
              </v-container>
            </b-form-group>

            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("content_name") }}
                </label>

                <el-input
                  :placeholder="$t('content_name')"
                  id="content"
                  name="content"
                  v-model="form.name"
                  clearable
                ></el-input>
              </v-container>
              <v-container>
                <label for="author_idea">
                  {{ $t("content_author_idea") }}
                </label>

                <el-row>
                  <el-col :span="24">
                    <el-select
                      filterable
                      v-model="form.author_idea"
                      clearable
                      :placeholder="$t('label.select_author')"
                      id="author_idea"
                      style="display: block"
                    >
                      <el-option
                        :value="user.id"
                        v-for="(user, index) in $this.users"
                        :key="index"
                        :label="user.full_name"
                      ></el-option>
                    </el-select>
                  </el-col>
                </el-row>
              </v-container>
            </b-form-group>

            <b-form-group>
              <span class="float-right">
                <v-btn color="error" @click="modalClose" text small>
                  <span>
                    {{ $t(modal.button.cancel) }}
                  </span>
                </v-btn>
                <v-btn color="success" @click="onSubmit" tile small>
                  <div v-if="modal.button.loading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <span v-else>
                    {{ $t(modal.button.update) }}
                  </span>
                </v-btn>
              </span>
            </b-form-group>
          </form>
        </div>
      </el-dialog>
    </v-app>
  </div>
</template>

<script>
import { updateContent, getContentName } from "./../../../api/content.js";

import contentMixins from "./mixins/contentMixins.js";

export default {
  props: ["$this"],

  mixins: [contentMixins],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      form: this.$this.form,

      formData: this.$this.formData,
    };
  },

  methods: {
    onSubmit() {
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("content", this.form.name);
      formData.append("author_idea", this.form.author_idea);
      formData.append("author_task", this.$userId);
      // formData.append("clipart",  this.form.clipart, );
      formData.append("language", this.form.language);
      console.log(this.form.author_idea);
      updateContent(formData)
        .then((resp) => {
          this.errors();

          this.$this.makeToast(
            resp.data.type,
            this.$t("content_updated"),
            this.$t("info_1_alert") +
              resp.data.message +
              this.$t("info_2_alert")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),

          //   type: resp.data.type

          // });

          this.form.reset();

          this.modal.isVisible = false;

          if (this.$this.search != "")
            this.$this.loadContent(this.$this.page, this.$this.search);
          this.$this.params != null
            ? this.$this.loadContent(this.$this.page, "", this.$this.params)
            : this.$this.loadContent(this.$this.page, "");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        })
        .catch((error) => {
          this.errors();

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);
            // console.log(field)
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

    selectLang(event) {
      this.form.language = event.target.value;

      getContentName(this.form.id, event.target.value).then((resp) => {
        if (resp) {
          this.form.name = resp.name;
        } else {
          this.form.name = "";
        }
      });
    },
  },
  computed: {
    // clipartChunks(){
    //     return _.chunk(Object.values(this.$this.cliparts), 10)
    // },
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


</style>
