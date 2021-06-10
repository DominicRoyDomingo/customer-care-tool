<template>
  <b-modal
    id="icon-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <span v-if="parent.iconForm.action == 'Add'">
          {{ $t("label.new") }} {{ $t("organization_category_icon") }}
        </span>
        <span
          class="d-inline-block text-truncate"
          style="max-width: 700px; letter-spacing: normal"
          v-else
        >
          {{ $t("button.update") }} "{{ parent.itemSelected.base_name }}"
        </span>
        <v-spacer></v-spacer>
        <v-btn icon color="error lighten-2" @click="onCancel">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-container>
        <div class="row">
          <div class="col-md-12">
            <form
              class="form"
              @submit.prevent="onSubmit"
              @keyup="parent.iconForm.errors.clear($event.target.name)"
            >
              <div
                class="form-group mb-4"
                v-show="parent.iconForm.action === 'Update'"
              >
                <div class="form-inline">
                  <label class="mr-sm-2" for="inline-form-custom-select-pref">
                    Language
                  </label>
                  <b-form-select
                    id="inline-form-custom-select-pref"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    v-model="parent.language"
                    :options="parent.languageOptions"
                  />
                </div>
                <hr />
              </div>

              <div class="form-group">
                <label for="term_types">
                  {{ $t("label.provider_types") }}
                </label>
                <v-select
                  id="term_types"
                  :options="providerTypeTerms"
                  v-model="parent.iconForm.provider_type"
                  name="term_types"
                  label="base_name"
                  required
                >
                  <template #option="{ term_name }">
                    <span v-html="term_name" />
                  </template>
                </v-select>
              </div>

              <div class="form-group mt-3 mb-0">
                <v-file-input
                  truncate-length="15"
                  :label="$t('organization_category_icon')"
                  @change="onselectimage"
                  v-model="file"
                />
              </div>
              <div class="form-group ml-3" v-if="parent.iconForm.file">
                <b-img
                  thumbnail
                  fluid
                  width="300"
                  :src="parent.iconForm.file"
                  alt="Image 1"
                ></b-img>
              </div>

              <v-card-actions class="float-right" v-show="isActionShow">
                <v-btn color="error" text tile @click="onCancel">
                  {{ $t("cancel") }}
                </v-btn>
                <v-btn
                  color="success"
                  tile
                  type="submit"
                  class="bg-success text-white"
                  :disabled="parent.btnloading"
                >
                  <div v-if="parent.btnloading" class="text-center">
                    <v-progress-circular
                      size="20"
                      width="1"
                      color="white"
                      indeterminate
                    />
                  </div>
                  <div v-else>
                    <span v-if="parent.iconForm.action === 'Add'">
                      {{ $t("button.save") }}
                    </span>
                    <span v-else>{{ $t("button.save_change") }}</span>
                  </div>
                </v-btn>
              </v-card-actions>
            </form>
          </div>
        </div>
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data() {
    return {
      file: null,
      isActionShow: true,
      filter: "health",
      specializations: [],
      formData: new FormData(),
    };
  },

  computed: {
    ...mapGetters("categ_terms", {
      providerTypeTerms: "get_linked_provider_terms_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["post_provider_term", "get_type_terms"]),

    showChildModal(modalId) {
      this.isActionShow = false;

      if (modalId === "term-type-modal") {
        this.parent.typeForm.action = "Add";
        this.parent.typeForm.term_type = "";
      } else {
        this.parent.form.action = "Add";
      }

      this.$bvModal.show(modalId);
    },

    closeChildModal() {
      this.isActionShow = true;
      this.parent.form.reset();
    },

    queryItems(api, params) {
      return axios.get(api, { params });
    },

    onselectimage() {
      if (this.file) {
        this.parent.iconForm.icon = URL.createObjectURL(this.file);
      } else {
        this.parent.iconForm.icon = "";
      }
    },

    onCancel() {
      this.file = null;
      this.isActionShow = true;
      this.$emit("close-modal")
      this.onReset();
      this.$bvModal.hide("icon-modal");
    },

    onSubmit() {
      this.parent.btnloading = true;

      this.setFormData();

      this.post_provider_term(this.formData)
        .then((resp) => {
          this.file = null;
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("icon-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.iconForm.errors.record(error.response.data.errors);
            let errors = error.response.data.errors;
            this.toastError(errors)
            if (this.parent.iconForm.name) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.parent.iconForm.errors.get("name")
              );
            }
          }
          this.parent.btnloading = false;
        });
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    setFormData() {
      this.formData = new FormData();
      
      let form = this.parent.iconForm;
      let providerType = ''
      let file = ""
      if(this.file != null) file = this.file
      if(form.provider_type != null) {
        if(form.provider_type.id != undefined) providerType = form.provider_type.id
      }
      let action = form.action;
      this.formData.append("id", form.id);
      this.formData.append("api_token", this.$user.api_token);
      this.formData.append("brand_id", this.$brand.id);
      this.formData.append("action", action);
      this.formData.append("icon", file);
      this.formData.append("term", form.term);
      this.formData.append("provider_type", providerType);
    },

    onReset() {
      this.isActionShow = true;
      this.parent.iconForm.reset();
    },
  },
};
</script>

