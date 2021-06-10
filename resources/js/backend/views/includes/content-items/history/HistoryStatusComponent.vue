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
} from "./../../../../api/content_item.js";

export default {
  props: ["$this"],

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
        this.$alert(this.$t("fill_up_content_name"), {
          confirmButtonText: "OK",

          type: "error",
        });

        return false;
      }

      this.modal.button.loading = true;

      let formData = new FormData();

      formData.append("id", this.form.id);
      formData.append("author_idea", this.form.author_idea);
      formData.append("author_task", this.$userId);
      formData.append("notes", this.form.notes);
      formData.append("status", this.form.status);

      updateStatus(formData).then((resp) => {
        if (resp.data.action == "duplicate") {
          this.$notify({
            title: resp.data.title,

            message:
              resp.data.data.name + " " + this.$t("duplicate_content_entry"),

            type: resp.data.type,
          });

          this.modal.button.loading = false;
        } else {
          // this.$notify({

          //   title: resp.data.title,

          //   message: this.$t( 'info_1_alert' ) + resp.data.message + this.$t( 'info_2_alert' ),

          //   type: resp.data.type

          // });

          this.$this.makeToast(
            resp.data.type,
            this.$t("item_status_updated"),
            this.$t("status_1_alert") +
              resp.data.message +
              this.$t("status_2_alert") +
              resp.data.status
          );

          this.form.reset();

          this.modal.isVisible = false;

          this.$this.loadItem();

          this.form.language = this.$i18n.locale;

          this.modal.button.loading = false;
        }
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
