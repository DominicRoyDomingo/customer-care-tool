<template>
  <div class="create">
    <b-modal
      id="provider-services-add-modal"
      hide-footer
      hide-header
      size="xl"
      no-close-on-backdrop
    >
      <v-app id="service-services__container">
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
            <v-card-text v-if="parent.isServicesLoading">
              <v-container>
                <v-row align="center" justify="center" style="min-height: 200px">
                  <v-progress-circular
                    :size="50"
                    color="primary"
                    indeterminate
                  ></v-progress-circular>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-text class="modal__content" v-else>
              <v-tabs
                show-arrows
                center-active
                grow
                background-color="blue-grey lighten-5"
                slider-color="blue-grey darken-2"
                color="blue-grey darken-2"
              >  
                <v-tab v-for="(term, keyName) in parent.terms" :key="keyName">
                  {{ keyName }}
                </v-tab>
                <v-tab-item eager v-for="(term,keyName) in parent.terms"
                    :key="keyName"
                    >
                  <!-- <v-container>
                    <label for="image">
                      {{ item.text }}
                    </label>
                  </v-container> -->
                  <v-container>
                    <v-container>
                      <!-- <b-form-group>
                        <b-row class="text-center">
                            <v-col cols="12">
                              <b-input-group>
                                <template v-slot:append>
                                  <b-dropdown right class="dropdown--filter">
                                    <template slot="button-content">
                                      <i class="fa fa-filter" aria-hidden="true"> </i>
                                    </template>
                                    <b-dropdown-item @click="parent.filterByNOT('term_name')">
                                      {{ $t("table.filter_by_service_name") }}
                                    </b-dropdown-item>
                                    <b-dropdown-item @click="parent.filterByNOT('all_terms')">
                                      {{ $t("table.filter_by_all_services") }}
                                    </b-dropdown-item>
                                  </b-dropdown>
                                </template>
                                <b-form-input
                                  v-model="parent.filterBySearch"
                                  type="search"
                                  :placeholder="$t('search_here')"
                                  style="border-radius: 0; height: 36px"
                                ></b-form-input>
                              </b-input-group>
                            </v-col>
                        </b-row>
                      </b-form-group> -->
                      <b-form-group v-if="parent.isSearchByHasValue">
                        <label for="name">
                          {{$t('label.services')}}
                        </label>
                        <el-checkbox-group v-model="parent.selectedServices" 
                            style="column-count: 2; width: 100%">
                          <el-checkbox 
                            v-for="(term, index2) in term.checkableServices" 
                            :key="index2"
                            :label="term.id"
                            style="width: 100%;"
                          ><span v-html="term.name"></span></el-checkbox>
                        </el-checkbox-group>
                      </b-form-group>
                      <b-form-group v-else>
                        <b-input-group 
                        class="mb-5" 
                        v-for="(term, index) in term.checkableServices" 
                        :key="term.id"
                        
                        >
                          <label for="name">
                            {{index}}
                          </label>
                          <el-checkbox-group v-model="parent.selectedServices" 
                              style="column-count: 2; width: 100%">
                            <el-checkbox 
                              v-for="(ter, index2) in term" 
                              :key="index2"
                              :label="ter.id"
                              style="width: 100%;"
                              :disabled="ter.route_show == 'disabled'"
                            ><span v-html="ter.name"></span></el-checkbox>
                          </el-checkbox-group>
                        </b-input-group>
                      </b-form-group>
                      <div v-if="term.uncheckableServices.length > 0">
                        <span 
                          v-for="(term, index) in term.uncheckableServices"
                          :key="index"
                        >{{term.name}}<span class='red--text'>*</span><br></span>
                        <span class='red--text'>*</span>
                        <span>{{ `Services that are linked to ${keyName} but has been linked to a category not offered by ${keyName}` }}</span>
                      </div>
                      
                      &nbsp;<span v-if="term.isDisabledServices">{{ `    Disabled services under a category are services that are not offered by ${keyName}` }}</span>
                    </v-container>
                  </v-container>
                </v-tab-item>
              </v-tabs>
            </v-card-text>
            <v-divider style="margin-bottom: 0"></v-divider>
              <v-card-actions class="modal__footer blue-grey lighten-5">
                <v-btn
                  color="success"
                  tile
                  @click="openServiceModal"
                  :disabled="parent.isServicesLoading"
                >
                  <v-icon left>
                    mdi-plus
                  </v-icon>
                  {{ $t("service_add_new_button") }}
                </v-btn>
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
                  :disabled="parent.isServicesLoading"
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
                      {{ $t(modal.button.save) }}
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

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

