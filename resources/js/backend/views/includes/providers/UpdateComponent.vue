<template>
  <div class="create">
      <b-modal
        id="provider-edit-modal"
        hide-footer
        hide-header
        size="lg"
        no-close-on-backdrop
        :title="$t(modal.name)"
      >
      <v-app id="create__container">
        <v-card>
          <v-form ref="form" v-model="valid" lazy-validation>
            <v-card-title class="title blue-grey lighten-4 text--secondary">
              <span>
                {{$t(modal.name)}}
              </span>
              <v-spacer></v-spacer>
              <v-btn
                icon
                color="error lighten-2"
                @click="modalClose"
              >
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-card-title>
            <v-card-text class="modal__content">
              <v-container>
                <v-container>
                  <v-row>
                    <v-col cols="12" md="12" class="modal__input-container">
                      <v-text-field
                        v-model="form.name"
                        :rules="[(v) => !!v || $t('label.sn_required')]"
                        :label="$t('label.name')"
                        suffix="*"
                        class="modal__input"
                        autocomplete="off"
                        required
                      >
                      </v-text-field>
                      <label for="name">
                        {{ $t("provider_type_category") }}
                      </label>
                      <v-select
                        name="provider_type"
                        label="base_name"
                        multiple
                        v-model="parent.provider_provider_types"
                        :options="parent.providerTypeTerms"
                        id="provider_type"
                      >
                        <template #list-header>
                          <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openProviderTypeModal">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("provider_type_add_new_button") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
                      <label for="plan">
                        {{ $t("label.plan") }}
                      </label>
                      <v-select
                        name="provider_type"
                        label="base_name"
                        v-model="parent.plan"
                        :options="parent.paymentPlans"
                        id="provider_type"
                      >
                        <template #list-header>
                          <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openPaymentPlanModal">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("payment_plan_add_edit_button") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
                    </v-col>
                  </v-row>
                </v-container>
                <v-card color="gray lighten-3" outlined tile>
                  <v-tabs
                    v-model="tabIndex"
                    show-arrows
                    center-active
                    grow
                    background-color="blue-grey lighten-5"
                    slider-color="blue-grey darken-2"
                    color="blue-grey darken-2"
                  >
                    <v-tab class="caption font-weight-bold">
                      {{ $t("label.location") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("label.contacts") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("label.facebook_url") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      LinkedIn
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("label.website") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("brands_image") }}
                    </v-tab>
                    <v-tab class="caption font-weight-bold">
                      {{ $t("label.other_info") }}
                    </v-tab>
                    <v-tab-item eager>
                      <v-container>
                        <label for="name">
                          {{ $t("label.country") }}
                        </label>

                        <el-row>
                          <el-col :span="24">
                            <v-select
                              name="provider_type"
                              label="name"
                              v-model="form.country"
                              :options="parent.countries"
                              @input="changeCountry"
                              id="countries"
                            >
                            </v-select>
                          </el-col>
                        </el-row>
                      </v-container>

                      <v-container v-if="form.country != null">
                        <label for="name">
                          {{ $t("label.province") }} <v-progress-circular v-if="loadingProvinces" indeterminate></v-progress-circular>
                        </label>

                        <el-row>
                          <el-col :span="24">
                            <v-select
                              :readonly="loadingProvinces"
                              name="provider_type"
                              label="name"
                              v-model="form.division"
                              :options="parent.divisions"
                              @input="changeDivision"
                              id="divisions"
                            >
                              <template #list-header>
                                <li style="margin-left:20px;" class="mb-2">
                                  <b-link href="#" @click="parent.modalAddNewProvince(form.country)">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ $t("label.new_province") }}
                                  </b-link>
                                </li>
                              </template>
                            </v-select>
                          </el-col>
                        </el-row>
                      </v-container>

                      <v-container v-if="form.country != null">
                        <label for="name">
                          {{ $t("label.city") }} <v-progress-circular v-if="loadingCities" indeterminate></v-progress-circular>
                        </label>

                        <el-row>
                          <el-col :span="24">
                            <v-select
                              :readonly="loadingCities"
                              name="provider_type"
                              label="name"
                              v-model="form.city"
                              :options="parent.cities"
                              id="cities"
                            >
                              <template #list-header>
                                <li style="margin-left:20px;" class="mb-2" v-if="form.division != null">
                                  <b-link href="#" @click="parent.modalAddNewCity({country: form.country, province: form.division})">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ $t("label.new_city") }}
                                  </b-link>
                                </li>
                              </template>
                            </v-select>
                          </el-col>
                        </el-row>
                      </v-container>

                      <v-container>
                          <label for="address">
                            {{ $t("label.address") }}
                          </label>

                          <input
                            id="address"
                            type="text"
                            name="address"
                            v-model="form.address"
                            class="form-control"
                            :placeholder="$t('label.address')"
                            autocomplete="off"
                            aria-describedby="address"
                          />
                      </v-container>

                      <v-container>
                        <label for="postal code">
                          {{ $t("label.postal_code") }}
                        </label>

                        <input
                          id="postal_code"
                          type="text"
                          name="postal_code"
                          v-model="form.postal_code"
                          class="form-control"
                          :placeholder="$t('label.postal_code')"
                          autocomplete="off"
                          aria-describedby="postal_code"
                        />
                      </v-container>
                    </v-tab-item>
                    <v-tab-item eager>
                      <label for="contact number">
                        {{ $t("label.contact_no") }}
                      </label>

                      <b-form-tags v-model="parent.contact_number" class="mb-2" placeholder="Enter new contact no."></b-form-tags>
                    </v-tab-item>
                    <v-tab-item eager>
                      <v-container>
                        <label for="facebook url">
                          {{ $t("label.facebook_url") }}
                        </label>

                        <input
                          id="facebook_url"
                          type="text"
                          name="facebook_url"
                          v-model="form.facebook_url"
                          class="form-control"
                          :placeholder="$t('label.facebook_url')"
                          autocomplete="off"
                          aria-describedby="facebook_url"
                        />
                      </v-container>
                    </v-tab-item>
                    <v-tab-item eager>
                      <v-container>
                        <label for="LinkedIn">
                          LinkedIn
                        </label>

                        <input
                          id="linkedin"
                          type="text"
                          name="linkedin"
                          v-model="form.linkedin"
                          class="form-control"
                          placeholder="LinkedIn"
                          autocomplete="off"
                          aria-describedby="linkedin"
                        />
                      </v-container>
                    </v-tab-item>
                    <v-tab-item eager>
                      <v-container>
                          <label for="website">
                            {{ $t("label.website") }}
                          </label>

                          <input
                            id="website"
                            type="text"
                            name="website"
                            v-model="form.website"
                            class="form-control"
                            :placeholder="$t('label.website')"
                            autocomplete="off"
                            aria-describedby="website"
                          />
                        </v-container>
                    </v-tab-item>
                    <v-tab-item eager>
                      <v-container>
                        <label for="browse images">
                          {{ $t("label.browse_images") }}
                        </label>

                        <a href="#" class="float-right" @click="checkExistingImages" v-if="parent.existingImages.length != 0">
                          {{ $t("label.check_existing_images") }}
                        </a>

                        <b-form-file
                          accept=".jpg, .png"
                          v-model="parent.files"
                          :state="Boolean(parent.files)"
                          multiple 
                          placeholder="Image/s"
                          drop-placeholder="Drop image/s here..."
                          @input="addImages"
                        ></b-form-file>

                        <v-row class="mt-3">
                          <v-col sm="3" v-for="(image,index) in parent.imagesForm.imagesPlaceholder" :key="index">
                            <v-hover v-slot="{ hover }">
                              <v-card>
                                <v-img
                                  height="175"
                                  contain
                                  :src="image"
                                  class="grey darken-4"
                                ></v-img>
                                <v-fade-transition>
                                  <v-overlay
                                    v-if="hover"
                                    absolute
                                    color="#282828"
                                    opacity="0.9"
                                  >
                                  <div class="d-flex fill-height" style="flex-direction:column">
                                    <div class="d-flex">
                                      <v-spacer></v-spacer>
                                      <div class="pa-1">
                                        <v-tooltip top>
                                          <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                              icon
                                              v-bind="attrs"
                                              v-on="on"
                                              @click="removeImage(index)"
                                            >
                                              <v-icon>
                                                mdi-close
                                              </v-icon>
                                            </v-btn>
                                          </template>
                                          <span>Remove</span>
                                        </v-tooltip>
                                      </div>
                                    </div>
                                  </div>
                                  </v-overlay>
                                </v-fade-transition>
                              </v-card>
                            </v-hover>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-tab-item>
                    <v-tab-item eager>
                      <v-container>
                        <div
                          class="note-div note-data"
                          v-for="(other_info, index) in parent.form
                            .provider_other_infos"
                          v-bind:key="'other_info_' + index"
                        >
                          <div class="row no-gutters">
                            <div class="col-md-11 text-left">
                              <hr />
                            </div>
                            <div class="col-md-1 text-right">
                              <v-btn
                                fab
                                dark
                                small
                                color="error"
                                @click.prevent="deleteProviderOtherInfoDiv(index)"
                              >
                                <v-icon dark>mdi-close</v-icon>
                              </v-btn>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="information_type">{{
                              $t("label.information_type")
                            }}</label>
                            <v-select
                              name="name"
                              label="base_name"
                              v-model="parent.form.provider_other_infos[index].type"
                              :options="parent.newInformationTypes"
                              id="information_type"
                              @input="parent.form.provider_other_infos[index].value = ''"
                            >
                              <template #list-header>
                                <li style="margin-left: 20px" class="mb-2">
                                  <b-link
                                    href="#"
                                    @click="openInformationTypeModal"
                                  >
                                    <i
                                      class="fa fa-plus"
                                      aria-hidden="true"
                                    ></i>
                                    {{ $t("information_type_new_button") }}
                                  </b-link>
                                </li>
                              </template>

                              <template #option="{ information_type_name }">
                                <span v-html="information_type_name" />
                              </template>
                            </v-select>
                          </div>

                          <div class="form-group" v-if="parent.form.provider_other_infos[index].type != null">
                            <div v-if="parent.form.provider_other_infos[index].type.type_of_data == 'Short Text'">
                              <v-text-field
                                v-model="parent.form.provider_other_infos[index].value"
                                :rules="[parent.rules.required]"
                                :label="$t('short_text')"
                              ></v-text-field>
                            </div>

                            <div v-if="parent.form.provider_other_infos[index].type.type_of_data == 'Email Format'">
                              <v-text-field
                                v-model="parent.form.provider_other_infos[index].value"
                                :rules="[parent.rules.required, parent.rules.email]"
                                :label="$t('label.email')"
                              ></v-text-field>
                            </div>

                            <div v-if="parent.form.provider_other_infos[index].type.type_of_data == 'Numeric'">
                              <v-text-field
                                v-model="parent.form.provider_other_infos[index].value"
                                :rules="[parent.rules.required, parent.rules.number]"
                                single-line
                                type="number"
                                :label="$t('numeric')"
                              ></v-text-field>
                            </div>

                            <div v-if="parent.form.provider_other_infos[index].type.type_of_data == 'Date'">
                              <v-menu content-class="elevation-0" >
                                <template v-slot:activator="{on}">
                                  <v-text-field
                                    v-model="parent.form.provider_other_infos[index].value"
                                    v-on="on"
                                    :label="$t('date')"
                                    :rules="[parent.rules.required]"
                                  ></v-text-field>
                                </template>
                                <v-date-picker v-model="parent.form.provider_other_infos[index].value" elevation="15"></v-date-picker>
                              </v-menu>
                            </div>

                            <div v-if="parent.form.provider_other_infos[index].type.type_of_data == 'Long Text'">
                              <v-textarea
                                :rules="[parent.rules.required]"
                                v-model="parent.form.provider_other_infos[index].value"
                                :label="$t('long_text')"
                              ></v-textarea>
                            </div>
                          </div>
                        </div>
                        <!-- <otherInfoDiv
                                v-for="(other_info, index) in parent.new_other_infos"
                                v-bind:key="'other_info_' + index"
                                :root_parent="parent"
                                :parent="vm"
                                :index="other_info.index"
                                :is_deletable="true"
                                ref="otherInfoDiv"
                            ></otherInfoDiv> -->
                        <div class="row">
                          <div class="col-md-12">
                            <v-btn
                              tile
                              block
                              color="success lighten-1"
                              @click.prevent="parent.addProviderOtherInfoDiv"
                            >
                              <v-icon>mdi-sticker-plus</v-icon>&nbsp;
                              {{ $t("label.add_other_info") }}
                            </v-btn>
                          </div>
                        </div>
                      </v-container>
                    </v-tab-item>
                  </v-tabs>
                </v-card>
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
                <v-btn
                  color="success"
                  tile
                  @click="onSubmit"
                >
                  <div v-if="this.modal.button.loading" class="text-center">
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
                      {{ $t(modal.button.update) }}
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
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data: function() {
    return {
      modal: this.parent.modal.edit,

      submitted: false,

      keep_open: false,

      form: this.parent.form,

      valid: true,

      tabIndex: 0,

      formData: this.parent.formData,

      country_id: "",

      formGroups: [],

      loadingProvinces: false,

      loadingCities: false,

      langs: [
        { id: "en", value: "English" },

        { id: "it", value: "Italian" },

        { id: "de", value: "German" },
      ],
    };
  },

  methods: {
    ...mapActions("providers", ["post_provider", "update_provider", "get_provider_name"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities", "reset_cities"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },

    modalClose(done) {
      $(".error-provider").remove();
      $("#name, #provider_type").removeAttr(
        "style"
      );
      this.file = "";
      this.parent.provider_provider_types  = [],
      this.parent.contact_number  = [],
      this.parent.plan = null,
      this.parent.$bvModal.hide("provider-edit-modal");
      this.form.reset();
      this.keep_open = false;
    },

    onSubmit() {
      if(!this.$refs.form.validate()) return;
      this.modal.button.loading = true;
      let formData = new FormData();
      let providerType = "";
      let country = '';
      let city = '';
      let division = '';
      let plan = '';
      let otherInfos = [];

      this.parent.form.provider_other_infos.forEach(function (data) {
        if (data.type == "" || data.value == "") return;
        otherInfos.push({
          type: data.type.id,
          value: data.value,
        });
      });
      if (this.form.country == "") this.form.country = null;
      if (this.form.city == "") this.form.city = null;
      if (this.form.division == "") this.form.division = null;
      if (this.form.country != null) country = this.form.country.id;
      if (this.form.city != null) city = this.form.city.id;
      if (this.form.division != null) division = this.form.division.id;
      if (this.parent.plan != null) plan = this.parent.plan.id
      if(JSON.stringify(this.parent.provider_provider_types) != '[]') providerType = JSON.stringify(this.parent.provider_provider_types)
      if (otherInfos.length == 0) {
        otherInfos = ""
      } else {
        otherInfos = JSON.stringify(otherInfos)
      };

      for( var i = 0; i < this.parent.imagesForm.images.length; i++ ){
        let file = this.parent.imagesForm.images[i];

        formData.append('images[' + i + ']', file);
      }

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", this.form.language);
      formData.append("locale", this.form.language);
      formData.append("provider_type", providerType);
      formData.append("country", country);
      formData.append("division", division);
      formData.append("city", city);
      formData.append("postal_code", this.form.postal_code);
      formData.append("name", this.parent.capitalizeFirstLetter(this.form.name));
      formData.append("website", this.form.website);
      formData.append("contact_no", JSON.stringify(this.parent.contact_number));
      formData.append("address", this.form.address);
      formData.append("facebook_url", this.form.facebook_url);
      formData.append("linkedin", this.form.linkedin);
      formData.append("other_info", otherInfos);
      formData.append("plan", plan);
      formData.append("brand_id", this.$brand.id);

      this.post_provider(formData)
        .then((resp) => {
          this.parent.btnloading = false;
          this.$bvModal.hide("provider-edit-modal");
          if (this.parent.providersResponseStatus) {
            let response = this.parent.providersResponseStatus;
            response.index = this.parent.editingIndex;
            this.update_provider(response)

            if(this.parent.initialProviderProviderTypesLength == 0 && this.parent.filterProviderBy == 'npt') this.parent.remove_from_provider_array(response)
            if(this.parent.provider_provider_types != 0) this.parent.checkAlgoliaPermission()

            let message = {
              update:
                this.$t("updated_message1") +
                this.$t("label.providers") +
                ` ID: ${this.parent.form.id} ` +
                this.$t("updated_message2"),
              title: {
                update: this.$t("record_updated"),
              },
            };

            this.parent.makeToast(
              "success",
              message.title.update,
              message.update
            );
            this.parent.successfullySavedProvider()
            this.parent.contact_number = [];
            this.parent.provider_provider_types  = [],
            this.modal.button.loading = false;
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
    },

    toastError(error){
      console.log(error);
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        this.parent.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    openProviderTypeModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.mtForm.action = "Add";
      this.parent.$bvModal.show("term-modal");
      // this.parent.modal.itemType.isVisible = true;
    },
    changeDivision(val) {
      let formData = new FormData();
      let vm = this;
      vm.loadingCities = true;
      formData.append("api_token", this.$user.api_token);
      if(val == null) {
        formData.append("id", this.country_id);
        formData.append("country_id", 'country_id');
          this.get_cities(formData).then((resp) => {
          vm.loadingCities = false;
        });
        return;
      }
      formData.append("id", val.id);
      formData.append("lang", this.form.language);
      this.get_cities(formData).then((resp) => {
        this.form.city = "";
        vm.loadingCities = false;
      });
    },

    changeCountry(val) {
      if(val == null) return;
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      formData.append("id", val.id);
      formData.append("lang", this.form.language);
      this.country_id = val.id;
      this.form.city = "";
      this.form.division = "";
      this.loadingProvinces = true;
      this.reset_cities();
      let vm = this;
      this.get_divisions(formData).then((resp) => {
        vm.loadingProvinces = false;
      });
      formData.append("country_id", 'country_id');
      this.get_cities(formData).then((resp) => {
      });
    },

    removeImage(index) {
			this.parent.imagesForm.images.splice(index,1)
			this.parent.imagesForm.imagesPlaceholder.splice(index,1)
		},

    addImages(){
      this.parent.imagesForm.images = this.parent.files
      this.parent.imagesForm.imagesPlaceholder = [];
			this.parent.imagesForm.images.forEach((image, index) => {
				this.parent.imagesForm.imagesPlaceholder[index] = URL.createObjectURL(image)
			})
    },

    checkExistingImages() {
      this.parent.$bvModal.show("existing-images-modal");
    },

    selectLang(event) {
      // this.form.language = event.target.value;
      let formData = new FormData();

      formData.append("api_token", this.$user.api_token);
      formData.append("id", this.form.id);
      formData.append("lang", event.target.value);
      this.form.language = event.target.value;
      this.get_provider_name(formData).then((resp) => {
        if (resp) {
          this.form.name = resp;
        } else {
          this.form.name = "";
        }
      });
    },

    openInformationTypeModal() {
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.$bvModal.show("information-type-add-modal");
    },

    openPaymentPlanModal() {
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.$bvModal.show("paymentplanmodal");
    },

    deleteProviderOtherInfoDiv: function (index) {
      let vm = this;

      $.confirm({
        title: this.$t("confirm_action"),
        content: vm.$t("label.delete_other_info_confirmation"),
        type: "red",
        typeAnimated: true,
        buttons: {
          tryAgain: {
            text: this.$t("confirm"),
            btnClass: "btn-red",
            action: function () {
              vm.parent.deleteProviderOtherInfoDiv(index);
            },
          },
          cancel: function () {},
        },
      });
    },
  },
};
</script>

<style></style>
