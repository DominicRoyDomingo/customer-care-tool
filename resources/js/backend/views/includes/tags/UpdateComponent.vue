<template>
  <!-- <div class="edit">
      <b-modal
        id="edit__container"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t(modal.name)"
        :visible.sync="modal.isVisible"
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
                  {{$t('button.update')}} {{ $this.default }}
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
                  <v-row>
                    <v-col cols="9">
                    </v-col>
                    <v-col cols="3">
                      <div>
                        <select
                        class="form-control"
                        @change.prevent="selectLang($event)"
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
                    </v-col>
                  </v-row>
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
                        {{ $t(modal.button.update) }}
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </form>
          </v-card>
        </v-app>
      </b-modal>
  </div> -->
  <div class="edit">
    <v-app id="edit__container">
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
                {{ $t("tags_name") }}

                <span class="text-danger">*</span>
              </label>

              <el-input
                :placeholder="$t('tags_name')"
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
              <v-container>
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
              </v-container>
            </b-form-group>
          </form>
        </div>
      </el-dialog>
    </v-app>
  </div>
</template>

<script>
import { updateTags, getTagsName } from "./../../../api/tags.js";

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

      updateTags(formData).then((resp) => {
        if (resp.data.action == "duplicate") {
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

            this.$t("tags_updated"),

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

          this.$this.loadTags();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        }
      });
    },

    selectLang(event) {
      this.form.language = event.target.value;

      getTagsName(this.form.id, event.target.value).then((resp) => {
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
