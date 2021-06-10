<template>
  <div class="edit">
    <el-dialog
      :visible.sync="modal.isVisible"
      width="35%"
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
            <div class="col-lg-3 col-md-3 float-right">
              <div class="row">
                <select
                  class="form-control"
                  @change.prevent="selectLang($event)"
                  style="margin-bottom:-35px"
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
            <label for="name">
              {{ $t("platform_type_name") }}

              <span class="text-danger">*</span>
            </label>

            <el-input
              :placeholder="$t('platform_type_name')"
              id="name"
              name="name"
              v-model="form.name"
              clearable
            ></el-input>

            <code
              v-if="form.errors.has('name')"
              v-text="form.errors.get('name')"
            ></code>
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
  </div>
</template>

<script>
import {
  updatePlatform,
  getPlatformName,
} from "./../../../api/platform_type.js";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.edit,

      submitted: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" }, 
        
        { id:'ph-fil', value: 'Filipino' },

        { id:'ph-bis', value: 'Visayan' }
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

            done();
          })

          .catch((_) => {});
      } else {
        this.form.reset();

        this.file = "";

        this.modal.isVisible = false;
      }
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

      updatePlatform(formData).then((resp) => {
        if (resp.data.action == "duplicate") {
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
              this.$t("info_1_alert") +
              resp.data.message +
              this.$t("info_2_alert"),

            type: resp.data.type,
          });

          this.form.reset();

          this.modal.isVisible = false;

          this.$this.loadPlatform();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        }
      });
    },

    selectLang(event) {
      this.form.language = event.target.value;

      getPlatformName(this.form.id, event.target.value).then((resp) => {
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
