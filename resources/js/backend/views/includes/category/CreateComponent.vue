<template>
  <div class="create">
    <b-modal
      id="category-modal"
      hide-footer
      hide-header
      size="md"
      :title="$t(modal.name)"
    >
      <v-app id="brand__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ this.$t(modal.name) }}
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('category-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container fluid>
              <form
                @submit.prevent="onSubmit"
                method="post"
                enctype="multipart/form-data"
                @keydown="form.errors.clear($event.target.name)"
              >
                <b-form-group>
                  <label for="name">
                    {{ $t("category_name") }}
                  </label>

                  <el-input
                    :placeholder="$t('category_name')"
                    id="name"
                    name="name"
                    v-model="form.name"
                    clearable
                  ></el-input>
                </b-form-group>

                <!-- <b-form-group>
                  <span class="float-left">
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
                  </span>
                </b-form-group> -->
              </form>
            </v-container>
          </v-card-text>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn color="error" text tile @click="modalClose">
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSubmit">
              <div v-if="modal.button.loading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div>
                  <v-icon left>mdi-tag</v-icon>
                  {{ $t(modal.button.save) }}
                </div>
              </div>
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-app>
    </b-modal>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createCategory } from "./../../../api/category.js";

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
      this.$confirm(this.$t("close_transaction_alert"), {
        confirmButtonText: "OK",

        cancelButtonText: this.$t("cancel"),

        type: "error",
      })
        .then((resp) => {
          this.$this.$bvModal.hide("category-modal");
          this.$this.$bvModal.show("category-brand-modal");
          this.$this.modal.category != undefined
            ? this.$this.$bvModal.show(this.$this.modalId)
            : this.form.reset();
        })

        .catch((_) => {});

      this.keep_open = false;
    },

    onSubmit() {
      if (this.form.name == "") {
        this.$alert(this.$t("fill_up_category_name"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      let formData = new FormData();
      let params = {
        api_token: this.$this.$user.api_token,
      };
      formData.append("name", this.form.name);

      createCategory(formData).then((resp) => {
        // console.log(resp.data)
        this.$this.makeToast(
          resp.data.type,

          this.$t("category_created"),

          resp.data.message + " " + this.$t("successfull_category_entry")
        );

        if (this.$listeners.loadTable) this.$emit("loadTable", params);

        this.form.language = this.$i18n.locale;

        this.modal.button.loading = false;

        if (this.keep_open == true) {
          this.modal.isVisible = true;
        } else {
          this.$this.$bvModal.hide("category-modal");

          if (this.$this.modal.category != undefined)
            this.$this.$bvModal.show(this.$this.modalId);

          this.keep_open == false;
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
