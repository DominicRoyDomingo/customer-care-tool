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
                    v-model="form.brand"
                    multiple
                    :options="brands"
                  >
                    <template #list-header>
                      <li style="margin-left:20px;" class="mb-2">
                        <b-link href="#" @click="openBrandModal()">
                          <i class="fa fa-plus" aria-hidden="true"></i>
                          {{ $t("label.new_brand") }}
                        </b-link>
                      </li>
                    </template>
                  </v-select>
                    <small style="color:red" v-if="brand_required"> {{ $t("brands_name") }} {{ $t('is_required')}}</small>
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
                    <div v-if="modal.add.button.loading" class="text-center">
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
                        {{ $t(modal.add.button.save) }}
                      </div>
                    </div>
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-app>
      </b-modal>

      <brandModal :parent="this" @loadTable="loadBrand"  @hide="closeBrandModal"></brandModal>

  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import brandModal from "./brand.modal.vue";

import toast from "./../../../helpers/toast.js";

export default {
  props: ["$this"],

  data: function() {
    
    return {
      modal_brand: this.$this.modal.brand,

      form: this.$this.form,

      valid: true,

      formData: this.$this.formData,

      link_from: this.$this.link_option,

      brand_required: false,

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

        btnloading: false,
    };
  },

  components: {

      brandModal

    },

  methods: {

    ...mapActions("request_type", ["post_link_brand_request_type"]),
    ...mapActions("reasons", ["post_link_brand_reasons"]),
    ...mapActions("color_coding", ["post_link_brand_color_coding"]),
    ...mapActions("default_groups", ["post_link_brand_default_groups"]),
    ...mapActions("calendar_notes", ["post_link_brand_calendar_notes"]),
    ...mapActions("brand", ["get_brands"]),

    loadBrand() {
      let params = {
        api_token: this.$user.api_token,
        lang: this.form.language,
        entries: this.perPage,
        page: this.currentPage,
        sort_name: this.sortBy,
        sort_desc: this.sortDesc,
        search: this.searched,
        org_id: this.$org_id
      };
      this.get_brands(params).then((_) => {
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
            // this.form.reset();
            this.$bvModal.hide("link-brand-modal");
            done();
          })
          .catch((_) => {});
      } else {
        // this.form.reset();
        this.file = "";
        this.$bvModal.hide("link-brand-modal");
      }
    },

    openBrandModal() {
    //   this.form.reset();
      this.form.action = "Add";
      this.form.actual_action = "Add";
      this.modal.add.isVisible = false;
      this.$bvModal.show("brand-modal");
    },

    closeBrandModal() {
      console.log('ssss');
      this.$bvModal.hide("brand-modal");
    },

    onSubmit() {
      this.brand_required = false;
      if( this.form.brand.length === 0 ) {
        this.brand_required = true;
        return false;
      }

      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("brand", JSON.stringify( this.form.brand ) );

      this.modal.add.button.loading = true;
      if( this.$this.link_option === 'post_link_brand_reasons' ){
        this.post_link_brand_reasons(formData)
          .then((resp) => {
            this.modal.add.button.loading = false;
            this.$bvModal.hide("link-brand-modal");
              this.$this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${resp.data.name} ${this.$t("successfully_link")}`
              );
              this.$this.loadItems(); 
              this.$emit("loadTable");
          })
          .catch((error) => {
            this.modal.add.button.loading = false;
            let errors = error.response.data.errors;
            $(".error-provider").remove();

            for (let field of Object.keys(errors)) {
              
              let el = $(`#${field}`);
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );

              el.after(
                $(
                  '<span style="color: #ff3b3b;" class="error-provider">' +
                    errors[field][0] +
                    "</span>"
                )
              );
            }
          });
      }
      
      if( this.$this.link_option === 'post_link_brand_request_type' ){
        this.post_link_brand_request_type(formData)
          .then((resp) => {
            this.modal.add.button.loading = false;
            this.$bvModal.hide("link-brand-modal");
              this.$this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${resp.data.name} ${this.$t("successfully_link")}`
              );
              this.$this.loadItems(); 
              this.$emit("loadTable");
          })
          .catch((error) => {
            this.modal.add.button.loading = false;
            let errors = error.response.data.errors;
            $(".error-provider").remove();

            for (let field of Object.keys(errors)) {
              
              let el = $(`#${field}`);
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );

              el.after(
                $(
                  '<span style="color: #ff3b3b;" class="error-provider">' +
                    errors[field][0] +
                    "</span>"
                )
              );
            }
          });
      }

      if( this.$this.link_option === 'post_link_color_coding_reasons' ){
        formData.append("session_brand", JSON.stringify( this.form.session_brand ) );
        this.post_link_brand_color_coding(formData)
          .then((resp) => {
              this.modal.add.button.loading = false;
              console.log( resp );
              if( resp.data.success === false ) {
                this.$this.makeToast(
                  "danger",
                  this.$t("create_publishing_unlink"),
                  `${resp.data.name.name} ${this.$t("unsuccessfully_link")}`
                );
              } else {
                this.$bvModal.hide("link-brand-modal");
                this.$this.makeToast(
                  "success",
                  this.$t("create_publishing_link"),
                  `${resp.data.name} ${this.$t("successfully_link")}`
                );
                this.$this.loadItems(); 
                this.$emit("loadTable");
              }
          })
          .catch((error) => {
            this.modal.add.button.loading = false;
            let errors = error.response.data.errors;
            $(".error-provider").remove();

            for (let field of Object.keys(errors)) {
              
              let el = $(`#${field}`);
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );

              el.after(
                $(
                  '<span style="color: #ff3b3b;" class="error-provider">' +
                    errors[field][0] +
                    "</span>"
                )
              );
            }
          });
      }

      if( this.$this.link_option === 'post_link_brand_default_groups' ){
        this.post_link_brand_default_groups(formData)
          .then((resp) => {
            this.modal.add.button.loading = false;
            this.$bvModal.hide("link-brand-modal");
              this.$this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${resp.data.name} ${this.$t("successfully_link")}`
              );
              this.$this.loadItems(); 
              this.$emit("loadTable");
          });
      }

      if( this.$this.link_option === 'post_link_brand_calendar_notes' ){
        this.post_link_brand_calendar_notes(formData)
          .then((resp) => {
            console.log( resp );
            this.modal.add.button.loading = false;
            this.$bvModal.hide("link-brand-modal");
              this.$this.makeToast(
                "success",
                this.$t("create_publishing_link"),
                `${resp.data.name} ${this.$t("successfully_link")}`
              );
              this.$this.loadItems(); 
              this.$emit("loadTable");
          })
          .catch((error) => {
            this.modal.add.button.loading = false;
            let errors = error.response.data.errors;
            $(".error-provider").remove();

            for (let field of Object.keys(errors)) {
              
              let el = $(`#${field}`);
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );

              el.after(
                $(
                  '<span style="color: #ff3b3b;" class="error-provider">' +
                    errors[field][0] +
                    "</span>"
                )
              );
            }
          });
      }

    },
  },

  computed:{
    ...mapGetters("brand", {
      brands: "brands",
    }),
  }
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