export default {
  props: ["parent"],

  data: function() {

    let weekdays = [
          { value: 'Sunday', text: 'Sunday' },
          { value: 'Monday', text: 'Monday' },
          { value: 'Tuesday', text: 'Tuesday' },
          { value: 'Wednesday', text: 'Wednesday' },
          { value: 'Thursday', text: 'Thursday' },
          { value: 'Friday', text: 'Friday' },
          { value: 'Saturday', text: 'Saturday' },
        ]
    return {
      modal: this.parent.modal.createProviderServices,

      submitted: false,

      keep_open: false,

      form: this.parent.form,

      valid: true,

      formData: this.parent.formData,

      weekdays: weekdays,

      weekdays2: weekdays,

      formGroups: [],

      tests: [
            {
                title: "First Item",
                text: "This is the first text",
                id: 1
            },
            {
                title: "Second Item",
                text: "This is the second text",
                id: 2
            },
            {
                title: "Third Text",
                text: "This is the third text",
                id: 3
            },
        ]
    };
  },

  methods: {
    ...mapActions("provider_service", [
      "post_provider_service",
      "add_provider_service",
      "update_brand",
    ]),
    ...mapActions("providers", ["update_provider"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    modalClose(done) {
      $(".error-provider").remove();

      $("#provider_type_name").removeAttr(
        "style"
      );
      this.file = "";

      this.parent.$bvModal.hide("provider-services-add-modal");

      this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
      if(_.difference(this.parent.selectedServices,this.parent.originalSelectedServices).length == 0) {
        this.parent.isServiceRemoved = false
        if(this.parent.selectedServices.length != this.parent.originalSelectedServices.length) {
          this.parent.isServiceRemoved = true;
        }
      };
      
      this.modal.button.loading = true;
      let formData = new FormData();
      
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("providers", this.parent.providers_iden);
      formData.append("services", JSON.stringify(this.parent.selectedServices));
      formData.append("day_start", true);
      this.post_provider_service(formData)
        .then((resp) => {
          let object = new Object();
          object.service_count = resp.data.length;
          object.provider_services = resp.data;
          object.index = this.parent.editingIndex;
          this.update_provider(object)
          if(this.parent.filterProviderBy == 'nsa') this.parent.remove_from_provider_array(object)
          this.parent.loadAlgoliaSummary()
          this.parent.selectedServices = [];
          this.parent.btnloading = false;
          this.$bvModal.hide("provider-services-add-modal");
          if (this.parent.providerServiceResponseStatus) {
            let message = {
              create:
                `${this.$t("added_services_message")} ${this.parent.provider_name}`,
              removed:
                `${this.$t("removed_services_message")} ${this.parent.provider_name}`,
              title: {
                create: this.$t("added_services"),
                removed: this.$t("removed_services"),
              },
            };

            this.parent.makeToast(
              this.parent.isServiceRemoved ? "danger" : "success",
              this.parent.isServiceRemoved ? message.title.removed : message.title.create,
              this.parent.isServiceRemoved ? message.removed : message.create
            );
            
            this.modal.button.loading = false;
            this.parent.isServiceRemoved = false
            
          }
        })
        .catch((error) => {
          this.modal.button.loading = false;
          console.log(error);
          let errors = error.response.data.errors;
          $(".error-provider").remove();

          $("#provider_type_name").removeAttr(
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

          this.modal.button.loading = false;
          console.log(error);
        });
    },

    openServiceModal() {
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.mtForm.action = "Add";
      this.parent.$bvModal.show("term-modal");
    },
  },
};
</script>

<style></style>
