<template>
  <div class="create">
    <b-modal id="create-item" hide-footer size="md" :title="$t(modal.name)">
      <div class="p-2 margin-top">
        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >
          <b-form-group>
            <el-row :gutter="20">
              <el-col :span="6">
                <label for="name">
                  {{ $t("item_name") }}
                </label>
              </el-col>
            </el-row>

            <el-input
              :placeholder="$t('item_name')"
              id="item_name"
              name="item_name"
              v-model="form.item_name"
              clearable
            ></el-input>

            <label for="name">
              {{ $t("item_type") }}
            </label>

            <el-row>
              <el-col :span="24">
                <v-select
                  name="category"
                  label="type_name"
                  v-model="form.item_type"
                  :options="$this.itemTypes"
                  id="item_type"
                >
                  <template #list-header>
                    <li style="margin-left:20px;" class="mb-2">
                      <b-link href="#" @click="openItemTypeModal">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("label.new_itemType") }}
                      </b-link>
                    </li>
                  </template>
                </v-select>
              </el-col>
            </el-row>

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

            <el-row>
              <el-col :span="24">
                <b-form-textarea
                  id="textarea"
                  v-model="form.notes"
                  :placeholder="$t('label.notes')"
                  rows="4"
                  max-rows="6"
                ></b-form-textarea>
              </el-col>
            </el-row>
          </b-form-group>

          <b-form-group>
            <!-- <span class="float-left">
              <div class="form-check form-check-inline mr-1">
                <input
                  class="form-check-input"
                  v-model="keep_open"
                  type="checkbox"
                />

                <label class="form-check-label" for="inline-checkbox3"
                  ><small>Keep the form open?</small></label
                >
              </div>
            </span> -->

            <span class="float-right">
              <el-button size="small" @click="modalClose">{{
                $t(modal.button.cancel)
              }}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="primary"
                :loading="modal.button.loading"
                >{{ $t(modal.button.save) }}</el-button
              >
            </span>
          </b-form-group>
        </form>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { createItem } from "./../../../api/content_item.js";

import itemMixins from "./mixins/itemMixins.js";

export default {
  props: ["$this"],

  mixins: [itemMixins],

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
      if (this.form.name == "") {
        this.$alert(this.$t("fill_up_platform_name"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      let itemType = "";

      if (this.form.item_type.item_type != undefined)
        itemType = this.form.item_type.item_type;

      let formData = new FormData();
      formData.append("item_name", this.form.item_name);
      formData.append("content", this.$this.content_id);
      formData.append("status", this.$this.status.id);
      formData.append("author_idea", this.form.author_idea);
      formData.append("author_task", this.$userId);
      formData.append("item_type", itemType);
      formData.append("notes", this.form.notes);

      createItem(formData)
        .then((resp) => {
          $(".error").remove();

          $("#item_name, #author_idea, #item_type").removeAttr(
            "style"
          );

          this.$this.makeToast(
            resp.data.type,
            this.$t("item_created"),
            resp.data.message + " " + this.$t("successfull_item_entry")
          );

          this.form.reset();

          this.$emit("loadTable");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.$this.$bvModal.show("create-item");
          } else {
            this.$this.$bvModal.hide("create-item");

            this.keep_open == false;
          }
        })
        .catch((error) => {
          $(".error").remove();

          $("#item_name, #author_idea, #item_type").removeAttr(
            "style"
          );

          let errors = error.response.data.errors;
        // console.log(errors)
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
