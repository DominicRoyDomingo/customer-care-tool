<template>
  <div class="edit">
    <b-modal id="edit-modal" hide-footer size="md" :title="$t(modal.name)">
      <div class="p-2 margin-top">
        <form
          @submit.prevent="onSubmit"
          method="post"
          enctype="multipart/form-data"
          @keydown="form.errors.clear($event.target.name)"
        >
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

          <b-form-group>
            <el-row :gutter="20">
              <el-col :span="6">
                <label for="name">
                  {{ $t("date_status") }}
                </label>
              </el-col>

              <el-col :span="24">
                <b-form-input
                  id="status"
                  v-model="form.status"
                  :placeholder="$t('date_status')"
                ></b-form-input>
              </el-col>

              <el-col :span="24" class="mt-3">
                <label for="name">
                  {{ $t("date_class") }}
                </label>
              </el-col>

              <el-col :span="24">
                <el-select
                  v-model="form.class"
                  clearable
                  :placeholder="$t('search_class')"
                  id="class"
                >
                  <!-- <el-option value="content">{{ $t( "content" )}}</el-option>
                  <el-option value="item">{{ $t( "item" )}}</el-option> -->
                  <el-option
                    v-for="item in option"
                    :key="item.id"
                    :label="item.label"
                    :value="item.id"
                  >
                    {{ item.label }}
                  </el-option>
                </el-select>
              </el-col>
            </el-row>
          </b-form-group>

          <b-form-group>
            <span class="float-right">
              <el-button size="small" @click="modalClose">{{
                $t(modal.button.cancel)
              }}</el-button>

              <el-button
                size="small"
                native-type="submit"
                type="success"
                :loading="modal.button.loading"
                >{{ $t(modal.button.update) }}</el-button
              >
            </span>
          </b-form-group>
        </form>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { updateDateStatus, getDateStatusName } from "./../../../api/date.js";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      submitted: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      option: [
        { id: "content", label: this.$t("content") },

        { id: "item", label: this.$t("item") },

        { id: "publishing", label: this.$t("publishing") },
      ],

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },
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

            this.$this.$bvModal.hide("edit-modal");

            this.$this.$bvModal.show(this.$this.modalId);

            $(".error").remove();

            $("#class, #status, #sequence").removeAttr("style");

            done();
          })

          .catch((_) => {});
      } else {
        this.form.reset();

        this.file = "";

        this.$this.$bvModal.hide("edit-modal");

        this.$this.$bvModal.show(this.$this.modalId);
      }
    },

    onSubmit() {
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("status", this.form.status);
      formData.append("class", this.form.class);
      formData.append("language", this.form.language);

      updateDateStatus(formData)
        .then((resp) => {
          $(".error").remove();

          $("#class, #status, #sequence").removeAttr("style");

          this.$this.makeToast(
            resp.data.type,
            "DATE STATUS UPDATED",
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

          this.$this.loadDate();

          this.$this.onEdit(this.$this.class);

          this.$this.$bvModal.hide("edit-modal");

          this.$this.$bvModal.show(this.$this.modalId);

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        })
        .catch((error) => {
          $(".field-error").remove();

          $("#class, #status, #sequence").removeAttr("style");

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            // console.log(field)
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

    selectLang(event) {
      this.form.language = event.target.value;

      getDateStatusName(this.form.id, event.target.value).then((resp) => {
        if (resp) {
          this.form.status = resp.status;
        } else {
          this.form.status = "";
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
