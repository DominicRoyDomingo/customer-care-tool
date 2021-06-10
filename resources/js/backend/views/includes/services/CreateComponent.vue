<template>
  <div class="create">
      <b-modal
        id="service-add-modal"
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
                          :rules="[(v) => !!v || $t('label.name_required')]"
                          :label="$t('label.name')"
                          suffix="*"
                          class="modal__input"
                          autocomplete="off"
                          required
                        >
                        </v-text-field>
                          <label for="name">
                            {{ $t("service_type_category") }}
                          </label>

                          <v-select
                            name="service_type"
                            label="name"
                            v-model="form.service_type"
                            :options="$this.service_types"
                            id="service_type"
                          >
                            <template #list-header>
                              <li style="margin-left:20px;" class="mb-2">
                                <b-link href="#" @click="openServiceTypeModal">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                  {{ $t("service_type_add_new_button") }}
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
                        {{ $t("label.description") }}
                      </v-tab>
                      <v-tab class="caption font-weight-bold">
                        {{ $t("brands_image") }}
                      </v-tab>
                      <v-tab-item eager>
                        <v-container>
                          <label for="description">
                            {{ $t("label.description") }}
                          </label>
                          
                          <input
                            id="description"
                            type="text"
                            name="description"
                            v-model="form.description"
                            class="form-control"
                            :placeholder="$t('label.description')"
                            autocomplete="off"
                            aria-describedby="description"
                          />
                        </v-container>
                      </v-tab-item>
                      <v-tab-item eager>
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
                        {{ $t(modal.button.save) }}
                      </div>
                    </div>
                  </v-btn>
                </v-card-actions>
            </v-form>
          </v-card>
          
        </v-app>
        
      </b-modal>
      <ServiceType :$this="this" @loadTable="$this.loadServiceTypes"></ServiceType>
    
  </div>

  
</template>

<script>
// import Api from "./../../../shared/api.js";

import { mapActions, mapGetters } from "vuex";

import ServiceType from "./../../includes/service-type/CreateComponent.vue";

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],

  props: ["$this"],

  components: {

    ServiceType,
  },

  data: function() {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      valid: true,

      modalId: "service-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],
      
      files: [],

      tabIndex: 0,

      modalServiceType: {
        add: {

        },
        
        createService: {
          name: "service_type_add_new_button",

          id: "service-type-add-modal",

          isVisible: false,

          button: {
            save: "save_button",

            cancel: "cancel",

            new: "service_type_add_new_button",

            loading: false,
          },
        },
      },
    };
  },

  methods: {
    ...mapActions("services", ["post_service", "add_service", "update_brand", "check_medical_type"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    
    modalClose(done) {

      $(".error-provider").remove();

      $("#name, #service_type").removeAttr(
        "style"
      );
      
      this.file = "";

      this.$this.$bvModal.hide("service-add-modal");

      this.$this.modal.createProviderServices != undefined
        ? this.$this.$bvModal.show(this.$this.modalId)
        : this.form.reset();

      this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
    this.$refs.form.validate();
    
    let vm = this;
    let formData = new FormData();
    formData.append("api_token", vm.$user.api_token);
    formData.append("lang", vm.form.language);
    formData.append("locale", vm.$i18n.locale);
    vm.check_medical_type(formData).then((resp) => {
      let serviceType = "";

      for( var i = 0; i < vm.$this.files.length; i++ ){
        let file = vm.$this.files[i];

        formData.append('images[' + i + ']', file);
        
      }

      if (vm.form.service_type.id != undefined)
        serviceType = vm.form.service_type.id;

      formData.append("api_token", vm.$user.api_token);
      formData.append("lang", vm.form.language);
      formData.append("name", vm.$this.capitalizeFirstLetter(vm.form.name));
      formData.append("description", vm.form.description);
      formData.append("service_type", serviceType);
      formData.append("brand_id", vm.$brand.id);
    
      if(resp == false) {
        $.confirm({
          title: vm.$t("warning").toUpperCase(),
          content: vm.$t("alerts.no_service_found_in_medical__term_type"),
          type: "red",
          typeAnimated: true,
          columnClass: "medium",
          buttons: {
            yes: {
              text: vm.$t("yes"),
              btnClass:
                "v-btn v-btn--contained v-size--default btn-red conf-btn conf-btn--yes",
              action: function(value) {
                if (value) {
                  vm.post_service(formData)
                  .then((resp) => {
                    vm.$this.btnloading = false;
                    vm.$bvModal.hide("service-add-modal");

                    if (vm.$this.servicesResponseStatus) {
                      console.log(vm.$this.servicesResponseStatus);
                      vm.add_service(vm.$this.servicesResponseStatus);

                      let message = {
                        create:
                          `${vm.form.name}` +
                          vm.$t("created_message") +
                          vm.$t("successfull_service"),
                        title: {
                          create: vm.$t("new_record_created"),
                        },
                      };

                      vm.$this.makeToast(
                        "success",
                        message.title.create,
                        message.create
                      );

                      vm.$emit("loadTable");
                    }
                  })
                  .catch((error) => {
                    let errors = error.response.data.errors;
                    $(".error-provider").remove();

                    $("#name, #service_type").removeAttr(
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
                }
              },
            },
            no: {
              text: vm.$t("no"),
              btnClass:
                "v-btn v-btn--flat v-btn--text v-size--default conf-btn conf-btn--no",
              action: function() {
              
              },
            },
          },
        });
      } else {
        formData.append("isMedical", true);
        vm.post_service(formData)
        .then((resp) => {
          vm.$this.btnloading = false;
          vm.$bvModal.hide("service-add-modal");

          if (vm.$this.servicesResponseStatus) {
            console.log(vm.$this.servicesResponseStatus);
            vm.add_service(vm.$this.servicesResponseStatus);

            let message = {
              create:
                `${vm.form.name}` +
                vm.$t("created_message") +
                vm.$t("successfull_service"),
              title: {
                create: vm.$t("new_record_created"),
              },
            };

            vm.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            vm.$emit("loadTable");
          }
        })
        .catch((error) => {
          let errors = error.response.data.errors;
          this.toastError(errors);
        });
      }
    })
    return;
    },

    toastError(error){
      let vm = this;
      for (const [key, value] of Object.entries(error)) {
        if(key == "service_type" || value[0].indexOf('is an existing') !== -1) this.$this.makeToast("danger", vm.$t('errors.error'), value);
      }
    },

    serviceTypePage(){
      window.location.href = `/admin/service-type`;
    },
    

    openServiceTypeModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.modalId);
      
      this.$bvModal.show("service-type-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },

  computed: {
    ...mapGetters("service_type", {
      service_types: "service_types",
      responseStatus: "get_response_status",
    }),
  },
};
</script>

<style></style>
