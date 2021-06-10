<template>
  <div class="create">
    <v-app id="create__container">
      <b-modal
        id="provider-add-modal"
        hide-footer
        size="md"
        no-close-on-backdrop
        :title="$t(modal.name)"
      >
        <div class="p-2 margin-top">
          <form
            @submit.prevent="onSubmit"
            method="post"
            enctype="multipart/form-data"
            @keydown="form.errors.clear($event.target.name)"
          >
            <br />
            <b-form-group>
              <v-container>
                <label for="name">
                  {{ $t("label.name") }}
                </label>

                <input
                  id="name"
                  type="text"
                  name="name"
                  v-model="form.name"
                  class="form-control"
                  :placeholder="$t('label.name')"
                  autocomplete="off"
                  aria-describedby="name"
                />
              </v-container>
              <v-container>
                <label for="name">
                  {{ $t("provider_type_category") }}
                </label>

                <el-row>
                  <el-col :span="24">
                    <v-select
                      name="provider_type"
                      label="name"
                      v-model="form.provider_type"
                      :options="$this.provider_types"
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
                  </el-col>
                </el-row>
              </v-container>

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
                      :options="$this.countries"
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
                      :options="$this.divisions"
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
                      :options="$this.cities"
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
                <label for="contact number">
                  {{ $t("label.contact_no") }}
                </label>

                <b-form-tags v-model="$this.contact_number" class="mb-2" placeholder="Enter new contact no."></b-form-tags>
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

              <v-container>
                <label for="website">
                  {{ $t("label.website") }}
                </label>
<!-- 
                <el-input
                  :placeholder="$t('label.website')"
                  id="website"
                  name="website"
                  v-model="form.website"
                  clearable
                ></el-input> -->

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

              <v-container>
                <label for="image">
                  {{ $t("brands_image") }}
                </label>

                <b-form-file
                  accept=".jpg, .png"
                  v-model="$this.files"
                  :state="Boolean($this.files)"
                  multiple 
                  placeholder="Image/s"
                  drop-placeholder="Drop image/s here..."
                ></b-form-file>
              </v-container>
    
            </b-form-group>

            <b-form-group>
              <v-container>
                <!-- <span class="float-left">
                  <div class="form-check form-check-inline mr-1">
                    <input
                      class="form-check-input"
                      v-model="keep_open"
                      type="checkbox"
                      id="inline-checkbox3"
                    />

                    <label class="form-check-label" for="inline-checkbox3">
                      <small>Keep the form open?</small>
                    </label>
                  </div>
                </span> -->

                <span class="float-right">
                  <el-button
                    size="small"
                    @click="modalClose"
                    type="danger"
                    plain
                  >
                    {{ $t(modal.button.cancel) }}
                  </el-button>

                  <el-button
                    size="small"
                    native-type="submit"
                    type="success"
                    :loading="modal.button.loading"
                    style="color: #fff !important"
                  >
                    {{ $t(modal.button.save) }}
                  </el-button>
                </span>
              </v-container>
            </b-form-group>
          </form>
        </div>
      </b-modal>
    </v-app>
  </div>
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["$this", "parent"],

  data: function() {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      country_id: "",

      formGroups: [],

      loadingProvinces: false,

      loadingCities: false,
    };
  },

  methods: {
    ...mapActions("providers", ["post_provider", "add_provider", "update_brand"]),
    ...mapActions("division", ["get_divisions"]),
    ...mapActions("city", ["get_cities", "reset_cities"]),
    
    modalClose(done) {
      $(".error-provider").remove();

      $("#name, #provider_type").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("provider-add-modal");

      this.form.reset();
      

      this.keep_open = false;
    },

    onSubmit() {
      this.modal.button.loading = true;
      let formData = new FormData();
      let providerType = "";
      let country = ''
      let city = ''
      let division = ''
      let website = ''
      let facebookUrl = ''
      let address = ''
      let postal_code = ''
      let images = [];
      
      if (this.form.country != null) country = this.form.country.id;

      if (this.form.city != null) city = this.form.city.id;
      
      if (this.form.division != null) division = this.form.division.id;

      if (this.form.website != '') website = this.form.website;

      if (this.form.facebook_url != '') facebookUrl = this.form.facebook_url;
  
      if (this.form.address != '') address = this.form.address;

      if (this.form.postal_code != '') postal_code = this.form.postal_code;
 
      if (this.form.provider_type != '') providerType = this.form.provider_type.id;
    
      for( var i = 0; i < this.$this.files.length; i++ ){
        let file = this.$this.files[i];

        formData.append('images[' + i + ']', file);
      }
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("name", this.form.name);
      formData.append("provider_type", providerType);
      formData.append("country", country);
      formData.append("division", division);
      formData.append("city", city);
      formData.append("website", website);
      formData.append("postal_code", postal_code);
      formData.append("contact_no", JSON.stringify(this.$this.contact_number));
      formData.append("address", address);
      formData.append("facebook_url", facebookUrl);
      formData.append("brand_id", this.$brand.id);

      this.post_provider(formData)
        .then((resp) => {
          
          this.$bvModal.hide("provider-add-modal");

          if (this.$this.providersResponseStatus) {

            let message = {
              create:
                `${this.form.name}` +
                this.$t("created_message") +
                this.$t("successfull_provider"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );
            this.$this.contact_number = [];
            this.modal.button.loading = false;
            this.$this.loadItems();
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#name, #provider_type").removeAttr(
            "style"
          );
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
    },

    changeDivision(val) {
      let formData = new FormData();
      formData.append("api_token", this.$user.api_token);
      let vm = this;
      vm.loadingCities = true;
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
        this.form.city = null;
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
      this.form.city = null;
      this.form.division = null;
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

    openProviderTypeModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("provider-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>

<style></style>
