<template>
  <div class="create">
    <v-app id="create__container">
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
                <label for="name" class="text--secondary">
                  {{ $t("content_name") }}
                </label>

                <el-input
                  :placeholder="$t('content_name')"
                  id="content"
                  name="name"
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

              <v-container>
                <label for="notes">
                  {{ $t("label.notes") }}
                </label>
                <el-row>
                  <el-col :span="24">
                    <el-input
                      type="textarea"
                      :rows="4"
                      :placeholder="$t('label.notes')"
                      id="notes"
                      v-model="form.notes"
                    >
                    </el-input>
                  </el-col>
                </el-row>
              </v-container>
            </b-form-group>

            <b-form-group>
              <!-- <span class="float-left">
                <div class="form-check form-check-inline mr-1">
                  <input
                    class="form-check-input"
                    v-model="keep_open"
                    type="checkbox"
                    id="inline-checkbox3"
                  />

                  <label class="form-check-label" for="inline-checkbox3"
                    ><small>Keep the form open?</small></label
                  >
                </div>
              </span> -->
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
                    {{ $t(modal.button.save) }}
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
// import Api from "./../../../shared/api.js";

import { createContent } from "./../../../api/content.js";

import contentMixins from "./mixins/contentMixins.js";

import _ from "lodash";

export default {
  props: ["$this"],

  mixins: [contentMixins],

  data: function() {
    return {
      modal: this.$this.modal.add,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,
    };
  },

  methods: {
    onSubmit() {
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("content", this.form.name);
      formData.append("status", this.$this.statuses.id);
      formData.append("author_idea", this.form.author_idea);
      formData.append("author_task", this.$userId);
      formData.append("notes", this.form.notes);

      createContent(formData)
        .then((resp) => {
          this.errors();

          this.$this.makeToast(
            resp.data.type,
            this.$t("content_created"),
            resp.data.message + " " + this.$t("successfull_content_entry")
          );

          this.form.reset();

          if (this.$this.search != "")
            this.$this.loadContent(this.$this.page, this.$this.search);
          this.$this.params != null
            ? this.$this.loadContent(this.$this.page, "", this.$this.params)
            : this.$this.loadContent(this.$this.page, "");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.modal.isVisible = true;
          } else {
            this.modal.isVisible = false;

            this.keep_open == false;
          }
        })
        .catch((error) => {
          this.errors();

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);

            if (field == "clipart") {
              el.append(
                $("<span color='#ff3b3b'>" + errors[field][0] + "</span>")
              );
            } else {
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );

              el.after(
                $(
                  '<span style="color: #ff3b3b;">' +
                    errors[field][0] +
                    "</span>"
                )
              );
            }
          }

          this.modal.button.loading = false;
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

<style scoped>
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

  line-height: 150px;

  text-align: center;
}

.avatar {
  width: 178px;

  height: 178px;

  display: block;
}

</style>
