<template>
  <div class="create">
    <b-modal
      id="provider-group-add-to-group-modal"
      hide-footer
      hide-header
      size="lg"
      no-close-on-backdrop
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
                      <label for="name">
                        {{ $t("provider_group") }}
                      </label>
                      <v-select
                        name="provider_group"
                        label="provider_group_name"
                        v-model="form.provider_group"
                        :options="$this.provider_groups"
                        id="provider_group"
                      >
                        <template #list-header>
                          <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openProviderGroupModal">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("provider_group_add_new_button") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
                    </v-col>
                  </v-row>
                </v-container>
              </v-container>
            </v-card-text>
            <v-divider style="margin-bottom: 0"></v-divider>
              <v-card-actions class="modal__footer blue-grey lighten-5">
                <div>
                  <v-btn
                    color="error"
                    text
                    tile
                    link
                    @click.stop="$this.providerGroupPage"
                  >
                    <v-icon>
                      mdi-open-in-new
                    </v-icon>
                    {{ $t("label.go_to_provider_group_page") }}
                  </v-btn>
                </div>
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
      modal: this.$this.modal.addToGroup,

      submitted: false,

      keep_open: false,

      form: this.$this.form,

      valid: true,

      formData: this.$this.formData,

      country_id: "",

      formGroups: [],

      tabIndex: 0,

      loadingProvinces: false,

      loadingCities: false,
    };
  },

  methods: {
    ...mapActions("providers", ["post_add_to_group", "update_provider"]),
    
    modalClose(done) {
      $(".error-provider").remove();

      $("#name, #provider_type").removeAttr(
        "style"
      );

      this.file = "";

      this.$this.$bvModal.hide("provider-group-add-to-group-modal");

      this.form.reset();
      

      this.keep_open = false;
    },

    onSubmit() {
      this.modal.button.loading = true;
      let formData = new FormData();
      let providerGroup = "";
      if (this.form.provider_group != null) providerGroup = this.form.provider_group.id;
      formData.append("api_token", this.$user.api_token);
      formData.append("lang", this.form.language);
      formData.append("id", this.form.id);
      formData.append("provider_group", providerGroup);

      this.post_add_to_group(formData)
        .then((resp) => {
          
          this.$bvModal.hide("provider-group-add-to-group-modal");

          if (this.$this.providersResponseStatus) {
            let response = this.$this.providersResponseStatus;
            response.index = this.$this.editingIndex;
            this.update_provider(response)
            let message = {
              create:
                `${this.form.name} ` +
                this.$t("added_group_message") +
                ` ${this.form.provider_group.name}`,
              title: {
                create: this.$t("new_record_created"),
              },
            };

            this.$this.successfullySavedProvider()
            this.$this.checkAlgoliaPermission()
            this.$this.makeToast(
              "success",
              message.title.create,
              message.create
            );
            this.$this.contact_number = [];
            this.modal.button.loading = false;
            // this.$this.currentPage = 1;
            // this.$this.items.data = [];
            // this.$this.loadItems();
          }
        })
        .catch((error) => {
          console.log(error);
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

    openProviderGroupModal() {
      // this.modal.isVisible = false;
      this.$this.$bvModal.hide(this.$this.modalId);

      this.$this.$bvModal.show("provider-group-add-modal");
      // this.$this.modal.itemType.isVisible = true;
    },
  },
};
</script>
