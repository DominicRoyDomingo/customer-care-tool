<template>
  <div>
    <b-modal
      id="template-modal"
      @hide="$bvModal.hide('template-modal')"
      hide-footer
      hide-header
      size="lg"
    >
      <v-app id="templates__modal">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              {{
                parent.form.action == "Add"
                  ? $t("label.add")
                  : $t("button.update")
              }}
              <span
                class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
              >
              </span>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('template-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <form
              class="form"
              @submit.prevent="onSave"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="name">
                      {{ $t("label.template_name") }}
                      <strong class="text-danger">*</strong>
                    </label>

                    <input
                      id="template_name"
                      type="text"
                      name="name"
                      v-model="parent.form.template_name"
                      class="form-control"
                      :placeholder="$t('label.required')"
                      autocomplete="off"
                      aria-describedby="template_name"
                    />
                    <small
                      id="job"
                      v-if="parent.form.errors.has('template_name')"
                      v-text="parent.form.errors.get('template_name')"
                      class="text-danger"
                    />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="preview">{{ $t("label.preview") }}</label>
                    <b-form-file
                      id="img-file"
                      @change="onGetFile"
                      accept=".png, .jpg, .jpeg"
                      plain
                    ></b-form-file>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="preview">{{ $t("label.html_content") }}</label>
                <b-textarea
                  v-model="parent.form.html_content"
                  rows="6"
                ></b-textarea>
              </div>

              <div id="variable-container">
                <h3>{{ $t("label.variables") }}</h3>
                <variableDiv
                  v-for="(variable, index) in parent.form.variables"
                  v-bind:key="'variable_' + index"
                  :root_parent="parent"
                  :parent="vm"
                  :index="index"
                  :is_deletable="true"
                ></variableDiv>
              </div>

              <div class="form-group">
                <v-btn
                  tile
                  block
                  color="success lighten-1"
                  @click="parent.addVariable()"
                >
                  {{ $t("label.add_section") }}
                </v-btn>
              </div>
            </form>
          </v-card-text>
          <v-divider style="margin-bottom: 0"></v-divider>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('template-modal')"
            >
              {{ $t("cancel") }}
            </v-btn>
            <v-btn color="success" tile dark @click="onSave">
              <div v-if="parent.btnloading">
                <v-progress-circular
                  size="20"
                  width="1"
                  color="white"
                  indeterminate
                >
                </v-progress-circular>
              </div>
              <div v-else>
                <div v-if="parent.form.action == 'Add'">
                  <v-icon left>mdi-text-box-plus</v-icon>
                  {{ $t("button.save") }}
                </div>
                <div v-else>
                  <v-icon left>mdi-file-document-edit</v-icon>
                  {{ $t("button.save_change") }}
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
import { mapActions, mapGetters } from "vuex";
import variableDiv from "./../components/variable_div.vue";

export default {
  props: ["parent"],

  data() {
    return {
      file: null,
      loading: true,
      vm: this,
    };
  },

  components: {
    variableDiv,
  },

  computed: {
    ...mapGetters("email_template", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("email_template", [
      "post_template",
      "add_template",
      "update_template",
    ]),
    onGetFile(event) {
      this.file = event.target.files[0];
    },
    hide() {
      this.$emit("hide");
    },
    onSave() {
      let vm = this;
      this.parent.btnloading = true;

      let formData = new FormData();

      if (this.parent.form.id != "") {
        formData.append("id", this.parent.form.id);
      }
      formData.append("api_token", this.$user.api_token);
      formData.append("action", this.parent.form.action);

      formData.append("template_name", this.parent.form.template_name);
      formData.append("html_content", this.parent.form.html_content);
      formData.append("variables", JSON.stringify(this.parent.form.variables));
      formData.append("preview", this.file);

      this.post_template(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("template-modal");
          if (this.responseStatus) {
            //this.parent.loadItems();

            if (this.parent.form.action == "Add") {
              this.add_template(this.responseStatus);
            } else {
              let template = vm.responseStatus;
              template.template_index = vm.parent.editingIndex;
              vm.update_template(template);
            }

            let message = {
              create: `${this.parent.form.name}` + this.$t("created_message"),
              update:
                this.$t("updated_message1") +
                this.$t("label.templates") +
                ` ID: ${this.parent.form.id} ` +
                this.$t("updated_message2"),
              title: {
                create: this.$t("new_record_created"),
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              this.parent.form.action === "Add" ? "success" : "warning",
              this.parent.form.action === "Add"
                ? message.title.create
                : message.title.update,
              this.parent.form.action === "Add"
                ? message.create
                : message.update
            );

            this.parent.successfullySavedTemplate();
          }
        })
        .catch((error) => {
          console.log(error);
          this.parent.form.errors.record(error.response.data.errors);
          this.parent.btnloading = false;
        });
    },
  },
};
</script>

<style scoped>
#imagePreview {
  height: 150px;
  width: 150px;
}
</style>
