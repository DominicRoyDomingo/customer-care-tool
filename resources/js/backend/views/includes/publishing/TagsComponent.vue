<template>
  <div class="edit">
    <v-app id="edit__container">
      <b-modal
        id="publishing-tags"
        hide-footer
        size="md"
        :title="$t($this.publishing_name)"
      >
        <div class="p-2 margin-top">
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <br />
            <b-form-group>
              <v-container>
                <label for="author_idea">
                  {{ $t("publishing_tags") }}
                </label>

                <el-select
                  v-model="form.tags"
                  multiple
                  id="tags"
                  filterable
                  placeholder="Select"
                  style="display: block"
                >
                  <el-option label="" value="">
                    <div class="tags-button" @click.stop="addTags()">
                      <b-link href="#">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("add_tags_item_tooltip") }}
                      </b-link>
                    </div>
                  </el-option>
                  <el-option label="" value="">
                    <div class="tags-button" @click.stop="copyTags()">
                      <b-link href="#">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("copy_tags_from_other_publishing") }}
                      </b-link>
                    </div>
                  </el-option>
                  <el-option
                    v-for="tag in $this.tags"
                    :key="tag.id"
                    :label="tag.name"
                    :value="tag.id"
                  >
                  </el-option>
                </el-select>
              </v-container>

              <code
                v-if="form.errors.has('name')"
                v-text="form.errors.get('name')"
              ></code>
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
                    {{ $t(modal_tags.button.cancel) }}
                  </el-button>

                  <el-button
                    size="small"
                    native-type="submit"
                    type="success"
                    :loading="modal_tags.button.loading"
                    style="color: #fff !important"
                  >
                    {{ $t(modal_tags.button.update) }}
                  </el-button>
                </span>
              </v-container>
            </b-form-group>
          </form>
        </div>
      </b-modal>

      <CreateTags :$this="$this" @loadTable="$this.loadTags"></CreateTags>

      <CopyTags :$this="$this" @loadTable="$this.loadPublishing"></CopyTags>
    </v-app>
  </div>
</template>

<script>
import { attachTags, getOtherTags } from "./../../../api/publishing.js";

import publishingMixins from "./mixins/publishingMixins.js";

import CreateTags from "./../tags/CreateComponent.vue";

import CopyTags from "./CopyTagsComponent.vue";

export default {
  props: ["$this"],

  mixins: [publishingMixins],

  components: {
    CreateTags,

    CopyTags,
  },

  data: function() {
    return {
      modal_tags: this.$this.modal.tags,

      form: this.$this.form,

      formData: this.$this.formData,

      modal: {
        add: {
          name: "tags_add_new_button",

          id: "tags-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "tags_add_new_button",

            loading: false,
          },
        },
      },
    };
  },

  methods: {
    addTags() {
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("tags-modal");
    },

    copyTags() {
      getOtherTags().then((resp) => {
        this.$this.otherTags = resp.otherTags;

        this.$this.$bvModal.hide(this.$this.modalId);

        this.$this.$bvModal.show("publishing-copy-tags");
      });
    },

    modalClose(done) {
      if (this.form.isDataEmpty()) {
        this.$confirm(this.$t("close_transaction_alert"), {
          confirmButtonText: "OK",

          cancelButtonText: this.$t("cancel"),

          type: "error",
        })
          .then((resp) => {
            this.form.reset();

            this.$this.$bvModal.hide(this.$this.modalId);

            done();
          })

          .catch((_) => {});
      } else {
        this.form.reset();

        this.file = "";

        this.$this.$bvModal.hide(this.$this.modalId);
      }
    },

    onSubmit() {
      this.modal_tags.button.loading = true;
      // console.log(this.form.tags);
      let formData = new FormData();
      formData.append("publishing_id", this.$this.publishing_id);
      formData.append("tag", JSON.stringify(this.form.tags));

      attachTags(formData).then((resp) => {
        if (resp.data.action == "duplicate") {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.data.name + " " + this.$t("duplicate_content_entry"),

            type: resp.data.type,
          });

          this.modal_tags.button.loading = false;
        } else {
          this.$this.makeToast(
            resp.data.type,
            this.$t("publishing_tags_attached"),
            this.$t("attach_publishing_tags")
          );

          this.form.reset();

          this.$this.$bvModal.hide(this.$this.modalId);

          if (this.$this.search != "")
            this.$this.loadPublishing(this.$this.page, this.$this.search);
          this.$this.params != null
            ? this.$this.loadPublishing(this.$this.page, "", this.$this.params)
            : this.$this.loadPublishing(this.$this.page, "");

          this.form.language = this.$i18n.locale;

          this.modal_tags.button.loading = false;
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

.tags-button {
  width: 100%;
}
</style>
