<template>
  <b-modal
    id="link-brand-modal"
    size="lg"
    hide-footer
    hide-header
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light">
        {{ $t("label.link_to_brand") }}

        <small
          class="mt-1 ml-1 d-inline-block text-truncate"
          style="max-width: 500px; letter-spacing: normal"
          v-html="`(${parent.itemSelected.base_name})`"
        />
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="on_cancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <div class="row">
          <div class="col-md-12 pl-4 pr-4">
            <form class="form" @submit.prevent="on_submit_form">
              <div class="form-group">
                <label for="brands">
                  {{ $t("label.brand_name") }}
                  <span class="text-danger">*</span>
                </label>
                <v-select
                  id="brand_name"
                  name="brand_name"
                  label="brand_name"
                  @change="parent.brandForm.errors.clear('brand_name')"
                  v-model="parent.brandForm.brands"
                  :options="parent.itemBrands"
                  multiple
                >
                  <template #list-header>
                    <li style="margin-left: 20px" class="mb-2">
                      <b-link
                        v-b-tooltip.hover
                        href="javascript:;"
                        @click="on_add_brand"
                      >
                        <b-spinner
                          v-if="isAddBrandLoading"
                          small
                          label="Small Spinner"
                          class="text-muted"
                        />
                        <i class="fa fa-plus" v-else aria-hidden="true"></i>
                        {{ $t("label.new") }} {{ $t("label.brand") }}
                      </b-link>
                    </li>
                  </template>
                </v-select>
                <small
                  v-if="parent.brandForm.errors.has('brand_name')"
                  v-text="parent.brandForm.errors.get('brand_name')"
                  class="text-danger"
                />
              </div>

              <v-card-actions
                class="float-right mb-0 mt-2"
                v-show="isActionShow"
              >
                <v-btn color="error" text tile @click="on_cancel">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                >
                  <div v-if="parent.btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    >
                    </v-progress-circular>
                  </div>
                  <div v-else>
                    {{ $t("button.save") }}
                  </div>
                </v-btn>
              </v-card-actions>
            </form>
          </div>
        </div>
      </v-container>
    </v-card>
    <createBrand
      :parent="this"
      @loadTable="parent.loadBrands"
      @done-success="create_success"
    ></createBrand>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import CreateBrand from "./../../../workforce_management/modals/brand.modal.vue";
import brandMixin from "./../../../brands/mixins/brandMixins";
import toast from "./../../../../helpers/toast";
export default {
  props: ["parent"],
  mixins: [toast, brandMixin],
  components: {
    CreateBrand,
  },

  data() {
    return {
      isAddBrandLoading: false,
      isActionShow: true,
      newSpecializationCategories: [],
      modal: {
        createBrand: {
          name: "brand_add",
          isVisible: false,
          button: {
            save: "save_button",
            cancel: "cancel",
            new: "content_add_new_button",
            loading: false,
          },
        },
      },
    };
  },

  computed: {
    ...mapGetters("brand", {
      items: "brands",
      categories: "categories",
      responseStatus: "get_response_status",
    }),

    ...mapGetters({
      categories: "jobs/get_job_categories",
    }),

    modalTitle() {
      switch (this.parent.brandForm.linkedType) {
        case "terminology":
          return this.parent.itemSelected.term_name;
        case "article":
          return this.parent.itemSelected.article_title;
        case "term_type":
          return this.parent.itemSelected.term_type_name;
      }
    },
  },

  methods: {
    ...mapActions("jobs", ["get_job_categories"]),

    on_submit_form() {
      this.parent.btnloading = true;
      let params = {
        id: this.parent.mtForm.id,
        api_token: this.$user.api_token,
        brand_name: this.parent.brandForm.brands,
        linkedType: this.parent.brandForm.linkedType,
      };

      axios
        .post("/api/link-to-brand", params)
        .then((resp) => {
          this.linkToast(this.parent.itemSelected.base_name);
          this.parent.btnloading = false;
          this.$emit("done-sucess");
          this.$bvModal.hide("link-brand-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.brandForm.errors.record(error.response.data.errors);
          }
          this.parent.btnloading = false;
        });
    },

    on_add_brand() {
      this.isAddBrandLoading = true;
      this.load_specialization_category();

      setTimeout(() => {
        this.isAddBrandLoading = false;
        // Open Modal Brand
        this.$bvModal.show("brand-modal");
      }, 700);
    },

    create_success() {},

    load_specialization_category() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      this.get_job_categories(params).then((_) => {
        let mapSpecializationCategories = this.categories.map((s) => ({
          category: s.category,
          category_name: s.category_name,
          created_at: s.created_at,
          id: s.id,
          is_english: s.is_english,
          is_german: s.is_german,
          is_italian: s.is_italian,
          is_loading: s.is_loading,
          updated_at: s.updated_at,
        }));
        this.newSpecializationCategories = mapSpecializationCategories;
      });
    },

    on_cancel() {
      // this.parent.form.reset();
      this.$bvModal.hide("link-brand-modal");
    },
  },
};
</script>

<style>
</style>