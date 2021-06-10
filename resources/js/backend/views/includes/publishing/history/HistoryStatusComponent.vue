<template>
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
            <span style="color: #5ab55e"> {{ form.sequence }}</span>
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
              <div class="text-center text-h6">
                {{ form.name }}
              </div>

              <v-container>
                <label for="author_idea">
                  {{ $t("content_person_in_charge") }}
                </label>
                <el-select
                  filterable
                  v-model="form.author_idea"
                  clearable
                  :placeholder="$t('label.select_author')"
                  id="author_idea"
                  style="display: block"
                >
                  <el-option
                    :value="user.id"
                    v-for="(user, index) in $this.users"
                    :key="index"
                    :label="user.full_name"
                  ></el-option>
                </el-select>
              </v-container>

              <v-container>
                <label for="notes">
                  {{ $t("label.notes") }}
                </label>
                <el-input
                  type="textarea"
                  :rows="4"
                  :placeholder="$t('label.notes')"
                  v-model="form.notes"
                  id="notes"
                >
                </el-input>
              </v-container>

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
    </v-app>
  </div>
</template>

<script>
import {
  updateContent,
  getContentName,
  updateStatus,
} from "./../../../../api/publishing.js";

import publishingMixins from "./../mixins/publishingMixins.js";

export default {
  props: ["$this"],

  mixins: [publishingMixins],

  data: function() {
    return {
      modal: this.$this.modal.status,

      form: this.$this.form,

      formData: this.$this.formData,

      nextStatus: [],

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },
      ],

      formGroups: [],

      submitted: false,
    };
  },

  methods: {
    onSubmit() {
      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("author", this.form.author_idea);
      formData.append("publisher", this.$userId);
      formData.append("platform", this.form.platformId);
      formData.append("notes", this.form.notes);
      formData.append("status", this.form.status);

      updateStatus(formData)
        .then((resp) => {
          this.errors();

          this.$this.makeToast(
            resp.data.type,
            this.$t("publishing_status_updated"),
            this.$t("status_1_alert") +
              resp.data.message +
              this.$t("status_2_alert") +
              resp.data.status
          );

          this.form.reset();

          this.modal.isVisible = false;

          this.$this.loadPublishing();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        })
        .catch((error) => {
          this.errors();

          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);

            el.attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 1px 1px 3px 1px #ff3b3b !important;"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error">' +
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
.el-row {
  margin-bottom: 20px;
  &:last-child {
    margin-bottom: 0;
  }
}

.margin-top {
  margin-top: -20px !important;
}

.dialog-footer {
  margin-top: -20px !important;
}
</style>
