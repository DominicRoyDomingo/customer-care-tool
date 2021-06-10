<template>
  <b-modal id="brand-modal" @hide="hide" hide-footer hide-header size="md">
    <v-app id="brand__container">
      <v-card>
        <v-card-title class="title blue-grey lighten-4 text--secondary">
          <span v-if="parent.form.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.brand") }}
          </span>
          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-b-tooltip.hover
            :title="parent.itemSelected.brand_name"
            v-else
          >
            {{ $t("button.update") }} "{{ parent.itemSelected.brand_name }}"
          </span>

          <v-spacer></v-spacer>
          <v-btn
            icon
            color="error lighten-2"
            @click="$bvModal.hide('brand-modal')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container fluid>
            <form
              class="form"
              @keyup="parent.form.errors.clear($event.target.name)"
            >
              <div class="form-group">
                <label for="name">
                  {{ $t("label.brand_name") }}
                  <strong class="text-danger">*</strong>
                </label>

                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="parent.form.name"
                  class="form-control"
                  :placeholder="$t('label.required')"
                  autocomplete="off"
                  aria-describedby="brand"
                />
                <small
                  id="job"
                  v-if="parent.form.errors.has('name')"
                  v-text="parent.form.errors.get('name')"
                  class="text-danger"
                />
              </div>

              <div class="form-group">
                <label for="website">{{ $t("label.website") }}</label>

                <input
                  id="website"
                  type="text"
                  name="website"
                  v-model="parent.form.website"
                  class="form-control"
                  autocomplete="off"
                  aria-describedby="brand"
                />
              </div>

              <b-form-group>
                <b-form-checkbox
                  id="checkbox-1"
                  v-model="parent.form.isDefault"
                  name="checkbox-1"
                  value="1"
                  unchecked-value="0"
                >
                  Is Default
                </b-form-checkbox>
              </b-form-group>

              <div class="form-group">
                <label for="logo">{{ $t("label.logo") }}</label>
                <b-form-file
                  id="img-file"
                  @change="onGetFile"
                  accept=".png, .jpg, .jpeg"
                  plain
                ></b-form-file>
              </div>
            </form>
          </v-container>
        </v-card-text>
        <v-card-actions class="modal__footer blue-grey lighten-5">
          <v-spacer></v-spacer>
          <v-btn color="error" text tile @click="$bvModal.hide('brand-modal')">
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
                <v-icon left>mdi-briefcase-plus</v-icon>
                {{ $t("button.save") }}
              </div>
              <div v-else>
                <v-icon left>mdi-briefcase-edit</v-icon>
                {{ $t("button.save_change") }}
              </div>
            </div>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
  props: ["parent"],

  data() {
    return {
      file: null,
      loading: true,
    };
  },

  computed: {
    ...mapGetters("brand", {
      responseStatus: "get_response_status",
    }),
  },

  methods: {
    ...mapActions("brand", ["post_brand", "add_brand", "update_brand"]),
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

      formData.append("id", this.parent.form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("action", this.parent.form.action);
      formData.append("name", this.parent.form.name);
      formData.append("website", this.parent.form.website);
      formData.append("isDefault", this.parent.form.isDefault);
      formData.append("org_id", this.$org_id);
      formData.append("file", this.file);

      this.post_brand(formData)
        .then((resp) => {
          this.$emit("done-success");

          this.parent.btnloading = false;
          this.$bvModal.hide("brand-modal");
          if (this.responseStatus) {
            if (this.parent.form.action == "Add") {
              vm.parent.loadItems();
              // add_brand(this.responseStatus);
            } else {
              let brand = vm.responseStatus;
              brand.brand_index = vm.parent.editingIndex;
              vm.update_brand(brand);
            }

            let message = {
              create: `${this.parent.form.name}` + this.$t("created_message"),
              update:
                this.$t("updated_message1") +
                this.$t("label.brands") +
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

            this.parent.successfullySavedBrand();
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
