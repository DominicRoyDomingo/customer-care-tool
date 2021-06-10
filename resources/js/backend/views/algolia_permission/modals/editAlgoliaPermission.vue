<template>
  <div class="create">
    <b-modal
      id="algolia-permission-edit-modal"
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
                      <label for="class">
                        {{ $t("date_class") }}
                      </label>
                      <v-select
                        name="class"
                        label="name"
                        v-model="form.algoliaClass"
                        :options="parent.algoliaClasses"
                        id="class"
                      >
                        <template #list-header>
                          <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openClassModal">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("algolia_class_add_new_button") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
											<label for="brand" class="mt-2">
                        {{ $t("brands_name") }}
                      </label>
                      <v-select
                        name="brand"
                        label="name"
                        v-model="form.brand"
                        :options="parent.brands"
                        id="brand"
                      >
                        <template #list-header>
                          <li style="margin-left:20px;" class="mb-2">
                            <b-link href="#" @click="openBrandModal">
                              <i class="fa fa-plus" aria-hidden="true"></i>
                              {{ $t("label.new_brand") }}
                            </b-link>
                          </li>
                        </template>
                      </v-select>
											<label for="staging index name" class="mt-2">
                        {{ $t("algolia_permission_staging_index_name") }}
                      </label>
											<input
												id="staging_index_name"
												type="text"
												name="staging_index_name"
												v-model="form.staging_index_name"
												class="form-control"
												:placeholder="$t('algolia_permission_staging_index_name')"
												autocomplete="off"
												aria-describedby="staging_index_name"
											/>
											<label for="live index name" class="mt-2">
                        {{ $t("algolia_permission_live_index_name") }}
                      </label>
											<input
												id="live_index_name"
												type="text"
												name="live_index_name"
												v-model="form.live_index_name"
												class="form-control"
												:placeholder="$t('algolia_permission_live_index_name')"
												autocomplete="off"
												aria-describedby="live_index_name"
											/>
											<div>
												<v-switch
													v-model="form.sync"
													class="v-input--reverse"
												>
													<template #label>
														Synchronization to Algolia
													</template>
												</v-switch>
											</div>
                    </v-col>
                  </v-row>
                </v-container>
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
      modal: this.parent.modal.editAlgoliaPermission,

      submitted: false,

      keep_open: false,

      form: this.parent.algoliaPermissionForm,

      valid: true,

      formData: this.parent.formData,

      country_id: "",

      image: undefined,

      formGroups: [],
    };
  },

  methods: {
    ...mapActions("algolia_permission", ["post_algolia_permission", "update_algolia_permission"]),
    
    modalClose(done) {
      this.removeErrors();
      this.parent.$bvModal.hide("algolia-permission-edit-modal");

      this.parent.modal.add != undefined
        ? this.parent.$bvModal.show(this.parent.modalId)
        : this.form.reset();
    },

    onSubmit() {
      this.$refs.form.validate();
      this.modal.button.loading = true;
			let brand = "";
			let algoliaClass = "";
			
			if (this.form.brand != null) brand = this.form.brand.id;
			if (this.form.algoliaClass != null) algoliaClass = this.form.algoliaClass.id;

			let params = {
				id: this.form.id,
				api_token: this.$user.api_token,
				class: algoliaClass,
				brand: brand,
				staging_index_name: this.form.staging_index_name,
				live_index_name: this.form.live_index_name,
				sync: this.form.sync,
			}
			
      this.post_algolia_permission(params)
        .then((resp) => {
          this.removeErrors();
          this.$bvModal.hide("algolia-permission-edit-modal");

					let response = this.parent.algoliaPermissionResponseStatus;
          response.index = this.parent.editingIndex;
          console.log(response);
          this.update_algolia_permission(response);

					let message = {
						update: this.$t( 'updated_message1' ) + this.$t( 'algolia_permission' ) + ` ID: ${this.form.id} ` + this.$t( 'updated_message2' ),
						title: {
							update: this.$t( 'record_updated' )
						}
					};
					
					this.parent.makeToast(
						"success",
						message.title.update,
						message.update
					);

					this.modal.button.loading = false;
					this.parent.successfullySavedAlgoliaPermission()
        })
        .catch((error) => {
          this.removeErrors();
          this.modal.button.loading = false;
          let errors = error.response.data.errors;

          for (let field of Object.keys(errors)) {
            let el = $(`#${field}`);
            if(errors[field][0].indexOf('existing') !== -1) {
              this.toastError(errors[field][0]);
              continue;
            }

            if(el.hasClass("v-select")) {
              el.children().first().attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );
            } else {
              el.attr(
                "style",
                "border-color: #ff3b3b; box-shadow: 0px 2px 2px -3px rgba(255, 59, 59, 0.2), 0px 5px 7px 1px rgba(255, 59, 59, 0.14), 0px 1px 8px 1px rgba(255, 59, 59, 0.12); #ff3b3b !important; margin-bottom: 5px"
              );
            }

            el.after(
              $(
                '<span style="color: #ff3b3b;" class="field-error">' +
                  `${this.$t('errors.'+errors[field][0])}` +
                  "</span>"
              )
            );
          }
        });
    },

    toastError(error){
      let vm = this;
      this.parent.makeToast("danger", vm.$t('errors.error'), vm.$t(error));
    },

		openClassModal() {
      // this.modal.isVisible = false;
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.className = "";
      this.parent.$bvModal.show("algolia-class-add-modal");
      // this.parent.modal.itemType.isVisible = true;
    },

		openBrandModal() {
      this.parent.$bvModal.hide(this.parent.modalId);
      this.parent.modal.createBrand.isVisible = true;
    },

    removeErrors() {
      $('.field-error').remove();
      $('#staging_index_name, #live_index_name').removeAttr('style')
      $('#brand').children().first().removeAttr('style')
      $('#class').children().first().removeAttr('style')
    }
  },
};
</script>

<style>
.v-input--reverse .v-input__slot {
  flex-direction: row-reverse;
  justify-content: flex-end;
}

.v-input--reverse .v-input__slot .v-application--is-ltr .v-input--selection-controls__input {
	margin-right: 0;
	margin-left: 8px;
}

.v-input--reverse .v-input__slot .v-application--is-ltr .v-input--selection-controls__input {
	margin-right: 0;
	margin-left: 8px;
}

.v-input--reverse .v-label {
	margin-right: 16px;
  margin-bottom: 0 !important;
	flex: initial !important;
}
</style>
