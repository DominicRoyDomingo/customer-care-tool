<template>
  <b-modal
    id="provider-type-modal"
    hide-footer
    hide-header
    size="lg"
    no-close-on-backdrop
  >
    <v-card class="border-0">
      <v-card-title class="bg-light text-uppercase">
        <span v-if="parent.mtForm.action == 'Add'">
          {{ $t("service_add_new_button") }}
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
                  v-if="!parent.mtForm.name && parent.mtForm.errors.has('name')"
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
                  v-model="parent.mtForm.term_types"
                  name="term_types"
                  label="base_name"
                  multiple
                  required
                >
                  <template #list-header>
                    <li style="margin-left: 20px" class="mb-2">
                      <b-link
                        v-b-tooltip.hover
                        :title="`${$t('label.new')}  ${$t(
                          'label.type_of_term'
                        )}`"
                        href="javascript:;"
                        @click="showChildModal('term-type-modal')"
                      >
                        <i class="fa fa-plus" aria-hidden="true"></i>
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
                  v-model="parent.mtForm.specializations"
                  name="specializations"
                  label="base_name"
                  multiple
                  required
                >
                  <template #list-header>
                    <li style="margin-left: 20px" class="mb-2">
                      <b-link
                        :title="`${$t('label.new')} ${$t(
                          'table.specialization'
                        )}`"
                        href="javascript:;"
                        @click="showChildModal('specialization-modal')"
                      >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        {{ $t("button.new") }} {{ $t("table.specialization") }}
                      </b-link>
                    </li>
                  </template>

                  <template #option="{ on_select_description_name }">
                    <span v-html="on_select_description_name" />
                  </template>
                </v-select>
              </div>

              <div class="form-group mt-3 mb-0">
                <v-file-input
                  truncate-length="15"
                  :label="$t('brands_image')"
                  @change="onselectimage"
                  v-model="file"
                />
              </div>
              <div class="form-group ml-3" v-if="parent.mtForm.file">
                <b-img
                  thumbnail
                  fluid
                  width="300"
                  :src="parent.mtForm.file"
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
          @on-close="closeChildModal"
        />

        <CreateSpecialization
          :parent="parent"
          :parentMedicalTerm="this"
          @done-success="create_specialization_success"
          @on-close="closeChildModal"
        />
      </v-container>
    </v-card>
  </b-modal>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

import CreateSpecialization from "../../medical_stuff/modals/create-specialization.modal";
import CreateTermType from "../../medical_stuff/modals/create-termtype.modal";

export default {
  props: ["parent"],

  components: {
    CreateTermType,
    CreateSpecialization,
  },

  data() {
    return {
      file: null,
      isActionShow: true,
      filter: "health",
      specializations: [],
      formData: new FormData(),
    };
  },

  mounted() {
    this.loadSpecializations();
  },

  created() {
    // Load term type items
    this.loadTermTypes();
  },

  computed: {
    ...mapGetters("categ_terms", {
      termtypes: "get_type_terms_items",
    }),
  },

  methods: {
    ...mapActions("categ_terms", ["post_term", "get_type_terms"]),

    loadSpecializations() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
      };

      axios.get("/api/jobs", { params }).then((resp) => {
        this.specializations = resp.data;
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
      this.parent.form.reset();
      this.loadSpecializations();
      this.$bvModal.hide("specialization-modal");
    },

    loadTermTypes() {
      let params = {
        api_token: this.$user.api_token,
        locale: this.$i18n.locale,
        brand_id: this.$brand.id,
      };

      this.get_type_terms(params).then((_) => {});
    },

    create_termtype_success(item) {
      this.termtypes.push(item);
      this.parent.storeToast(
        item.term_type_name,
        this.$t("label.type_of_terms")
      );
      this.isActionShow = true;
    },

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
        this.parent.mtForm.file = URL.createObjectURL(this.file);
      } else {
        this.parent.mtForm.file = "";
      }
    },

    onCancel() {
      this.file = null;
      this.isActionShow = true;
      this.onReset();
      this.$bvModal.hide("term-modal");
      this.$bvModal.show(this.parent.modalId);
    },

    onSubmit() {
      this.parent.btnloading = true;

      this.setFormData();
			
      this.post_term(this.formData)
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

    setFormData() {
      let form = this.parent.mtForm;
      let action = form.action;

      let specializations = form.specializations
        ? JSON.stringify(form.specializations)
        : "";

      let term_types = "";
      if (form.term_types && form.term_types.length > 0) {
        term_types = JSON.stringify(form.term_types);
      }

      this.formData.append("id", form.id);
      this.formData.append("api_token", this.$user.api_token);
      this.formData.append("brand_id", this.$brand.id);
      this.formData.append("name", form.name);
      this.formData.append("specializations", specializations);
      this.formData.append("term_types", term_types);
      this.formData.append("action", action);
      this.formData.append("brand_id", this.$brand.id);
      this.formData.append("file", this.file ?? "");
      this.formData.append(
        "locale",
        action === "Add" ? this.$i18n.locale : this.parent.language
      );
    },

    onReset() {
      this.isActionShow = true;
      this.parent.mtForm.reset();
    },
  },
};
</script>

