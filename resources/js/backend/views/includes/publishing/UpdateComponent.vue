<template>
  <div class="edit">
    <v-app id="edit__container">
      <b-modal
        ref="modalId"
        id="edit-publishing"
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
            </b-form-group>

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
                  {{ $t("publishing_author_idea") }}
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
            </b-form-group>

            <b-form-group>
              <v-container>
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
                    {{ $t(modal.button.update) }}
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
import {
  updatePublishing,
  getPublishingName,
} from "./../../../api/publishing.js";

import publishingMixins from "./mixins/publishingMixins.js";

export default {
  props: ["$this"],

  mixins: [publishingMixins],

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

      let platform = "";

      if (this.form.platform != undefined)
        platform = this.form.platform.platform_id;

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("name", this.form.name);
      formData.append("item", this.form.item);
      formData.append("author", this.form.author);
      formData.append("publisher", this.$userId);
      formData.append("platform", platform);
      formData.append("language", this.form.language);

      updatePublishing(formData)
        .then((resp) => {
          this.errors();

          this.$this.makeToast(
            resp.data.type,
            this.$t("publishing_updated"),
            this.$t("info_1_alert") +
              resp.data.message +
              this.$t("info_2_alert")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),

          //   type: resp.data.type

          // });
          // console.log(this.form.name)
          if (this.$this.search != "")
            this.$this.loadPublishing(this.$this.page, this.$this.search);
          this.$this.params != null
            ? this.$this.loadPublishing(this.$this.page, "", this.$this.params)
            : this.$this.loadPublishing(this.$this.page, "");

          this.form.reset();

          this.$this.$bvModal.hide("edit-publishing");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        })
        .catch((error) => {
          this.errors();

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);

            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 1px 1px 3px 1px #ff3b3b !important;"
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

    selectLang(event) {
      this.form.language = event.target.value;

      getPublishingName(this.form.id, event.target.value).then((resp) => {
        if (resp) {
          this.form.name = resp.name;
        } else {
          this.form.name = "";
        }
      });
    },
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
