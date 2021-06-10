<template>
<!-- <div class="create">
      <b-modal
        id="tags-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t(modal.name)"
      >
        <v-app id="create__container">
          <v-card>
            <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{ $t(modal.name) }}
                  <small
                        v-if="$this.convertion == true"
                        style="color:red"
                      >
                        (No {{ $this.language }} translation yet)</small
                      >
                </span>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="modalClose"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text class="modal__content">
                <v-container>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="modal__input-container">
                      <b-form-group style="margin-bottom:1%">
                        <v-text-field
                          v-model="form.name"
                          :label="$t('tags_name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          style="position:relative; width:100%;margin: 10px"
                        >
                        </v-text-field>
                        <small style="color:red; margin-top:-15px;position:absolute" v-if="tags_required"> {{ $t("label.request_type") }} {{ $t('is_required')}}</small>
                      </b-form-group>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-container>
              </v-card-text>
              <v-divider style="margin-bottom: 0"></v-divider>
                <v-card-actions class="modal__footer blue-grey lighten-5">
                  <v-spacer></v-spacer>
                  <v-btn
                    color="error"
                    text
                    tile
                    @click="modalClose"
                  >
                    {{ $t("cancel") }}
                  </v-btn>
                  <v-btn
                    color="success"
                    tile
                    @click="onSubmit"
                  >
                    <div v-if="this.modal.button.loading" class="text-center">
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
                        {{ $t(modal.button.save) }}
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </form>
          </v-card>
        </v-app>
      </b-modal>
  </div> -->
<div class="create">
    <b-modal id="tags-modal" hide-header hide-footer size="md">
      <v-app id="create__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{ $t(modal.name) }}
            </div>
            <v-spacer></v-spacer>
            <v-btn icon color="error lighten-2" @click="modalClose">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container fluid>
              <b-form-group>
                <label for="name">
                  {{ $t("tags_name") }}
                </label>

                <el-input
                  :placeholder="$t('tags_name')"
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
                    />

                    <label class="form-check-label" for="inline-checkbox3"
                      ><small>Keep the form open?</small></label
                    >
                  </div>
                </span>
              </b-form-group> -->
            </v-container>
          </v-card-text>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn color="error" text tile @click="modalClose">
              {{ $t(modal.button.cancel) }}
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
                  <v-icon left>mdi-tag-plus</v-icon>
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

import { createTags } from "./../../../api/tags.js";

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

      tags_required: false

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
            this.$this.$bvModal.hide("tags-modal");

            this.$this.modal.tags != undefined
              ? this.$this.$bvModal.show(this.$this.modalId)
              : this.form.reset();
          })

          .catch((_) => {});
      } else {
        this.file = "";

        this.$this.$bvModal.hide("tags-modal");

        this.$this.modal.tags != undefined
          ? this.$this.$bvModal.show(this.$this.modalId)
          : this.form.reset();
      }

      this.keep_open = false;
    },

    onSubmit() {
      if (this.form.name == "") {
        this.tags_required = true;
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

      createTags(formData).then((resp) => {
        if (resp.action == "duplicate") {
          this.$this.makeToast(
            resp.data.type,

            resp.data.title,

            resp.data.data.name + " " + this.$t("duplicate_tags_entry")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: resp.data.data.name + ' ' + this.$t( 'duplicate_tags_entry' ),

          //   type: resp.data.type

          // });

          this.modal.button.loading = false;
        } else {
          this.$this.makeToast(
            resp.data.type,

            this.$t("tags_created"),

            resp.data.message + " " + this.$t("successfull_tags_entry")
          );
          // this.$notify({

          //   title: resp.data.title,

          //   message: resp.data.message + ' ' + this.$t( 'successfull_tags_entry' ),

          //   type: resp.data.type

          // });

          this.$emit("loadTable");

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;

          if (this.keep_open == true) {
            this.modal.isVisible = true;
          } else {
            this.$this.$bvModal.hide("tags-modal");

            this.$this.modal.tags != undefined
              ? this.$this.$bvModal.show(this.$this.modalId)
              : this.form.reset();

            this.keep_open == false;
          }
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
