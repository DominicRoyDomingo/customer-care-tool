<template>
  <div class="create">
    <v-app id="create__container">
      <b-modal
        id="service-exclusive-add-modal"
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
                  {{ $t("service_type_category") }}
                </label>

                <el-row>
                  <el-col :span="24">
                    <v-select
                      name="service"
                      label="name"
                      v-model="form.service"
                      :options="$this.serviceTypes"
                      id="services"
                    >
                      <!-- <template #list-header>
                        <li style="margin-left:20px;" class="mb-2">
                          <b-link href="#" @click="openServiceTypeModal">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            {{ $t("service_type_add_new_button") }}
                          </b-link>
                        </li>
                      </template> -->
                    </v-select>
                  </el-col>
                </el-row>
              </v-container>

              <v-container>
                <label for="text_display">
                  {{ $t("label.text_display") }}
                </label>
                
                <input
                  id="text_display"
                  type="text"
                  name="text_display"
                  v-model="form.text_display"
                  class="form-control"
                  :placeholder="$t('label.text_display')"
                  autocomplete="off"
                  aria-describedby="text_display"
                />
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

                    <label class="form-check-label" for="inline-checkbox3"
                      ><small>Keep the form open?</small></label
                    >
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

    <!-- <ServiceType :$this="this" @loadTable="$this.loadServiceTypes"></ServiceType> -->
  </div>

  
</template>

<script>
// import Api from "./../../../shared/api.js";

import { createTags } from "./../../../api/tags.js";

import { mapActions, mapGetters } from "vuex";

// import ServiceType from "./../../includes/service-type/CreateComponent.vue";

import toast from "./../../../helpers/toast.js";

export default {

  mixins: [toast],

  props: ["$this"],

  // components: {

  //   ServiceType,
  // },

  data: function() {
    return {
      modal: this.$this.modal.add,

      submitted: false,

      modalId: "service-add-modal",

      keep_open: false,

      form: this.$this.form,

      formData: this.$this.formData,

      formGroups: [],

      modalServiceType: {
        add: {

        },
        
        createService: {
          name: "service_type_add_new_button",

          id: "service-add-modal",

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
    ...mapActions("services_exclusive", ["post_services_exclusive", "add_service"]),
    onGetFile(event) {
      this.form.icon = event.target.files[0];
    },
    
    modalClose(done) {

      $(".error-provider").remove();

      $("#name, #service_type").removeAttr(
        "style"
      );
      
      this.file = "";

      this.$this.$bvModal.hide("service-exclusive-add-modal");

      // this.$this.modal.createProviderServices != undefined
      //   ? this.$this.$bvModal.show(this.$this.modalId)
      //   : this.form.reset();

      this.form.reset();

      this.keep_open = false;
    },

    onSubmit() {
      let formData = new FormData();

      let service = "";

      if (this.form.service.id != undefined)
        service = this.form.service.id;

      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("text_display", this.form.text_display);
      formData.append("service", service);

      this.post_services_exclusive(formData)
        .then((resp) => {
          this.$this.btnloading = false;
          this.$bvModal.hide("service-exclusive-add-modal");

          if (this.$this.servicesExclusivesResponseStatus) {

            let message = {
              create:
                `${this.form.text_display}` +
                this.$t("created_message") +
                this.$t("successfull_service_exclusive"),
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );

            this.$emit("loadTable");
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
    },

    // serviceTypePage(){
    //   window.location.href = `/admin/service-type`;
    // },

    // openServiceTypeModal() {
    //   // this.modal.isVisible = false;
    //   this.$this.$bvModal.hide(this.modalId);
      
    //   this.$bvModal.show("service-type-add-modal");
    //   // this.$this.modal.itemType.isVisible = true;
    // },
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
