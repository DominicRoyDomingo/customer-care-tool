<template>
  <div class="create">
    <b-modal
      :visible.sync="modal.isVisible"
      width="40%"
      hide-footer
      hide-header
      :before-close="modalClose"
      no-close-on-backdrop
    >
    <v-app id="create__container">
      <form
      @submit.prevent="onSubmit"
      method="post"
      enctype="multipart/form-data"
      @keydown="form.errors.clear($event.target.name)"
    >
    <v-card class="border-0">
      <v-card-title class="title blue-grey lighten-4 text-capitalize">
        <span>{{$t(modal.name)}}</span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="modalClose">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text class="modal__content" style="">
        <v-container>
          <v-container>
            <div class="p-2 margin-top">
              
                <b-form-group>
                   <v-text-field
                    :label="$t('publishing_type_name').toUpperCase()"
                    id="type_name"
                    type="type_name"
                    name="title"
                    v-model="form.type_name"
                    hide-details="auto"
                  />
                </b-form-group>
               
            </div>
          </v-container>
        </v-container>
      </v-card-text>
       <v-divider style="margin-bottom: 0"></v-divider>
       <v-card-actions class="modal__footer blue-grey lighten-5 justify-content-between px-5">

            <div>
              <a href="/admin/software-publishing/item-types/manage">{{$t('publishing_item_link')}}</a>
            </div>
            <div class='d-flex'>
              <v-btn color="error" text tile @click="modalClose">{{
                $t(modal.button.cancel)
              }}</v-btn>

              <v-btn
                 color="success" tile type='submit'>
                <div v-if="modal.button.loading" class="text-center">
                  <v-progress-circular
                    size="20"
                    width="1"
                    color="white"
                    indeterminate
                  >
                  </v-progress-circular>
                </div>
                {{ $t(modal.button.save) }}
              
              </v-btn>
            </div>

      </v-card-actions>
      </v-card>
    </form>
    </v-app>
    </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createItemType } from "./../../../api/item_type.js";

export default {
  props: ["$this", 'itemTypes'],

  data: function () {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
    };
  },

  mounted() {},  
  methods: {
    modalClose(done) {
      if (this.form.isDataEmpty()) {
        this.$confirm(this.$t("close_transaction_alert"), {
          confirmButtonText: "OK",

          cancelButtonText: this.$t("cancel"),

          type: "error",
        })
          .then((resp) => {
            this.form.reset();

            this.modal.isVisible = false;
          })

          .catch((_) => {});
      } else {
        this.form.reset();

        this.file = "";

        this.modal.isVisible = false;
        this.$emit("show-modal");
      }

      this.keep_open = false;
    },

    onSubmit() {
      if (this.form.type_name == "") {
        this.$alert(this.$t("fill_up_publishing_type_name"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      const data = {
        id: this.form.id,

        type_name: this.form.type_name,

        language: this.form.language,
      };

      let formData = new FormData();

      formData.append("data", JSON.stringify(data));

      createItemType(formData).then((resp) => {
        if (resp.action == "duplicate") {
          this.$this.makeToast(
            resp.data.type,

            resp.data.title,

            resp.data.data.name +
              " " +
              this.$t("duplicate_publishing_type_entry")
          );

          // this.$notify({

          //   title: resp.data.title,

          //   message: resp.data.data.name+ ' ' + this.$t( 'duplicate_publishing_type_entry' ),

          //   type: resp.data.type

          // });

          this.modal.button.loading = false;
        } else {
          this.$this.makeToast(
            resp.data.type,

            "ITEM TYPE CREATED",

            resp.data.message +
              " " +
              this.$t("successfull_publishing_type_entry")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: resp.data.message + ' ' + this.$t( 'successfull_publishing_type_entry' ),

          //   type: resp.data.type

          // });

          this.form.reset();

          this.$this.loadItemType();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.modal.isVisible = true;
          } else {
            this.modal.isVisible = false;

            this.keep_open == false;
          }
        }

        this.$emit("show-modal");
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
