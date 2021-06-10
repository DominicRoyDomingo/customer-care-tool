<template>
  <div class="create">
    <el-dialog
      :visible.sync="modal.isVisible"
      width="35%"
      :before-close="modalClose"
      @close="
        $this.modal.add.from == 'item'
          ? $this.successfullySavedPlatformType()
          : undefined
      "
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
            <label for="name">
              {{ $t("platform_type_name") }}
            </label>

            <el-input
              :placeholder="$t('platform_type_name')"
              id="name"
              name="name"
              v-model="form.name"
              clearable
            ></el-input>
          </b-form-group>

          <b-form-group>
            <v-container fluid>
              <span class="float-left">
                <span v-if="$this.modal.add.from == 'item'">
                  <v-icon small color="primary">mdi-chevron-left</v-icon>
                  <a href="/admin/platform-type" class="caption">
                    Go to Platform Type Page
                  </a>
                </span>
                <div v-else class="form-check form-check-inline mr-1">
                  <!-- <input
                    class="form-check-input"
                    v-model="keep_open"
                    type="checkbox"
                  />
                  <label class="form-check-label" for="inline-checkbox3">
                    <small>Keep the form open?</small>
                  </label> -->
                </div>
              </span>

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
            </v-container>
          </b-form-group>
        </form>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { createPlatform } from "./../../../api/platform_type.js";

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
      if (this.form.name == "") {
        this.$alert(this.$t("fill_up_platform_type_name"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      const data = {
        id: this.form.id,

        name: this.form.name,

        language: this.form.language,
      };

      let formData = new FormData();

      formData.append("data", JSON.stringify(data));

      createPlatform(formData).then((resp) => {
        if (resp.action == "duplicate") {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.data.name +
              " " +
              this.$t("duplicate_platform_type_entry"),

            type: resp.data.type,
          });

          this.modal.button.loading = false;
        } else {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.message +
              " " +
              this.$t("successfull_platform_type_entry"),

            type: resp.data.type,
          });

          this.form.reset();

          this.$this.loadPlatform();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.modal.isVisible = true;
          } else {
            this.modal.isVisible = false;

            this.keep_open == false;
          }
        }
      });

      this.$this.successfullySavedPlatformType();
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
