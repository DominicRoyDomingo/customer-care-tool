<template>
  <div class="create">
      <b-modal
      :visible.sync="modal.isVisible"
      width="35%"
      :before-close="modalClose"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
      >
        <v-app id="create__container">
          <v-card>
            <form 
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)">
            
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <span>
                  {{ $t(modal.name) }}
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
              <v-card-text class="modal__content" style="">
                <v-container>
                  <v-container>
                    <v-row>
                      <v-col class="modal__input-container">
                        <b-form-group>
                          <label for="name">
                            {{ $t("brands_name") }}
                          </label>
                          <v-select
                            id="brands_name"
                            name="brands_name"
                            label="name"
                            v-model="form.brand"
                            :options="$this.brands"
                            :reduce="(brand) => brand.id"
                          >
                            <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="$this.openBrandModal">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("label.new_brand") }}
                                </b-link>
                              </li>
                            </template>
                          </v-select>
                        </b-form-group>
                        <b-form-group>
                          <label for="name">
                            {{ $t("platform_type_name") }}
                          </label>
                          <v-select
                            id="platform_type"
                            name="platform_type"
                            label="name"
                            v-model="form.platform_type"
                            :options="$this.platform_type"
                            :reduce="(platform_type) => platform_type.id"
                          >
                            <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="$this.openPlatformTypeModal">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("label.new") + " " + $t("label.platform_type") }}
                                </b-link>
                              </li>
                            </template>
                          </v-select>
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

  </div>
  
  <!-- <div class="create">
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
            <label for="name">
              {{ $t("brands_name") }}
            </label>
            <v-select
              id="brands_name"
              name="brands_name"
              label="name"
              v-model="form.brand"
              :options="$this.brands"
              :reduce="(brand) => brand.id"
            >
              <template #list-header>
                <li style="margin-left:20px;" class="mb-2">
                  <b-link href="#" @click="$this.openBrandModal">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    {{ $t("label.new_brand") }}
                  </b-link>
                </li>
              </template>
            </v-select>
          </b-form-group>

          <b-form-group>
            <br />
            <label for="name">
              {{ $t("platform_type_name") }}
            </label>
            <v-select
              id="platform_type"
              name="platform_type"
              label="name"
              v-model="form.platform_type"
              :options="$this.platform_type"
              :reduce="(platform_type) => platform_type.id"
            >
              <template #list-header>
                <li style="margin-left:20px;" class="mb-2">
                  <b-link href="#" @click="$this.openPlatformTypeModal">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    {{ $t("label.new") + " " + $t("label.platform_type") }}
                  </b-link>
                </li>
              </template>
            </v-select>
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
                    {{ $t(modal.button.save) }}
                  </span>
                </v-btn>
              </span>
            </v-container>
          </b-form-group>
        </form>
      </div>
    </el-dialog>
  </div> -->
</template>

<script>
import { createPlatform } from "./../../../api/platform_item.js";

export default {
  props: ["$this"],

  data: function() {
    return {
      modal: this.$this.modal.addItem,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      btnloading: false,
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
      if (this.form.brand == "") {
        this.$alert(this.$t("fill_up_platform_item_brand"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      if (this.form.platform_type == "") {
        this.$alert(this.$t("fill_up_platform_item_brand"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      const data = {
        id: this.form.id,

        brand: this.form.brand,

        platform_type: this.form.platform_type,

        language: this.form.language,
      };

      let formData = new FormData();

      formData.append("data", JSON.stringify(data));

      createPlatform(formData).then((resp) => {
        if (resp.data.title == "Duplicate") {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.message +
              " " +
              this.$t("duplicate_publishing_item_entry"),

            type: resp.data.type,
          });

          this.modal.button.loading = false;
        } else {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.message +
              " " +
              this.$t("successfull_platform_item_entry"),

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
