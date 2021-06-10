<template>
  <div class="edit">
      <b-modal
        id="link-brand-modal"
        hide-footer
        hide-header
        size="md"
        no-close-on-backdrop
        :title="$t(modal_brand.name)"
      >
      
        <v-app id="link_brand_modal">
          <v-card>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-card-title class="title blue-grey lighten-4 text--secondary">
                <div>
                  {{ $t("label.link_to_brand") }}
                  <span
                    class="body-1 blue-grey lighten-4 text--disabled font-weight-light"
                  >
                  </span>
                </div>
                <v-spacer></v-spacer>
                <v-btn
                  icon
                  color="error lighten-2"
                  @click="modalClose"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <label :for="'brands'">
                    {{ $t("label.brands") }}
                    <strong class="text-danger">*</strong>
                  </label>
                  <v-select
                    :id="'brands'"
                    label="brand_name"
                    @change="parent.form.errors.clear('brands')"
                    :reduce="(brand) => brand.id"
                    v-model="form.brands"
                    multiple
                    :options="parent.brands"
                  >
                    <template #list-header>
                      <li style="margin-left:20px;" class="mb-2">
                        <b-link href="#" @click="openBrandModal">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          {{ $t("label.new_brand") }}
                        </b-link>
                      </li>
                    </template>
                  </v-select>
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
                <v-btn color="success" tile dark @click="onSubmit">
                  <div v-if="this.modal_brand.button.loading">
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
                      {{ $t(modal_brand.button.save) }}
                    </div>
                  </div>
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-app>
      </b-modal>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

//   components: {
//     CreateTags,

//     CopyTags,
//   },

  data: function() {
    return {
      modal_brand: this.parent.modal.brand,

      form: this.parent.form,

      valid: true,

      formData: this.parent.formData,

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
    ...mapActions("providers", ["post_link_brand", "update_provider", "get_provider_name"]),

    modalClose(done) {
        $(".error-brands").remove();

        $("#brands").removeAttr(
          "style"
        );
        this.form.reset();

        this.parent.$bvModal.hide(this.parent.modalId);
    },

    openBrandModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);
      
      this.parent.modal.createBrand.isVisible = true;
      // this.parent.modal.itemType.isVisible = true;
    },

    onSubmit() {
      this.modal_brand.button.loading = true;
      let formData = new FormData();
      let brands = ""
      if(this.form.brands.length != 0) brands = JSON.stringify(this.form.brands)
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("brands", brands);

      this.post_link_brand(formData)
        .then((resp) => {
          let response = this.parent.providersResponseStatus;
          response.index = this.parent.editingIndex;
          this.update_provider(response)
          this.parent.btnloading = false;
          this.$bvModal.hide("link-brand-modal");
          let message = {
            update:
              this.$t("updated_message1") +
              this.$t("label.providers") +
              ` ID: ${this.parent.form.id} ` +
              this.$t("updated_message2"),
            title: {
              update: this.$t("create_publishing_link"),
            },
          };
          this.parent.successfullySavedProvider()
          this.parent.makeToast(
            "success",
            message.title.update,
            `${this.form.name} ${this.$t("successfully_link")}`
          );
          this.parent.contact_number = [];
          this.modal_brand.button.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.modal_brand.button.loading = false;
          let errors = error.response.data.errors;
          $(".error-brand").remove();

          $("#brands").removeAttr(
            "style"
          );
          for (let field of Object.keys(errors)) {
            
            let el = $(`#${field}`);
            console.log(el)
            el.find('.vs__dropdown-toggle').attr(
              "style",
              "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); margin-bottom: 5px"
            );

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="error-brand">' +
                  errors[field][0] +
                  "</span>"
              )
            );
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

.brands-button {
  width: 100%;
}
</style>
