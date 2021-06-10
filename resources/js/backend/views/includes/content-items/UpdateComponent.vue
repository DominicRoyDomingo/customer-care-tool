<template>
  <div class="create">
    <v-app id="create__container">
      <b-modal id="edit-item" hide-footer size="md" :title="$t(modal.name)">
        <div class="p-2 margin-top">
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <v-container>
              <b-form-group>
                <div class="col-lg-3 col-md-3 float-right mb-4">
                  <div class="row">
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
                  </div>
                </div>
              </b-form-group>
            </v-container>

            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("item_name") }}
                </label>

                <el-input
                  :placeholder="$t('item_name')"
                  id="item_name"
                  name="item_name"
                  v-model="form.item_name"
                  clearable
                ></el-input>
              </v-container>

              <v-container>
                <label for="name">
                  {{ $t("item_type") }}
                </label>

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
              </v-container>

              <v-container>
                <label for="author_idea">
                  {{ $t("content_author_idea") }}
                </label>

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
  updateItem,
  getTranslatedItemName,
} from "./../../../api/content_item.js";

import itemMixins from "./mixins/itemMixins.js";

export default {
  props: ["$this"],

  mixins: [itemMixins],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
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
      
      if (this.form.item_type != undefined)
        itemType = this.form.item_type.item_type;

      let formData = new FormData();
      formData.append("id", this.form.id);
      formData.append("item_name", this.form.item_name);
      formData.append("author_idea", this.form.author_idea);
      formData.append("author_task", this.$userId);
      formData.append("item_type", itemType);
      formData.append("language", this.form.language);

      updateItem(formData)
        .then((resp) => {
          $(".error").remove();

          $("#item_name, #author_idea, #item_type").removeAttr("style");

          // this.$notify({

          //   title: resp.data.title,

          //   message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),

          //   type: resp.data.type

          // });

          this.$this.makeToast(
            resp.data.type,
            this.$t("item_updated"),
            this.$t("info_1_alert") +
              resp.data.message +
              this.$t("info_2_alert")
          );
          // console.log(this.form.item_name);
          this.form.reset();

          this.$this.$bvModal.hide("edit-item");

          this.$this.loadItem(this.$this.search, this.$this.params);

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        })
        .catch((error) => {
          $(".error").remove();

          $("#item_name, #author_idea, #item_type").removeAttr("style");

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

      getTranslatedItemName(this.form.id, event.target.value).then((resp) => {
        if (resp) {
          this.form.item_name = resp;
        } else {
          this.form.item_name = "";
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
