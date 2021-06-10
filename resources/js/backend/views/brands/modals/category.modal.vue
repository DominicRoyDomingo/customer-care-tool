<template>
  <div>
    <b-modal
      id="category-brand-modal"
      @hide="hide"
      hide-footer
      hide-header
      size="md"
    >
      <v-app id="brand__container">
        <v-card>
          <v-card-title class="title blue-grey lighten-4 text--secondary">
            <div>
              Category
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon
              color="error lighten-2"
              @click="$bvModal.hide('category-brand-modal')"
            >
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container fluid>
              <form
                class="form"
                @submit.prevent="onSave"
                @keyup="parent.form.errors.clear($event.target.name)"
              >
                <div class="form-group">
                  <label for="name">
                    {{ $t("label.category_name") }}
                    <strong class="text-danger">*</strong>
                  </label>

                  <el-select
                    v-model="parent.form.categories"
                    multiple
                    id="categories"
                    filterable
                    placeholder="Select"
                    style="display: block"
                  >
                    <el-option label="" value="">
                      <div class="tags-button" @click.stop="openCategoryModal">
                        <b-link href="#">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          {{ $t("label.new_category") }}
                        </b-link>
                      </div>
                    </el-option>
                    <el-option
                      v-for="category in parent.categories"
                      :key="category.id"
                      :label="category.category_name"
                      :value="category.id"
                    >
                    </el-option>
                  </el-select>
                </div>
              </form>
            </v-container>
          </v-card-text>
          <v-card-actions class="modal__footer blue-grey lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="error"
              text
              tile
              @click="$bvModal.hide('category-brand-modal')"
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
                <div>
                  <v-icon left>mdi-tag-multiple</v-icon>
                  {{ $t("button.save") }}
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
    ...mapActions("brand", ["post_category", "add_brand", "update_brand"]),
    onGetFile(event) {
      this.file = event.target.files[0];
    },
    openCategoryModal() {
      this.$bvModal.hide("category-brand-modal");
      this.$bvModal.show("category-modal");
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
      formData.append(
        "category_ids",
        JSON.stringify(this.parent.form.categories)
      );

      this.post_category(formData).then((resp) => {
        this.parent.btnloading = false;
        this.$bvModal.hide("category-brand-modal");
        // console.log(resp)
        let message = {
          create: `${this.parent.form.name} ${this.$t(
            "brand_linked1"
          )} ${resp.data.join()} ${this.$t("brand_linked2")}`,
          title: this.$t("new_record_created"),
        };
        this.parent.makeToast("success", message.title, message.create);
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
