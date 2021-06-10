<style>
.v-window {
  overflow: inherit !important;
}
</style>
<template>
  <b-modal
    id="term-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-app id="create__container">
      <v-card class="border-0">
        <v-card-title class="bg-light text-uppercase">
          <span v-if="parent.mtForm.action == 'Add'">
            {{ $t("label.new") }} {{ $t("label.terminilogy") }}
          </span>
          <span
            class="d-inline-block text-truncate"
            style="max-width: 700px; letter-spacing: normal"
            v-else
          >
            {{ $t("button.update") }} "{{ parent.itemSelected.base_name }}"
          </span>
          <v-spacer></v-spacer>
          <v-btn icon color="error lighten-2" @click="on_cancel">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-container>
          <div class="row">
            <div class="col-md-12">
              <form
                class="form"
                @submit.prevent="on_form_submit"
                @keyup="parent.mtForm.errors.clear($event.target.name)"
              >
                <div
                  class="form-group mb-4"
                  v-show="parent.mtForm.action === 'Update'"
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
                  <v-text-field
                    v-model="parent.mtForm.name"
                    :label="$t('label.name')"
                    type="text"
                    name="name"
                    hide-details="auto"
                  />
                  <small
                    v-if="
                      !parent.mtForm.name && parent.mtForm.errors.has('name')
                    "
                    v-text="parent.mtForm.errors.get('name')"
                    class="text-danger"
                  />
                </div>
                <br />
                <div class="form-group">
                  <label for="term_types">
                    {{ $t("label.type_of_term") }}
                  </label>
                  <v-select
                    id="term_types"
                    :options="termtypes"
                    :loading="isLoadingTermType"
                    :disabled="isLoadingTermType"
                    v-model="parent.mtForm.term_types"
                    name="term_types"
                    label="base_name"
                    multiple
                    required
                  >
                    <template #spinner="{ loading }">
                      <b-spinner
                        v-if="loading"
                        class="text-info"
                        small
                        label="Small Spinner"
                      />
                    </template>

                    <template #list-header>
                      <li style="margin-left: 20px" class="mb-2">
                        <b-link
                          v-b-tooltip.hover
                          :title="`${$t('label.new')}  ${$t(
                            'label.type_of_term'
                          )}`"
                          href="javascript:;"
                          @click="show_child_modal('term-type-modal')"
                        >
                          <b-spinner
                            v-if="isAddTermType"
                            small
                            label="Small Spinner"
                          />
                          <i v-else class="fa fa-plus" aria-hidden="true" />
                          {{ $t("label.type_of_term") }}
                        </b-link>
                      </li>
                    </template>
                    <template #option="{ on_select_term_type_name }">
                      <span v-html="on_select_term_type_name" />
                    </template>
                  </v-select>
                </div>

                <div class="form-group">
                  <label for="specializations">
                    {{ $t("label.specializations") }}
                  </label>
                  <v-select
                    id="specializations"
                    :options="specializations"
                    :loading="isLoadingSpec"
                    :disabled="isLoadingSpec"
                    v-model="parent.mtForm.specializations"
                    name="specializations"
                    label="base_name"
                    multiple
                    required
                  >
                    <template #spinner="{ loading }">
                      <b-spinner
                        v-if="loading"
                        class="text-info"
                        small
                        label="Small Spinner"
                      />
                    </template>

                    <template #list-header>
                      <li style="margin-left: 20px" class="mb-2">
                        <b-link
                          :title="`${$t('label.new')} ${$t(
                            'table.specialization'
                          )}`"
                          href="javascript:;"
                          @click="show_child_modal('specialization-modal')"
                        >
                          <b-spinner
                            v-if="isAddSpec"
                            small
                            label="Small Spinner"
                          />
                          <i v-else class="fa fa-plus" aria-hidden="true" />

                          {{ $t("button.new") }}
                          {{ $t("table.specialization") }}
                        </b-link>
                      </li>
                    </template>

                    <template #option="{ on_select_description_name }">
                      <span v-html="on_select_description_name" />
                    </template>
                  </v-select>
                </div>
                <br />
                <div class="form-group">
                  <v-tabs
                    show-arrows
                    center-active
                    grow
                    background-color="blue-grey lighten-5"
                    slider-color="blue-grey darken-2"
                    color="blue-grey darken-2"
                  >
                    <v-tab class="caption font-weight-bold">
                      {{ $t("term") }} {{ $t("brands_image") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("term") }} {{ $t("icon") }}
                    </v-tab>
                    <v-tab-item>
                      <div>
                        <v-file-input
                          truncate-length="15"
                          :label="$t('brands_image')"
                          @change="on_select_image"
                          v-model="file"
                          name="file"
                        />
                      </div>

                      <div v-if="parent.mtForm.file">
                        <div
                          class="alert alert-danger"
                          role="alert"
                          v-if="parent.mtForm.errors.has('file')"
                        >
                          <strong>Error!</strong>
                          <span v-text="parent.mtForm.errors.get('file')" />
                        </div>
                        <b-img
                          thumbnail
                          fluid
                          width="300"
                          :src="parent.mtForm.file"
                          alt="Image 1"
                        />
                      </div>
                    </v-tab-item>
                    <v-tab-item>
                      <div>
                        <v-file-input
                          truncate-length="15"
                          :label="$t('organization_category_icon')"
                          @change="on_select_icon"
                          name="icon"
                          v-model="icon"
                        />
                      </div>

                      <div v-if="parent.mtForm.icon">
                        <div
                          class="alert alert-danger"
                          role="alert"
                          v-if="parent.mtForm.errors.has('icon')"
                        >
                          <strong>Error!</strong>
                          <span v-text="parent.mtForm.errors.get('icon')" />
                        </div>
                        <b-img
                          thumbnail
                          fluid
                          width="300"
                          :src="parent.mtForm.icon"
                          alt="Image 1"
                        />
                      </div>
                    </v-tab-item>
                  </v-tabs>
                </div>
                <v-card-actions class="float-right" v-show="isActionShow">
                  <v-btn color="error" text tile @click="on_cancel">
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
                      <span v-if="parent.mtForm.action === 'Add'">
                        {{ $t("button.save") }}
                      </span>
                      <span v-else>{{ $t("button.save_change") }}</span>
                    </div>
                  </v-btn>
                </v-card-actions>
              </form>
            </div>
          </div>

          <CreateTermType
            :parent="parent"
            :parentMedicalTerm="this"
            @done-success="create_termtype_success"
            @on-close="close_child_modal"
          />

          <CreateSpecialization
            v-if="isCreateSpec"
            :parent="parent"
            :parentMedicalTerm="this"
            @done-success="create_specialization_success"
            @on-close="close_child_modal"
          />
        </v-container>
      </v-card>
    </v-app>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateSpecialization from "./create-specialization.modal";
import CreateTermType from "./create-termtype.modal";

export default {
  props: ["parent"],

  components: {
    CreateTermType,
    CreateSpecialization,
  },

  data() {
    return {
      file: null,
      icon: null,
      isCreateSpec: false,
      isCreateTermTypes: false,
      isLoadingSpec: true,
      isLoadingTermType: true,
      isAddSpec: false,
      isAddTermType: false,
      isActionShow: true,
      filter: "health",
      specializations: [],
      formData: new FormData(),
      params: {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      },
    };
  },

  mounted() {
    this.load_items();
  },

  computed: {
    ...mapGetters("categ_terms", {
      termtypes: "get_type_terms_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["post_term", "get_type_terms"]),

    load_items() {
      // load specializations
      this.load_specializations({ ...this.params });

      // Load term type items
      this.get_type_terms({ ...this.params }).then((_) => {
        this.isLoadingTermType = false;
      });
    },

    load_specializations(params) {
      axios
        .get("/api/select/job_description/list_job_descriptions", { params })
        .then((resp) => {
          this.specializations = resp.data;
          this.isLoadingSpec = false;
        });
    },

    create_specialization_success(item, action) {
      this.btnloading = false;
      this.isActionShow = true;
      let recordName = this.$t("label.specializations");
      if (action === "Add") {
        this.parent.storeToast(item.base_name, recordName);
      } else {
        this.parent.updateToast(item.id, recordName);
      }
      // this.parent.form.reset();
      this.load_specializations({ ...this.params });
      this.$bvModal.hide("specialization-modal");
    },

    create_termtype_success(item) {
      this.termtypes.push(item);
      this.parent.storeToast(
        item.term_type_name,
        this.$t("label.type_of_terms")
      );
      this.isActionShow = true;
    },

    show_child_modal(modalId) {
      this.isActionShow = false;

      if (modalId === "term-type-modal") {
        this.parent.typeForm.action = "Add";
        this.parent.typeForm.term_type = "";
        this.isAddTermType = true;
      } else {
        this.parent.form.action = "Add";
        this.isCreateSpec = true;
        this.isAddSpec = true;
      }

      setTimeout(() => {
        this.isAddSpec = false;
        this.isAddTermType = false;
        this.$bvModal.show(modalId);
      }, 1000);
    },

    close_child_modal() {
      this.isActionShow = true;
      this.isCreateSpec = false;
    },

    queryItems(api, params) {
      return axios.get(api, { params });
    },

    on_select_icon() {
      if (this.icon) {
        this.parent.mtForm.icon = URL.createObjectURL(this.icon);
      } else {
        this.parent.mtForm.icon = "";
        this.icon = null;
      }
      this.parent.mtForm.errors.clear("icon");
    },

    on_select_image() {
      if (this.file) {
        this.parent.mtForm.file = URL.createObjectURL(this.file);
      } else {
        this.parent.mtForm.file = "";
        this.file = null;
      }
      this.parent.mtForm.errors.clear("file");
    },

    on_cancel() {
      this.file = null;
      this.on_reset();
      this.$emit("close-modal");
      this.$bvModal.hide("term-modal");
    },

    on_form_submit() {
      this.parent.btnloading = true;
      let data = this.set_form_data();

      this.post_term(data)
        .then((resp) => {
          this.file = null;
          this.parent.btnloading = false;
          this.$emit("done-success", resp.data);
          this.$bvModal.hide("term-modal");
        })
        .catch((error) => {
          if (error.response) {
            this.parent.mtForm.errors.record(error.response.data.errors);
            if (this.parent.mtForm.name) {
              this.parent.errorToast(
                `${this.$t("errors.duplicate")}!`,
                this.parent.mtForm.errors.get("name")
              );
            }
          }
          this.parent.btnloading = false;
        });
    },

    set_form_data() {
      let form = this.parent.mtForm;
      let action = form.action;

      let specializations = form.specializations
        ? JSON.stringify(form.specializations)
        : "";

      let term_types = "";
      if (form.term_types && form.term_types.length > 0) {
        term_types = JSON.stringify(form.term_types);
      }

      let formData = new FormData();

      formData.append("id", form.id);
      formData.append("api_token", this.$user.api_token);
      formData.append("brand_id", this.$brand.id);
      formData.append("name", form.name);
      formData.append("specializations", specializations);
      formData.append("term_types", term_types);
      formData.append("action", action);
      formData.append("brand_id", this.$brand.id);
      formData.append("file", this.file ?? "");
      formData.append("icon", this.icon ?? "");
      formData.append(
        "locale",
        action === "Add" ? this.$i18n.locale : this.parent.language
      );

      return formData;
    },

    on_reset_form_data() {
      for (var key of this.formData.keys()) {
        this.formData.delete(key);
      }
    },

    on_reset() {
      this.isActionShow = true;
      this.parent.mtForm.reset();
    },
  },
};
</script>

