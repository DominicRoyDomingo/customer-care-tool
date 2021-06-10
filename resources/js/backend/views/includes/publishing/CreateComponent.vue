<template>
  <div class="create">
    <v-app id="create__container">
      <b-modal
        id="create-publishing"
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
            <br />
            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("publishing_name") }}
                </label>

                <el-input
                  :placeholder="$t('publishing_name')"
                  id="name"
                  name="name"
                  v-model="form.name"
                  clearable
                ></el-input>
              </v-container>

              <v-container>
                <label for="author_idea">
                  {{ $t("publishing_item") }}
                </label>

                <el-select
                  filterable
                  v-model="form.item"
                  clearable
                  :placeholder="$t('label.select_item')"
                  id="item"
                  style="display: block"
                >
                  <el-option
                    :value="item.id"
                    v-for="(item, index) in $this.items"
                    :key="index"
                    :label="item.item_name"
                  ></el-option>
                </el-select>
              </v-container>

              <v-container>
                <label for="author">
                  {{ $t("item_platform") }}
                </label>

                <v-select
                  name="platform"
                  label="platform_name"
                  v-model="form.platform"
                  :options="$this.platforms"
                  id="platform"
                >
                  <template #list-header>
                    <li style="margin-left:20px;" class="mb-2">
                      <b-link href="#" @click="openPlatformTypeModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("label.new_platform") }}
                      </b-link>
                    </li>
                  </template>
                </v-select>
              </v-container>

              <v-container>
                <label for="author_idea">
                  {{ $t("content_author_idea") }}
                </label>

                <el-select
                  filterable
                  v-model="form.author"
                  clearable
                  :placeholder="$t('label.select_author')"
                  id="author"
                  style="display: block"
                >
                  <el-option
                    :value="user.id"
                    v-for="(user, index) in $this.users"
                    :key="index"
                    :label="user.full_name"
                  ></el-option>
                </el-select>
              </v-container>

              <v-container>
                <label for="textarea">
                  {{ $t("label.notes") }}
                </label>
                <el-input
                  type="textarea"
                  :rows="4"
                  :placeholder="$t('label.notes')"
                  v-model="form.notes"
                >
                </el-input>
              </v-container>
            </b-form-group>

            <b-form-group>
              <v-container>
                <span class="float-left">
                  <div class="form-check form-check-inline mr-1">
                    <input
                      class="form-check-input"
                      v-model="keep_open"
                      type="checkbox"
                      id="inline-checkbox3"
                    />

                    <label class="form-check-label" for="inline-checkbox3">
                      <small>{{ $t("label.keep_open") }}</small>
                    </label>
                  </div>
                </span>

                <span class="float-right">
                  <el-button
                    type="danger"
                    size="small"
                    @click="modalClose"
                    plain
                  >
                    {{ $t(modal.button.cancel) }}
                  </el-button>

                  <el-button
                    size="small"
                    native-type="submit"
                    type="success"
                    :loading="modal.button.loading"
                    style="color: #fff !important"
                  >
                    {{ $t(modal.button.save) }}
                  </el-button>
                </span>
              </v-container>
            </b-form-group>
          </form>
        </div>
      </b-modal>
    </v-app>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createPublishing } from "./../../../api/publishing.js";

import publishingMixins from "./mixins/publishingMixins.js";

import _ from "lodash";

export default {
  props: ["$this"],

  mixins: [publishingMixins],

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

      let platform = "";

      if (this.form.platform.platform_id != undefined)
        platform = this.form.platform.platform_id;

      formData.append("name", this.form.name);
      formData.append("item", this.form.item);
      formData.append("status", this.$this.statuses.id);
      formData.append("platform", platform);
      formData.append("author", this.form.author);
      formData.append("publisher", this.$userId);
      formData.append("notes", this.form.notes);
      createPublishing(formData)
        .then((resp) => {
          this.errors();

          this.$this.makeToast(
            resp.data.type,
            this.$t("publishing_created"),
            resp.data.message + " " + this.$t("successfull_publishing_entry")
          );

          this.form.reset();

          if (this.$this.search != "")
            this.$this.loadPublishing(this.$this.page, this.$this.search);
          this.$this.params != null
            ? this.$this.loadPublishing(this.$this.page, "", this.$this.params)
            : this.$this.loadPublishing(this.$this.page, "");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.$this.$bvModal.show("create-publishing");
          } else {
            this.$this.$bvModal.hide("create-publishing");

            this.keep_open == false;
          }
        })
        .catch((error) => {
          this.errors();

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);

            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error">' +
                  errors[field][0] +
                  "</span>"
              )
            );
          }

          this.modal.button.loading = false;
        });
    },
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
