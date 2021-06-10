<template>
  <div class="create">
    <v-app id="create__container">
      <el-dialog
        :visible.sync="modal.isVisible"
        width="35%"
        :before-close="modalClose"
      >
        <template v-slot:title>
          <div class="text--secondary text-subtitle-1">
            {{ $t(modal.name + form.class) }}
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
                <label for="name">
                  {{ $t("date_status") }}
                </label>
                <el-input
                  :placeholder="$t('date_status')"
                  id="status"
                  name="status"
                  v-model="form.status"
                  clearable
                ></el-input>
              </v-container>
            </b-form-group>

            <b-form-group>
              <v-container>
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
      </el-dialog>
    </v-app>
  </div>
</template>

<script>
import { createDateStatus } from "./../../../api/date.js";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      option: [
        { id: "content", label: this.$t("content") },

        { id: "item", label: this.$t("item") },

        { id: "publishing", label: this.$t("publishing") },
      ],
    };
  },

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

            $(".error").remove();

            $("#class, #status, #sequence").removeAttr("style");
          })

          .catch((_) => {});
      } else {
        this.form.reset();

        this.file = "";

        this.keep_open = false;

        this.modal.isVisible = false;
      }

      this.keep_open = false;
    },

    onSubmit() {
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("status", this.form.status);
      formData.append("class", this.form.class);

      createDateStatus(formData)
        .then((resp) => {
          $(".error").remove();

          $("#class, #status, #sequence").removeAttr("style");

          this.$this.makeToast(
            resp.data.type,
            "DATE STATUS CREATED",
            resp.data.message + " " + this.$t("successfull_date_entry")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: resp.data.message + ' ' + this.$t( 'successfull_date_entry' ),

          //   type: resp.data.type

          // });

          this.form.reset();

          this.$this.loadDate();

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
          $(".field-error").remove();

          $("#class, #status, #sequence").removeAttr("style");

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
